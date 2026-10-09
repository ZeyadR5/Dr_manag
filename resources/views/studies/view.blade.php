<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عارض الأشعة DICOM - {{ $patientName }}</title>

    <!-- Google Fonts: Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind Play Standalone -->
    <script src="{{ asset('tailwind.js') }}"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #090d16;
            color: #f1f5f9;
            overflow: hidden;
            user-select: none;
        }

        /* Thumbnails scrollbar */
        .filmstrip::-webkit-scrollbar {
            width: 5px;
        }
        .filmstrip::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .main-image {
            transition: transform 0.08s ease-out, filter 0.15s ease;
            transform-origin: center center;
            cursor: grab;
            max-width: 92%;
            max-height: 92%;
            object-fit: contain;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.8);
        }
        .main-image:active {
            cursor: grabbing;
        }

        .active-thumb {
            border-color: #0284c7 !important;
            box-shadow: 0 0 12px rgba(2, 132, 199, 0.6);
        }
    </style>
</head>
<body class="h-screen w-screen flex flex-col bg-[#090d16]">

    <!-- Medical Topbar -->
    <header class="h-14 bg-slate-900 border-b border-slate-800 px-4 flex items-center justify-between z-30 shrink-0">
        <!-- Patient Info -->
        <div class="flex items-center gap-3">
            <a href="{{ url('index.php') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="العودة لأرشيف الأشعة">
                <i class="bi bi-arrow-right text-lg"></i>
            </a>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-600/20 text-sky-400 border border-sky-500/30 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold text-white leading-tight flex items-center gap-2">
                        <span>{{ $patientName ?: 'مريض غير محدد' }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/30">DICOM PACS</span>
                    </h1>
                    <p class="text-[11px] text-slate-400">إجمالي اللقطات: {{ count($instances) }} صورة</p>
                </div>
            </div>
        </div>

        <!-- Central Workstation Controls -->
        <div class="hidden md:flex items-center gap-1.5 bg-slate-800/80 p-1 rounded-xl border border-slate-700/60 text-xs">
            <button onclick="zoomIn()" class="p-2 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="تكبير">
                <i class="bi bi-zoom-in text-base"></i>
            </button>
            <button onclick="zoomOut()" class="p-2 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="تصغير">
                <i class="bi bi-zoom-out text-base"></i>
            </button>
            <button onclick="resetZoom()" class="p-2 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="إعادة ضبط الحجم">
                <i class="bi bi-arrows-fullscreen text-base"></i>
            </button>
            <div class="h-4 w-px bg-slate-700 mx-1"></div>
            <button onclick="rotateImage()" class="p-2 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="تدوير الصورة">
                <i class="bi bi-arrow-clockwise text-base"></i>
            </button>
            <button onclick="toggleInvert()" id="invertBtn" class="p-2 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="عكس الألوان (Negative / Positive)">
                <i class="bi bi-circle-half text-base"></i>
            </button>
            <button onclick="resetAll()" class="p-2 rounded-lg hover:bg-rose-900/40 text-rose-400 transition-colors" title="إعادة الضبط الشامل">
                <i class="bi bi-arrow-counterclockwise text-base"></i>
            </button>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-2">
            <button onclick="toggleFullscreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors text-sm" title="شاشة كاملة">
                <i class="bi bi-fullscreen"></i>
            </button>
            <a href="{{ url('index.php') }}" class="px-3.5 py-1.5 rounded-xl bg-clinic-600 hover:bg-clinic-700 text-white font-bold text-xs shadow-sm transition-all">
                خروج من العارض
            </a>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden relative">

        <!-- Left / Side Filmstrip (Thumbnails) -->
        <div class="filmstrip w-32 md:w-40 bg-slate-950 border-l border-slate-800 p-2.5 overflow-y-auto flex flex-col gap-2 shrink-0 z-20">
            <div class="text-[11px] font-bold text-slate-400 px-1 pb-1 border-b border-slate-800 flex items-center justify-between">
                <span>اللقطات</span>
                <span>{{ count($instances) }}</span>
            </div>

            @foreach($instances as $idx => $instance)
                @php $imgUrl = $orthanc->renderedUrl($instance['ID']); @endphp
                <div class="thumbnail-item group relative cursor-pointer rounded-lg overflow-hidden border-2 border-slate-800 hover:border-slate-600 transition-all {{ $idx === 0 ? 'active-thumb' : '' }}" onclick="showImage(this, '{{ $imgUrl }}')">
                    <img src="{{ $imgUrl }}" data-full="{{ $imgUrl }}" class="w-full aspect-square object-cover bg-black" alt="Series #{{ $idx+1 }}" loading="lazy">
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-1 text-center">
                        <span class="text-[10px] font-bold text-slate-300">#{{ $idx + 1 }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Center Diagnostic Canvas Viewport -->
        <div class="flex-1 bg-[#050811] relative flex items-center justify-center overflow-hidden" id="viewport">

            <!-- HUD Overlay Watermark -->
            <div class="absolute top-4 right-4 pointer-events-none z-10 text-[11px] font-mono text-slate-500 bg-black/40 px-3 py-1.5 rounded-lg border border-white/5 backdrop-blur-sm">
                <div>ZOOM: <span id="hudZoom" class="text-sky-400 font-bold">100%</span></div>
                <div>ROTATION: <span id="hudRot" class="text-sky-400 font-bold">0°</span></div>
            </div>

            <!-- Floating Adjustment Widget (Brightness & Contrast) -->
            <div class="absolute bottom-5 left-5 z-20 bg-slate-900/90 backdrop-blur-md p-3.5 rounded-2xl border border-slate-800 shadow-2xl flex flex-col gap-3 w-56">
                <div class="flex items-center justify-between text-xs font-bold text-slate-300">
                    <span class="flex items-center gap-1.5"><i class="bi bi-sliders"></i> ضبط الإضاءة والتباين</span>
                    <button onclick="resetAdjustments()" class="text-[11px] text-rose-400 hover:text-rose-300">إعادة ضبط</button>
                </div>

                <div>
                    <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                        <span>السطوع</span>
                        <span id="brightness-value" class="font-mono text-white">0</span>
                    </div>
                    <input type="range" id="brightness" min="-100" max="100" value="0" oninput="adjustImage()" class="w-full accent-sky-500 bg-slate-700 h-1.5 rounded-lg appearance-none cursor-pointer">
                </div>

                <div>
                    <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                        <span>التباين</span>
                        <span id="contrast-value" class="font-mono text-white">0</span>
                    </div>
                    <input type="range" id="contrast" min="-100" max="100" value="0" oninput="adjustImage()" class="w-full accent-sky-500 bg-slate-700 h-1.5 rounded-lg appearance-none cursor-pointer">
                </div>
            </div>

            <!-- Active Medical DICOM Image -->
            @if(!empty($instances[0]['ID']))
                <img src="{{ $orthanc->renderedUrl($instances[0]['ID']) }}" id="mainImage" class="main-image" alt="{{ $patientName }}" draggable="false">
            @else
                <div class="text-center p-8 text-slate-500">
                    <i class="bi bi-image text-5xl mb-2"></i>
                    <p class="text-sm">لا توجد صور متوفرة في هذه الحالة</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const mainImage = document.getElementById('mainImage');
        const brightnessSlider = document.getElementById('brightness');
        const contrastSlider = document.getElementById('contrast');
        const brightnessValue = document.getElementById('brightness-value');
        const contrastValue = document.getElementById('contrast-value');
        const hudZoom = document.getElementById('hudZoom');
        const hudRot = document.getElementById('hudRot');

        let isDragging = false, startX = 0, startY = 0;
        let rotation = 0;
        let isInverted = false;

        if (mainImage) {
            mainImage.dataset.scale = "1";
            mainImage.dataset.posX = "0";
            mainImage.dataset.posY = "0";
            mainImage.dataset.rotation = "0";
        }

        function applyTransform() {
            if (!mainImage) return;
            const scale = parseFloat(mainImage.dataset.scale);
            const posX = parseFloat(mainImage.dataset.posX);
            const posY = parseFloat(mainImage.dataset.posY);
            const rot = parseFloat(mainImage.dataset.rotation);

            mainImage.style.transform = `translate(${posX}px, ${posY}px) rotate(${rot}deg) scale(${scale})`;
            if (hudZoom) hudZoom.textContent = Math.round(scale * 100) + '%';
            if (hudRot) hudRot.textContent = rot + '°';
        }

        function zoomIn() {
            if (!mainImage) return;
            let scale = parseFloat(mainImage.dataset.scale);
            scale = Math.min(5, scale + 0.2);
            mainImage.dataset.scale = scale;
            applyTransform();
        }

        function zoomOut() {
            if (!mainImage) return;
            let scale = parseFloat(mainImage.dataset.scale);
            scale = Math.max(0.2, scale - 0.2);
            mainImage.dataset.scale = scale;
            applyTransform();
        }

        function resetZoom() {
            if (!mainImage) return;
            mainImage.dataset.scale = "1";
            mainImage.dataset.posX = "0";
            mainImage.dataset.posY = "0";
            applyTransform();
        }

        function rotateImage() {
            if (!mainImage) return;
            rotation = (rotation + 90) % 360;
            mainImage.dataset.rotation = rotation;
            applyTransform();
        }

        function toggleInvert() {
            isInverted = !isInverted;
            adjustImage();
            const btn = document.getElementById('invertBtn');
            if (btn) {
                btn.classList.toggle('text-sky-400', isInverted);
            }
        }

        function showImage(el, url) {
            if (!mainImage) return;
            mainImage.src = url;
            document.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active-thumb'));
            el.classList.add('active-thumb');
            resetZoom();
            resetAdjustments();
            rotation = 0;
            mainImage.dataset.rotation = "0";
            applyTransform();
        }

        function adjustImage() {
            if (!mainImage) return;
            const b = brightnessSlider.value;
            const c = contrastSlider.value;
            brightnessValue.textContent = b;
            contrastValue.textContent = c;
            const inv = isInverted ? 'invert(100%)' : '';
            mainImage.style.filter = `${inv} brightness(${100 + parseInt(b)}%) contrast(${100 + parseInt(c)}%)`;
        }

        function resetAdjustments() {
            brightnessSlider.value = 0;
            contrastSlider.value = 0;
            brightnessValue.textContent = "0";
            contrastValue.textContent = "0";
            isInverted = false;
            adjustImage();
        }

        function resetAll() {
            resetZoom();
            resetAdjustments();
            rotation = 0;
            if (mainImage) mainImage.dataset.rotation = "0";
            applyTransform();
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Mouse Pan & Zoom Events
        if (mainImage) {
            mainImage.addEventListener('wheel', function(e) {
                e.preventDefault();
                let scale = parseFloat(mainImage.dataset.scale);
                scale += (e.deltaY < 0) ? 0.15 : -0.15;
                scale = Math.max(0.2, Math.min(6, scale));
                mainImage.dataset.scale = scale;
                applyTransform();
            }, { passive: false });

            mainImage.addEventListener('mousedown', function(e) {
                e.preventDefault();
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
            });

            window.addEventListener('mousemove', function(e) {
                if (!isDragging || !mainImage) return;
                let dx = e.clientX - startX;
                let dy = e.clientY - startY;
                startX = e.clientX;
                startY = e.clientY;
                mainImage.dataset.posX = parseFloat(mainImage.dataset.posX) + dx;
                mainImage.dataset.posY = parseFloat(mainImage.dataset.posY) + dy;
                applyTransform();
            });

            window.addEventListener('mouseup', () => { isDragging = false; });

            mainImage.addEventListener('dblclick', function(e) {
                e.preventDefault();
                resetZoom();
            });
        }
    </script>
</body>
</html>
