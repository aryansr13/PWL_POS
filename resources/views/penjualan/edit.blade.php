@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/penjualan/'.$penjualan->penjualan_id) }}" class="form-horizontal">
            @csrf
            {!! method_field('PUT') !!}

            <div class="form-group row">
                <label class="col-2 control-label col-form-label">Kode</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="penjualan_kode" value="{{ old('penjualan_kode', $penjualan->penjualan_kode) }}" required>
                    @error('penjualan_kode')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2">ID User</label>
                <div class="col-10">
                    <select class="form-control" name="user_id" required>
                        <option value="">- Pilih User -</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ $penjualan->user_id == $user->user_id ? 'selected' : '' }}>
                                {{ $user->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-2 control-label col-form-label">Pembeli</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="pembeli" value="{{ old('pembeli', $penjualan->pembeli) }}" required>
                    @error('pembeli')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 control-label col-form-label">Tanggal</label>
                <div class="col-10">
                    <input type="datetime-local" class="form-control" name="penjualan_tanggal" value="{{ old('penjualan_tanggal', $penjualan->penjualan_tanggal) }}" required>
                    @error('penjualan_tanggal')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <div class="col-10 offset-2">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    <a class="btn btn-sm btn-default ml-1" href="{{ url('penjualan') }}">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
