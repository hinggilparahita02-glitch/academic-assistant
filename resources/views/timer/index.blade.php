@extends('layouts.app')

@section('content')
<div class="card" style="background:#fff7ed;border:2px solid #fdba74;">
  <h3 style="margin-top:0;">⏱️ Study Timer</h3>

    <div class="timer-grid">
        <div class="field">
            <label>Jam</label>
            <input id="h" type="number" min="0" max="23" value="0">
        </div>
        <div class="field">
            <label>Menit</label>
            <input id="m" type="number" min="0" max="59" value="0">
        </div>
        <div class="field">
            <label>Detik</label>
            <input id="s" type="number" min="0" max="59" value="30">
        </div>
    </div>

    <div class="timer-actions">
        <button class="btn" id="startBtn">Mulai</button>
        <button class="btn" id="pauseBtn" style="background:#f59e0b;">Pause</button>
        <button class="btn" id="resetBtn" style="background:#6b7280;">Reset</button>
    </div>


  <div style="height:12px;"></div>

  <div class="card" style="text-align:center;">
    <div style="font-size:64px;font-weight:1000;" id="display">00:00</div>
    <div style="opacity:.8;">Alarm akan berbunyi saat selesai!</div>
  </div>

  <audio id="alarm" src="{{ asset('assets/Alarm05.wav') }}" preload="auto"></audio>

  <form id="logForm" method="post" action="{{ route('timer.log') }}">
    @csrf
    <input type="hidden" name="duration_seconds" id="duration_seconds">
    <input type="hidden" name="started_at" id="started_at">
    <input type="hidden" name="ended_at" id="ended_at">
  </form>
</div>

<div style="height:14px;"></div>

<div class="card">
  <h3 style="margin-top:0;">Ringkasan Hari Ini</h3>
  <div class="success">Total belajar hari ini: <b>{{ $minutesToday }} menit</b></div>

  <div style="height:12px;"></div>

  <h3 style="margin-top:0;">Log Terakhir</h3>
  @if($todaySessions->isEmpty())
    <div class="success">Belum ada sesi belajar hari ini.</div>
  @else
    @foreach($todaySessions as $ss)
      <div style="border:1px solid #eef2ff;border-radius:14px;padding:12px;margin-bottom:10px;">
        <div style="font-weight:900;">Durasi: {{ (int)round($ss->duration_seconds/60) }} menit</div>
        <div style="color:var(--muted);font-size:13px;margin-top:4px;">
          {{ $ss->started_at }} → {{ $ss->ended_at }}
        </div>
      </div>
    @endforeach
  @endif
</div>

<script>
(function(){
  const h = document.getElementById('h');
  const m = document.getElementById('m');
  const s = document.getElementById('s');
  const display = document.getElementById('display');
  const alarm = document.getElementById('alarm');

  const startBtn = document.getElementById('startBtn');
  const pauseBtn = document.getElementById('pauseBtn');
  const resetBtn = document.getElementById('resetBtn');

  const durationEl = document.getElementById('duration_seconds');
  const startedEl = document.getElementById('started_at');
  const endedEl = document.getElementById('ended_at');

  let timer = null;
  let remaining = 0;
  let startedAt = null;
  let initial = 0;

  function pad(n){ return String(n).padStart(2,'0'); }
  function render(){
    const mm = Math.floor(remaining/60);
    const ss = remaining%60;
    display.textContent = `${pad(mm)}:${pad(ss)}`;
  }

  function getTotalSeconds(){
    const hh = parseInt(h.value||'0',10);
    const mm = parseInt(m.value||'0',10);
    const ss = parseInt(s.value||'0',10);
    return hh*3600 + mm*60 + ss;
  }

  function stop(){
    if (timer) clearInterval(timer);
    timer = null;
  }

  startBtn.addEventListener('click', async () => {
    if (timer) return;
    if (remaining <= 0) {
      initial = getTotalSeconds();
      remaining = initial;
    }
    if (remaining <= 0) return;

    if (!startedAt) startedAt = new Date();

    render();
    timer = setInterval(async () => {
      remaining--;
      render();

      if (remaining <= 0) {
        stop();
        try { await alarm.play(); } catch(e) {}

        const endedAt = new Date();
        const duration = initial; // total set waktu

        // submit log ke server (AJAX)
        const payload = new FormData();
        payload.append('_token', document.querySelector('input[name=_token]').value);
        payload.append('duration_seconds', String(duration));
        payload.append('started_at', startedAt.toISOString().slice(0,19).replace('T',' '));
        payload.append('ended_at', endedAt.toISOString().slice(0,19).replace('T',' '));

        fetch('{{ route('timer.log') }}', { method:'POST', body: payload })
          .then(r => r.json())
          .then(() => window.location.reload())
          .catch(() => window.location.reload());
      }
    }, 1000);
  });

  pauseBtn.addEventListener('click', () => {
    if (!timer) return;
    stop();
  });

  resetBtn.addEventListener('click', () => {
    stop();
    remaining = 0;
    startedAt = null;
    initial = 0;
    render();
  });

  render();
})();
</script>
@endsection
