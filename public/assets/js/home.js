
    if (typeof confetti === 'undefined') {
      (function(){
        var C = document.createElement('canvas');
        C.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:999999';
        document.body.appendChild(C);
        var ctx = C.getContext('2d'), particles = [];
        function rnd(a,b){return Math.random()*(b-a)+a;}
        window.confetti = function(opts){
          opts = opts||{};
          var cnt = opts.particleCount||50;
          var ox = (opts.origin&&opts.origin.x!=null?opts.origin.x:0.5)*C.offsetWidth;
          var oy = (opts.origin&&opts.origin.y!=null?opts.origin.y:0.5)*C.offsetHeight;
          var colors = opts.colors||['#ffb703','#ffc929','#e5a503','#fff1c1','#ffffff'];
          var ang = (opts.angle||90)*Math.PI/180;
          var spd = opts.spread||90;
          for(var i=0;i<cnt;i++){
            var a=ang+rnd(-spd/2,spd/2)*Math.PI/180;
            particles.push({x:ox,y:oy,vx:Math.cos(a)*rnd(4,10),vy:-Math.sin(a)*rnd(4,10),
              r:rnd(4,8),c:colors[Math.floor(Math.random()*colors.length)],life:1,rot:rnd(0,360)});
          }
          if(!window._confLoop) loop();
        };
        function loop(){
          C.width=window.innerWidth; C.height=window.innerHeight;
          ctx.clearRect(0,0,C.width,C.height);
          particles = particles.filter(function(p){
            p.x+=p.vx; p.y+=p.vy; p.vy+=0.18; p.life-=0.013; p.rot+=4;
            ctx.save(); ctx.translate(p.x,p.y); ctx.rotate(p.rot*Math.PI/180);
            ctx.globalAlpha=Math.max(0,p.life);
            ctx.fillStyle=p.c; ctx.fillRect(-p.r/2,-p.r/2,p.r,p.r*1.6);
            ctx.restore();
            return p.life>0 && p.y<C.height+20;
          });
          window._confLoop = particles.length>0 ? requestAnimationFrame(loop) : null;
        }
      })();
    }

        const HERO_FALLBACK =
            "https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=2000&q=80";

        window.addEventListener("load", () => {
            const preloader = document.getElementById("preloader");
            setTimeout(() => {
                preloader.classList.add("loader-hidden");
                if (typeof AOS !== "undefined") {
                    AOS.init({
                        duration: 800,
                        easing: "ease-out-cubic",
                        once: true
                    });
                }
                initGSAPScroll();
            }, 1100);
        });

        document.addEventListener("DOMContentLoaded", () => {
            initTheme();
            bindThemeButtons();
            setupNavbarScroll();
            setupIngredientChips();
            setupCamera();
            fixHeroBg();

            bindGoldConfetti();
            autoGoldConfettiOnPanel5();
        });

                function fixHeroBg() {
            const img = document.getElementById("heroBg");
            if (!img) return;

            img.addEventListener("error", () => {
                img.src = HERO_FALLBACK;
            });
            img.addEventListener("load", () => {
                if (img.naturalWidth === 0) img.src = HERO_FALLBACK;
            });

            setTimeout(() => {
                if (!img.complete || img.naturalWidth === 0) img.src = HERO_FALLBACK;
            }, 500);
        }

                function applyTheme(theme) {
            document.documentElement.setAttribute("data-theme", theme);
            localStorage.setItem("theme", theme);
            syncThemeButton(document.getElementById("themeToggle"), theme);
            syncMobileThemeBtn(theme);
            syncNavbarState();
        }

        function syncThemeButton(btn, theme) {
            if (!btn) return;
            const label = btn.querySelector(".theme-pill-label");
            const icon = btn.querySelector(".theme-pill-icon i");
            const isDark = theme === "dark";
            if (label) label.textContent = isDark ? "DARK" : "LIGHT";
            if (icon) icon.className = isDark ? "fas fa-moon" : "fas fa-sun";
        }

        function initTheme() {
            const saved = localStorage.getItem("theme");
            if (saved === "light" || saved === "dark") {
                applyTheme(saved);
                return;
            }
            const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
            applyTheme(prefersDark ? "dark" : "light");
        }

        function bindThemeButtons() {

            const btn = document.getElementById("themeToggle");
            if (btn) {
                btn.addEventListener("click", () => {
                    const current = document.documentElement.getAttribute("data-theme") || "light";
                    applyTheme(current === "dark" ? "light" : "dark");
                });
            }

            const mobileBtn = document.getElementById("themeToggleMobile");
            if (mobileBtn) {
                mobileBtn.addEventListener("click", () => {
                    const current = document.documentElement.getAttribute("data-theme") || "light";
                    applyTheme(current === "dark" ? "light" : "dark");
                });
            }
        }

        function syncMobileThemeBtn(theme) {
            const btn = document.getElementById("themeToggleMobile");
            if (!btn) return;
            const icon  = btn.querySelector(".mobile-theme-icon i");
            const label = btn.querySelector(".mobile-theme-label");
            if (theme === "dark") {
                if (icon)  { icon.className = "fas fa-moon"; }
                if (label) label.textContent = "DARK MOD";
            } else {
                if (icon)  { icon.className = "fas fa-sun"; }
                if (label) label.textContent = "LIGHT MOD";
            }
        }

                function setupNavbarScroll() {
            syncNavbarState();
            window.addEventListener("scroll", syncNavbarState, {
                passive: true
            });
        }

        function syncNavbarState() {
            const navbar = document.querySelector(".chef-navbar");
            if (!navbar) return;
            const hasHero = !!document.querySelector(".hero");
            const scrolled = window.scrollY > 40;

            if (!hasHero) {
                navbar.classList.add("bg-solid");
                navbar.classList.remove("bg-transparent");
                return;
            }

            if (scrolled) {
                navbar.classList.add("bg-solid");
                navbar.classList.remove("bg-transparent");
            } else {
                navbar.classList.add("bg-transparent");
                navbar.classList.remove("bg-solid");
            }
        }

        window.sharedIngredients = [];

        function setupIngredientChips() {
            const input = document.getElementById("ingredient-input");
            const addButton = document.getElementById("add-ingredient");
            const heroContainer = document.getElementById("ingredient-chips");
            if (!input || !addButton || !heroContainer) return;

            function renderHeroChips() {
                heroContainer.innerHTML = '';
                window.sharedIngredients.forEach(name => {
                    const chip = document.createElement("div");
                    chip.className = "chip";
                    chip.style.animation = "chipPop 0.4s ease forwards";

                    const span = document.createElement("span");
                    span.textContent = name;

                    const btn = document.createElement("button");
                    btn.className = "chip-remove-btn";
                    btn.type = "button";
                    btn.innerHTML = "&times;";
                    btn.addEventListener("click", () => {
                        chip.style.animation = "chipOut 0.3s ease forwards";
                        setTimeout(() => {
                            window.sharedIngredients = window.sharedIngredients.filter(i => i !== name);
                            renderHeroChips();
                            if (typeof window.renderAIList === 'function') window.renderAIList();
                        }, 300);
                    });

                    chip.appendChild(span);
                    chip.appendChild(btn);
                    heroContainer.appendChild(chip);
                });
            }

            window.addChip = (text) => {
                const trimmed = String(text || "").trim().toLowerCase();
                if (!trimmed) return;
                if (window.sharedIngredients.includes(trimmed)) {
                    input.value = "";
                    // AI bölümündeki flash
                    if (typeof window.flashAIChip === 'function') window.flashAIChip(trimmed);
                    return;
                }
                window.sharedIngredients.push(trimmed);
                renderHeroChips();
                if (typeof window.renderAIList === 'function') window.renderAIList();
                input.value = "";
            };

            // Global addIngredientShared: AI bölümünden çağrılır
            window.addIngredientShared = (text) => {
                const trimmed = String(text || "").trim().toLowerCase();
                if (!trimmed) return;
                if (window.sharedIngredients.includes(trimmed)) {
                    if (typeof window.flashAIChip === 'function') window.flashAIChip(trimmed);
                    return;
                }
                window.sharedIngredients.push(trimmed);
                renderHeroChips();
                if (typeof window.renderAIList === 'function') window.renderAIList();
            };

            // Global removeIngredientShared
            window.removeIngredientShared = (name) => {
                window.sharedIngredients = window.sharedIngredients.filter(i => i !== name);
                renderHeroChips();
                if (typeof window.renderAIList === 'function') window.renderAIList();
            };

            addButton.addEventListener("click", () => {
                if (input.value.trim()) window.addChip(input.value);
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Enter" && input.value.trim()) {
                    e.preventDefault();
                    window.addChip(input.value);
                }
            });
        }

                document.addEventListener('DOMContentLoaded', function () {
            const suggestBtn = document.getElementById('suggest-btn');
            if (!suggestBtn) return;
            suggestBtn.addEventListener('click', async function () {
                const ingredients = (window.sharedIngredients || []).filter(Boolean);
                if (ingredients.length === 0) { alert('Lütfen önce malzeme ekleyin.'); return; }
                const origHTML = suggestBtn.innerHTML;
                suggestBtn.disabled = true;
                suggestBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...';
                let ok = 0, fail = 0;
                try {
                    for (const ing of ingredients) {
                        const fd = new FormData();
                        fd.append('name', ing);
                        fd.append('expiry_date', (function(){ var d=new Date(); d.setDate(d.getDate()+7); return d.toISOString().slice(0,10); })());
                        fd.append('shelf', 'shelf-1');
                        let j = {success:false};
                        try {
                            const r = await fetch('api/fridge', {method:'POST', body:fd});
                            const raw = await r.text();
                            try { j = JSON.parse(raw); } catch(e) { console.error('Parse:', raw); }
                        } catch(e) { console.error('Fetch:', e); }
                        if (j.success) ok++; else { fail++; console.warn('Eklenemedi:', ing, j); }
                    }
                } finally {
                    suggestBtn.innerHTML = origHTML;
                    suggestBtn.disabled = false;
                }
                if (ok > 0) {
                    alert(ok + ' malzeme buzdolabına eklendi!' + (fail > 0 ? ' (' + fail + ' eklenemedi)' : ''));
                    window.sharedIngredients = [];
                    var hc = document.getElementById('ingredient-chips');
                    if (hc) hc.innerHTML = '';
                    if (typeof window.renderAIList === 'function') window.renderAIList();
                } else { alert('Eklenemedi. Giriş yaptığınızdan emin olun.'); }
            });
        });

                function initGSAPScroll() {
            if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;

            gsap.registerPlugin(ScrollTrigger);

            const sections = gsap.utils.toArray(".panel");
            const container = document.querySelector(".horizontal-wrapper");
            const triggerEl = document.querySelector(".gsap-section");
            if (!container || sections.length === 0 || !triggerEl) return;

            const totalScroll = () => container.scrollWidth - window.innerWidth;

            const tween = gsap.to(sections, {
                x: () => -totalScroll(),
                ease: "none",
                scrollTrigger: {
                    trigger: triggerEl,
                    pin: true,
                    scrub: 1,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                    end: () => "+=" + container.scrollWidth
                }
            });

            sections.forEach((panel) => {
                const img = panel.querySelector("img");
                if (!img) return;

                gsap.to(img, {
                    scale: 1.08,
                    ease: "none",
                    scrollTrigger: {
                        trigger: panel,
                        containerAnimation: tween,
                        start: "left center",
                        end: "right center",
                        scrub: true
                    }
                });
            });
        }

                function goldConfettiBurst(durationMs = 1800) {
            if (typeof confetti === "undefined") return;

            const end = Date.now() + durationMs;
            const colors = ["#ffb703", "#ffc929", "#e5a503", "#fff1c1", "#ffffff"];

            (function frame() {
                confetti({
                    particleCount: 10,
                    angle: 60,
                    spread: 90,
                    origin: {
                        x: 0,
                        y: 0.65
                    },
                    colors
                });
                confetti({
                    particleCount: 10,
                    angle: 120,
                    spread: 90,
                    origin: {
                        x: 1,
                        y: 0.65
                    },
                    colors
                });
                confetti({
                    particleCount: 8,
                    angle: 90,
                    spread: 120,
                    origin: {
                        x: 0.5,
                        y: 0.15
                    },
                    colors
                });

                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        }

        function bindGoldConfetti() {
            const btn = document.getElementById("startConfettiBtn");
            if (!btn) return;

            btn.addEventListener("click", (e) => {

                goldConfettiBurst(1700);
            });
        }

        function autoGoldConfettiOnPanel5() {
            const target = document.querySelector(".cta-section-ultimate");
            if (!target) return;

            let fired = false;
            function checkScroll() {
                if (fired) return;
                const rect = target.getBoundingClientRect();
                const inView = rect.top < window.innerHeight * 0.85 && rect.bottom > 0;
                if (inView) {
                    fired = true;
                    window.removeEventListener('scroll', checkScroll);
                    goldConfettiBurst(2500);
                }
            }
            window.addEventListener('scroll', checkScroll, { passive: true });
            checkScroll();
        }

                document.addEventListener('DOMContentLoaded', function() {
            (function() {
                'use strict';

                let lastPredicted = null;
                let cameraStream = null;
                let activeMode = 'file';
                let capturedBlob = null;

                const imageInput = document.getElementById('aiImageInput');
                const uploadLabel = document.getElementById('aiUploadLabel');
                const uploadText = document.getElementById('aiUploadText');
                const previewWrap = document.getElementById('aiPreviewWrap');
                const previewImg = document.getElementById('aiPreviewImg');
                const clearImgBtn = document.getElementById('aiClearImg');
                const scanBtn = document.getElementById('aiScanBtn');
                const statusBox = document.getElementById('aiStatusBox');
                const resultBox = document.getElementById('aiResultBox');
                const resultValue = document.getElementById('aiResultValue');
                const resultConf = document.getElementById('aiResultConf');
                const addToListBtn = document.getElementById('aiAddToListBtn');
                const chipContainer = document.getElementById('aiIngredientList');
                const emptyMsg = document.getElementById('aiEmptyMsg');
                const clearListBtn = document.getElementById('aiClearListBtn');
                const findRecipesBtn = document.getElementById('aiFindRecipesBtn');
                const recipeStatus = document.getElementById('aiRecipeStatus');
                const recipeResults = document.getElementById('aiRecipeResults');
                const recipeGrid = document.getElementById('aiRecipeGrid');

                const tabFile = document.getElementById('aiTabFile');
                const tabCamera = document.getElementById('aiTabCamera');
                const fileArea = document.getElementById('aiFileArea');
                const cameraArea = document.getElementById('aiCameraArea');
                const cameraVideo = document.getElementById('aiCameraStream');
                const cameraCanvas = document.getElementById('aiCameraCanvas');
                const cameraStatusEl = document.getElementById('aiCameraStatus');
                const stopCameraBtn = document.getElementById('aiStopCameraBtn');

                if (!imageInput) {
                    console.error('[ChefMate AI] #aiImageInput elementi bulunamadı. HTML yüklendi mi?');
                    return;
                }

                const criticalEls = {
                    scanBtn,
                    chipContainer,
                    findRecipesBtn,
                    recipeGrid
                };
                for (const [name, el] of Object.entries(criticalEls)) {
                    if (!el) console.warn('[ChefMate AI] Element bulunamadı:', name);
                }

                tabFile.addEventListener('click', () => switchMode('file'));
                tabCamera.addEventListener('click', () => switchMode('camera'));

                function switchMode(mode) {
                    activeMode = mode;
                    resetSharedState();

                    if (mode === 'file') {
                        tabFile.classList.add('active');
                        tabCamera.classList.remove('active');
                        fileArea.style.display = '';
                        cameraArea.style.display = 'none';
                        scanBtn.classList.remove('camera-mode');
                        scanBtn.innerHTML = '<i class="fas fa-search me-2"></i>Malzemeyi Tara';
                        stopActiveCamera();
                    } else {
                        tabCamera.classList.add('active');
                        tabFile.classList.remove('active');
                        fileArea.style.display = 'none';
                        cameraArea.style.display = '';
                        scanBtn.classList.add('camera-mode');
                        scanBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Fotoğrafı Çek & Tara';
                        scanBtn.disabled = true;
                        startCamera();
                    }
                }

                async function startCamera() {
                    cameraStatusEl.classList.remove('hidden');
                    cameraStatusEl.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Kamera başlatılıyor...';
                    stopCameraBtn.style.display = 'none';
                    stopActiveCamera();

                    try {
                        cameraStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                },
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            },
                            audio: false
                        });
                        cameraVideo.srcObject = cameraStream;
                        await cameraVideo.play();

                        cameraStatusEl.classList.add('hidden');
                        scanBtn.disabled = false;
                        stopCameraBtn.style.display = '';
                    } catch (err) {
                        let msg = 'Kamera açılamadı.';
                        if (err.name === 'NotAllowedError') msg = 'Kamera izni reddedildi. Tarayıcı ayarlarından izin verin.';
                        else if (err.name === 'NotFoundError') msg = 'Kamera bulunamadı.';
                        else if (err.name === 'NotReadableError') msg = 'Kamera başka bir uygulama tarafından kullanılıyor.';
                        cameraStatusEl.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + msg;
                        scanBtn.disabled = true;
                    }
                }

                function stopActiveCamera() {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(t => t.stop());
                        cameraStream = null;
                    }
                    if (cameraVideo) {
                        cameraVideo.pause();
                        cameraVideo.srcObject = null;
                    }
                    if (stopCameraBtn) stopCameraBtn.style.display = 'none';
                }

                stopCameraBtn.addEventListener('click', () => {
                    stopActiveCamera();
                    switchMode('file');
                });

                window.addEventListener('beforeunload', stopActiveCamera);

                imageInput.addEventListener('change', () => {
                    const file = imageInput.files[0];
                    if (!file) return;
                    capturedBlob = null;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        previewWrap.classList.remove('d-none');
                        uploadLabel.style.display = 'none';
                        scanBtn.disabled = false;
                        hideElement(statusBox);
                        hideElement(resultBox);
                        lastPredicted = null;
                    };
                    reader.readAsDataURL(file);
                    uploadText.textContent = file.name;
                });

                clearImgBtn.addEventListener('click', resetImageState);

                function resetImageState() {
                    imageInput.value = '';
                    capturedBlob = null;
                    previewImg.src = '';
                    previewWrap.classList.add('d-none');
                    uploadLabel.style.display = '';
                    uploadText.textContent = 'Resim seçmek için tıkla veya sürükle';
                    if (activeMode === 'file') scanBtn.disabled = true;
                    hideElement(statusBox);
                    hideElement(resultBox);
                    lastPredicted = null;
                }

                function resetSharedState() {
                    capturedBlob = null;
                    lastPredicted = null;
                    hideElement(statusBox);
                    hideElement(resultBox);
                    previewWrap.classList.add('d-none');
                    previewImg.src = '';
                    if (activeMode === 'file') {
                        imageInput.value = '';
                        uploadLabel.style.display = '';
                        uploadText.textContent = 'Resim seçmek için tıkla veya sürükle';
                        scanBtn.disabled = true;
                    }
                }

                scanBtn.addEventListener('click', async () => {
                    let fileToSend = null;

                    if (activeMode === 'camera') {

                        if (!cameraStream || !cameraVideo.videoWidth) {
                            showStatus(statusBox, 'error', '<i class="fas fa-exclamation-circle me-2"></i>Kamera henüz hazır değil.');
                            return;
                        }
                        cameraCanvas.width = cameraVideo.videoWidth;
                        cameraCanvas.height = cameraVideo.videoHeight;
                        cameraCanvas.getContext('2d').drawImage(cameraVideo, 0, 0);

                        capturedBlob = await new Promise(res => cameraCanvas.toBlob(res, 'image/jpeg', 0.92));
                        const dataUrl = URL.createObjectURL(capturedBlob);
                        previewImg.src = dataUrl;
                        previewWrap.classList.remove('d-none');

                        stopActiveCamera();
                        cameraArea.style.display = 'none';
                        stopCameraBtn.style.display = 'none';

                        fileToSend = new File([capturedBlob], 'capture.jpg', {
                            type: 'image/jpeg'
                        });
                    } else {
                        fileToSend = imageInput.files[0];
                        if (!fileToSend) return;
                    }

                    showStatus(statusBox, 'loading', '<i class="fas fa-spinner fa-spin me-2"></i>Malzeme analiz ediliyor...');
                    hideElement(resultBox);
                    scanBtn.disabled = true;

                    try {
                        const formData = new FormData();
                        formData.append('image', fileToSend);

                        const res = await fetch('api/predict', {
                            method: 'POST',
                            body: formData
                        });
                        const json = await res.json();
                        hideElement(statusBox);

                        if (json.success && json.food) {
                            lastPredicted = json.food.trim().toLowerCase();
                            resultValue.textContent = lastPredicted;
                            resultConf.textContent = json.confidence ?
                                'Güven: %' + Math.round(json.confidence * 100) :
                                '';
                            showElement(resultBox);

                            // ── OTOMATİK LİSTEYE EKLE ─────────────────────
                            // Tahmin başarılıysa malzeme hemen listeye eklenir.
                            addIngredient(lastPredicted);
                            lastPredicted = null;
                            // Kamera modundaysa kısa gecikme sonrası kamerayı yeniden aç
                            if (activeMode === 'camera') {
                                setTimeout(() => {
                                    hideElement(resultBox);
                                    previewWrap.classList.add('d-none');
                                    cameraArea.style.display = '';
                                    startCamera();
                                }, 1200);
                            } else {
                                // Dosya modunda önizleme göründükten sonra sıfırla
                                setTimeout(() => {
                                    hideElement(resultBox);
                                    resetImageState();
                                }, 1200);
                            }
                        } else {
                            const msg = json.error || json.message || 'Malzeme tanınamadı.';
                            showStatus(statusBox, 'error', '<i class="fas fa-exclamation-circle me-2"></i>' + escHtmlAI(msg));
                            lastPredicted = null;

                            if (activeMode === 'camera') {
                                cameraArea.style.display = '';
                                startCamera();
                            }
                        }
                    } catch (err) {
                        hideElement(statusBox);
                        showStatus(statusBox, 'error', '<i class="fas fa-exclamation-circle me-2"></i>Bağlantı hatası. Lütfen tekrar deneyin.');
                        lastPredicted = null;
                        if (activeMode === 'camera') {
                            cameraArea.style.display = '';
                            startCamera();
                        }
                    } finally {
                        scanBtn.disabled = false;
                    }
                });

                // ── Malzemeyi Listeye Ekle ─────────────────────────────
                addToListBtn.addEventListener('click', () => {
                    if (!lastPredicted) return;
                    addIngredient(lastPredicted);
                    lastPredicted = null;
                    hideElement(resultBox);

                    if (activeMode === 'camera') {
                        previewWrap.classList.add('d-none');
                        cameraArea.style.display = '';
                        startCamera();
                    } else {
                        resetImageState();
                    }
                });

                function addIngredient(name) {
                    // Paylaşımlı global state'e ekle (hero chip'leri de günceller)
                    if (typeof window.addIngredientShared === 'function') {
                        window.addIngredientShared(name);
                    }
                }

                function removeIngredient(name) {
                    if (typeof window.removeIngredientShared === 'function') {
                        window.removeIngredientShared(name);
                    }
                }

                window.renderAIList = function renderIngredientList() {
                    const list = window.sharedIngredients || [];
                    if (list.length === 0) {
                        chipContainer.innerHTML = '';
                        chipContainer.appendChild(emptyMsg);
                        emptyMsg.style.display = '';
                        showElement(emptyMsg);
                        clearListBtn.classList.add('d-none');
                        findRecipesBtn.disabled = true;
                        hideElement(recipeResults);
                        return;
                    }

                    emptyMsg.style.display = 'none';
                    clearListBtn.classList.remove('d-none');
                    findRecipesBtn.disabled = false;

                    chipContainer.innerHTML = list.map(name => `
                    <span class="ai-chip" data-ingredient="${escHtmlAI(name)}">
                        <i class="fas fa-tag" style="font-size:0.7rem;opacity:0.6;"></i>
                        ${escHtmlAI(name)}
                        <button class="ai-chip-remove" data-remove="${escHtmlAI(name)}" title="Kaldır">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `).join('');

                    chipContainer.querySelectorAll('.ai-chip-remove').forEach(btn => {
                        btn.addEventListener('click', () => {
                            removeIngredient(btn.dataset.remove);
                        });
                    });
                };

                window.flashAIChip = function flashChip(name) {
                    const chip = chipContainer.querySelector(`[data-ingredient="${name}"]`);
                    if (!chip) return;
                    chip.style.outline = '2px solid #cfae55';
                    chip.style.transition = 'outline 0.3s';
                    setTimeout(() => { chip.style.outline = ''; }, 700);
                };

                // ── Listeyi Temizle ────────────────────────────────────
                clearListBtn.addEventListener('click', () => {
                    window.sharedIngredients = [];

                    const heroChips = document.getElementById('ingredient-chips');
                    if (heroChips) heroChips.innerHTML = '';
                    window.renderAIList();
                    hideElement(recipeResults);
                    hideElement(recipeStatus);
                });

                // ── Buzdolabına Ekle ──────────────────────────────────
                findRecipesBtn.addEventListener('click', async () => {
                    const list = window.sharedIngredients || [];
                    if (list.length === 0) return;
                    findRecipesBtn.disabled = true;
                    findRecipesBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...';
                    let ok = 0, fail = 0;
                    try {
                        for (const ing of list) {
                            const fd = new FormData();
                            fd.append('name', ing);
                            fd.append('expiry_date', (function(){ var d=new Date(); d.setDate(d.getDate()+7); return d.toISOString().slice(0,10); })());
                            fd.append('shelf', 'shelf-1');
                            let j = {success:false};
                            try {
                                const r = await fetch('api/fridge', {method:'POST', body:fd});
                                const raw = await r.text();
                                try { j = JSON.parse(raw); } catch(e) { console.error('Parse:', raw); }
                            } catch(e) { console.error('Fetch:', e); }
                            if (j.success) ok++; else { fail++; console.warn('Eklenemedi:', ing, j); }
                        }
                    } finally {
                        findRecipesBtn.innerHTML = '<i class="fas fa-snowflake me-2"></i>Buzdolabına Ekle';
                        findRecipesBtn.disabled = false;
                    }
                    if (ok > 0) {
                        window.sharedIngredients = [];
                        var hc = document.getElementById('ingredient-chips');
                        if (hc) hc.innerHTML = '';
                        window.renderAIList();
                        recipeStatus.className = 'ai-status-box loading';
                        recipeStatus.innerHTML =
                            '<i class="fas fa-check-circle me-2" style="color:#22a55a;"></i>' +
                            ok + ' malzeme buzdolabına eklendi! ' +
                            '<a href="dashboard" style="color:#cfae55;font-weight:700;margin-left:6px;">Buzdolabını Görüntüle &rarr;</a>';
                        recipeStatus.classList.remove('d-none');
                        findRecipesBtn.disabled = false;
                    } else if (fail > 0) {
                        recipeStatus.className = 'ai-status-box error';
                        recipeStatus.innerHTML =
                            '<i class="fas fa-exclamation-circle me-2"></i>' +
                            'Eklenemedi. <a href="login" style="color:#cfae55;font-weight:700;">Giriş Yap &rarr;</a>';
                        recipeStatus.classList.remove('d-none');
                    }
                });

                function showElement(el) {
                    el && el.classList.remove('d-none');
                }

                function hideElement(el) {
                    el && el.classList.add('d-none');
                }

                function showStatus(el, type, html) {
                    if (!el) return;
                    el.className = 'ai-status-box ' + type;
                    el.innerHTML = html;
                    el.classList.remove('d-none');
                }

                function escHtmlAI(str) {
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                }

                // İlk render — boş liste
                window.renderAIList();

                // ── HERO DOSYA BUTONU (fotoğraf ikonuna tıklanınca) ──────────
                // Kullanıcı hero'daki fotoğraf ikonundan dosya seçerse:

                const heroFileInput = document.getElementById('heroFileInput');
                if (heroFileInput) {
                    heroFileInput.addEventListener('change', function() {
                        const file = this.files[0];
                        if (!file) return;

                        const aiSection = document.getElementById('ai-ingredient-detection');
                        const aiDivider = document.getElementById('aiSectionDivider');
                        if (aiSection) aiSection.style.display = '';
                        if (aiDivider) aiDivider.style.display = '';

                        // Galeri sekmesine geç (file modu)
                        const tabFile = document.getElementById('aiTabFile');
                        if (tabFile) tabFile.click();

                        const dt = new DataTransfer();
                        dt.items.add(file);
                        const aiInput = document.getElementById('aiImageInput');
                        if (aiInput) {
                            aiInput.files = dt.files;
                            aiInput.dispatchEvent(new Event('change'));
                        }

                        setTimeout(() => {
                            aiSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            setTimeout(() => {
                                const scanBtn = document.getElementById('aiScanBtn');
                                if (scanBtn && !scanBtn.disabled) scanBtn.click();
                            }, 700);
                        }, 300);

                        this.value = '';
                    });
                }

            })();
        });
