<?php

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChefMate.AI — Giriş Yap</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family:'Inter',sans-serif; margin:0; height:100vh; display:flex; align-items:center; justify-content:center; background-color:#792b2b; background-image:url('../hero.jpg'); background-size:cover; background-position:center; position:relative; }
        body::before { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.75); z-index:1; }
        .login-card { position:relative; z-index:2; background:rgba(50,50,50,0.85); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:clamp(28px,6vw,50px) clamp(20px,5vw,40px); width:100%; max-width:400px; text-align:center; box-shadow:0 20px 50px rgba(0,0,0,0.5); }
        @media (max-width:480px) {
          body { padding: 16px; align-items: flex-start; padding-top: 40px; }
          .login-card { border-radius:16px; }
          .brand-title { font-size:1.7rem; margin-bottom:28px; }
          .form-control { padding:12px 16px 12px 40px; font-size:0.9rem; }
          .btn-login { padding:13px; }
        }
        .brand-title { font-family:'Cinzel',serif; font-size:2.2rem; color:#cfae55; margin-bottom:40px; letter-spacing:1px; text-transform:uppercase; }
        .form-group { position:relative; margin-bottom:20px; }
        .form-control { background-color:#f5ebeb; border:1px solid #e0d2d2; border-radius:30px; color:#333; padding:15px 20px 15px 45px; font-size:0.95rem; width:100%; box-sizing:border-box; }
        .form-control:focus { background-color:#333; border-color:#cfae55; color:#fff; box-shadow:none; outline:none; }
        .input-icon { position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#888; font-size:1rem; z-index:5; }
        .btn-login { background:linear-gradient(to right,#cfae55,#b8963e); border:none; border-radius:30px; width:100%; padding:15px; font-weight:700; color:#1a1a1a; text-transform:uppercase; letter-spacing:1px; margin-top:10px; transition:transform 0.2s; cursor:pointer; }
        .btn-login:hover { transform:scale(1.02); background:linear-gradient(to right,#e0c060,#cfae55); color:#000; }
        .bottom-text { margin-top:25px; color:#f0efed; font-size:0.9rem; }
        .bottom-text a { color:#cfae55; text-decoration:none; font-weight:600; }
        .bottom-text a:hover { color:#fff; }
        .alert-danger { background:rgba(220,53,69,0.2); border:1px solid #dc3545; color:#ff8d96; border-radius:10px; font-size:0.9rem; padding:10px; margin-bottom:20px; }
        .alert-success { background:rgba(25,135,84,0.2); border:1px solid #198754; color:#75d4a8; border-radius:10px; font-size:0.9rem; padding:10px; margin-bottom:20px; }
        .mvc-badge { position:absolute; top:10px; right:10px; z-index:3; background:#0d6efd; color:#fff; font-size:0.65rem; padding:3px 8px; border-radius:20px; letter-spacing:0.5px; }
    </style>
</head>
<body>
    <div class="mvc-badge">MVC · yeni sistem</div>
    <div class="login-card">
        <div class="brand-title">CHEFMATE AI</div>

        <?php if ($error): ?>
            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" class="form-control" name="email" placeholder="E-posta Adresiniz" value="<?= htmlspecialchars($oldEmail) ?>" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="form-control" name="password" placeholder="Şifreniz" required>
            </div>
            <button type="submit" class="btn-login">GİRİŞ YAP</button>
        </form>

        <div class="bottom-text">Hesabınız yok mu? <a href="register">Kayıt Olun</a></div>
        <div class="bottom-text mt-2"><a href=".">← Ana Sayfaya Dön</a></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
