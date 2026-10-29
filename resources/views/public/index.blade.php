@extends('layouts.public')

@section('content')
<div class="flex flex-col w-full space-y-8">
<!-- Animated Breaking News Ticker -->
<div class="w-full bg-surface-container-lowest rounded-xl shadow-sm flex items-center gap-3 overflow-x-auto py-2">
<div class="flex items-center gap-1.5 bg-breaking-red text-on-primary px-3 py-1 rounded font-label-ticker text-label-ticker tracking-wide shrink-0 z-10">
<span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
<span>शीर्ष घटनाक्रम</span>
</div>
<div class="flex-1 overflow-visible relative group">
<div class="ticker-track flex items-center gap-8 text-ink-primary font-body-md text-body-md whitespace-nowrap group-hover:[animation-play-state:paused]">
@php $fi = 0; @endphp
@foreach($flash as $f)
<span class="flex items-center gap-2 hover:text-primary cursor-pointer transition-colors shrink-0" onclick="location.href='{{ url('category/latest-news') }}'">
<span class="text-primary font-bold shrink-0">➤</span>
<span class="shrink-0">{{ $f->text }}</span>
</span>
<span class="text-ink-muted shrink-0">•</span>
@endforeach
<!-- Duplicate for seamless loop -->
@foreach($flash as $f)
<span class="flex items-center gap-2 hover:text-primary cursor-pointer transition-colors shrink-0" onclick="location.href='{{ url('category/latest-news') }}'">
<span class="text-primary font-bold shrink-0">➤</span>
<span class="shrink-0">{{ $f->text }}</span>
</span>
<span class="text-ink-muted shrink-0">•</span>
@endforeach
</div>
</div>
</div>
<!-- SECTION 1: Hero Editorial Grid (12-col structure: 7-col lead + 5-col side spotlight & quick bulletin) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- Lead Main Story (7 Columns) -->
@if($hero)
<article class="lg:col-span-7 bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col group">
<div class="relative w-full aspect-[16/9] overflow-hidden bg-surface-container-high">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $hero->image_alt ?: $hero->title }}" src="{{ $hero->image_url }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
<div class="absolute top-4 left-4 flex items-center gap-2">
<span class="bg-breaking-red text-on-primary font-label-badge text-label-badge uppercase tracking-wider px-2.5 py-1 rounded font-semibold shadow">
            {{ $hero->badge_label ?: $hero->cat_name }}
          </span>
<span class="bg-ink-primary/80 backdrop-blur-sm text-surface font-caption text-caption px-2.5 py-1 rounded">
            विशेष रिपोर्ट
          </span>
</div>
<div class="absolute bottom-4 left-4 right-4 text-on-primary">
<div class="flex items-center gap-2 text-primary-fixed text-xs font-caption mb-1">
<span class="material-symbols-outlined text-[16px]">schedule</span>
<span>अपडेटेड: {{ \App\Support\Site::hiDate($hero->published_at) }} | {{ $hero->location ?: 'नई दिल्ली' }}</span>
</div>
</div>
</div>
<div class="p-6 flex flex-col space-y-4">
<a href="{{ $hero->url }}"><h2 class="font-headline-hero text-headline-hero text-ink-primary leading-tight hover:text-primary transition-colors cursor-pointer">
          {{ $hero->title }}
        </h2></a>
<p class="font-body-lg text-body-lg text-ink-secondary leading-relaxed">
          {{ $hero->excerpt ?: excerpt($hero->body ?? '', 200) }}
        </p>
<div class="pt-2 flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
              {{ mb_substr($hero->author_name ?: 'वि', 0, 1) }}
            </div>
<div>
<p class="font-body-md text-ink-primary font-semibold text-xs leading-none">{{ $hero->author_name }}</p>
<p class="font-caption text-caption text-ink-muted mt-0.5">{{ $hero->author_role ?: 'दैनिक विश्वामित्र' }}</p>
</div>
</div>
<div class="flex items-center gap-2">
<button class="w-8 h-8 rounded-full bg-surface-container hover:bg-primary hover:text-on-primary text-ink-secondary flex items-center justify-center transition-colors" title="शेयर करें" onclick="navigator.share ? navigator.share({title:document.title,url:location.href}) : navigator.clipboard.writeText(location.href)">
<span class="material-symbols-outlined text-[18px]">share</span>
</button>
<a href="{{ $hero->url }}" class="w-8 h-8 rounded-full bg-surface-container hover:bg-primary hover:text-on-primary text-ink-secondary flex items-center justify-center transition-colors" title="पढ़ें">
<span class="material-symbols-outlined text-[18px]">bookmark</span>
</a>
</div>
</div>
</div>
</article>
@endif
<!-- Side Spotlight & Bulletins (5 Columns) -->
<div class="lg:col-span-5 flex flex-col space-y-5">
<!-- Side Spotlight Cards (2 Medium Stories) -->
@foreach($spots as $sp)
<article class="bg-surface-container-lowest p-4 rounded-xl shadow-sm flex gap-4 hover:shadow-md transition-shadow group cursor-pointer" onclick="location.href='{{ $sp->url }}'">
<div class="w-32 h-28 rounded-lg overflow-hidden shrink-0 bg-surface-container-high">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $sp->image_alt ?: $sp->title }}" src="{{ $sp->image_url }}"/>
</div>
<div class="flex flex-col justify-between min-w-0">
<div>
<span class="inline-block @if($sp->badge_color === 'bg-secondary') bg-secondary/10 text-secondary @elseif($sp->badge_color === 'bg-tertiary') bg-tertiary/10 text-tertiary @else bg-primary/10 text-primary @endif font-label-badge text-label-badge px-2 py-0.5 rounded font-semibold mb-1">
              {{ $sp->badge_label ?: $sp->cat_name }}
            </span>
