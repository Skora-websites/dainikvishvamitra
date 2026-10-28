<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $admin_title ?? 'Admin' }} | दैनिक विश्वामित्र</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Serif:wght@600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
body{font-family:'Noto Sans',sans-serif;}
.serif{font-family:'Noto Serif',serif;}
/* Admin sidebar: single-line nav items, one icon/metric grid, no wrapping. */
.nav-item{display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;border-radius:.5rem;transition:background-color .15s,color .15s;position:relative;}
.nav-item:hover{background:rgba(255,255,255,.06);}
.nav-item.is-active{background:#991b1b;}
.nav-icon{width:20px;height:20px;flex:0 0 20px;display:flex;align-items:center;justify-content:center;font-size:20px;line-height:1;}
.nav-text{min-width:0;display:flex;flex-direction:column;line-height:1.25;}
.nav-group{font-size:.625rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:1rem .75rem .375rem;color:#78716c;}
.admin-scroll{scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.18) transparent;}
.admin-scroll::-webkit-scrollbar{width:6px;}
.admin-scroll::-webkit-scrollbar-track{background:transparent;}
.admin-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,.18);border-radius:3px;}
</style>
@stack('head')
</head>
<body class="bg-stone-100 text-stone-900">

@php
    $adminUser = session('admin') ?? [];
    // Guest pages (login) get a bare shell: no sidebar, no nav, no user block.
    $isAdminAuthed = $adminUser !== [];
    $adminOnly = in_array(($adminUser['role'] ?? ''), ['admin'], true);

    // Sidebar groups: English labels only.
    $navGroups = [
        [
            'label' => 'Content',
            'items' => [
                ['admin.dashboard', 'dashboard', 'Dashboard'],
                ['admin.articles', 'article', 'Articles'],
                ['admin.categories', 'category', 'Categories'],
                ['admin.pages', 'description', 'Pages'],
            ],
        ],
        [
            'label' => 'Modules',
            'items' => [
                ['admin.ticker', 'campaign', 'Ticker'],
                ['admin.bulletins', 'bolt', 'Bulletins'],
                ['admin.market', 'trending_up', 'Market'],
                ['admin.videos', 'videocam', 'Videos'],
                ['admin.horoscope', 'stars', 'Horoscope'],
                ['admin.polls', 'ballot', 'Polls'],
                ['admin.elections', 'event', 'Elections'],
            ],
        ],
        [
            'label' => 'Publishing',
            'items' => [
                ['admin.epapers', 'menu_book', 'Epaper'],
                ['admin.messages', 'inbox', 'Messages'],
                ['admin.subscribers', 'mail', 'Subscribers'],
            ],
        ],
    ];

    $adminGroup = [
        'label' => 'Administration',
        'items' => [
            ['admin.users', 'group', 'Users'],
            ['admin.settings', 'settings', 'Settings'],
        ],
    ];
    if ($adminOnly) {
        $navGroups[] = $adminGroup;
    }
@endphp

@if($isAdminAuthed)
<div class="flex min-h-screen">
<aside class="w-64 shrink-0 bg-[#1c1917] text-stone-200 hidden md:flex flex-col border-r border-black/40">
<div class="px-4 py-4 border-b border-white/10 flex items-center gap-3">
<span class="material-symbols-outlined text-[28px] text-red-500">newsmode</span>
<div class="min-w-0">
<div class="serif font-bold text-base text-white leading-tight truncate">दैनिक विश्वामित्र</div>
<div class="text-[11px] text-stone-400 leading-tight">Admin Panel</div>
</div>
</div>
<nav class="flex-1 overflow-y-auto admin-scroll py-2 px-2">
@foreach($navGroups as $group)
<div class="nav-group">{{ $group['label'] }}</div>
@foreach($group['items'] as [$route, $icon, $label])
    @php($active = request()->routeIs($route) || request()->routeIs($route.'.*'))
    <a href="{{ route($route) }}" class="nav-item {{ $active ? 'is-active text-white' : 'text-stone-300' }}" @if($active) aria-current="page" @endif>
        <span class="nav-icon material-symbols-outlined">{{ $icon }}</span>
        <span class="nav-text">{{ $label }}</span>
    </a>
