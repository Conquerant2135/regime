<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="site-body">

    <header class="site-header">
        <div class="site-nav">
            <a class="brand" href="<?= site_url('/') ?>">
                <span class="brand-mark">FR</span>
                <span class="brand-text">Fitness Régime</span>
            </a>
            <nav class="site-links" aria-label="Navigation principale">
                <a href="<?= site_url('/') ?>">Accueil</a>
                <a href="<?= site_url('regime-sport') ?>">Régimes</a>
                <a href="<?= site_url('mon-compte') ?>">Mon compte</a>
                <a href="<?= site_url('/admin/dashboard') ?>">Dashboard admin</a>
            </nav>
            <a class="wallet-pill" href="<?= site_url('portefeuille') ?>">
                Solde: <?= number_format((float) (session()->get('solde') ?? 0), 2) ?> €
            </a>
            <?php if (session()->get('logged_in')): ?>
                <a class="logout-btn" href="<?= site_url('logout') ?>" title="Déconnexion">Déconnexion</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- MAIN DASHBOARD -->
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar">
            <span class="sidebar-title">⚙️ Gestion</span>
            <nav class="sidebar-menu" aria-label="Menu gestion admin">
                <a href="<?= site_url('/admin/dashboard') ?>"
                    class="sidebar-link <?= url_is('admin/dashboard') ? 'active' : '' ?>">
                    <span class="sidebar-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= site_url('/admin/regimes') ?>"
                    class="sidebar-link <?= url_is('admin/regimes*') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🥗</span>
                    <span>CRUD Régimes</span>
                </a>
                <a href="<?= site_url('/admin/sports') ?>"
                    class="sidebar-link <?= url_is('admin/sports*') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🏃‍♂️</span>
                    <span>CRUD Sports</span>
                </a>
            </nav>

            <span class="sidebar-title" style="margin-top: 28px;">⚙️ Paramètres</span>
            <nav class="sidebar-menu" aria-label="Menu parametres admin">
                <a href="<?= site_url('/admin/options') ?>"
                    class="sidebar-link <?= url_is('admin/options*') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🔧</span>
                    <span>CRUD options</span>
                </a>
                <a href="<?= site_url('/admin/codes') ?>"
                    class="sidebar-link <?= url_is('admin/codes*') ? 'active' : '' ?>">
                    <span class="sidebar-icon">🎟️</span>
                    <span>CRUD Codes Promo</span>
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="dashboard-main">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</body>

</html>