<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .navbar h1 {
        margin: 0;
        font-size: 24px;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .btn-solde {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid white;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-solde:hover {
        background: white;
        color: #667eea;
    }

    .navbar-links {
        display: flex;
        gap: 20px;
    }

    .navbar-links a {
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .navbar-links a:hover {
        text-decoration: underline;
    }
</style>

<?php if (session()->get('logged_in')): ?>
    <div class="navbar">
        <h1>💪 Fitness Régime</h1>
        <div class="navbar-right">
            <a href="/portefeuille" class="btn-solde">
                💰 Solde: <?= number_format(session()->get('solde') ?? 0, 2) ?> € (Euro)
            </a>
            <div class="navbar-links">
                <a href="/home">Accueil</a>
                <a href="/regime-sport">Régimes</a>
                <a href="/logout">Déconnexion</a>
            </div>
        </div>
    </div>
<?php endif; ?>