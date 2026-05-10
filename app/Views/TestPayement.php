<?php
$regime_id = 1;
$sport_id = 1;
$prix_regime_sport = 100;
$duree_regime_sport = 30;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Paiement</title>
</head>

<body>
    <h1>Test Paiement</h1>

    <?php if (session()->has('error')): ?>
        <div
            style="color: #d32f2f; padding: 15px; background: #ffebee; border: 2px solid #d32f2f; border-radius: 5px; margin: 15px 0;">
            <strong>❌ Erreur :</strong> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div
            style="color: #388e3c; padding: 15px; background: #e8f5e9; border: 2px solid #388e3c; border-radius: 5px; margin: 15px 0;">
            <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <p>Regime ID: <?php echo $regime_id; ?></p>
    <p>Sport ID: <?php echo $sport_id; ?></p>
    <p>Prix: <?php echo $prix_regime_sport; ?></p>
    <p>Durée: <?php echo $duree_regime_sport; ?></p>
    <form method="post" action="/acheter_regime_sport">
        <?= csrf_field() ?>
        <input type="hidden" name="regime_id" value="<?php echo $regime_id; ?>">
        <input type="hidden" name="sport_id" value="<?php echo $sport_id; ?>">
        <input type="hidden" name="objectif_id" value="1">
        <input type="hidden" name="duree" value="<?php echo $duree_regime_sport; ?>">
        <input type="hidden" name="prix" value="<?php echo $prix_regime_sport; ?>">
        <input type="submit" value="Procéder au paiement">
    </form>
</body>

</html>