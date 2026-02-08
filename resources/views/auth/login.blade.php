@extends('layouts.app')

@section('content')
  <div class="card" style="max-width:520px;margin:40px auto;">
    <h2 style="text-align:center;margin:0 0 18px;">Asisten Akademik Harian</h2>

    @if($errors->any())
      <div class="alert">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('login.post') }}">
      @csrf
      <div class="field">
        <label>Nama</label>
        <input name="name" placeholder="Masukkan nama Anda" required>
      </div>
      <div class="field">
        <label>Kelas</label>
        <input name="class" placeholder="Masukkan kelas Anda" required>
      </div>
      <button class="btn" style="width:100%;">Masuk</button>
    </form>
  </div>
@endsection
