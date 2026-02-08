@extends('layouts.app')

@section('content')
  <div class="card" style="background:linear-gradient(90deg,#2563eb,#7c3aed); color:white;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
      <div>
        <div style="font-size:28px;font-weight:900;">Selamat Datang!👋</div>
        <div style="opacity:.9;margin-top:6px;">{{ date('l, d F Y') }}</div>
        <div class="badge" style="margin-top:10px;background:rgba(255,255,255,.2);color:#fff;">
          Jangan menyerah! Konsistensi adalah kunci! 🚀
        </div>
      </div>
      <div class="card" style="min-width:140px; background:rgba(255,255,255,.15); color:#fff;">
        <div style="font-size:12px;opacity:.9;">Belajar hari ini</div>
        <div style="font-size:28px;font-weight:900;">{{ $minutesToday }}m</div>
      </div>
    </div>
  </div>

  <div style="height:14px"></div>

  <div class="grid4">
    <div class="kpi"><div>Catatan</div><div class="n">{{ count($notes) }}</div><div class="badge">terbaru</div></div>
    <div class="kpi"><div>Tugas Hari Ini</div><div class="n">{{ count($todayTasks) }}</div><div class="badge">deadline</div></div>
    <div class="kpi"><div>Mendesak (≤3 hari)</div><div class="n">{{ count($urgentTasks) }}</div><div class="badge">urgent</div></div>
    <div class="kpi"><div>Waktu Belajar</div><div class="n">{{ $minutesToday }}m</div><div class="badge">hari ini</div></div>
  </div>
  <div style="height:14px"></div>

    <div class="row">
    <div class="card">
        <h3 style="margin-top:0;">🗒️ Catatan Terbaru</h3>

        @if($notes->isEmpty())
        <div class="success">Belum ada catatan. Tambahkan di menu Catatan.</div>
        @else
        @foreach($notes as $n)
            <div style="padding:12px;border:1px solid #eef2ff;border-radius:14px;margin-bottom:10px;">
            <div style="font-weight:800;">
                {{ $n->title }} {!! $n->pinned ? '📌' : '' !!}
            </div>
            <div style="color:var(--muted);font-size:13px;margin-top:6px;">
                {{ \Illuminate\Support\Str::limit($n->content, 120) }}
            </div>
            </div>
        @endforeach
        @endif

        <a class="btn btn-small" href="{{ route('notes') }}">Buka Catatan</a>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">📅 Deadline Mendatang</h3>

        @if($urgentTasks->isEmpty())
        <div class="success">Tidak ada tugas mendesak. Tetap semangat 💪</div>
        @else
        @foreach($urgentTasks as $t)
            <div class="alert" style="margin-bottom:10px;">
            <b>{{ $t->title }}</b><br>
            Deadline: {{ $t->due_date }}
            </div>
        @endforeach
        @endif

        <a class="btn btn-small" href="{{ route('calendar') }}">Buka Kalender</a>
    </div>
    </div>

@endsection
