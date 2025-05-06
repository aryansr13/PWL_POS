<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use App\Models\PenjualanModel;
use Illuminate\Support\Facades\DB;
use App\Models\PenjualanDetailModel;
use App\Models\StokModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
{
    $penjualan = PenjualanModel::with('user')->select(
        'penjualan_id',
        'user_id',
        'pembeli',
        'penjualan_kode',
        'penjualan_tanggal'
    );

    return DataTables::of($penjualan)
        ->addIndexColumn()
        ->addColumn('user', function ($penjualan) { // Tambahkan kolom user
            return $penjualan->user ? $penjualan->user->username : '-';
        })
        ->addColumn('aksi', function ($penjualan) {
            // $btn = '<a href="' . url('/penjualan/' . $penjualan->penjualan_id) . '" class="btn btn-info btn-sm">Detail</a>';
            // $btn .= '<a href="' . url('/penjualan/' . $penjualan->penjualan_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>';
            // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/penjualan/' . $penjualan->penjualan_id) . '">' .
            //     csrf_field() . method_field('DELETE') .
            //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
            // return $btn;
            $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
            '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
        $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
            '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
        $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
            '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
        return $btn;
            
        })
        ->rawColumns(['aksi'])
        ->make(true);
}




    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list' => ['Home', 'Penjualan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah transaksi penjualan baru'
        ];

        $users = UserModel::all();
        $activeMenu = 'penjualan';

        return view('penjualan.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'users' => $users, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date'
        ]);

        PenjualanModel::create($request->all());

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil disimpan');
    }

    public function show(string $id)
{
    $penjualan = PenjualanModel::with('user')->find($id);
    $penjualanDetail = PenjualanDetailModel::where('penjualan_id', $id)->with('barang')->get();

    if (!$penjualan) {
        return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
    }

    $breadcrumb = (object) [
        'title' => 'Detail Penjualan',
        'list' => ['Home', 'Penjualan', 'Detail']
    ];

    $page = (object) [
        'title' => 'Detail informasi penjualan'
    ];

    $activeMenu = 'penjualan';

    return view('penjualan.show', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'penjualan' => $penjualan,
        'penjualanDetail' => $penjualanDetail,
        'activeMenu' => $activeMenu
    ]);
}


public function edit(string $id)
{
    $penjualan = PenjualanModel::find($id);
    $users = UserModel::all(); // Ambil semua user

    if (!$penjualan) {
        return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
    }

    $breadcrumb = (object) [
        'title' => 'Edit Penjualan',
        'list' => ['Home', 'Penjualan', 'Edit']
    ];

    $page = (object) [
        'title' => 'Edit Data Penjualan'
    ];

    $activeMenu = 'penjualan';

    return view('penjualan.edit', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'penjualan' => $penjualan,
        'users' => $users, // Kirim data user ke view
        'activeMenu' => $activeMenu
    ]);
}



public function update(Request $request, string $id)
{
    $request->validate([
        'user_id' => 'required|integer',
        'pembeli' => 'required|string|max:50',
        'penjualan_kode' => 'required|string|max:20',
        'penjualan_tanggal' => 'required|date'
    ]);

    $penjualan = PenjualanModel::find($id);
    
    if (!$penjualan) {
        return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
    }

    $penjualan->update([
        'user_id' => $request->user_id,
        'pembeli' => $request->pembeli,
        'penjualan_kode' => $request->penjualan_kode,
        'penjualan_tanggal' => $request->penjualan_tanggal
    ]);

    return redirect('/penjualan')->with('success', 'Data penjualan berhasil diperbarui');
}

public function destroy(string $id)
{
    $penjualan = PenjualanModel::find($id);

    if (!$penjualan) {
        return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
    }

    try {
        // Hapus detail penjualan terlebih dahulu
        PenjualanDetailModel::where('penjualan_id', $id)->delete();

        // Hapus data penjualan utama
        $penjualan->delete();

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
    } catch (\Illuminate\Database\QueryException $e) {
        return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus karena masih terkait dengan tabel lain');
    }
}

