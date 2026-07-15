<?php

$extraCss = $extraCss ?? [];
?>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($pageTitle) ?> — ChefMate.AI</title>

<!-- Bootstrap 5: Responsive grid ve UI bileşenleri -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<!-- Font Awesome 6: Navbar ikonları (çatal-bıçak, güneş/ay vb.) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<!-- Google Fonts: Inter (metin) + Playfair Display (başlıklar) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet"/>
<!-- AOS (Animate On Scroll): Scroll sırasında elementlerin animasyonla belirmesi için -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"/>

<!-- Ortak site CSS'i (navbar, tema butonu, genel tasarım token'ları) -->
<link rel="stylesheet" href="assets/css/index.css" />

<?php foreach ($extraCss as $cssPath): ?>
<link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>" />
<?php endforeach; ?>