<h3 class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-primary transition-colors line-clamp-2">
              {{ $sp->title }}
            </h3>
</div>
<span class="font-caption text-caption text-ink-muted flex items-center gap-1 mt-2">
<span class="material-symbols-outlined text-[14px]">history</span>
            {{ ($sp->time_label !== '' && $sp->time_label !== null) ? $sp->time_label : \App\Support\Site::timeAgo($sp->published_at) }} | {{ $sp->location ?: 'डेस्क' }}
          </span>
</div>
</article>
@endforeach
<!-- Rapid Bulletin Container -->
<div class="bg-surface-cream rounded-xl p-4 shadow-sm">
<div class="flex items-center justify-between pb-3">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
<h4 class="font-headline-sm text-headline-sm text-ink-primary">त्वरित बुलेटिन (Quick Updates)</h4>
</div>
<a href="{{ url('category/latest-news') }}" class="font-caption text-caption text-primary font-semibold cursor-pointer hover:underline">सभी देखें</a>
</div>
<ul class="space-y-3 font-body-sm text-body-sm">
@foreach($bul_home as $b)
<li class="flex items-start gap-2 hover:text-primary cursor-pointer transition-colors">
<span class="text-primary font-bold mt-0.5">▪</span>
<span class="line-clamp-2">{{ $b->text }}</span>
</li>
@endforeach
</ul>
</div>
</div>
</div>
<!-- SECTION 2: Markets & Business Section (शेयर बाजार एवं व्यापार) -->
<section class="w-full bg-surface-container-lowest rounded-xl shadow-sm p-6 flex flex-col space-y-6">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
<div class="flex items-center gap-3">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<h2 class="font-headline-lg text-headline-lg text-ink-primary">शेयर बाजार एवं व्यापार</h2>
</div>
<!-- Live Indices Quick Strip -->
<div class="flex items-center gap-4 overflow-x-auto whitespace-nowrap bg-surface-container-low px-4 py-2 rounded-lg text-xs font-semibold">
@php $ii = 0; @endphp
@foreach($indices as $ix)@if($ii++ > 0)<div class="w-px h-4 bg-ink-muted/30"></div>@endif
<div class="flex items-center gap-1.5">
<span class="text-ink-secondary">{{ $ix->label }}</span>
<span class="text-ink-primary font-bold">{{ $ix->value_text }}</span>
<span class="@if($ix->direction === 'up') text-market-up @else text-market-down @endif flex items-center font-bold">{{ $ix->change_text }}</span>
</div>
@endforeach
</div>
</div>
<!-- Markets Story Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
@foreach($market as $mk)
<div class="bg-surface-cream p-4 rounded-xl flex flex-col justify-between hover:shadow-md transition-shadow cursor-pointer group" onclick="location.href='{{ $mk->url }}'">
<div>
<div class="flex items-center justify-between mb-2">
<span class="@if($mk->badge_color === 'bg-secondary') bg-secondary/10 text-secondary @elseif($mk->badge_color === 'bg-tertiary') bg-tertiary/10 text-tertiary @else bg-primary/10 text-primary @endif font-label-badge text-label-badge px-2 py-0.5 rounded font-semibold">{{ $mk->badge_label ?: 'बाज़ार' }}</span>
{!! \App\Http\Controllers\HomeController::statBadgeHtml($mk->stat_text) !!}
</div>
<h3 class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-primary transition-colors leading-snug">
            {{ $mk->title }}
          </h3>