public function create_ajax()
{
    $users = UserModel::all();
    $barangs = BarangModel::all();
    return view('penjualan.create_ajax')->with(['users' => $users, 'barangs' => $barangs]);
}

public function store_ajax(Request $request)
{
    $rules = [
        'pembeli'             => ['required', 'string', 'max:100'],
        'penjualan_kode'      => ['required', 'string', 'max:20', 'unique:t_penjualan,penjualan_kode'],
        'details'             => ['required', 'array', 'min:1'],
        'details.*.barang_id' => ['required', 'integer'],
        'details.*.jumlah'    => ['required', 'integer', 'min:1'],
        'details.*.harga'     => ['required', 'numeric'],
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'message'  => 'Validasi Gagal',
            'msgField' => $validator->errors()
        ]);
    }

    DB::beginTransaction();
    try {
        // Data untuk tabel t_penjualan
        $dataPenjualan = $request->only(['pembeli', 'penjualan_kode']);
        $dataPenjualan['user_id'] = auth()->id();
        $dataPenjualan['penjualan_tanggal'] = now();

        $penjualan = PenjualanModel::create($dataPenjualan);

        foreach ($request->details as $index => $detail) {
            $barang = BarangModel::find($detail['barang_id']);

            if (!$barang) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Barang tidak ditemukan pada baris ke-' . ($index + 1)
                ]);
            }

            // Menggunakan accessor untuk stok real-time
            $stokTersedia = $barang->barang_stok; 

            if ($stokTersedia < 1) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Stok barang tidak tersedia atau habis pada baris ke-' . ($index + 1)
                ]);
            }

            if ($detail['jumlah'] > $stokTersedia) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Jumlah yang diminta melebihi stok yang tersedia pada baris ke-' . ($index + 1)
                ]);
            }

            // Simpan detail penjualan
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id'    => $detail['barang_id'],
                'jumlah'       => $detail['jumlah'],
                'harga'        => $detail['harga'],
            ]);

            // Kurangi stok barang berdasarkan jumlah yang dijual
            StokModel::create([
                'barang_id'    => $detail['barang_id'],
                'user_id'      => auth()->id(),
                'stok_tanggal' => now(),
                'stok_jumlah'  => -$detail['jumlah'], // stok keluar
                'supplier_id'  => null, // karena bukan dari supplier
            ]);
        }

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Data penjualan beserta detail berhasil disimpan'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => false,
            'message' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()
        ]);
    }
}

public function edit_ajax($id)
{
    $penjualan = PenjualanModel::with(['penjualanDetail.barang'])->find($id);
    $barang = BarangModel::all();
    
    if (!$penjualan) {
        return response()->json([
            'status' => false,
            'message' => 'Data penjualan tidak ditemukan'
        ]);
    }

    return view('penjualan.edit_ajax', [
        'penjualan' => $penjualan,
        'barang' => $barang
    ]);
}

public function update_ajax(Request $request, $id)
{
    $penjualan = PenjualanModel::find($id);
    
    if (!$penjualan) {
        return response()->json([
            'status' => false,
            'message' => 'Data penjualan tidak ditemukan'
        ]);
    }

    $rules = [
        'pembeli' => 'required|string|max:100',
        'details' => 'required|array|min:1',
        'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
        'details.*.jumlah' => 'required|integer|min:1',
        'details.*.harga' => 'required|numeric|min:0'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'msgField' => $validator->errors()
        ]);
    }

    DB::beginTransaction();
    try {
        // Update main penjualan data
        $penjualan->update([
            'pembeli' => $request->pembeli
        ]);

        // Get current details for stock restoration
        $currentDetails = $penjualan->penjualanDetail;

        // Process old details - restore stock
        foreach ($currentDetails as $detail) {
            StokModel::create([
                'barang_id' => $detail->barang_id,
                'user_id' => auth()->id(),
                'stok_tanggal' => now(),
                'stok_jumlah' => $detail->jumlah, // return stock
                'supplier_id' => null
            ]);
        }

        // Delete old details
        $penjualan->penjualanDetail()->delete();

        // Process new details
        foreach ($request->details as $detail) {
            $barang = BarangModel::find($detail['barang_id']);
            
            // Check stock availability
            $stokTersedia = $barang->barang_stok;
            if ($stokTersedia < $detail['jumlah']) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Stok tidak mencukupi untuk barang: ' . $barang->barang_nama
                ]);
            }

            // Create new detail
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $detail['barang_id'],
                'jumlah' => $detail['jumlah'],
                'harga' => $detail['harga']
            ]);

            // Reduce stock
            StokModel::create([
                'barang_id' => $detail['barang_id'],
                'user_id' => auth()->id(),
                'stok_tanggal' => now(),
                'stok_jumlah' => -$detail['jumlah'], // reduce stock
                'supplier_id' => null
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data penjualan berhasil diperbarui'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
}

public function show_ajax($id)
{
    $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])->find($id);

    $penjualanDetail = $penjualan->penjualanDetail;

    return view('penjualan.show_ajax', ['penjualanDetail' => $penjualanDetail]);
}

