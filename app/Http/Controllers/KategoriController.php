<?php

namespace App\Http\Controllers;


use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;


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
                $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a>';
                $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
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



}