<aside class="lg:col-span-4 flex flex-col gap-6">
<div class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden">
<div class="bg-primary px-4 py-3 text-on-primary flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">bolt</span>
<h3 class="font-headline-sm text-headline-sm">@if($is_politics) राजनीति त्वरित बुलेटिन @else त्वरित बुलेटिन @endif</h3>
</div>
<span class="bg-on-primary text-primary text-[10px] font-bold px-2 py-0.5 rounded uppercase">लाइव</span>
</div>
<div class="p-4 divide-y-0 space-y-4">
@foreach($side_bul as $sb)
<div class="bg-surface-container-low p-3 rounded hover:bg-surface-cream transition-colors cursor-pointer">
<div class="flex items-center justify-between text-xs font-caption text-ink-muted mb-1">
<span class="text-primary font-semibold">{{ $sb->source }}</span>
<span>{{ $sb->time_text }}</span>
</div>
<h4 class="font-headline-sm text-sm text-ink-primary font-semibold leading-snug">
                {{ $sb->text }}
              </h4>
</div>
@endforeach
</div>
</div>

@if($is_politics)
<div class="bg-surface-container-lowest p-5 rounded-lg shadow-sm">
<div class="flex items-center justify-between pb-3 mb-4 bg-surface-container-low px-3 py-2 rounded">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">how_to_vote</span>
<h3 class="font-headline-sm text-sm font-bold text-ink-primary">संसद का समीकरण व आगामी चुनाव</h3>
</div>
<span class="text-[11px] font-caption text-ink-muted">२०२५-२६</span>
</div>
<div class="space-y-3">
<div>
<div class="flex justify-between text-xs font-caption mb-1">
<span class="font-semibold text-ink-primary">एनडीए (NDA गठजोड़)</span>
<span class="font-bold text-ink-primary">{{ \App\Support\Site::setting('seats_nda', '२९३ सीटें (५४%)') }}</span>
</div>
<div class="w-full bg-surface-container h-2.5 rounded-full overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: {{ (int) \App\Support\Site::setting('seats_nda_pct', '54') }}%"></div>
</div>
</div>
<div>
<div class="flex justify-between text-xs font-caption mb-1">
<span class="font-semibold text-ink-primary">इंडिया ब्लॉक (INDIA Alliance)</span>
<span class="font-bold text-ink-primary">{{ \App\Support\Site::setting('seats_india', '२३४ सीटें (४३%)') }}</span>
</div>
<div class="w-full bg-surface-container h-2.5 rounded-full overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: {{ (int) \App\Support\Site::setting('seats_india_pct', '43') }}%"></div>
</div>
</div>
<div>
<div class="flex justify-between text-xs font-caption mb-1">
<span class="font-semibold text-ink-primary">अन्य / निर्दलीय</span>
<span class="font-bold text-ink-primary">{{ \App\Support\Site::setting('seats_other', '१६ सीटें (३%)') }}</span>
</div>
<div class="w-full bg-surface-container h-2.5 rounded-full overflow-hidden">
<div class="bg-ink-muted h-full rounded-full" style="width: {{ (int) \App\Support\Site::setting('seats_other_pct', '3') }}%"></div>
</div>
</div>
</div>
<div class="mt-4 pt-4 bg-surface-cream p-3 rounded">
<h4 class="font-caption text-xs font-bold text-ink-primary mb-2 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px] text-primary">event_upcoming</span>
              आगामी विधानसभा चुनाव कैलेंडर:
            </h4>
<div class="grid grid-cols-2 gap-2 text-xs font-caption">
@foreach($events as $ev)
<div class="bg-surface-container-lowest p-2 rounded">
<span class="font-semibold block text-ink-primary">{{ $ev->state_name }}</span>
<span class="text-ink-muted">{{ $ev->date_text }}</span>
</div>
@endforeach
</div>
</div>
</div>
@elseif($most_read)
<div class="bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden">
<div class="bg-ink-primary px-4 py-3 text-on-primary flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">trending_up</span>
<h3 class="font-headline-sm text-headline-sm">सबसे ज्यादा पढ़ी गई</h3>
</div>
<div class="p-4 space-y-3">
@php $mr = 1; @endphp@foreach($most_read as $m)
<div class="flex gap-3 cursor-pointer group" onclick="location.href='{{ $m->url }}'">
<span class="font-headline-lg text-headline-lg text-primary/30 font-bold leading-none">{{ \App\Support\Site::hiDigits((string) $mr++) }}</span>
<div class="min-w-0">
<h4 class="font-headline-sm text-sm text-ink-primary font-semibold leading-snug group-hover:text-primary transition-colors line-clamp-2">{{ $m->title }}</h4>
<span class="font-caption text-caption text-ink-muted">{{ $m->cat_name }} • {{ \App\Support\Site::hiDigits(number_format((int) $m->views)) }} पाठक</span>
</div>
</div>
@endforeach
</div>
</div>
@endif

