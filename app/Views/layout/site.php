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

    <!-- Bouton scroll to top -->
    <button class="scroll-to-top" id="scrollToTopBtn" aria-label="Retour en haut">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <script>
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        
        // Affiche/masque le bouton au scroll
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        // Scroll smooth au clic
        scrollToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
