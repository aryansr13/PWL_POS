<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        return PenjualanModel::with(['user', 'penjualanDetail.barang'])->get();
    }

    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
        'pembeli' => 'required|string|max:100',
        'penjualan_kode' => 'required|string|max:20|unique:t_penjualan',
        'details' => 'required|array|min:1',
        'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
        'details.*.jumlah' => 'required|integer|min:1'
    ]);

    DB::beginTransaction();
    try {
        $penjualan = PenjualanModel::create([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => now()
        ]);

        foreach ($request->details as $detail) {
            $barang = BarangModel::find($detail['barang_id']);
            
            // Gunakan accessor untuk cek stok
            if ($barang->barang_stok < $detail['jumlah']) {
                throw new \Exception("Stok barang {$barang->barang_nama} tidak mencukupi");
            }

            // Simpan detail dengan harga dari database
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $detail['barang_id'],
                'jumlah' => $detail['jumlah'],
                'harga' => $barang->harga_jual // Ambil harga dari database
            ]);

            // Jika menggunakan stok fisik:
            // $barang->decrement('barang_stok', $detail['jumlah']);
        }

        DB::commit();
        return response()->json(['success' => true, 'data' => $penjualan]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])->find($id);
        
        if (!$penjualan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'penjualan' => $penjualan
        ]);
    }

    public function update(Request $request, $id)
    {
        $penjualan = PenjualanModel::find($id);
        
        if (!$penjualan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode,'.$id.',penjualan_id',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // Update penjualan
            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode
            ]);

            // Restore old stock
            foreach ($penjualan->penjualanDetail as $detail) {
                $barang = BarangModel::find($detail->barang_id);
                $barang->barang_stok += $detail->jumlah;
                $barang->save();
            }

            // Delete old details
            $penjualan->penjualanDetail()->delete();

            // Add new details
            foreach ($request->details as $detail) {
                $barang = BarangModel::find($detail['barang_id']);
                
                // Check stock
                if ($barang->barang_stok < $detail['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi untuk barang: ' . $barang->barang_nama
                    ], 422);
                }

                // Create detail
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $detail['barang_id'],
                    'jumlah' => $detail['jumlah'],
                    'harga' => $detail['harga']
                ]);

                // Update stock
                $barang->barang_stok -= $detail['jumlah'];
                $barang->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'penjualan' => $penjualan->load('penjualanDetail.barang')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $penjualan = PenjualanModel::find($id);
        
        if (!$penjualan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($penjualan->penjualanDetail as $detail) {
                $barang = BarangModel::find($detail->barang_id);
                $barang->barang_stok += $detail->jumlah;
                $barang->save();
            }

            // Delete details
            $penjualan->penjualanDetail()->delete();
            
            // Delete penjualan
            $penjualan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}