@if($cat_poll)
<div class="bg-surface-container-lowest p-5 rounded-lg shadow-sm" id="politicalPollWidget">
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-masthead-crimson text-[20px]">ballot</span>
<span class="font-label-badge text-xs font-bold text-primary uppercase">दैनिक जनमत पोल</span>
</div>
<h3 class="font-headline-sm text-sm font-bold text-ink-primary leading-snug mb-3">
            {{ $cat_poll->question }}
          </h3>
@if($cat_poll_voted && $cat_poll_total > 0): ?>
<div class="space-y-3 pt-2">
@foreach($cat_poll_opts as $o)@php $pct = (int) round($o->votes * 100 / $cat_poll_total); @endphp
<div>
<div class="flex justify-between text-xs font-caption mb-1">
<span class="text-ink-primary font-medium">{{ $o->option_text }}</span>
<span class="font-bold text-primary">{{ \App\Support\Site::hiDigits((string) $pct) }}% ({{ \App\Support\Site::hiDigits(number_format((int) $o->votes)) }} मत)</span>
</div>
<div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
<div class="{{ $o->color_class ?: 'bg-primary' }} h-full rounded-full" style="width: {{ $pct }}%"></div>
</div>
</div>
@endforeach
<p class="text-[11px] font-caption text-market-up pt-1 text-center font-medium">
              ✓ आपका मत सफलतापूर्वक दर्ज कर लिया गया है। कुल मत: {{ \App\Support\Site::hiDigits(number_format($cat_poll_total)) }}
            </p>
</div>
@else
<form class="space-y-2.5 text-xs font-body-md" action="{{ url('poll-vote') }}" method="post">
@csrf
<input type="hidden" name="poll_id" value="{{ (int) $cat_poll->id }}">
<input type="hidden" name="back" value="{{ $sidebar_back ?? '' }}">
@foreach($cat_poll_opts as $k => $o)
<label class="flex items-center gap-2 p-2.5 rounded bg-surface-container-low hover:bg-surface-container cursor-pointer transition-colors">
<input @if($k === 0) checked @endif class="accent-primary" name="option_id" type="radio" value="{{ (int) $o->id }}"/>
<span class="font-medium text-ink-primary">{{ $o->option_text }}</span>
</label>
@endforeach
<button class="w-full bg-primary hover:bg-masthead-crimson text-on-primary py-2 rounded font-semibold text-xs transition-colors mt-2" type="submit">
              अपना मत दर्ज करें (Vote)
            </button>
</form>
@endif
</div>
@endif

<div class="bg-gradient-to-br from-primary to-masthead-crimson text-on-primary p-5 rounded-lg shadow-md">
<div class="flex items-center gap-2 mb-2">
<span class="material-symbols-outlined text-[22px]">mark_email_read</span>
<span class="font-label-badge text-xs font-bold uppercase tracking-wider text-primary-fixed">
              प्रातःकालीन सियासी डायरी
            </span>
</div>
<h3 class="font-headline-sm text-headline-sm mb-2 text-white">
            दैनिक राजनीतिक बुलेटिन - हर सुबह ८ बजे
          </h3>
<p class="font-body-sm text-body-sm text-on-primary/90 mb-4">
            संसद के गलियारों से लेकर राज्यों की विधानसभाओं तक के पर्दे के पीछे की हर हलचल आपके इनबॉक्स में।
          </p>
<form class="space-y-2.5" action="{{ url('subscribe') }}" method="post">
@csrf
<input class="w-full px-3 py-2 text-xs rounded bg-surface-container-lowest text-ink-primary placeholder:text-ink-muted focus:outline-none" name="email" placeholder="आपका व्यक्तिगत ईमेल पता दर्ज करें" required type="email"/>
<button class="w-full bg-surface-container-lowest hover:bg-surface-cream text-primary font-bold text-xs py-2 rounded transition-colors" type="submit">
              निःशुल्क सब्सक्राइब करें
            </button>
</form>
</div>

@if($archive)
<div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm cursor-pointer" onclick="location.href='{{ $archive->url }}'">
<div class="relative aspect-video">
<img class="w-full h-full object-cover" alt="{{ $archive->image_alt ?: $archive->title }}" src="{{ $archive->image_url }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-ink-primary via-ink-primary/30 to-transparent"></div>
<div class="absolute bottom-3 left-3 right-3 text-on-tertiary">
<span class="bg-primary px-2 py-0.5 rounded text-[10px] font-bold uppercase">विश्वामित्र पुरालेख</span>
<h4 class="font-headline-sm text-sm text-white font-bold mt-1">
                {{ $archive->title }}
              </h4>
</div>
</div>
</div>
@endif
</aside>
