<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data User</title>
</head>
<body>
    <h1>Form Ubah Data User</h1>
    <a href="/user">Kembali</a>
    <br><br>
    <form method="post" action="{{ url('user/ubah_simpan', $data->user_id) }}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
    
        <label>Username</label>
        <input type="text" name="username" value="{{ $data->username }}" required><br>
    
        <label>Nama</label>
        <input type="text" name="nama" value="{{ $data->nama }}" required><br>
    
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan Password (Opsional)"><br>
    
        <label>Level ID</label>
        <input type="number" name="level_id" value="{{ $data->level_id }}" required><br><br>
    
        <input type="submit" value="Ubah">
    </form>
    
</body>
</html>
