<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+'); // Parameter {id} harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin']);
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);

    Route::middleware(['authorize:ADM,MNG,STF,KSR,SPV'])->group(function () {
 

        Route::get('/profile', [ProfileController::class, 'profil'])->name('profil');  
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');


    });    

    Route::middleware(['authorize:ADM,MNG'])->group(function () {
    Route::prefix('level')->group(function () {
        Route::get('/', [LevelController::class, 'index']);
        Route::post('/list', [LevelController::class, 'list']);
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        Route::post('/ajax', [LevelController::class, 'store_ajax']);
        Route::get('/{id}', [LevelController::class, 'show']);
        Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']); // menampilkan detail Level ajax
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
        Route::delete('/{id}', [LevelController::class, 'destroy']);
        Route::get('/import', [LevelController::class, 'import']); // menampilkan halaman form import Level
        Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // menyimpan data Level dari file import
        Route::get('/export_excel', [LevelController::class,'export_excel']); // ajax export excel
        Route::get('/export_pdf', [LevelController::class,'export_pdf']); // ajax export pdf
    });
    });

    Route::middleware(['authorize:ADM,'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/list', [UserController::class, 'list']);
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        Route::post('/ajax', [UserController::class, 'store_ajax']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/import', [UserController::class, 'import']); // menampilkan halaman form import User
        Route::post('/import_ajax', [UserController::class, 'import_ajax']); // menyimpan data User dari file import
        Route::get('/export_excel', [UserController::class,'export_excel']); // ajax export excel
        Route::get('/export_pdf', [UserController::class,'export_pdf']); // ajax export pdf
    });
    });


    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/list', [KategoriController::class, 'list']);
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
        Route::post('/ajax', [KategoriController::class, 'store_ajax']);
        Route::get('/{id}', [KategoriController::class, 'show']);
        Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
        Route::delete('/{id}', [KategoriController::class, 'destroy']);
        Route::get('/import', [KategoriController::class, 'import']); // menampilkan halaman form import Kategori
        Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); // menyimpan data Kategori dari file import
        Route::get('/export_excel', [KategoriController::class,'export_excel']); // ajax export excel
        Route::get('/export_pdf', [KategoriController::class,'export_pdf']); // ajax export pdf
        
    });
    });

    Route::middleware(['authorize:ADM,MNG'])->group(function () {
    Route::prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/list', [SupplierController::class, 'list']);
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
        Route::post('/ajax', [SupplierController::class, 'store_ajax']);
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::get('/import', [SupplierController::class, 'import']); // menampilkan halaman form import Supplier
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // menyimpan data Supplier dari file import
        Route::get('/export_excel', [SupplierController::class,'export_excel']); // ajax export excel
        Route::get('/export_pdf', [SupplierController::class,'export_pdf']); // ajax export pdf
    });
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
    Route::prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('barang.index');
        Route::post('/list', [BarangController::class, 'list']);
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
        Route::post('/ajax', [BarangController::class, 'store_ajax']);
        Route::get('/{id}', [BarangController::class, 'show']);
        Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
        Route::delete('/{id}', [BarangController::class, 'destroy']);
        Route::get('/import', [BarangController::class, 'import']); 
        Route::post('/import_ajax', [BarangController::class, 'import_ajax']); 
        Route::get('/import', [SupplierController::class, 'import']); // menampilkan halaman form import Supplier
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // menyimpan data Supplier dari file import
        Route::get('/export_excel', [BarangController::class,'export_excel']); // ajax export excel
        Route::get('/export_pdf', [BarangController::class,'export_pdf']); // ajax export pdf
    });
    });

    
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'stok'], function () {
            Route::get('/', [StokController::class, 'index']); // menampilkan halaman awal Stok
            Route::post('/list', [StokController::class, 'list']); // menampilkan data Stok dalam bentuk json untuk datatable
            Route::get('/create', [StokController::class, 'create']); // menampilkan halaman form tambah Stok
            Route::post('/', [StokController::class, 'store']); // menyimpan data Stok baru
            Route::get('/create_ajax', [StokController::class, 'create_ajax']); // menampilkan halaman form tambah Stok ajax
            Route::post('/ajax', [StokController::class, 'store_ajax']); // menyimpan data Stok baru ajax
            Route::get('/{id}', [StokController::class, 'show']); // menampilkan detail Stok
            Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax']); // menampilkan detail Stok ajax
            Route::get('/{id}/edit', [StokController::class, 'edit']); // menampilkan halaman form edit Stok
            Route::put('/{id}', [StokController::class, 'update']); // menyimpan perubahan data Stok
            Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']); // menampilkan halaman form edit Stok ajax
            Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']); // menyimpan perubahan data Stok ajax
            Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']); // untuk tampilan form confirm delete Stok ajax
            Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']); // menghapus data Stok ajax
            Route::delete('/{id}', [StokController::class, 'destroy']); // menghapus data Stok
            Route::get('/import', [StokController::class, 'import']); // menampilkan halaman form import Stok
            Route::post('/import_ajax', [StokController::class, 'import_ajax']); // menyimpan data Stok dari file import
            Route::get('/export_excel', [StokController::class,'export_excel']); // ajax export excel
            Route::get('/export_pdf', [StokController::class,'export_pdf']); // ajax export pdf
        });
    });
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'penjualan'], function () {
            Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index'); // menampilkan halaman awal Penjualan
            Route::post('/list', [PenjualanController::class, 'list']); // menampilkan data Penjualan dalam bentuk json untuk datatable
            Route::get('/create', [PenjualanController::class, 'create']); // menampilkan halaman form tambah Penjualan
            Route::post('/', [PenjualanController::class, 'store']); // menyimpan data Penjualan baru
            Route::get('/create_ajax', [PenjualanController::class, 'create_ajax'])->name('penjualan.create');; // menampilkan halaman form tambah Penjualan ajax
            Route::post('/ajax', [PenjualanController::class, 'store_ajax']); // menyimpan data Penjualan baru ajax
            Route::get('/{id}', [PenjualanController::class, 'show']); // menampilkan detail Penjualan
            Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax'])->name('penjualan.show');; // menampilkan detail Penjualan ajax
            Route::get('/{id}/edit', [PenjualanController::class, 'edit']); // menampilkan halaman form edit Penjualan
            Route::put('/{id}', [PenjualanController::class, 'update']); // menyimpan perubahan data Penjualan
            Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']); // menampilkan halaman form edit Penjualan ajax
            Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax']); // menyimpan perubahan data Penjualan ajax
            Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']); // untuk tampilan form confirm delete Penjualan ajax
            Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']); // menghapus data Penjualan ajax
            Route::delete('/{id}', [PenjualanController::class, 'destroy']); // menghapus data Penjualan
            Route::get('/import', [PenjualanController::class, 'import']); // menampilkan halaman form import Penjualan
            Route::post('/import_ajax', [PenjualanController::class, 'import_ajax']); // menyimpan data Penjualan dari file import
            Route::get('/export_excel', [PenjualanController::class,'export_excel']); // ajax export excel
            Route::get('/export_pdf', [PenjualanController::class,'export_pdf']); // ajax export pdf
        });
    });
});
