<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="/assets/css/form.css">
    <title>S'inscrire</title>
</head>

<body>

    <main>
        <section class="login">
            <div class="container">
                <div class="form-container">
                    <form action="/inscription/info" method="post" class="login-form">
                        <div class="form-group">
                            <h2>S'inscrire</h2>
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="nom">Nom :</label>
                                <?php if (isset($validation) && $validation->hasError('nom')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('nom') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <input type="text" name="nom" id="nom" placeholder="Nom" value="<?= esc(old('nom')) ?>">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="email">Email :</label>
                                <?php if (isset($validation) && $validation->hasError('email')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('email') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <input type="email" name="email" id="email" placeholder="example@example.com" value="<?= esc(old('email')) ?>">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="password">Mot de passe :</label>
                                <?php if (isset($validation) && $validation->hasError('mot_de_passe')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('mot_de_passe') ?>
                                    </small>
                                <?php } ?>
                            </div>

                            <input type="password" name="mot_de_passe" id="password" placeholder="Mot de passe">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="naissance">Date de naissance :</label>
                                <?php if (isset($validation) && $validation->hasError('naissance')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('naissance') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <input type="date" name="naissance" id="naissance" value="<?= esc(old('naissance')) ?>">
                        </div>

                        <div class="form-group">
                            <div class="form-label">
                                <label>Sexe :</label>
                                <?php if (isset($validation) && $validation->hasError('sexe')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('sexe') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <div class="sex-options">
                                <div class="sex-option">
                                    <input type="radio" id="sexe_homme" name="sexe" value="homme" <?= old('sexe') === 'homme' ? 'checked' : '' ?>>
                                    <label for="sexe_homme">Homme</label>
                                </div>
                                <div class="sex-option">
                                    <input type="radio" id="sexe_femme" name="sexe" value="femme" <?= old('sexe') === 'femme' ? 'checked' : '' ?>>
                                    <label for="sexe_femme">Femme</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="Page suivante" class="btn btn-accept">
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>

    </footer>

    <script src="script.js"></script>
</body>

</html>