<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Matière') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>
                <div>
                    <div class="brand-name">Matière</div>
                    <div class="brand-sub">v1.0.0</div>
                </div>
            </div>

            <div class="sidebar-section">Navigation</div>

            <a href="<?= site_url('regime-sport') ?>"
                class="nav-item <?= (strpos(current_url(), 'regime-sport') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2l4 4-4 4-4-4 4-4z" />
                    <path d="M4 14h16v6H4z" />
                </svg>
                Régime sport
            </a>

            <div class="sidebar-bottom">
                <a href="<?= site_url('auth/logout') ?>" class="user-row" style="color:#fff;text-decoration:none">
                    <div class="avatar"><?= substr(session()->get('userEmail'), 0, 2) ?></div>
                    <div class="user-info">
                        <div class="name"><?= esc(session()->get('userEmail')) ?></div>
                        <div class="role">Administrateur</div>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="main">
            <div class="topbar">
                <div class="topbar-title"><?= esc($title ?? 'Tableau de bord') ?></div>
                <div class="topbar-actions">
                    <a href="<?= site_url('auth/logout') ?>" class="icon-btn" title="Déconnexion">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="content">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
</body>

</html>