<?php

$extraJs = $extraJs ?? [];
?>
<!-- Bootstrap JS: Hamburger menü ve diğer interaktif bileşenler için -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS kütüphanesi: Scroll animasyonlarını başlatmak için -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once: true, duration: 1000, easing: 'ease-out-quart' });</script>

<!-- Ortak tema (light/dark) yönetimi -->
<script src="assets/js/theme.js"></script>

<?php foreach ($extraJs as $jsPath): ?>
<script src="<?= htmlspecialchars($jsPath) ?>"></script>
<?php endforeach; ?>
