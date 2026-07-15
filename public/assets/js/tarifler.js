            const bar = document.getElementById('katBar');
            const btnL = document.getElementById('katScrollLeft');
            const btnR = document.getElementById('katScrollRight');
            function update() {
                btnL.style.display = bar.scrollLeft > 4 ? 'flex' : 'none';
                btnR.style.display = bar.scrollLeft + bar.clientWidth < bar.scrollWidth - 4 ? 'flex' : 'none';
            }
            btnL.addEventListener('click', () => { bar.scrollBy({ left: -220, behavior: 'smooth' }); });
            btnR.addEventListener('click', () => { bar.scrollBy({ left: 220, behavior: 'smooth' }); });
            bar.addEventListener('scroll', update);
            window.addEventListener('resize', update);
            update();
        })();

        async function tarifApi(action, body) {
            const map = {
                'recipe_like_public': 'api/recipes/like',
                'recipe_save_public': 'api/recipes/save',
                'recipe_comment_public': 'api/recipes/comment'
            };
            const url = map[action] || ('api/' + action);
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            });
            return res.json();
        }

        function tarifToast(msg, type = 's') {
            const el = document.createElement('div');
            el.className = 'tarif-toast ' + type;
            el.textContent = msg;
            document.getElementById('tarifToast').appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }

        async function toggleLike(btn) {
            const r = await tarifApi('recipe_like_public', {
                recipe_key: btn.dataset.key,
                recipe_title: btn.dataset.title,
                recipe_image: btn.dataset.img || '',
                recipe_url: btn.dataset.url || ''
            });
            if (r.success) {
                btn.classList.toggle('active', r.data.liked);
                btn.textContent = r.data.liked ? '❤️ Beğenildi' : '🤍 Beğen';
                tarifToast(r.data.liked ? '❤️ Tarif beğenildi!' : '💔 Beğeni kaldırıldı');
            } else {
                tarifToast(r.message || 'Hata oluştu', 'e');
            }
        }

        async function toggleSave(btn) {
            const r = await tarifApi('recipe_save_public', {
                recipe_key: btn.dataset.key,
                recipe_title: btn.dataset.title,
                recipe_image: btn.dataset.img || '',
                recipe_url: btn.dataset.url || ''
            });
            if (r.success) {
                btn.classList.toggle('active', r.data.saved);
                btn.textContent = r.data.saved ? '📖 Kaydedildi' : '🔖 Kaydet';
                tarifToast(r.data.saved ? '📖 Tarif defterine kaydedildi!' : '🗑️ Tarif kaldırıldı');
            } else {
                tarifToast(r.message || 'Hata oluştu', 'e');
            }
        }

        let cmCurrent = null;
        async function openComment(btn) {
            cmCurrent = {
                key: btn.dataset.key,
                title: btn.dataset.title,
                img: btn.dataset.img || '',
                url: btn.dataset.url || ''
            };
            document.getElementById('cmTitle').textContent = '💬 ' + cmCurrent.title;
            document.getElementById('cmInput').value = '';
            document.getElementById('cmOverlay').classList.add('open');
            await loadComments();
        }

        async function loadComments() {
            if (!cmCurrent) return;
            const res = await fetch('api/recipes/public?recipe_key=' + encodeURIComponent(cmCurrent.key));
            const r = await res.json();
            if (r.success) {
                const comments = r.data.comments || [];
                const userId = <?= $currentUserId ?>;
                document.getElementById('cmList').innerHTML = comments.length ?
                    comments.map(c => `<div class="cm-card" id="cm-card-${c.id||'x'}">
                        <div class="cu">👤 ${c.user_name||'Kullanıcı'}</div>
                        <div class="ct" id="cm-text-${c.id||'x'}">${c.comment_text}</div>
                        <div class="cd">${c.created_at}</div>
                        ${c.is_mine ? `
                          <div id="cm-edit-${c.id}" style="display:none;margin-top:6px;">
                            <textarea style="width:100%;padding:6px;border-radius:6px;border:1px solid #333;background:#222;color:#eee;font-size:.82rem;resize:vertical;" id="cm-edit-input-${c.id}">${c.comment_text}</textarea>
                            <div style="display:flex;gap:6px;margin-top:4px;">
                              <button onclick="saveTarifComment(${c.id})" style="padding:4px 12px;background:#cfae55;color:#000;border:none;border-radius:6px;cursor:pointer;font-size:.78rem;font-weight:700;">Kaydet</button>
                              <button onclick="cancelTarifEdit(${c.id})" style="padding:4px 12px;background:#444;color:#eee;border:none;border-radius:6px;cursor:pointer;font-size:.78rem;">İptal</button>
                            </div>
                          </div>
                          <div style="display:flex;gap:6px;margin-top:4px;">
                            <button onclick="startTarifEdit(${c.id})" style="padding:3px 10px;background:none;border:1px solid #3b82f6;color:#3b82f6;border-radius:6px;cursor:pointer;font-size:.75rem;">✏️ Düzenle</button>
                            <button onclick="deleteTarifComment(${c.id})" style="padding:3px 10px;background:none;border:1px solid #ef4444;color:#ef4444;border-radius:6px;cursor:pointer;font-size:.75rem;">🗑️ Sil</button>
                          </div>` : ''}
                      </div>`).join('') :
                    '<p style="color:#666;text-align:center;font-size:.85rem;">Henüz yorum yok.</p>';
            }
        }

        document.getElementById('cmSend').addEventListener('click', async () => {
            if (!cmCurrent) return;
            const text = document.getElementById('cmInput').value.trim();
            if (!text) {
                tarifToast('Yorum boş olamaz!', 'e');
                return;
            }
            const r = await tarifApi('recipe_comment_public', {
                recipe_key: cmCurrent.key,
                recipe_title: cmCurrent.title,
                recipe_image: cmCurrent.img,
                recipe_url: cmCurrent.url,
                comment_text: text
            });
            if (r.success) {
                document.getElementById('cmInput').value = '';
                tarifToast('💬 Yorum gönderildi!');
                await loadComments();
            } else {
                tarifToast(r.message || 'Hata', 'e');
            }
        });

        document.getElementById('cmOverlay').addEventListener('click', e => {
            if (e.target === document.getElementById('cmOverlay'))
                document.getElementById('cmOverlay').classList.remove('open');
        });

        function startTarifEdit(id) {
            document.getElementById('cm-text-' + id).style.display = 'none';
            document.getElementById('cm-edit-' + id).style.display = 'block';
        }

        function cancelTarifEdit(id) {
            document.getElementById('cm-text-' + id).style.display = 'block';
            document.getElementById('cm-edit-' + id).style.display = 'none';
        }
        async function saveTarifComment(id) {
            const text = document.getElementById('cm-edit-input-' + id).value.trim();
            if (!text) {
                tarifToast('Yorum boş olamaz!', 'e');
                return;
            }
            const res = await fetch('api/comments/' + id, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comment_text: text })
            });
            const r = await res.json();
            if (r.success) {
                document.getElementById('cm-text-' + id).textContent = text;
                cancelTarifEdit(id);
                tarifToast('✅ Yorum güncellendi!');
            } else tarifToast(r.message || 'Hata', 'e');
        }
        async function deleteTarifComment(id) {
            if (!confirm('Bu yorumu silmek istediğinize emin misiniz?')) return;
            const res = await fetch('api/comments/' + id, { method: 'DELETE' });
            const r = await res.json();
            if (r.success) {
                document.getElementById('cm-card-' + id)?.remove();
                tarifToast('Yorum silindi.');
            } else tarifToast(r.message || 'Hata', 'e');
        }

        function favToggle(btn) {
            const url = btn.dataset.url;
            const favs = JSON.parse(localStorage.getItem('cm_favs') || '[]');
            const idx = favs.indexOf(url);
            if (idx === -1) {
                favs.push(url);
                btn.textContent = '♥';
                btn.classList.add('ak');
            } else {
                favs.splice(idx, 1);
                btn.textContent = '♡';
                btn.classList.remove('ak');
            }
            localStorage.setItem('cm_favs', JSON.stringify(favs));
        }
        document.addEventListener('DOMContentLoaded', () => {
            const favs = JSON.parse(localStorage.getItem('cm_favs') || '[]');
            document.querySelectorAll('.fav-btn').forEach(btn => {
                if (favs.includes(btn.dataset.url)) {
                    btn.textContent = '♥';
                    btn.classList.add('ak');
                }
            });
        });

        let currentImgUrl = '';

        function openLightbox(btn) {
            currentImgUrl = btn.dataset.img;
            document.getElementById('lb-img').src = currentImgUrl;
            document.getElementById('lb-title').textContent = btn.dataset.title;
            document.getElementById('lb-open').href = currentImgUrl;
            document.getElementById('lightbox').classList.add('open');
            btn.classList.add('captured');
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                btn.classList.remove('captured');
                btn.innerHTML = '<i class="fas fa-camera"></i>';
            }, 1800);
        }

        function closeLightbox(e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('lightbox').classList.remove('open');
        }

        function downloadImg() {
            if (!currentImgUrl) return;
            const a = document.createElement('a');
            a.href = currentImgUrl;
            a.download = 'chefmate-tarif.jpg';
            a.target = '_blank';
            a.rel = 'noopener';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLightbox();
        });

        let st;
        document.querySelector('.nav-search input')?.addEventListener('input', function() {
            clearTimeout(st);
            if (this.value.length === 0 || this.value.length > 2)
                st = setTimeout(() => this.closest('form').submit(), 650);
        });

        async function openRecipeDetail(data) {
            const overlay = document.getElementById('recipeDetailOverlay');
            const imgWrap = document.getElementById('rdImgWrap');
            const titleEl = document.getElementById('rdTitle');
            const meta = document.getElementById('rdMeta');
            const ingSection = document.getElementById('rdIngSection');
            const ingList = document.getElementById('rdIngList');
            const stepSection = document.getElementById('rdStepSection');
            const stepsList = document.getElementById('rdStepsList');
            const descSection = document.getElementById('rdDescSection');
            const descEl = document.getElementById('rdDesc');
            const sourceLink = document.getElementById('rdSourceLink');

            titleEl.textContent = data.title || 'Tarif Detayı';
            imgWrap.innerHTML = data.resim ?
                `<img src="${data.resim}" class="rd-img" alt="${data.title}" onerror="this.parentElement.innerHTML='<div class=rd-img-placeholder>🍽️</div>'">` :
                '<div class="rd-img-placeholder">🍽️</div>';
            meta.innerHTML = (data.tarih ? `<span>📅 ${data.tarih}</span>` : '') + (data.kat ? `<span>🏷️ ${data.kat}</span>` : '');
            sourceLink.href = data.url || '#';
            ingSection.style.display = '';
            ingList.innerHTML = '<div style="color:var(--muted);padding:10px 0;font-size:.85rem;">⏳ Tarif detayları yükleniyor...</div>';
            stepSection.style.display = 'none';
            descSection.style.display = data.ozet ? '' : 'none';
            if (data.ozet) descEl.textContent = data.ozet;
            overlay.classList.add('open');

            try {
                const res = await fetch('api/recipes/detail&url=' + encodeURIComponent(data.url));
                const r = await res.json();
                if (r.success && r.data) {
                    const d = r.data;
                    if (d.img) {
                        imgWrap.innerHTML = `<img src="${d.img}" class="rd-img" alt="${titleEl.textContent}" onerror="this.parentElement.innerHTML='<div class=rd-img-placeholder>🍽️</div>'">`;
                    }
                    if (d.title) titleEl.textContent = d.title;
                    let mhtml = `<span>🏷️ ${data.kat||'Tarif'}</span>`;
                    if (d.time) mhtml += `<span>⏱️ ${d.time}</span>`;
                    if (d.serving) mhtml += `<span>👥 ${d.serving}</span>`;
                    if (data.tarih) mhtml += `<span>📅 ${data.tarih}</span>`;
                    meta.innerHTML = mhtml;
                    if (d.ingredients && d.ingredients.length > 0) {
                        ingList.innerHTML = d.ingredients.map(i => `<div>• ${i}</div>`).join('');
                    } else {
                        ingList.innerHTML = '<div style="color:var(--muted);font-size:.85rem;">Malzeme listesi bulunamadı. <a href="' + data.url + '" target="_blank" style="color:var(--gold);">Kaynağa git →</a></div>';
                    }
                    if (d.steps && d.steps.length > 0) {
                        stepsList.innerHTML = d.steps.map((s, i) => `<div class="rd-step"><div class="rd-step-num">${i+1}</div><div>${s}</div></div>`).join('');
                        stepSection.style.display = '';
                    }
                    if (d.desc) {
                        descEl.textContent = d.desc;
                        descSection.style.display = '';
                    }
                } else {
                    ingList.innerHTML = '<div style="color:var(--muted);font-size:.85rem;">Detay yüklenemedi. <a href="' + data.url + '" target="_blank" style="color:var(--gold);">Kaynağa git →</a></div>';
                }
            } catch (e) {
                ingList.innerHTML = '<div style="color:var(--muted);font-size:.85rem;">Hata oluştu. <a href="' + data.url + '" target="_blank" style="color:var(--gold);">Kaynağa git →</a></div>';
            }
        }

        // closeRecipeDetail(): Modalı kapatır (.open class'ını kaldırır).

        function closeRecipeDetail() {
            document.getElementById('recipeDetailOverlay').classList.remove('open');
        }
        document.getElementById('recipeDetailOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeRecipeDetail();
        });

        async function fixCardImageFromDetail(imgEl) {
            try {
                const pageUrl = imgEl.getAttribute('data-page-url');
                if (!pageUrl) return;

                const res = await fetch('api/recipes/detail&url=' + encodeURIComponent(pageUrl));
                const r = await res.json();
                if (r.success && r.data && r.data.img) {
                    imgEl.src = r.data.img;

                    const ph = imgEl.parentElement.querySelector('.img-ph');
                    if (ph) ph.style.display = 'none';
                }
            } catch (e) {

            }
        }

        (function() {
            let offset = <?= count($initialCards) ?>;
            const total = <?= $totalCards ?>;
            let loading = false;
            let allLoaded = (total > 0 && offset >= total);
            const grid = document.getElementById('recipesGrid');
            const loader = document.getElementById('scrollLoader');
            const endMsg = document.getElementById('scrollEnd');
            const sentinel = document.getElementById('scrollSentinel');
            const katIkon = <?= json_encode($aktifKat === 'kullanici-tarifleri' ? '👨‍🍳' : ($kategoriler[$aktifKat]['ikon'] ?? '🍽️')) ?>;
            const katIsim = <?= json_encode($aktifKat === 'kullanici-tarifleri' ? 'Kullanıcı Tarifi' : ($kategoriler[$aktifKat]['isim'] ?? 'Tarif')) ?>;
            const isUserRecipesMode = <?= json_encode($aktifKat === 'kullanici-tarifleri') ?>;
            const myLikes = <?= json_encode(array_keys($myLikes)) ?>;
            const mySaves = <?= json_encode(array_keys($mySaves)) ?>;
            const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
            const currentUrl = window.location.href.split('?')[0];
            const currentParams = new URLSearchParams(window.location.search);

            if (allLoaded && offset > 0) {
                endMsg.style.display = 'block';
            }

            function makeCardHTML(t) {
                const rKey = t.rkey || '';
                const isLiked = myLikes.includes(rKey);
                const isSaved = mySaves.includes(rKey);
                const isUserRecipe = t.is_user_recipe || isUserRecipesMode;
                const imgHtml = t.resim
                    ? `<img src="${t.resim}" alt="${esc(t.baslik)}" loading="lazy" data-page-url="${esc(t.url)}" onerror="fixCardImageFromDetail(this)">`
                    : '';
                const interactHtml = isLoggedIn
                    ? `<div class="card-interact" onclick="event.stopPropagation()">
                        <button class="int-btn int-like ${isLiked ? 'active' : ''}" data-key="${rKey}" data-title="${esc(t.baslik)}" data-img="${esc(t.resim||'')}" data-url="${esc(t.url)}" onclick="toggleLike(this)">${isLiked ? '❤️' : '🤍'} Beğen</button>
                        <button class="int-btn int-save ${isSaved ? 'active' : ''}" data-key="${rKey}" data-title="${esc(t.baslik)}" data-img="${esc(t.resim||'')}" data-url="${esc(t.url)}" onclick="toggleSave(this)">${isSaved ? '📖 Kaydedildi' : '🔖 Kaydet'}</button>
                        <button class="int-btn int-comment" data-key="${rKey}" data-title="${esc(t.baslik)}" data-img="${esc(t.resim||'')}" data-url="${esc(t.url)}" onclick="openComment(this)">💬 Yorum</button>
                        ${!isUserRecipe ? `<button class="int-btn int-menu-add" data-key="${rKey}" data-title="${esc(t.baslik)}" data-img="${esc(t.resim||'')}" data-url="${esc(t.url)}" onclick="openMenuAdd(this)">🍽️ Menüye Ekle</button>` : ''}
                      </div>`
                    : `<div class="card-interact" style="text-align:center;padding:8px 12px;"><a href="public/login" style="color:#cfae55;font-size:.8rem;text-decoration:none;">🔒 Giriş yaparak beğen & kaydet</a></div>`;

                const _urDetail = JSON.stringify({id:t.recipe_id||0,title:t.baslik,resim:t.resim||'',ozet:t.ozet||'',tarih:t.tarih||'',user_name:t.user_name||'',ingredients:t.ingredients||'',instructions:t.instructions||'',is_mine:isLoggedIn&&!!(t.recipe_id)});
                const cardOnClick = isUserRecipe
                    ? `openUserRecipeDetail(${_urDetail.replace(/"/g,'&quot;')})`
                    : `window.open('${esc(t.url)}','_blank')`;

                return `<article class="recipe-card" onclick="${isUserRecipe ? `openUserRecipeDetail(${esc(_urDetail)})` : `window.open('${esc(t.url)}','_blank')`}">
                    <div class="card-img-wrap">
                        ${imgHtml}
                        <div class="img-ph" ${t.resim ? 'style="display:none"' : ''}>
                            <img src="https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=60" alt="Yemek" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.6;">
                            <span class="e" style="position:relative;z-index:1;">${katIkon}</span>
                        </div>
                        <span class="rozet">${isUserRecipe && t.user_name ? '👤 ' + esc(t.user_name) : katIsim}</span>
                        ${!isUserRecipe ? `<button class="fav-btn" data-url="${esc(t.url)}" onclick="event.stopPropagation();favToggle(this)" title="Favorilere Ekle">♡</button>` : ''}
                        ${t.resim ? `<button class="capture-btn" data-img="${esc(t.resim)}" data-title="${esc(t.baslik)}" onclick="event.stopPropagation();openLightbox(this)" title="Fotoğrafı Görüntüle"><i class="fas fa-camera"></i></button>` : ''}
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">${esc(t.baslik)}</h3>
                        ${t.ozet ? `<p class="card-desc">${esc(t.ozet)}</p>` : ''}
                    </div>
                    <div class="card-footer-custom">
                        ${t.tarih ? `<span class="card-date"><i class="fas fa-calendar-days" style="font-size:0.65rem;"></i> ${esc(t.tarih)}</span>` : '<span></span>'}
                        <div style="display:flex;gap:6px;align-items:center;">
                            ${isUserRecipe
                                ? `<button class="card-link" onclick="event.stopPropagation();openUserRecipeDetail(${esc(_urDetail)})" style="background:transparent;border:none;cursor:pointer;padding:0;">Detay <i class="fas fa-info-circle" style="font-size:0.6rem;"></i></button>`
                                : `<button class="card-link" onclick="event.stopPropagation();openRecipeDetail(${esc(JSON.stringify({title:t.baslik,resim:t.resim||'',url:t.url,ozet:t.ozet||'',tarih:t.tarih||'',kat:katIsim}))})" style="background:transparent;border:none;cursor:pointer;padding:0;">Detay <i class="fas fa-info-circle" style="font-size:0.6rem;"></i></button>
                                  <span style="color:var(--border);">|</span>
                                  <a class="card-link" href="${esc(t.url)}" target="_blank" rel="noopener" onclick="event.stopPropagation()">Kaynak <i class="fas fa-arrow-up-right-from-square" style="font-size:0.6rem;"></i></a>`
                            }
                        </div>
                    </div>
                    ${interactHtml}
                </article>`;
            }

            // esc(): XSS koruması — HTML özel karakterlerini encode eder.
            // innerHTML'e gömmeden önce her string bu fonksiyondan geçirilir.
            function esc(s) {
                if (!s) return '';
                return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
            }

            async function loadMore() {
                if (loading || allLoaded) return;
                loading = true;
                loader.style.display = 'flex';
                try {
                    const params = new URLSearchParams(currentParams);
                    params.set('ajax', '1');
                    params.set('offset', offset);
                    const res = await fetch('?' + params.toString());
                    const data = await res.json();
                    if (data.items && data.items.length > 0) {
                        const frag = document.createDocumentFragment();
                        data.items.forEach(t => {
                            const div = document.createElement('div');
                            div.innerHTML = makeCardHTML(t);
                            while (div.firstChild) frag.appendChild(div.firstChild);
                        });
                        grid.appendChild(frag);
                        offset += data.items.length;
                    }
                    if (!data.hasMore) {
                        allLoaded = true;
                        endMsg.style.display = 'block';
                        loader.style.display = 'none';
                    }
                } catch(e) {
                    console.error('Scroll yükleme hatası:', e);
                }
                loading = false;
                if (!allLoaded) loader.style.display = 'none';
            }

            if (!allLoaded) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) loadMore();
                    });
                }, { rootMargin: '200px' });
                observer.observe(sentinel);
            }
        })();

        let _editingRecipeId = null;

        function openUserRecipeDetail(data) {
            _editingRecipeId = data.id || null;
            const overlay = document.getElementById('recipeDetailOverlay');
            const imgWrap = document.getElementById('rdImgWrap');
            const titleEl = document.getElementById('rdTitle');
            const meta = document.getElementById('rdMeta');
            const ingSection = document.getElementById('rdIngSection');
            const ingList = document.getElementById('rdIngList');
            const stepSection = document.getElementById('rdStepSection');
            const stepsList = document.getElementById('rdStepsList');
            const descSection = document.getElementById('rdDescSection');
            const descEl = document.getElementById('rdDesc');
            const sourceLink = document.getElementById('rdSourceLink');

            document.getElementById('rdEditPanel')?.remove();

            titleEl.textContent = data.title || 'Tarif Detayı';
            imgWrap.innerHTML = data.resim
                ? `<img src="${data.resim}" class="rd-img" alt="${data.title}" onerror="this.parentElement.innerHTML='<div class=rd-img-placeholder>🍽️</div>'">`
                : '<div class="rd-img-placeholder">👨‍🍳</div>';

            let metaHtml = '';
            if (data.user_name) metaHtml += `<span>👤 ${data.user_name}</span>`;
            if (data.tarih) metaHtml += `<span>📅 ${data.tarih}</span>`;
            meta.innerHTML = metaHtml;
            sourceLink.style.display = 'none';

            if (data.ingredients && data.ingredients.trim()) {
                const lines = data.ingredients.split('\n').map(l => l.trim()).filter(Boolean);
                ingList.innerHTML = lines.map(l => `<div>• ${l}</div>`).join('') || '<div style="color:var(--muted);">Malzeme belirtilmemiş.</div>';
                ingSection.style.display = '';
            } else { ingSection.style.display = 'none'; }

            if (data.instructions && data.instructions.trim()) {
                const steps = data.instructions.split('\n').map(l => l.trim()).filter(Boolean);
                stepsList.innerHTML = steps.map((s, i) => `<div class="rd-step"><div class="rd-step-num">${i+1}</div><div>${s}</div></div>`).join('');
                stepSection.style.display = '';
            } else { stepSection.style.display = 'none'; }

            if (data.ozet) { descEl.textContent = data.ozet; descSection.style.display = ''; }
            else { descSection.style.display = 'none'; }

            const isMine = data.is_mine && data.id;
            if (isMine) {
                const editBtn = document.createElement('button');
                editBtn.id = 'rdEditToggleBtn';
                editBtn.style.cssText = 'margin:12px 0 0;padding:10px 20px;border-radius:10px;border:1.5px solid rgba(207,174,85,.5);background:rgba(207,174,85,.08);color:#cfae55;font-weight:700;font-size:.88rem;cursor:pointer;width:100%;';
                editBtn.textContent = '✏️ Bu Tarifi Düzenle';
                editBtn.onclick = () => showEditPanel(data);

                const modal = overlay.querySelector('.rd-modal') || overlay.querySelector('[class*="modal"]') || overlay.firstElementChild;
                if (modal) modal.appendChild(editBtn);
            }

            overlay.classList.add('open');
        }

        function showEditPanel(data) {
            document.getElementById('rdEditPanel')?.remove();
            document.getElementById('rdEditToggleBtn')?.remove();

            const overlay = document.getElementById('recipeDetailOverlay');
            const modal = overlay.querySelector('.rd-modal') || overlay.querySelector('[class*="modal"]') || overlay.firstElementChild;
            if (!modal) return;

            const panel = document.createElement('div');
            panel.id = 'rdEditPanel';
            panel.style.cssText = 'margin-top:20px;border-top:1px solid rgba(207,174,85,.2);padding-top:18px;';
            panel.innerHTML = `
              <h4 style="margin:0 0 14px;font-size:.95rem;color:#cfae55;">✏️ Tarifi Düzenle</h4>
              <div style="margin-bottom:10px;">
                <label style="font-size:.78rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px;">Tarif Adı *</label>
                <input id="rdEditTitle" type="text" value="${escHtml(data.title)}"
                  style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid rgba(207,174,85,.35);background:var(--surface);color:var(--ink);font-size:.88rem;box-sizing:border-box;">
              </div>
              <div style="margin-bottom:10px;">
                <label style="font-size:.78rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px;">Malzemeler *</label>
                <textarea id="rdEditIng" rows="5"
                  style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid rgba(207,174,85,.35);background:var(--surface);color:var(--ink);font-size:.85rem;box-sizing:border-box;resize:vertical;">${escHtml(data.ingredients||'')}</textarea>
                <div style="font-size:.72rem;color:var(--muted);margin-top:2px;">Her malzeme ayrı satıra</div>
              </div>
              <div style="margin-bottom:10px;">
                <label style="font-size:.78rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px;">Yapılış *</label>
                <textarea id="rdEditInst" rows="5"
                  style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid rgba(207,174,85,.35);background:var(--surface);color:var(--ink);font-size:.85rem;box-sizing:border-box;resize:vertical;">${escHtml(data.instructions||'')}</textarea>
                <div style="font-size:.72rem;color:var(--muted);margin-top:2px;">Her adım ayrı satıra</div>
              </div>
              <div style="display:flex;gap:10px;margin-top:14px;">
                <button id="rdEditSaveBtn" style="flex:1;padding:11px;border-radius:10px;border:none;background:linear-gradient(135deg,#cfae55,#b8963e);color:#1a1a1a;font-weight:800;font-size:.88rem;cursor:pointer;">💾 Kaydet</button>
                <button onclick="document.getElementById('rdEditPanel').remove()" style="padding:11px 18px;border-radius:10px;border:1px solid #444;background:transparent;color:var(--muted);cursor:pointer;font-size:.85rem;">İptal</button>
              </div>
              <div id="rdEditMsg" style="margin-top:10px;font-size:.82rem;text-align:center;display:none;"></div>`;
            modal.appendChild(panel);

            document.getElementById('rdEditSaveBtn').onclick = async () => {
                const title = document.getElementById('rdEditTitle').value.trim();
                const ing   = document.getElementById('rdEditIng').value.trim();
                const inst  = document.getElementById('rdEditInst').value.trim();
                if (!title || !ing || !inst) {
                    showEditMsg('Lütfen tüm alanları doldurun.', 'error'); return;
                }
                const btn = document.getElementById('rdEditSaveBtn');
                btn.disabled = true; btn.textContent = '⏳...';
                const fd = new FormData();
                fd.append('id', _editingRecipeId);
                fd.append('title', title);
                fd.append('ingredients', ing);
                fd.append('instructions', inst);
                try {
                    const res = await fetch('api/user-recipes', { method:'POST', body:fd });
                    const r = await res.json();
                    if (r.success) {
                        showEditMsg('✅ Tarif güncellendi!', 'ok');

                        document.getElementById('rdTitle').textContent = title;
                        document.getElementById('rdIngList').innerHTML =
                            ing.split('\n').filter(Boolean).map(l=>`<div>• ${l.trim()}</div>`).join('');
                        document.getElementById('rdStepsList').innerHTML =
                            inst.split('\n').filter(Boolean).map((s,i)=>`<div class="rd-step"><div class="rd-step-num">${i+1}</div><div>${s.trim()}</div></div>`).join('');
                        document.getElementById('rdIngSection').style.display = '';
                        document.getElementById('rdStepSection').style.display = '';
                        setTimeout(() => document.getElementById('rdEditPanel')?.remove(), 1500);
                    } else {
                        showEditMsg(r.message || 'Güncelleme hatası.', 'error');
                        btn.disabled = false; btn.textContent = '💾 Kaydet';
                    }
                } catch(e) {
                    showEditMsg('Bağlantı hatası.', 'error');
                    btn.disabled = false; btn.textContent = '💾 Kaydet';
                }
            };
        }

        function showEditMsg(msg, type) {
            const el = document.getElementById('rdEditMsg');
            if (!el) return;
            el.textContent = msg;
            el.style.display = 'block';
            el.style.color = type === 'ok' ? '#4ade80' : '#f87171';
        }

        function escHtml(s) {
            return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // ── Tema Toggle (Dark / Light) ───────────────────────────────
        // IIFE: Tema tercihi localStorage'da 'cm_theme' anahtarıyla saklanır.

        (function() {
            const key = 'cm_theme';
            const btn = document.getElementById('themeToggle');
            if (!btn) return;

            function apply(theme) {
                const isLight = theme === 'light';
                document.body.classList.toggle('light', isLight);
                const label = btn.querySelector('.theme-pill-label');
                const icon = btn.querySelector('.theme-pill-icon i');
                if (label) label.textContent = isLight ? 'DARK' : 'LIGHT';
                if (icon) icon.className = isLight ? 'fas fa-moon' : 'fas fa-sun';
            }

            const saved = localStorage.getItem(key);
            if (saved === 'light' || saved === 'dark') apply(saved);

            btn.addEventListener('click', () => {
                const next = document.body.classList.contains('light') ? 'dark' : 'light';
                localStorage.setItem(key, next);
                apply(next);
            });
        })();

    let _maCurrent = null;

    (function() {
        const d = document.getElementById('maDate');
        if (d) d.value = new Date().toISOString().slice(0, 10);
    })();

    function openMenuAdd(btn) {
        _maCurrent = {
            title: btn.dataset.title || '',
            img:   btn.dataset.img   || '',
            url:   btn.dataset.url   || ''
        };
        // Sıfırla
        document.getElementById('maFormView').style.display = 'block';
        document.getElementById('maSuccess').style.display  = 'none';
        document.getElementById('maSubmitBtn').disabled    = false;
        document.getElementById('maSubmitBtn').textContent = '➕ Menüye Ekle';
        document.getElementById('maDate').value = new Date().toISOString().slice(0,10);
        document.getElementById('maCal').value  = '';

        document.getElementById('maRecipeName').textContent = '🍽️ ' + _maCurrent.title;
        document.getElementById('menuAddOverlay').classList.add('open');
    }

    function closeMenuAdd() {
        document.getElementById('menuAddOverlay').classList.remove('open');
    }

    document.getElementById('menuAddOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeMenuAdd();
    });

    async function submitMenuAdd() {
        if (!_maCurrent) return;
        const btn  = document.getElementById('maSubmitBtn');
        const date = document.getElementById('maDate').value || new Date().toISOString().slice(0,10);
        const type = document.getElementById('maMealType').value;
        const cal  = parseInt(document.getElementById('maCal').value) || 0;

        btn.disabled    = true;
        btn.textContent = '⏳ Ekleniyor...';

        try {
            const fd = new FormData();
            fd.append('menu_date',   date);
            fd.append('meal_type',   type);
            fd.append('description', _maCurrent.title);
            fd.append('calories',    cal);

            const res  = await fetch('api/menu', { method: 'POST', body: fd });
            const json = await res.json();

            if (json.success) {
                document.getElementById('maFormView').style.display = 'none';
                document.getElementById('maSuccess').style.display  = 'block';
            } else {
                alert(json.message || 'Bir hata oluştu. Giriş yaptığınızdan emin olun.');
                btn.disabled    = false;
                btn.textContent = '➕ Menüye Ekle';
            }
        } catch(err) {
            alert('Bağlantı hatası. Lütfen giriş yaptığınızdan emin olun.');
            btn.disabled    = false;
            btn.textContent = '➕ Menüye Ekle';
        }
    }

    function openUserRecipeAdd() {
        document.getElementById('uraFormView').style.display = 'block';
        document.getElementById('uraSuccess').style.display = 'none';
        ['uraTitle','uraCategory','uraIngredients','uraInstructions'].forEach(id => {
            const el = document.getElementById(id); if (el) el.value = '';
        });
        const uraImg = document.getElementById('uraImage'); if (uraImg) uraImg.value = '';
        const uraFile = document.getElementById('uraImageFile'); if (uraFile) uraFile.value = '';
        const modeFile = document.getElementById('uraImgModeFile');
        if (modeFile) { modeFile.checked = true; }
        const fw = document.getElementById('uraImgFileWrap'); if (fw) fw.style.display = '';
        const uw = document.getElementById('uraImgUrlWrap'); if (uw) uw.style.display = 'none';
        const pv = document.getElementById('uraImgPrev'); if (pv) pv.style.display = 'none';
        document.getElementById('uraSubmitBtn').disabled = false;
        document.getElementById('uraSubmitBtn').textContent = '📤 Tarifi Paylaş';
        document.getElementById('uraOverlay').classList.add('open');
    }

    function closeUserRecipeAdd() {
        document.getElementById('uraOverlay').classList.remove('open');
    }

    document.getElementById('uraOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeUserRecipeAdd();
    });

    document.querySelectorAll('input[name="uraImgMode"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const isFile = document.getElementById('uraImgModeFile').checked;
            const fw = document.getElementById('uraImgFileWrap'); if (fw) fw.style.display = isFile ? '' : 'none';
            const uw = document.getElementById('uraImgUrlWrap'); if (uw) uw.style.display = isFile ? 'none' : '';
            const pv = document.getElementById('uraImgPrev'); if (pv) pv.style.display = 'none';
        });
    });

    const _uraFile = document.getElementById('uraImageFile');
    if (_uraFile) _uraFile.addEventListener('change', e => {
        const f = e.target.files[0]; if (!f) return;
        const pi = document.getElementById('uraImgPrevImg'); if (pi) pi.src = URL.createObjectURL(f);
        const pv = document.getElementById('uraImgPrev'); if (pv) pv.style.display = '';
    });
    // URL input'tan çıkınca (blur) URL geçerliyse önizleme gösterilir.

    const _uraImgUrl = document.getElementById('uraImage');
    if (_uraImgUrl) _uraImgUrl.addEventListener('blur', e => {
        const url = e.target.value.trim();
        if (url.startsWith('http')) {
            const pi = document.getElementById('uraImgPrevImg'); if (pi) pi.src = url;
            const pv = document.getElementById('uraImgPrev'); if (pv) pv.style.display = '';
        }
    });

    // submitUserRecipe(): Formu doğrular ve api.php → user_recipe_add action'ına gönderir.

    async function submitUserRecipe() {
        const title = document.getElementById('uraTitle').value.trim();
        const category = document.getElementById('uraCategory').value.trim();
        const ingredients = document.getElementById('uraIngredients').value.trim();
        const instructions = document.getElementById('uraInstructions').value.trim();

        if (!title) { tarifToast('Tarif adı boş olamaz!', 'e'); return; }
        if (!ingredients) { tarifToast('Malzemeler boş olamaz!', 'e'); return; }
        if (!instructions) { tarifToast('Yapılış adımları boş olamaz!', 'e'); return; }

        const btn = document.getElementById('uraSubmitBtn');
        btn.disabled = true;
        btn.textContent = '⏳ Yükleniyor...';

        try {
            const fd = new FormData();
            fd.append('title', title);
            fd.append('category', category);
            fd.append('ingredients', ingredients);
            fd.append('instructions', instructions);

            const modeFile = document.getElementById('uraImgModeFile');
            const fileInput = document.getElementById('uraImageFile');
            if (modeFile && modeFile.checked && fileInput && fileInput.files.length > 0) {
                fd.append('recipe_image', fileInput.files[0]);
            } else {
                const imgUrl = (document.getElementById('uraImage')?.value || '').trim();
                if (imgUrl) fd.append('image_url', imgUrl);
            }

            const res = await fetch('api/user-recipes', { method:'POST', body:fd });
            const json = await res.json();
            if (json.success) {
                document.getElementById('uraFormView').style.display = 'none';
                document.getElementById('uraSuccess').style.display = 'block';
            } else {
                tarifToast(json.message || 'Bir hata oluştu.', 'e');
                btn.disabled = false;
                btn.textContent = '📤 Tarifi Paylaş';
            }
        } catch(err) {
            tarifToast('Bağlantı hatası.', 'e');
            btn.disabled = false;
            btn.textContent = '📤 Tarifi Paylaş';
        }
    }
