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
    <?php $validation = $validation ?? session()->getFlashdata('validation'); ?>
    <main>
        <section class="login">
            <div class="container">
                <div class="form-container">
                    <form action="/inscription" method="post" class="login-form">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <h2>S'inscrire</h2>
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="Taille">Taille (en cm) :</label>
                                <?php if (isset($validation) && $validation->hasError('taille')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('taille') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <input type="number" step="any" name="taille" id="taille" placeholder="taille en cm"
                                value="<?= esc(old('taille')) ?>">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="poid">Poid (en kg) :</label>
                                <?php if (isset($validation) && $validation->hasError('poid')) { ?>
                                    <small class="form-error">
                                        <?= $validation->getError('poid') ?>
                                    </small>
                                <?php } ?>
                            </div>
                            <input type="number" step="0.01" name="poid" id="poid" placeholder="poid en kg"
                                value="<?= esc(old('poid')) ?>">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="S'inscrire" class="btn btn-accept">
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