@extends('layouts.public')

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb & Reader Header --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-border-hairline p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-ink-muted mb-2 font-caption">
                    <a href="{{ url('/') }}" class="hover:text-primary transition-colors">होम</a>
                    <span>/</span>
                    <a href="{{ route('epaper') }}" class="hover:text-primary transition-colors">ई-पेपर आर्काइव</a>
                    <span>/</span>
                    <span class="text-ink-primary font-medium truncate max-w-[200px] sm:max-w-xs">{{ $paper->title }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[32px]">menu_book</span>
                    <div>
                        <h1 class="font-headline-md text-xl sm:text-2xl text-ink-primary font-bold leading-tight">{{ $paper->title }}</h1>
                        <p class="font-caption text-xs sm:text-sm text-ink-secondary mt-0.5">
                            संस्करण तिथि: <strong class="text-ink-primary">{{ \App\Support\Site::hiDateShort($paper->edition_date) }}</strong>
                            @if(!$paper->is_active)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800">अप्रकाशित पूर्वावलोकन · Draft Preview</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap shrink-0">
                <a href="{{ route('epaper') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-border-hairline text-ink-secondary hover:text-primary hover:border-primary text-xs font-semibold transition-colors bg-white">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>सभी अंक · Archive</span>
                </a>
                <a href="{{ asset(ltrim((string) $paper->pdf_path, '/')) }}" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary hover:bg-masthead-crimson text-white text-xs font-semibold shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>PDF डाउनलोड · Download</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Interactive PDF Reader Shell --}}
    <div id="epaper-reader" class="bg-surface-container-lowest rounded-xl shadow-sm border border-border-hairline overflow-hidden">
        {{-- Toolbar --}}
        <div class="bg-surface-cream border-b border-border-hairline p-3 flex flex-wrap items-center justify-between gap-2.5 text-xs select-none">
            {{-- Navigation Controls --}}
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" id="prev-page" class="p-1.5 sm:px-2.5 sm:py-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-1 font-medium transition-colors" title="पिछला पृष्ठ · Previous Page (Left Arrow)">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    <span class="hidden sm:inline">पिछला</span>
                </button>

                <div class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-border-hairline rounded-lg font-medium text-ink-primary">
                    <span>पृष्ठ</span>
                    <input type="number" id="page-num" min="1" value="1" class="w-12 text-center border-b border-primary font-bold focus:outline-none py-0.5"/>
                    <span>/</span>
                    <span id="page-count" class="font-bold">--</span>
                </div>

                <button type="button" id="next-page" class="p-1.5 sm:px-2.5 sm:py-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-1 font-medium transition-colors" title="अगला पृष्ठ · Next Page (Right Arrow)">
                    <span class="hidden sm:inline">अगला</span>
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>

            {{-- Zoom & Layout Controls --}}
            <div class="flex items-center gap-1.5">
                <button type="button" id="zoom-out" class="p-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary transition-colors" title="ज़ूम आउट · Zoom Out (-)">
                    <span class="material-symbols-outlined text-[18px]">zoom_out</span>
                </button>
                <button type="button" id="zoom-reset" class="px-2 py-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary font-medium text-[11px] transition-colors" title="सामान्य आकार · Reset Zoom">
                    <span id="zoom-level">100%</span>
                </button>
                <button type="button" id="zoom-in" class="p-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary transition-colors" title="ज़ूम इन · Zoom In (+)">
                    <span class="material-symbols-outlined text-[18px]">zoom_in</span>
                </button>
                <button type="button" id="fit-width" class="hidden md:inline-flex p-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary transition-colors items-center gap-1" title="चौड़ाई अनुसार · Fit Width">
                    <span class="material-symbols-outlined text-[18px]">fit_screen</span>
                    <span class="text-[11px] font-medium">फिट चौड़ाई</span>
                </button>
            </div>

            {{-- View Options --}}
            <div class="flex items-center gap-1.5">
                <button type="button" id="toggle-thumbs" class="p-1.5 px-2.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary inline-flex items-center gap-1 transition-colors" title="पृष्ठ सूची · Page Thumbnails">
                    <span class="material-symbols-outlined text-[18px]">view_sidebar</span>
                    <span class="hidden lg:inline text-[11px] font-medium">पृष्ठ सूची</span>
                </button>
                <button type="button" id="toggle-fullscreen" class="p-1.5 rounded-lg border border-border-hairline bg-white hover:bg-surface-container text-ink-primary transition-colors" title="पूर्ण स्क्रीन · Fullscreen">
                    <span class="material-symbols-outlined text-[18px]" id="fs-icon">fullscreen</span>
                </button>
            </div>
        </div>

        {{-- Main Reading Viewport --}}
        <div id="viewport-container" class="relative flex min-h-[600px] max-h-[85vh] bg-[#3a3a3a] overflow-hidden">
            {{-- Thumbnails Sidebar --}}
            <aside id="thumbnails-sidebar" class="hidden w-44 bg-[#2b2b2b] border-r border-[#444] overflow-y-auto shrink-0 p-3 space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2 px-1">सभी पृष्ठ</div>
                <div id="thumbnails-list" class="space-y-3">
                    {{-- Dynamically populated --}}
                </div>
            </aside>

            {{-- Central Canvas Container --}}
            <div id="canvas-scroll-wrapper" class="flex-1 overflow-auto flex items-start justify-center p-4 sm:p-6">
                <div id="canvas-wrapper" class="relative shadow-2xl transition-transform origin-top">
                    <div id="reader-spinner" class="absolute inset-0 bg-[#2b2b2b]/90 backdrop-blur-sm flex flex-col items-center justify-center text-white p-6 rounded-lg z-10">
                        <div class="w-10 h-10 border-4 border-white/20 border-t-primary rounded-full animate-spin mb-3"></div>
                        <p class="font-medium text-sm text-stone-200">ई-पेपर लोड हो रहा है…</p>
                        <p class="text-xs text-stone-400 mt-1">Dainik Vishwamitra E-Paper Reader</p>
                    </div>
                    <canvas id="pdf-canvas" class="bg-white rounded block shadow-xl"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Other Recent Editions --}}
    @if(isset($recent) && $recent->isNotEmpty())
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-border-hairline p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-border-hairline">
                <h2 class="font-headline-sm text-base font-bold text-ink-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">history</span>
                    <span>हाल के अन्य अंक · Recent Editions</span>
                </h2>
                <a href="{{ route('epaper') }}" class="text-xs font-semibold text-primary hover:underline">सभी देखें →</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($recent as $r)
                    <a href="{{ route('epaper.show', $r->id) }}" class="group block bg-surface-container-low hover:bg-surface-container rounded-lg p-2.5 border border-border-hairline transition-all hover:shadow-md">
                        <div class="aspect-[3/4] bg-surface-container rounded overflow-hidden mb-2 flex items-center justify-center text-ink-muted group-hover:scale-[1.02] transition-transform">
                            @if(!empty($r->cover_image))
                                <img src="{{ asset(ltrim((string) $r->cover_image, '/')) }}" alt="{{ $r->title }}" class="w-full h-full object-cover"/>
                            @else
                                <span class="material-symbols-outlined text-[32px] text-ink-muted">picture_as_pdf</span>
                            @endif
                        </div>
                        <h3 class="font-headline-sm text-xs font-semibold text-ink-primary line-clamp-2 leading-tight group-hover:text-primary transition-colors">{{ $r->title }}</h3>
                        <p class="text-[11px] text-ink-muted mt-1 font-caption">{{ \App\Support\Site::hiDateShort($r->edition_date) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Standalone Mozilla PDF.js Script --}}
