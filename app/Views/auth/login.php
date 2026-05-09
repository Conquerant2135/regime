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

    <main>
        <section class="login">
            <div class="container">
                <div class="form-container">
                    <form action="/login" class="login-form" method="post">
                        <div class="form-group">
                            <h2> Se connecter</h2>
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
                            <input type="email" name="email" id="email" placeholder="example@example.com" value="batman69@gmail.com">
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
                            <input type="password" name="mot_de_passe" id="password" placeholder="Mot de passe" value="batman69">
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
        </section>
    </main>

    <footer>

    </footer>

    <script src="script.js"></script>
</body>

</html>