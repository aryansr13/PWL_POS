<?php

namespace App\Http\Controllers;


use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    // Menampilkan halaman awal kategori
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori'; // set menu yang sedang aktif

       

        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    // Ambil data kategori dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        return DataTables::of($kategoris)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) { // menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<bsutton onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                    '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
{
    $breadcrumb = (object) [
        'title' => 'Tambah kategori',
        'list' => ['Home', 'kategori', 'Tambah']
    ];

    $page = (object) [
        'title' => 'Tambah kategori baru'
    ];

    
    $activeMenu = 'kategori'; // Set menu yang sedang aktif

    return view('kategori.create', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'activeMenu' => $activeMenu
    ]);
}

public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,',
        'kategori_nama' => 'required|string|max:100',
    ]);

    // Simpan data ke database
    KategoriModel::create([
        'kategori_kode' => $request->kategori_kode,
        'kategori_nama' => $request->kategori_nama,
    ]);

    // Redirect dengan pesan sukses
    return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
}

public function show(string $id)
{
    // Ambil data kategori beserta relasi level
    $kategori = KategoriModel::find($id);

    // Breadcrumb untuk navigasi
    $breadcrumb = (object) [
        'title' => 'Detail Kategori',
        'list' => ['Home', 'Kategori', 'Detail']
    ];

    // Informasi halaman
    $page = (object) [
        'title' => 'Detail Kategori'
    ];

    // Menu aktif
    $activeMenu = 'kategori';

    // Tampilkan view dengan data
    return view('kategori.show', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'kategori' => $kategori,
        'activeMenu' => $activeMenu
    ]);
}

public function edit(string $id)
{
    $kategori = KategoriModel::find($id);
  

    $breadcrumb = (object) [
        'title' => 'Edit kategori',
        'list'  => ['Home', 'kategori', 'Edit']
    ];

    $page = (object) [
        'title' => 'Edit kategori'
    ];

    $activeMenu = 'kategori'; // set menu yang sedang aktif

    return view('kategori.edit', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'kategori' => $kategori,
        'activeMenu' => $activeMenu
    ]);
}

// Menyimpan perubahan data kategori
public function update(Request $request, string $id)
{
    $request->validate([
        'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
        'kategori_nama' => 'required|string|max:100',
        
        
    ]);

    KategoriModel::find($id)->update([
        'kategori_kode' => $request->kategori_kode,
        'kategori_nama' => $request->kategori_nama,
        
        
    ]);

    return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
}

public function destroy(string $id)
{
    $check = KategoriModel::find($id);
    
    if (!$check) {
        // Mengecek apakah data kategori dengan ID yang dimaksud ada atau tidak
        return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
    }
    
    try {
        KategoriModel::destroy($id);
        // Hapus data level
        return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
    } catch (\Illuminate\Database\QueryException $e) {
        // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
        return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
    }
}

public function create_ajax()
{
    return view('kategori.create_ajax',);
}

public function store_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|min:3',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validasi gagal",
                'msgField' => $validator->errors()
            ]);
        }

        KategoriModel::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data kategori berhasil disimpan'
        ]);
    }
    return redirect('/kategori');
}

public function edit_ajax(string $id)
{
    $kategori = KategoriModel::find($id);
    return view('kategori.edit_ajax', [
        'kategori' => $kategori,
    ]);
}

public function update_ajax(Request $request, $id)
{
    // cek apakah request dari ajax
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'kategori_kode' => 'required|string|min:3',
            'kategori_nama' => 'required|string|min:3',
        ];
        // use Illuminate\Support\Facades\Validator;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false, // respon json, true: berhasil, false: gagal
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors() // menunjukkan field mana yang error
            ]);
        }
        $check = KategoriModel::find($id);
        if ($check) {
            $check->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    return response()->json([
        'status' => false,
        'message' => 'Request Bukan Ajax'
    ]);

    return redirect('/');
}

public function confirm_ajax(string $id)
{
    $kategori = KategoriModel::find($id);

    return view('kategori.confirm_ajax', [
        'kategori' => $kategori
    ]);
}

public function delete_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $kategori = KategoriModel::find($id);

        if ($kategori) {
            $kategori->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
   return redirect('/');
}



}