<script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
<script>
(function() {
    'use strict';

    if (typeof pdfjsLib === 'undefined') {
        var wrap = document.getElementById('canvas-scroll-wrapper');
        if (wrap) {
            wrap.innerHTML = '<div class="text-center text-white p-8"><p class="text-base font-bold mb-2">PDF रीडर प्रारंभ नहीं हो सका।</p><p class="text-xs text-stone-300 mb-4">आप नीचे दिए गए लिंक से सीधे PDF डाउनलोड कर सकते हैं:</p><a href="{{ asset(ltrim((string) $paper->pdf_path, "/")) }}" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary text-white text-xs font-semibold">PDF डाउनलोड करें</a></div>';
        }
        return;
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

    var url = "{{ asset(ltrim((string) $paper->pdf_path, '/')) }}";
    var pdfDoc = null;
    var pageNum = 1;
    var pageRendering = false;
    var pageNumPending = null;
    var scale = 1.25;
    var canvas = document.getElementById('pdf-canvas');
    var ctx = canvas.getContext('2d');
    var spinner = document.getElementById('reader-spinner');
    var pageNumInput = document.getElementById('page-num');
    var pageCountSpan = document.getElementById('page-count');
    var zoomLevel = document.getElementById('zoom-level');
    var thumbsList = document.getElementById('thumbnails-list');
    var sidebar = document.getElementById('thumbnails-sidebar');
    var readerEl = document.getElementById('epaper-reader');

    function renderPage(num) {
        pageRendering = true;
        if (spinner) spinner.style.display = 'flex';

        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({ scale: scale });
            var pixelRatio = Math.max(window.devicePixelRatio || 1, 1.5);

            canvas.height = viewport.height * pixelRatio;
            canvas.width = viewport.width * pixelRatio;
            canvas.style.height = viewport.height + 'px';
            canvas.style.width = viewport.width + 'px';

            var renderContext = {
                canvasContext: ctx,
                viewport: viewport,
                transform: [pixelRatio, 0, 0, pixelRatio, 0, 0]
            };

            var renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pageRendering = false;
                if (spinner) spinner.style.display = 'none';

                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }

                // Update thumbnail selection
                document.querySelectorAll('.thumb-btn').forEach(function(b) {
                    var p = parseInt(b.getAttribute('data-page'), 10);
                    if (p === num) {
                        b.classList.add('border-primary', 'ring-2', 'ring-primary');
                        b.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        b.classList.remove('border-primary', 'ring-2', 'ring-primary');
                    }
                });
            });
        }).catch(function(err) {
            console.error('Error rendering page:', err);
            pageRendering = false;
            if (spinner) spinner.style.display = 'none';
        });

        pageNumInput.value = num;
        zoomLevel.textContent = Math.round(scale * 100) + '%';
        document.getElementById('prev-page').disabled = (num <= 1);
        document.getElementById('next-page').disabled = (num >= (pdfDoc ? pdfDoc.numPages : 1));
    }

    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    function onPrevPage() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    }

    function onNextPage() {
        if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    }

    function fitWidth() {
        if (!pdfDoc) return;
        pdfDoc.getPage(pageNum).then(function(page) {
            var scrollWrapper = document.getElementById('canvas-scroll-wrapper');
            var availableWidth = scrollWrapper.clientWidth - 48;
            var unscaledViewport = page.getViewport({ scale: 1.0 });
            scale = Math.max(0.5, Math.min(3.0, availableWidth / unscaledViewport.width));
            queueRenderPage(pageNum);
        });
    }

    // Controls listeners
    document.getElementById('prev-page').addEventListener('click', onPrevPage);
    document.getElementById('next-page').addEventListener('click', onNextPage);

    pageNumInput.addEventListener('change', function() {
        var val = parseInt(this.value, 10);
        if (pdfDoc && val >= 1 && val <= pdfDoc.numPages) {
            pageNum = val;
            queueRenderPage(pageNum);
        } else {
            this.value = pageNum;
        }
    });

    document.getElementById('zoom-in').addEventListener('click', function() {
        if (scale < 3.0) {
            scale = Math.min(3.0, scale + 0.25);
            queueRenderPage(pageNum);
        }
    });

    document.getElementById('zoom-out').addEventListener('click', function() {
        if (scale > 0.5) {
            scale = Math.max(0.5, scale - 0.25);
            queueRenderPage(pageNum);
        }
    });

    document.getElementById('zoom-reset').addEventListener('click', function() {
        scale = 1.0;
        queueRenderPage(pageNum);
    });

    document.getElementById('fit-width').addEventListener('click', fitWidth);

    document.getElementById('toggle-thumbs').addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
    });

    document.getElementById('toggle-fullscreen').addEventListener('click', function() {
        if (!document.fullscreenElement) {
            if (readerEl.requestFullscreen) {
                readerEl.requestFullscreen();
            }
            document.getElementById('fs-icon').textContent = 'fullscreen_exit';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            document.getElementById('fs-icon').textContent = 'fullscreen';
        }
    });

    // Keyboard navigation
    window.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT') return;
        if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
            e.preventDefault();
            onPrevPage();
        } else if (e.key === 'ArrowRight' || e.key === 'PageDown') {
            e.preventDefault();
            onNextPage();
        } else if (e.key === '+' || e.key === '=') {
            scale = Math.min(3.0, scale + 0.25);
            queueRenderPage(pageNum);
        } else if (e.key === '-') {
            scale = Math.max(0.5, scale - 0.25);
            queueRenderPage(pageNum);
        }
    });

    // Load PDF Document
    pdfjsLib.getDocument({
        url: url,
        cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
        cMapPacked: true
    }).promise.then(function(doc) {
        pdfDoc = doc;
        pageCountSpan.textContent = doc.numPages;
        pageNumInput.max = doc.numPages;

        // Auto-fit on mobile screens
        if (window.innerWidth < 768) {
            fitWidth();
        } else {
            renderPage(pageNum);
        }

        // Build thumbnail sidebar
        buildThumbnails(doc);
    }).catch(function(error) {
        console.error('PDF loading error:', error);
        if (spinner) spinner.style.display = 'none';
        var wrap = document.getElementById('canvas-scroll-wrapper');
        if (wrap) {
            wrap.innerHTML = '<div class="text-center text-white p-8"><p class="text-base font-bold mb-2">ई-पेपर लोड करने में असमर्थ।</p><p class="text-xs text-stone-300 mb-4">' + (error.message || 'अज्ञात त्रुटि') + '</p><a href="' + url + '" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded bg-primary text-white text-xs font-semibold">PDF डाउनलोड करके पढ़ें</a></div>';
        }
    });

    function buildThumbnails(doc) {
        if (!thumbsList) return;
        thumbsList.innerHTML = '';

        for (var i = 1; i <= doc.numPages; i++) {
            (function(pNum) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'thumb-btn w-full text-left p-1.5 rounded-lg border border-[#444] bg-[#222] hover:bg-[#333] transition-all block' + (pNum === pageNum ? ' border-primary ring-2 ring-primary' : '');
                btn.setAttribute('data-page', pNum);

                var label = document.createElement('div');
                label.className = 'text-[11px] font-semibold text-stone-300 mb-1 px-1';
                label.textContent = 'पृष्ठ ' + pNum;
                btn.appendChild(label);

                var thumbCanvas = document.createElement('canvas');
                thumbCanvas.className = 'w-full h-auto bg-white rounded shadow-sm block';
                btn.appendChild(thumbCanvas);

                btn.addEventListener('click', function() {
                    pageNum = pNum;
                    queueRenderPage(pageNum);
                });

                thumbsList.appendChild(btn);

                // Render low-res thumbnail
                doc.getPage(pNum).then(function(page) {
                    var thumbViewport = page.getViewport({ scale: 0.2 });
                    thumbCanvas.height = thumbViewport.height;
                    thumbCanvas.width = thumbViewport.width;
                    page.render({
                        canvasContext: thumbCanvas.getContext('2d'),
                        viewport: thumbViewport
                    });
                });
            })(i);
        }
    }
})();
</script>
@endsection
