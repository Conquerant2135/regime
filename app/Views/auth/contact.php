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
                            </div>
                            <input type="text" name="nom" id="nom" placeholder="Nom">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="email">Email :</label>
                            </div>
                            <input type="email" name="email" id="email" placeholder="example@example.com">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="password">Mot de passe :</label>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Mot de passe">
                        </div>
                        <div class="form-group">
                            <div class="form-label">
                                <label for="naissance">Date de naissance :</label>
                            </div>
                            <input type="date" name="naissance" id="naissance">
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