public function confirm_ajax($id)
{
    $penjualan = PenjualanModel::with(['penjualanDetail.barang', 'user'])->find($id);
    return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
}

public function delete_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $penjualan = PenjualanModel::find($id);
        if ($penjualan) {
            $penjualan->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Data penjualan beserta detailnya berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }
    }
}

public function import()
{
    return view('penjualan.import');
}

public function import_ajax(Request $request)
{
    if (! $request->ajax() && ! $request->wantsJson()) {
        return redirect()->back();
    }

    // Validasi file Excel
    $validator = Validator::make($request->all(), [
        'file_penjualan' => ['required','mimes:xlsx','max:2048'],
    ]);
    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'message'  => 'Validasi gagal',
            'msgField' => $validator->errors()
        ]);
    }

    // Load spreadsheet
    $path        = $request->file('file_penjualan')->getPathname();
    $reader      = IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);

    $sheetH = $spreadsheet->getSheet(0)->toArray(null, true, true, true);
    $sheetD = $spreadsheet->getSheet(1)->toArray(null, true, true, true);

    DB::beginTransaction();
    try {
        $mapKode = [];

        // Proses sheet header (penjualan)
        foreach ($sheetH as $rowNum => $row) {
            if ($rowNum === 1) continue;

            $userId  = intval($row['A'] ?? 0);
            $pembeli = trim($row['B']  ?? '');
            $kode    = trim($row['C']  ?? '');
            $tgl     = trim($row['D']  ?? '');

            if (! $userId || $kode === '' || ! $tgl) continue;

            $p = PenjualanModel::create([
                'user_id'           => $userId,
                'pembeli'           => $pembeli,
                'penjualan_kode'    => $kode,
                'penjualan_tanggal' => date('Y-m-d H:i:s', strtotime($tgl)),
            ]);

            $mapKode[$kode] = $p->penjualan_id;
        }

        // Proses sheet detail
        foreach ($sheetD as $rowNum => $row) {
            if ($rowNum === 1) continue;

            $kode     = trim($row['A'] ?? '');
            $barangId = intval($row['B'] ?? 0);
            $jumlah   = intval($row['C'] ?? 0);
            $harga    = floatval($row['D'] ?? 0);

            if (! isset($mapKode[$kode])) {
                throw new \Exception("Header penjualan kode “{$kode}” tidak ditemukan (baris {$rowNum}).");
            }

            $penjualanId = $mapKode[$kode];

            $barang = BarangModel::find($barangId);
            if (! $barang) {
                throw new \Exception("Barang dengan ID {$barangId} tidak ditemukan (baris {$rowNum}).");
            }

            if ($barang->barang_stok < $jumlah) {
                throw new \Exception("Stok tidak mencukupi untuk barang “{$barang->barang_nama}” (baris {$rowNum}).");
            }

            // Simpan detail penjualan
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualanId,
                'barang_id'    => $barangId,
                'jumlah'       => $jumlah,
                'harga'        => $harga,
            ]);

            // Kurangi stok: catat stok keluar di tabel t_stok
            StokModel::create([
                'barang_id'    => $barangId,
                'user_id'      => $userId,
                'stok_tanggal' => now(),
                'stok_jumlah'  => -$jumlah,
                'supplier_id'  => null, // karena ini bukan barang masuk
            ]);
        }

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Import berhasil: data penjualan & detail tersimpan, stok terupdate.'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => false,
            'message' => 'Import gagal: ' . $e->getMessage()
        ]);
    }
}


