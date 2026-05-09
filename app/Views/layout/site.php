<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Fitness Régime') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
</head>
<body class="site-body">
    <header class="site-header">
        <div class="site-nav">
            <a class="brand" href="<?= site_url('/') ?>">
                <span class="brand-mark">FR</span>
                <span class="brand-text">Fitness Régime</span>
            </a>

            <nav class="site-links">
                <a href="<?= site_url('/') ?>">Accueil</a>
                <a href="<?= site_url('regime-sport') ?>">Régimes</a>
                <a href="<?= site_url('mon-compte') ?>">Mon compte</a>
                <a href="<?= site_url('mon-regime') ?>">Mon régime</a>
                <?php if (session()->get('logged_in')): ?>
                    <a href="<?= site_url('logout') ?>">Déconnexion</a>
                <?php else: ?>
                    <a class="nav-cta" href="<?= site_url('login') ?>">Connexion / Inscription</a>
                <?php endif; ?>
            </nav>

            <a class="wallet-pill" href="<?= site_url('portefeuille') ?>">
                Solde : <?= number_format((float) (session()->get('solde') ?? 0), 2) ?> €
            </a>
        </div>
    </header>

    <main class="site-main">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="site-footer">
        <div>
            <strong>Fitness Régime</strong>
            <p>Un accompagnement simple, clair et rassurant pour vos objectifs bien-être.</p>
        </div>
        <div class="footer-copy">© <?= date('Y') ?> Fitness Régime</div>
    </footer>
</body>
</html>