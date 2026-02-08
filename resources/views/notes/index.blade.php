@extends('layouts.app')

@section('content')
  {{-- Header --}}
  <div class="card notes-head-card">
    <div class="notes-head">
      <div class="title">
        <span class="icon">📝</span>
        <span>Catatan Cepat</span>
      </div>

      <a href="#formNote" class="btn btn-small notes-add-btn">+ Tambah Catatan</a>
    </div>

    {{-- kalau kamu masih ada search bar, hapus bagian ini --}}
    {{-- <input placeholder="Cari catatan..." class="notes-search" /> --}}
  </div>

  <div style="height:14px;"></div>

  {{-- 2 kolom --}}
  <div class="notes-two">
    {{-- Kiri: form --}}
    <div class="card" id="formNote">
      <h3 style="margin-top:0;">Tambah Catatan</h3>

      <form method="post" action="{{ route('notes.store') }}">
        @csrf

        <div class="field">
          <label>Judul</label>
          <input name="title" placeholder="Judul catatan..." required>
        </div>

        <div class="field">
          <label>Tag (opsional)</label>
          <input name="tag" placeholder="mis: tugas">
        </div>

        <div class="field">
          <label>Isi</label>
          <textarea name="content" rows="6" placeholder="Tulis catatan..." required></textarea>
        </div>

        <div class="notes-actions">
          <label class="check">
            <input type="checkbox" name="pinned" value="1">
            <span>Pin catatan</span>
          </label>

          <button class="btn" style="min-width:200px;">Simpan</button>
        </div>
      </form>
    </div>

    {{-- Kanan: daftar --}}
    <div class="card">
      <h3 style="margin-top:0;">Daftar Catatan</h3>

      @if($notes->isEmpty())
        <div class="success">Belum ada catatan.</div>
      @else
        @foreach($notes as $n)
          <div class="note-item">
            <div>
              <div class="note-title">
                {{ $n->title }} {!! $n->pinned ? '📌' : '' !!}
                @if($n->tag)
                  <span class="badge">{{ $n->tag }}</span>
                @endif
              </div>
              <div class="note-meta">
                {{ \Illuminate\Support\Str::limit($n->content, 120) }}
              </div>
            </div>

            <div class="note-actions">
              <form method="post" action="{{ route('notes.pin', $n->id) }}">
                @csrf
                <button class="btn btn-small" type="submit" style="background:#8b5cf6;">
                  {{ $n->pinned ? 'Unpin' : 'Pin' }}
                </button>
              </form>

              <form method="post" action="{{ route('notes.delete', $n->id) }}" onsubmit="return confirm('Hapus catatan ini?')">
                @csrf
                <button class="btn btn-small" type="submit" style="background:#ef4444;">Hapus</button>
              </form>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
@endsection