@endforeach
@endforeach
</nav>
<div class="p-3 border-t border-white/10">
<div class="flex items-center gap-3 px-1 py-1.5">
<div class="w-9 h-9 rounded-full bg-red-800 text-white flex items-center justify-center font-bold text-sm shrink-0">{{ mb_substr((string) ($adminUser['name'] ?? 'A'), 0, 1) }}</div>
<div class="min-w-0 flex-1">
<div class="text-sm font-semibold text-white truncate">{{ e($adminUser['name'] ?? '') }}</div>
<div class="text-[11px] text-stone-400 truncate">{{ e($adminUser['role'] ?? '') }}</div>
</div>
</div>
<div class="grid grid-cols-2 gap-2 mt-2">
<a class="text-center text-xs px-2 py-1.5 rounded bg-white/5 hover:bg-white/10 text-stone-300 transition-colors" target="_blank" href="{{ url('/') }}">View site</a>
<a class="text-center text-xs px-2 py-1.5 rounded bg-red-900/60 hover:bg-red-900 text-red-200 transition-colors" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Log out</a>
<form id="logout-form" action="{{ route('admin.logout') }}" method="post" class="hidden">@csrf</form>
</div>
</div>
</aside>
<div class="flex-1 min-w-0 flex flex-col">
@endif

{{-- Mobile top bar (shown only when logged in) --}}
@if($isAdminAuthed)
<div class="md:hidden sticky top-0 z-30 bg-[#1c1917] text-white px-4 py-3 flex items-center justify-between">
<span class="serif font-bold flex items-center gap-2"><span class="material-symbols-outlined text-red-500">newsmode</span>दैनिक विश्वामित्र · Admin</span>
<button type="button" onclick="document.getElementById('mob-nav').classList.toggle('hidden')" class="p-1.5 rounded hover:bg-white/10 transition-colors" aria-label="Menu"><span class="material-symbols-outlined">menu</span></button>
</div>
<div id="mob-nav" class="md:hidden hidden bg-[#1c1917] text-stone-200 px-3 pb-3 border-b border-white/10">
@foreach($navGroups as $group)
<div class="nav-group">{{ $group['label'] }}</div>
@foreach($group['items'] as [$route, $icon, $label])
    @php($active = request()->routeIs($route) || request()->routeIs($route.'.*'))
    <a href="{{ route($route) }}" class="nav-item {{ $active ? 'is-active text-white' : 'text-stone-300' }}">
        <span class="nav-icon material-symbols-outlined">{{ $icon }}</span>
        <span class="nav-text">{{ $label }}</span>
    </a>
@endforeach
@endforeach
<form action="{{ route('admin.logout') }}" method="post" class="mt-2">@csrf<button class="w-full text-center text-xs px-2 py-2 rounded bg-red-900/60 text-red-200" type="submit">Log out</button></form>
</div>
@else
<div class="md:hidden bg-[#1c1917] text-white px-4 py-3 flex items-center justify-center">
<span class="serif font-bold flex items-center gap-2"><span class="material-symbols-outlined text-red-500">newsmode</span>दैनिक विश्वामित्र · Admin</span>
</div>
@endif

@if($isAdminAuthed)
<main class="flex-1 p-4 sm:p-6 lg:p-8 w-full max-w-6xl mx-auto">
@if(!empty($admin_title))
<div class="mb-6">
<h1 class="serif text-2xl font-bold text-stone-900 leading-tight">{{ e($admin_title) }}</h1>
</div>
@endif
@else
<main class="min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 sm:p-6 max-w-6xl mx-auto">
@endif

@if(session('ok'))
<div class="mb-3 px-4 py-2.5 rounded-lg text-sm font-medium bg-green-100 text-green-800 border border-green-200">{{ session('ok') }}</div>
@endif
@if(session('err'))
<div class="mb-3 px-4 py-2.5 rounded-lg text-sm font-medium bg-red-100 text-red-800 border border-red-200">{{ session('err') }}</div>
@endif
@if($errors->any())
<div class="mb-3 px-4 py-2.5 rounded-lg text-sm font-medium bg-red-100 text-red-800 border border-red-200">
<ul class="list-disc list-inside space-y-0.5">
@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
</ul>
</div>
@endif

@yield('content')

</main>

@if($isAdminAuthed)
<footer class="px-6 py-4 text-xs text-stone-500 border-t border-stone-200 w-full max-w-6xl mx-auto">दैनिक विश्वामित्र · Admin Panel v{{ config('app.version', '1.0.0') }}</footer>
</div>
</div>
@endif

<script>
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    var bad = [];
    form.querySelectorAll('input[type=file][data-max-kb]').forEach(function (inp) {
        if (inp.files && inp.files[0] && inp.files[0].size > parseInt(inp.getAttribute('data-max-kb'), 10) * 1024) {
            bad.push(inp.getAttribute('data-label') || inp.name);
        }
    });
    if (bad.length) {
        e.preventDefault();
        alert('Selected file(s) too large: ' + bad.join(', ') + ' — please choose smaller files.');
    }
});
</script>

@stack('scripts')
</body>
</html>
