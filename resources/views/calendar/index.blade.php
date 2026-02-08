@extends('layouts.app')

@section('content')
@php
  $firstDay = strtotime("$year-$month-01");
  $daysInMonth = (int)date('t', $firstDay);
  $startWeekday = (int)date('w', $firstDay); // 0=Sun
  $label = date('F Y', $firstDay);

  $prev = strtotime("-1 month", $firstDay);
  $next = strtotime("+1 month", $firstDay);
@endphp

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h3 style="margin:0;">📅 Kalender Deadline</h3>

    <div style="display:flex;gap:10px;align-items:center;">
      <a class="btn btn-small" href="{{ route('calendar', ['m'=>date('m',$prev),'y'=>date('Y',$prev)]) }}">‹</a>
      <div style="font-weight:900;">{{ $label }}</div>
      <a class="btn btn-small" href="{{ route('calendar', ['m'=>date('m',$next),'y'=>date('Y',$next)]) }}">›</a>
      <a class="btn btn-small" href="{{ route('calendar') }}">Hari Ini</a>
    </div>
  </div>

  <div style="height:12px"></div>

  <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:8px;">
    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
      <div style="text-align:center;font-weight:800;color:var(--muted);">{{ $d }}</div>
    @endforeach

    @for($i=0;$i<$startWeekday;$i++)
      <div class="card" style="height:78px;opacity:.3;"></div>
    @endfor

    @for($day=1;$day<=$daysInMonth;$day++)
      @php
        $dateStr = sprintf('%04d-%02d-%02d',$year,$month,$day);
        $isToday = $dateStr === date('Y-m-d');
        $hasTasks = isset($taskMap[$dateStr]);
        $pendingCount = $hasTasks ? collect($taskMap[$dateStr])->where('status','pending')->count() : 0;
        $doneCount = $hasTasks ? collect($taskMap[$dateStr])->where('status','done')->count() : 0;
      @endphp
      <div class="card" style="height:78px; border:2px solid {{ $isToday ? '#3b82f6' : '#eef2ff' }};">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
          <div style="font-weight:900;">{{ $day }}</div>
          @if($hasTasks)
            <div style="font-size:12px;">
              @if($pendingCount>0)<span class="badge" style="background:#fee2e2;color:#b91c1c;">{{ $pendingCount }} pending</span>@endif
              @if($doneCount>0)<span class="badge" style="background:#dcfce7;color:#166534;">{{ $doneCount }} done</span>@endif
            </div>
          @endif
        </div>
      </div>
    @endfor
  </div>
</div>

<div style="height:14px"></div>

<div class="card">
  <h3 style="margin-top:0;">Tambah Tugas</h3>
  <form method="post" action="{{ route('tasks.store') }}">
    @csrf
    <div class="row">
      <div class="field" style="flex:1;">
        <label>Judul tugas</label>
        <input name="title" placeholder="Contoh: Kerjakan laporan..." required>
      </div>
      <div class="field" style="width:220px;">
        <label>Deadline</label>
        <input type="date" name="due_date" value="{{ date('Y-m-d') }}" required>
      </div>
    </div>
    <button class="btn">Tambah Tugas</button>
  </form>
</div>

<div style="height:14px"></div>

<div class="card">
  <h3 style="margin-top:0;">Deadline Mendatang</h3>

  @php
    $upcoming = collect($tasks)->where('status','pending')->take(20);
  @endphp

  @if($upcoming->isEmpty())
    <div class="success">Tidak ada deadline mendatang! 🎉</div>
  @else
    @foreach($upcoming as $t)
      <div style="border:1px solid #eef2ff;border-radius:14px;padding:12px;margin-bottom:10px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
          <div>
            <div style="font-weight:900;">{{ $t->title }}</div>
            <div style="color:var(--muted);font-size:13px;margin-top:4px;">Deadline: {{ $t->due_date }}</div>
          </div>

          <div style="display:flex;gap:8px;">
            <form method="post" action="{{ route('tasks.toggle', $t->id) }}">
              @csrf
              <button class="btn btn-small" style="background:#22c55e;">Selesai</button>
            </form>
            <form method="post" action="{{ route('tasks.delete', $t->id) }}" onsubmit="return confirm('Hapus tugas ini?')">
              @csrf
              <button class="btn btn-small" style="background:#ef4444;">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  @endif
</div>
@endsection
