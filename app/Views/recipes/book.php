<?php

$pageTitle = 'Tarif Defterim';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle) ?> — ChefMate.AI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/recipe-book.css">
</head>
<body>
    <!-- ÜST NAVİGASYON ÇUBUĞU
         Sol: Dashboard'a geri dön linki (dashboard.php henüz migrate edilmedi, legacy)
         Orta: Toplam kaydedilen tarif sayısı
         Sağ: Yeni tarif kaydetmek için tarifler.php'ye git (henüz migrate edilmedi, legacy)
    -->
    <div class="top-bar">
        <a href="dashboard">← Dashboard</a>
        <div class="info">📖 <?= count($tarifler) ?> TARİF</div>
        <a href="tarifler">🍽️ Tariflere Git</a>
    </div>

    <?php if (empty($tarifler)): ?>
    <!-- BOŞ DURUM EKRANI: Kaydedilmiş tarif yok -->
    <div class="empty-state">
        <h2>📖 Tarif Defteri Boş</h2>
        <p>Henüz tarif kaydetmediniz.</p>
        <a href="tarifler">🍽️ Tariflere Git &amp; Kaydet</a>
    </div>
    <?php else: ?>

    <div class="scene">
        <div class="book" id="book">

            <!-- ÖN KAPAK (1. sayfa) -->
            <div class="page" id="p1" style="z-index:<?= count($tarifler) + 5 ?>;">
                <div class="front cover-face">
                    <div style="font-size:.75rem;letter-spacing:3px;margin-bottom:10px;">GURME MUTFAK</div>
                    <h1>Chef<br>Mate.AI</h1>
                    <div class="subtitle">Tarif Koleksiyonu</div>
                    <div style="margin-top:40px;border:1px solid var(--gold-dim);padding:4px 14px;border-radius:20px;font-size:.68rem;"><?= count($tarifler) ?> TARİF</div>
                </div>
                <div class="back">
                    <div class="content" style="justify-content:center;align-items:center;text-align:center;">
                        <h2 style="border:none;font-size:1.8rem;">Hoş Geldiniz</h2>
                        <p style="text-align:center;color:#555;margin-top:10px;">
                            Bu koleksiyon <strong><?= htmlspecialchars($currentName) ?></strong>'e aittir.<br><br>
                            Ok butonları veya klavye ile sayfaları çevirin.
                        </p>
                        <div style="font-size:2.5rem;margin-top:16px;">🍽️</div>
                        <div class="page-num">I</div>
                    </div>
                </div>
            </div>

            <?php
            $pageNum = 2;
            $chunks = array_chunk($tarifler, 2);
            $totalChunks = count($chunks);
            foreach ($chunks as $ci => $chunk):
                $zi  = count($tarifler) + 4 - $ci;
                $pid = 'p' . ($ci + 2);
                $rec1 = $chunk[0];
                $rec2 = $chunk[1] ?? null;
            ?>
            <div class="page" id="<?= htmlspecialchars($pid) ?>" style="z-index:<?= $zi ?>;">
                <!-- ÖN YÜZ: $rec1 tarifi -->
                <div class="front">
                    <div class="content">
                        <?php if (!empty($rec1['image_url'])): ?>
                            <img src="<?= htmlspecialchars($rec1['image_url']) ?>" class="recipe-img"
                                alt="" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <h2><?= htmlspecialchars($rec1['title']) ?></h2>
                        <?php if (!empty($rec1['ingredients_array'])): ?>
                            <div class="ingredients-list">
                                <?php
                                $ings = array_slice($rec1['ingredients_array'], 0, 5);
                                foreach ($ings as $ing) echo '<div>• ' . htmlspecialchars($ing) . '</div>';
                                if (count($rec1['ingredients_array']) > 5) echo '<div style="color:var(--gold-dim)">+ daha fazla...</div>';
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($rec1['instructions'])): ?>
                            <p><?= htmlspecialchars(mb_substr(strip_tags($rec1['instructions']), 0, 150)) ?>...</p>
                        <?php endif; ?>
                        <?php if (!empty($rec1['source'])): ?>
                            <div class="recipe-source">🔗 <a href="<?= htmlspecialchars($rec1['source']) ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"><?= htmlspecialchars(parse_url($rec1['source'], PHP_URL_HOST) ?: $rec1['source']) ?></a></div>
                        <?php endif; ?>
                        <div class="recipe-date">📅 <?= date('d.m.Y', strtotime($rec1['saved_at'])) ?></div>
                        <div class="page-num"><?= $pageNum++ ?></div>
                    </div>
                </div>
                <!-- ARKA YÜZ: $rec2 tarifi (veya boş sayfa) -->
                <div class="back">
                    <?php if ($rec2): ?>
                    <div class="content">
                        <?php if (!empty($rec2['image_url'])): ?>
                            <img src="<?= htmlspecialchars($rec2['image_url']) ?>" class="recipe-img"
                                alt="" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <h2><?= htmlspecialchars($rec2['title']) ?></h2>
                        <?php if (!empty($rec2['ingredients_array'])): ?>
                            <div class="ingredients-list">
                                <?php
                                $ings2 = array_slice($rec2['ingredients_array'], 0, 5);
                                foreach ($ings2 as $i2) echo '<div>• ' . htmlspecialchars($i2) . '</div>';
                                if (count($rec2['ingredients_array']) > 5) echo '<div style="color:var(--gold-dim)">+ daha fazla...</div>';
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($rec2['instructions'])): ?>
                            <p><?= htmlspecialchars(mb_substr(strip_tags($rec2['instructions']), 0, 150)) ?>...</p>
                        <?php endif; ?>
                        <?php if (!empty($rec2['source'])): ?>
                            <div class="recipe-source">🔗 <a href="<?= htmlspecialchars($rec2['source']) ?>" target="_blank" rel="noopener" onclick="event.stopPropagation()"><?= htmlspecialchars(parse_url($rec2['source'], PHP_URL_HOST) ?: $rec2['source']) ?></a></div>
                        <?php endif; ?>
                        <div class="recipe-date">📅 <?= date('d.m.Y', strtotime($rec2['saved_at'])) ?></div>
                        <div class="page-num"><?= $pageNum++ ?></div>
                    </div>
                    <?php else: ?>
                    <div class="content" style="justify-content:center;align-items:center;text-align:center;">
                        <div style="font-size:2rem;">📖</div>
                        <p style="color:#aaa;margin-top:10px;">Sayfa boş</p>
                        <div class="page-num"><?= $pageNum++ ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- SON KAPAK -->
            <div class="page" id="p<?= $totalChunks + 2 ?>" style="z-index:1;">
                <div class="front">
                    <div class="content" style="justify-content:center;align-items:center;text-align:center;">
                        <div style="font-size:2rem;">🍽️</div>
                        <h2 style="border:none;margin-top:10px;font-size:1.3rem;">Koleksiyon Sonu</h2>
                        <p style="text-align:center;color:#555;margin-top:8px;">Daha fazla tarif kaydetmek için tariflere gidin.</p>
                        <a href="tarifler" style="display:inline-block;margin-top:16px;padding:8px 18px;border:1.5px solid var(--gold-dim);color:var(--gold);text-decoration:none;font-family:'Cinzel',serif;font-size:.72rem;">🔖 Tariflere Git</a>
                        <div class="page-num"><?= $pageNum ?></div>
                    </div>
                </div>
                <div class="back cover-face">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--gold);text-align:center;">
                        <h1 style="font-size:2.5rem;border-color:var(--gold);">Afiyet Olsun!</h1>
                        <p style="color:rgba(255,255,255,0.5);margin-top:10px;">ChefMate.AI</p>
                        <button class="reset-btn" id="resetBtn">Başa Dön</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="spine"></div>
    </div>

    <div class="controls">
        <button id="prevBtn" disabled>← GERİ</button>
        <button id="nextBtn">İLERİ →</button>
    </div>

    <script src="assets/js/recipe-book.js"></script>
    <?php endif; ?>
</body>
</html>
