{{--
    Public site shell. Pages set $page_title / $page_desc and @section(content).
    Ported from legacy includes/head.php + includes/header.php + includes/footer.php.
--}}
<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="shell-type" content="web_standard"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Noto+Serif:wght@400;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}
::-webkit-scrollbar{display:none;}
.scrollbar-none{scrollbar-width:none;-ms-overflow-style:none;}
.scrollbar-none::-webkit-scrollbar{display:none;}
body{font-family:'Inter','Noto Sans',system-ui,sans-serif;}
h1,h2,h3,.font-headline-hero,.font-headline-lg,.font-headline-md,.font-headline-sm{font-family:'Noto Serif','Noto Sans',serif;}
input:focus,select:focus,button:focus-visible,a:focus-visible{outline:2px solid #C4161C;outline-offset:1px;}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.article-body{font-family:'Inter','Noto Sans',sans-serif;}
.article-body::after{content:"";display:table;clear:both;}
.article-body figure{margin:1.5rem 0;text-align:center;}
.article-body figure img{max-width:100%;border-radius:0.5rem;display:inline-block;height:auto;}
.article-body figcaption{font-size:0.825rem;color:#6B7280;font-style:italic;margin-top:0.4rem;}
.article-body figure.align-left{float:left;margin:0.5rem 1.5rem 1rem 0;max-width:50%;clear:left;text-align:left;}
.article-body figure.align-right{float:right;margin:0.5rem 0 1rem 1.5rem;max-width:50%;clear:right;text-align:right;}
.article-body figure.align-center{float:none;margin:1.5rem auto;text-align:center;clear:both;display:block;}
.article-body figure.align-full{float:none;width:100%;max-width:100%;margin:1.5rem 0;clear:both;text-align:center;}
.article-body figure.align-full img{width:100%;}
@media (max-width:640px){
  .article-body figure.align-left,
  .article-body figure.align-right{float:none!important;max-width:100%!important;margin:1rem auto!important;text-align:center!important;}
}
@media print{header,footer,#zodiac-tabs,.no-print{display:none!important;}main{padding-top:0!important;}}
</style>
@stack("head")
<style>
.ticker-track{display:flex;gap:1.5rem;animation:ticker-scroll 25s linear infinite;white-space:nowrap;}
.ticker-track:hover{animation-play-state:paused;}
@keyframes ticker-scroll{0%{transform:translateX(0);}100%{transform:translateX(-50%);}}
</style>
<script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config={darkMode:"class",theme:{extend:{colors:{"background":"#f9f9f8","surface-container-lowest":"#ffffff","masthead-crimson":"#B31B1B","market-down":"#DC2626","primary-fixed-dim":"#ffb4ab","border-hairline":"#E5E7EB","primary-container":"#c4161c","surface-tint":"#bd0e18","tertiary":"#414b5a","market-up":"#16A34A","inverse-on-surface":"#f1f1f0","ink-primary":"#111827","outline":"#906f6b","surface-bright":"#f9f9f8","surface-container-highest":"#e2e2e2","tertiary-fixed-dim":"#bdc7d9","on-surface-variant":"#5c403d","surface-cream":"#F9F9F7","error":"#ba1a1a","inverse-surface":"#2f3130","on-background":"#1a1c1c","on-tertiary":"#ffffff","on-secondary-container":"#004972","on-primary":"#ffffff","inverse-primary":"#ffb4ab","secondary-fixed-dim":"#94ccff","ink-muted":"#6B7280","secondary":"#006399","on-surface":"#1a1c1c","on-tertiary-container":"#d5dff2","on-secondary":"#ffffff","on-primary-fixed":"#410002","on-tertiary-fixed":"#121c2a","surface-container":"#eeeeed","surface":"#f9f9f8","on-error-container":"#93000a","tertiary-container":"#596373","on-secondary-fixed-variant":"#004b74","primary":"#9b000e","error-container":"#ffdad6","surface-container-high":"#e8e8e7","secondary-fixed":"#cde5ff","on-error":"#ffffff","tertiary-fixed":"#d9e3f6","outline-variant":"#e5bdb9","surface-variant":"#e2e2e2","border-strong":"#111827","breaking-red":"#E21B22","surface-container-low":"#f3f4f3","secondary-container":"#67bafd","ink-secondary":"#4B5563","on-secondary-fixed":"#001d32","on-tertiary-fixed-variant":"#3d4756","primary-fixed":"#ffdad6","on-primary-fixed-variant":"#93000c","surface-dim":"#dadad9","on-primary-container":"#ffd6d2"},borderRadius:{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},spacing:{"space-xl":"2rem","space-xs":"0.25rem","space-sm":"0.5rem","margin-desktop":"2.5rem","gutter-desktop":"1.5rem","margin":"1rem","space-md":"0.875rem","space-lg":"1.25rem","gutter":"1.25rem"},fontFamily:{"headline-lg-mobile":["Noto Serif"],"headline-hero-mobile":["Noto Serif"],"headline-hero":["Noto Serif"],"headline-lg":["Noto Serif"],"headline-sm":["Noto Serif"],"label-badge":["Noto Sans"],"body-lg":["Inter"],"headline-md":["Noto Serif"],"body-sm":["Inter"],"body-md":["Inter"],"caption":["Noto Sans"],"label-ticker":["Noto Sans"]},fontSize:{"headline-lg-mobile":["24px",{"lineHeight":"34px","fontWeight":"700"}],"headline-hero-mobile":["30px",{"lineHeight":"40px","letterSpacing":"-0.01em","fontWeight":"700"}],"headline-hero":["44px",{"lineHeight":"58px","letterSpacing":"-0.01em","fontWeight":"700"}],"headline-lg":["32px",{"lineHeight":"44px","fontWeight":"700"}],"headline-sm":["18px",{"lineHeight":"26px","fontWeight":"600"}],"label-badge":["12px",{"lineHeight":"16px","letterSpacing":"0.03em","fontWeight":"600"}],"body-lg":["18px",{"lineHeight":"30px","fontWeight":"400"}],"headline-md":["22px",{"lineHeight":"32px","fontWeight":"600"}],"body-sm":["13px",{"lineHeight":"20px","fontWeight":"400"}],"body-md":["15px",{"lineHeight":"24px","fontWeight":"400"}],"caption":["12px",{"lineHeight":"16px","fontWeight":"400"}],"label-ticker":["14px",{"lineHeight":"18px","letterSpacing":"0.02em","fontWeight":"700"}]}}}}</script>
<title>{{ $page_title ?? "दैनिक विश्वामित्र" }}</title>
<meta name="description" content="{{ $page_desc ?? "भारत का प्रमुख एवं विश्वसनीय हिंदी दैनिक | स्थापना १९१६" }}"/>
</head>
<body class="bg-surface font-body-md text-ink-primary antialiased selection:bg-primary selection:text-on-primary">

@include("partials.header", ["nav_active" => $nav_active ?? "home"])

<main class="max-w-7xl mx-auto px-4 lg:px-8 pt-48 pb-12">

    @if (session('ok'))
        <div class="mb-4 px-4 py-2.5 rounded-lg text-sm font-semibold bg-market-up/10 text-market-up border border-market-up/20">{{ session('ok') }}</div>
    @endif
    @if (session('err'))
        <div class="mb-4 px-4 py-2.5 rounded-lg text-sm font-semibold bg-market-down/10 text-market-down border border-market-down/20">{{ session('err') }}</div>
    @endif

    @yield('content')
</main>

@include("partials.footer")

</body>
</html>