<p class="font-body-sm text-body-sm text-ink-secondary mt-2 line-clamp-3">
            {{ $mk->excerpt ?: excerpt($mk->body ?? '', 120) }}
          </p>
</div>
<div class="pt-4 flex items-center justify-between text-xs text-ink-muted font-caption">
<span>{{ $mk->author_name }}</span>
<span>{{ ($mk->time_label !== '' && $mk->time_label !== null) ? $mk->time_label : \App\Support\Site::timeAgo($mk->published_at) }}</span>
</div>
</div>
@endforeach
</div>
</section>
<!-- SECTION 3: Categorized Editorial Section (राजनीति, खेल, मनोरंजन, रियल एस्टेट) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- Desk Column 1: राजनीति (Politics) - 4 Cols -->
<section class="lg:col-span-4 bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-1.5 h-5 bg-breaking-red rounded"></span>
<h3 class="font-headline-md text-headline-md text-ink-primary">राजनीति व शासन</h3>
</div>
<a href="{{ url('category/politics') }}" class="font-caption text-caption text-primary font-semibold hover:underline cursor-pointer">और पढ़ें</a>
</div>
@if($pol_lead)
<div class="relative rounded-lg overflow-hidden aspect-[16/10] bg-surface-container-high group cursor-pointer" onclick="location.href='{{ $pol_lead->url }}'">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $pol_lead->image_alt ?: $pol_lead->title }}" src="{{ $pol_lead->image_url }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
<div class="absolute bottom-3 left-3 right-3 text-on-primary">
<span class="text-xs bg-primary px-2 py-0.5 rounded font-label-badge font-semibold">{{ $pol_lead->badge_label ?: 'विशेष कवरेज' }}</span>
<h4 class="font-headline-sm text-headline-sm text-on-primary mt-1 line-clamp-2">
            {{ $pol_lead->title }}
          </h4>
</div>
</div>
@endif
<div class="space-y-3 pt-1">
@foreach($pol_list as $pl)
<div class="p-3 bg-surface rounded-lg hover:bg-surface-cream transition-colors cursor-pointer" onclick="location.href='{{ $pl->url }}'">
<h5 class="font-headline-sm text-headline-sm text-ink-primary text-sm hover:text-primary transition-colors line-clamp-2">
            {{ $pl->title }}
          </h5>
<p class="font-caption text-caption text-ink-muted mt-1">{{ $pl->excerpt ?: '' }}</p>
</div>
@endforeach
</div>
</section>
<!-- Desk Column 2: खेल जगत (Sports) - 4 Cols -->
<section class="lg:col-span-4 bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-1.5 h-5 bg-secondary rounded"></span>
<h3 class="font-headline-md text-headline-md text-ink-primary">खेल जगत</h3>
</div>
<a href="{{ url('category/sports') }}" class="font-caption text-caption text-primary font-semibold hover:underline cursor-pointer">और पढ़ें</a>
</div>
@if($sport_lead)
<div class="relative rounded-lg overflow-hidden aspect-[16/10] bg-surface-container-high group cursor-pointer" onclick="location.href='{{ $sport_lead->url }}'">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $sport_lead->image_alt ?: $sport_lead->title }}" src="{{ $sport_lead->image_url }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
<div class="absolute bottom-3 left-3 right-3 text-on-primary">
<span class="text-xs bg-secondary px-2 py-0.5 rounded font-label-badge font-semibold">{{ $sport_lead->badge_label ?: 'खेल' }}</span>
<h4 class="font-headline-sm text-headline-sm text-on-primary mt-1 line-clamp-2">
            {{ $sport_lead->title }}
          </h4>
</div>
</div>
@endif
<div class="space-y-3 pt-1">
@foreach($sport_list as $sl)
<div class="p-3 bg-surface rounded-lg hover:bg-surface-cream transition-colors cursor-pointer" onclick="location.href='{{ $sl->url }}'">
<h5 class="font-headline-sm text-headline-sm text-ink-primary text-sm hover:text-primary transition-colors line-clamp-2">
            {{ $sl->title }}
          </h5>
<p class="font-caption text-caption text-ink-muted mt-1">{{ $sl->excerpt ?: '' }}</p>
</div>
@endforeach
</div>
</section>
<!-- Desk Column 3: मनोरंजन एवं रियल एस्टेट (Entertainment & Real Estate) - 4 Cols -->
<section class="lg:col-span-4 bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-5">
<div class="space-y-3">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-1.5 h-5 bg-tertiary rounded"></span>
<h3 class="font-headline-md text-headline-md text-ink-primary">मनोरंजन</h3>
</div>
<a href="{{ url('category/entertainment') }}" class="font-caption text-caption text-primary font-semibold hover:underline cursor-pointer">सिनेमा</a>
</div>
@foreach($ent as $k => $en)
<div class="p-3 bg-surface rounded-lg hover:bg-surface-cream transition-colors cursor-pointer" onclick="location.href='{{ $en->url }}'">
@if($k === 0 && $en->badge_label)<span class="text-xs bg-tertiary/10 text-tertiary px-2 py-0.5 rounded font-semibold">{{ $en->badge_label }}</span>@endif
<h5 class="font-headline-sm text-headline-sm text-ink-primary text-sm hover:text-primary transition-colors mt-1 line-clamp-2">
            {{ $en->title }}
          </h5>
<p class="font-caption text-caption text-ink-muted mt-1">{{ $en->excerpt ?: '' }}</p>
</div>
@endforeach
</div>
<div class="space-y-3 pt-2">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-1.5 h-5 bg-primary rounded"></span>
<h3 class="font-headline-md text-headline-md text-ink-primary">रियल एस्टेट</h3>
</div>
<a href="{{ url('category/real-estate') }}" class="font-caption text-caption text-primary font-semibold hover:underline cursor-pointer">प्रॉपर्टी</a>
</div>
@foreach($real as $rl)
<div class="p-3 bg-surface rounded-lg hover:bg-surface-cream transition-colors cursor-pointer" onclick="location.href='{{ $rl->url }}'">
<h5 class="font-headline-sm text-headline-sm text-ink-primary text-sm hover:text-primary transition-colors line-clamp-2">
            {{ $rl->title }}
          </h5>
<p class="font-caption text-caption text-ink-muted mt-1">{{ $rl->excerpt ?: '' }}</p>
</div>
@endforeach
</div>
</section>
</div>
<!-- SECTION 4: Visual Stories & Specials (दृश्य कहानियाँ एवं वेब फ़ीचर्स) -->
{{-- Visual stories section removed at user request --}}
</section>
<!-- SECTION 5: Interactive Astrology & Opinion Poll Widget (दैनिक राशिफल एवं जनमत सर्वेक्षण) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
<!-- Horoscope Carousel Widget (8 Cols) -->
<div class="lg:col-span-8 bg-surface-cream rounded-xl p-6 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<span class="w-1.5 h-6 bg-primary rounded"></span>
<div>
<h2 class="font-headline-lg text-headline-lg text-ink-primary">आज का दैनिक राशिफल</h2>
<p class="font-caption text-caption text-ink-secondary">{{ (\App\Support\Site::setting('edition_date') !== '' ? \App\Support\Site::setting('edition_date') : \App\Support\Site::hiDate()) . ' | पंडितजी द्वारा ग्रह दशा का विश्लेषण' }}</p>
</div>
</div>
<span class="text-xs bg-primary/10 text-primary px-3 py-1 rounded font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">stars</span> १२ राशियाँ
          </span>
</div>
<div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap pb-3 scrollbar-none" id="zodiac-tabs">
@php $zi = 0; @endphp@foreach($signs as $s)@php $zk = $zkeys[$zi] ?? ('s' . $zi); $zi++; @endphp
<button type="button" class="zodiac-btn px-3 py-1.5 rounded-lg text-xs @if($zi === 1) font-bold bg-primary text-on-primary @else font-medium bg-surface-container text-ink-primary hover:bg-surface-container-high @endif transition-all" onclick="selectZodiac('{{ $zk }}',this)">{{ $s->name_hi }}</button>
@endforeach
</div>
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm mt-3 flex items-start gap-4" id="zodiac-content">
<div class="w-14 h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold text-xl" id="zodiac-sign">
            {{ $signs[0]['symbol'] ?? '♈' }}
          </div>
<div class="space-y-1.5">
<h4 class="font-headline-md text-headline-md text-ink-primary font-bold" id="zodiac-title">{{ ($signs[0]['name_hi'] ?? 'मेष') . ' राशि (' . ($signs[0]['name_en'] ?? 'Aries') . ')' }}</h4>
<p class="font-body-md text-body-md text-ink-secondary leading-relaxed" id="zodiac-desc">
              {{ $signs[0]['prediction'] ?? '' }}
            </p>
<div class="flex items-center gap-4 pt-2 text-xs font-caption text-ink-muted" id="zodiac-meta">
<span>शुभ रंग: <strong class="text-ink-primary" id="zodiac-color">{{ $signs[0]['lucky_color'] ?? '' }}</strong></span>
<span>•</span>
<span>शुभ अंक: <strong class="text-ink-primary" id="zodiac-num">{{ $signs[0]['lucky_number'] ?? '' }}</strong></span>
<span>•</span>
<span>आराध्य: <strong class="text-ink-primary" id="zodiac-deity">{{ $signs[0]['deity'] ?? '' }}</strong></span>
</div>
</div>
</div>
</div>
<div class="pt-4 flex items-center justify-between text-xs text-ink-muted">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[15px]">verified</span> प्रमाणित ज्योतिषीय गणना
        </span>
<a href="{{ url('horoscope') }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
          पूर्ण साप्ताहिक राशिफल <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</a>
</div>
</div>
<!-- Opinion Poll & Subscription Widget (4 Cols) -->
<div class="lg:col-span-4 bg-surface-container-lowest rounded-xl p-6 shadow-sm flex flex-col justify-between" id="poll-box">
@if($poll)
<div>
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-2">
<span class="w-1.5 h-5 bg-breaking-red rounded"></span>
<h3 class="font-headline-md text-headline-md text-ink-primary">जनमत सर्वेक्षण</h3>
</div>
<span class="bg-breaking-red/10 text-breaking-red text-[11px] font-bold px-2 py-0.5 rounded">लाइव पोल</span>
</div>
<p class="font-headline-sm text-headline-sm text-ink-primary mt-2">
          {{ $poll->question }}
        </p>
@if($voted && $poll_total > 0)
<div class="mt-4 space-y-3">
@foreach($poll_opts as $o)@php $pct = (int) round($o->votes * 100 / max(1,$poll_total)); @endphp
<div class="space-y-1">
<div class="flex justify-between text-xs font-semibold text-ink-primary">
<span>{{ $o->option_text }}</span>
<span class="font-bold">{{ \App\Support\Site::hiDigits((string) $pct) }}% ({{ \App\Support\Site::hiDigits(number_format((int) $o->votes)) }} मत)</span>
</div>
<div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
<div class="{{ $o->color_class ?: 'bg-primary' }} h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
</div>
</div>
@endforeach
<p class="text-xs text-market-up font-semibold text-center pt-1">आपका मत दर्ज कर लिया गया है। कुल मत: {{ \App\Support\Site::hiDigits(number_format($poll_total)) }}</p>
</div>
@elseif($poll_total > 0)
<form class="mt-4 space-y-3" action="{{ url('poll-vote') }}" method="post">
@csrf
<input type="hidden" name="poll_id" value="{{ (int) $poll->id }}">
@foreach($poll_opts as $k => $o)@php $pct = (int) round($o->votes * 100 / max(1, $poll_total)); @endphp
<div class="space-y-1">
<div class="flex justify-between text-xs font-semibold text-ink-primary">
<label class="flex items-center gap-2 cursor-pointer">
<input @if($k === 0) checked @endif class="text-primary focus:ring-0" name="option_id" type="radio" value="{{ (int) $o->id }}"/>
<span>{{ $o->option_text }}</span>
</label>
<span class="@if(str_contains($o->color_class ?? '', 'market-up')) text-market-up @elseif(str_contains($o->color_class ?? '', 'market-down')) text-market-down @else text-ink-primary @endif font-bold">{{ \App\Support\Site::hiDigits((string) $pct) }}% ({{ \App\Support\Site::hiDigits(number_format((int) $o->votes)) }} मत)</span>
</div>
<div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
<div class="{{ $o->color_class ?: 'bg-primary' }} h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
</div>
</div>
@endforeach
<button class="w-full mt-3 bg-primary text-on-primary py-2 rounded text-xs font-semibold hover:bg-masthead-crimson transition-colors flex items-center justify-center gap-1.5" type="submit">
<span class="material-symbols-outlined text-[16px]">how_to_vote</span> अपना मत दर्ज करें
          </button>
</form>
@endif
</div>
@endif
<div class="mt-6 pt-5 bg-surface-cream p-4 rounded-lg">
<div class="flex items-center gap-3 mb-2">
<span class="material-symbols-outlined text-primary text-2xl">newspaper</span>
<h4 class="font-headline-sm text-sm font-bold text-ink-primary">अखबार घर पर मंगाएँ</h4>
</div>
<p class="font-caption text-caption text-ink-secondary mb-3">
          दैनिक विश्वामित्र की हार्ड कॉपी अपने द्वार पर पाने हेतु आज ही सदस्यता लें।
        </p>
<a href="{{ url('subscribe') }}" class="w-full bg-ink-primary hover:bg-primary text-on-primary text-xs py-2 rounded font-semibold transition-colors flex items-center justify-center gap-1">
<span>सदस्यता फॉर्म भरें</span>
<span class="material-symbols-outlined text-[15px]">arrow_forward</span>
</a>
</div>
</div>
</div>
</div></div>
<script>
  const zodiacData = @json($zdata, JSON_UNESCAPED_UNICODE);
  function selectZodiac(key, btn) {
    const data = zodiacData[key];
    if (!data) return;
    document.querySelectorAll('.zodiac-btn').forEach(b => {
      b.classList.remove('bg-primary', 'text-on-primary', 'font-bold');
      b.classList.add('bg-surface-container', 'text-ink-primary', 'font-medium');
    });
    btn = btn || (window.event ? window.event.currentTarget : null);
    if (btn) {
      btn.classList.add('bg-primary', 'text-on-primary', 'font-bold');
      btn.classList.remove('bg-surface-container', 'text-ink-primary', 'font-medium');
    }
    document.getElementById('zodiac-sign').innerText = data.sign;
    document.getElementById('zodiac-title').innerText = data.title;
    document.getElementById('zodiac-desc').innerText = data.desc;
    document.getElementById('zodiac-color').innerText = data.color;
    document.getElementById('zodiac-num').innerText = data.num;
    document.getElementById('zodiac-deity').innerText = data.deity;
  }
</script>

@endsection