public function export_excel()
{
    // Ambil semua penjualan beserta relasi user dan detail->barang
    $penjualans = PenjualanModel::with(['user', 'penjualanDetail.barang'])
        ->orderBy('penjualan_tanggal')
        ->get();

    // Buat objek spreadsheet dan sheet pertama
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Master Penjualan');

    // Header untuk sheet Master
    $sheet1->setCellValue('A1', 'No');
    $sheet1->setCellValue('B1', 'User');
    $sheet1->setCellValue('C1', 'Pembeli');
    $sheet1->setCellValue('D1', 'Kode Penjualan');
    $sheet1->setCellValue('E1', 'Tanggal Penjualan');
    $sheet1->getStyle('A1:E1')->getFont()->setBold(true);

    // Isi data master
    $row = 2;
    $no = 1;
    foreach ($penjualans as $penjualan) {
        $sheet1->setCellValue("A{$row}", $no);
        $sheet1->setCellValue("B{$row}", $penjualan->user->username);
        $sheet1->setCellValue("C{$row}", $penjualan->pembeli);
        $sheet1->setCellValue("D{$row}", $penjualan->penjualan_kode);
        $sheet1->setCellValue("E{$row}", date('Y-m-d H:i:s', strtotime($penjualan->penjualan_tanggal)));
        $no++;
        $row++;
    }

    // Auto‑size kolom sheet1
    foreach (range('A','E') as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }

    // Buat sheet kedua untuk detail
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Detail Penjualan');

    // Header untuk sheet Detail
    $sheet2->setCellValue('A1', 'No');
    $sheet2->setCellValue('B1', 'Kode Penjualan');
    $sheet2->setCellValue('C1', 'Nama Barang');
    $sheet2->setCellValue('D1', 'Jumlah');
    $sheet2->setCellValue('E1', 'Harga');
    $sheet2->getStyle('A1:E1')->getFont()->setBold(true);

    // Isi data detail
    $row = 2;
    $no = 1;
    foreach ($penjualans as $penjualan) {
        foreach ($penjualan->penjualanDetail as $detail) {
            $sheet2->setCellValue("A{$row}", $no);
            $sheet2->setCellValue("B{$row}", $penjualan->penjualan_kode);
            $sheet2->setCellValue("C{$row}", $detail->barang->barang_nama);
            $sheet2->setCellValue("D{$row}", $detail->jumlah);
            $sheet2->setCellValue("E{$row}", $detail->harga);
            $no++;
            $row++;
        }
    }

    // Auto‑size kolom sheet2
    foreach (range('A','E') as $col) {
        $sheet2->getColumnDimension($col)->setAutoSize(true);
    }

    // Kirim header untuk download
    $writer   = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data_Penjualan_Detail_' . date('Y-m-d_H-i-s') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. $filename .'"');
    header('Cache-Control: max-age=0');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    $writer->save('php://output');
    exit;
}

public function export_pdf(){
    $penjualan = PenjualanModel::with(['user','penjualanDetail'])
        ->orderBy('penjualan_id')
        ->orderBy('penjualan_kode')
        ->get();


    // use Barryvdh\DomPDF\Facade\Pdf;
    $pdf = PDF::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
    $pdf->setPaper('A4', 'portrait'); // set ukuran kertas dan orientasi
    $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
    $pdf->render(); // render pdf

    return $pdf->stream('Data Supplier '.date('Y-m-d H-i-s').'.pdf');
}

public function export_receipt($id)
{
    $penjualan = PenjualanModel::with(['user', 'penjualanDetail.barang'])
        ->find($id);

    $pdf = Pdf::loadView('penjualan.receipt', compact('penjualan'));
    $pdf->setPaper('A6', 'portrait');
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'Struk_' . $penjualan->penjualan_kode . '.pdf';
    return $pdf->stream($filename);
}


}
