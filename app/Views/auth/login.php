<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="/assets/css/form.css">
    <title>Se connecter</title>
</head>

<body>
    <?php $validation = $validation ?? session()->getFlashdata('validation'); ?>

    <main>
        <section class="login-section">
            <div class="login-container">
                <!-- Demo Accounts Column -->
                <div class="demo-column">
                    <div class="demo-header">
                        <h3>📋 Comptes de Démonstration</h3>
                        <p>Cliquez sur un compte pour le précharger</p>
                    </div>

                    <div class="accounts-grid">
                        <!-- Admin Account -->
                        <div class="account-card admin-card" onclick="loadAccount('admin@regime.com', 'admin123')">
                            <div class="account-icon">👑</div>
                            <div class="account-info">
                                <h4>Admin</h4>
                                <p class="account-email">admin@regime.com</p>
                                <span class="account-role">Administrateur</span>
                            </div>
                            <div class="account-action">→</div>
                        </div>

                        <!-- Jade Colin -->
                        <div class="account-card user-card" onclick="loadAccount('jade.colin.2026@example.com', 'test123')">
                            <div class="account-icon">👤</div>
                            <div class="account-info">
                                <h4>Jade Colin</h4>
                                <p class="account-email">jade.colin.2026@example.com</p>
                                <span class="account-role">Utilisateur</span>
                            </div>
                            <div class="account-action">→</div>
                        </div>

                        <!-- Lina Perrin -->
                        <div class="account-card user-card" onclick="loadAccount('lina.perrin.2026@example.com', 'test123')">
                            <div class="account-icon">👤</div>
                            <div class="account-info">
                                <h4>Lina Perrin</h4>
                                <p class="account-email">lina.perrin.2026@example.com</p>
                                <span class="account-role">Utilisateur</span>
                            </div>
                            <div class="account-action">→</div>
                        </div>

                        <!-- Samir Meunier -->
                        <div class="account-card user-card" onclick="loadAccount('samir.meunier.2026@example.com', 'test123')">
                            <div class="account-icon">👤</div>
                            <div class="account-info">
                                <h4>Samir Meunier</h4>
                                <p class="account-email">samir.meunier.2026@example.com</p>
                                <span class="account-role">Utilisateur</span>
                            </div>
                            <div class="account-action">→</div>
                        </div>

                        <!-- Chloe Henry -->
                        <div class="account-card user-card" onclick="loadAccount('chloe.henry.2026@example.com', 'test123')">
                            <div class="account-icon">👤</div>
                            <div class="account-info">
                                <h4>Chloe Henry</h4>
                                <p class="account-email">chloe.henry.2026@example.com</p>
                                <span class="account-role">Utilisateur</span>
                            </div>
                            <div class="account-action">→</div>
                        </div>
                    </div>
                </div>

                <!-- Form Column -->
                <div class="form-column">
                    <div class="form-container">
                        <form action="/login" class="login-form" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <h2>Se connecter</h2>
                            </div>
                            <div class="form-group">
                                <div class="form-label">
                                    <label for="email">Email : </label>
                                    <?php if (isset($notFound)) { ?>
                                        <small class="form-error">
                                            <?= $notFound ?>
                                        </small>
                                    <?php } ?>
                                    <?php if (isset($validation) && $validation->hasError('email')) { ?>
                                        <small class="form-error">
                                            <?= $validation->getError('email') ?>
                                        </small>
                                    <?php } ?>
                                </div>
                                <input type="email" name="email" id="email" placeholder="example@example.com"
                                    value="<?= esc(old('email')) ?>">
                            </div>
                            <div class="form-group">
                                <div class="form-label">
                                    <label for="password">Mot de passe :</label>
                                    <?php if (isset($validation) && $validation->hasError('mot_de_passe')) { ?>
                                        <small class="form-error">
                                            <?= $validation->getError('mot_de_passe') ?>
                                        </small>
                                    <?php } ?>
                                    <?php if (isset($wrong)) { ?>
                                        <small class="form-error">
                                            <?= $wrong ?>
                                        </small>
                                    <?php } ?>
                                </div>
                                <input type="password" name="mot_de_passe" id="password" placeholder="Mot de passe">
                            </div>
                            <div class="form-group">
                                <p>Pas encore de compte ? <a href="/inscription/contact">S'inscrire</a></p>
                            </div>

                            <div class="form-group">
                                <input type="submit" value="Se connecter" class="btn btn-accept">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>

    </footer>

    <script>
        function loadAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('email').focus();
            
            // Smooth scroll to form
            document.querySelector('.login-form').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
    <script src="script.js"></script>
</body>

</html>