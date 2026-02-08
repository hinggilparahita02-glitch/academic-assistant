<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Asisten Akademik Harian</title>
  <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
</head>
<body>
  <header class="topbar">
    <div class="brand">
      <div class="logo">🎓</div>
      <div>
        <div class="title">Asisten Akademik Harian ✨</div>
        @if(session('user_id'))
          <div class="subtitle">{{ session('user_name') }} • {{ session('user_class') }}</div>
        @endif
      </div>
    </div>
    <div class="actions">
      @if(session('user_id'))
        <a class="btn-outline" href="{{ route('logout') }}">Keluar</a>
      @endif
    </div>
  </header>

  @if(session('user_id'))
    <nav class="tabs">
      <a class="tab {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
      <a class="tab {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">Kalender</a>
      <a class="tab {{ request()->routeIs('timer') ? 'active' : '' }}" href="{{ route('timer') }}">Study Timer</a>
      <a class="tab {{ request()->routeIs('notes') ? 'active' : '' }}" href="{{ route('notes') }}">Catatan</a>
    </nav>
  @endif

  <main class="container">
    @yield('content')
  </main>

  <script src="{{ asset('assets/app.js') }}"></script>
</body>
</html>
