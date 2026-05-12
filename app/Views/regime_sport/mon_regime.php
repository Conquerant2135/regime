<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>

<div class="regime-page">
    <section class="regime-hero card">
        <div class="regime-hero-copy">
            <span class="eyebrow">Mon régime</span>
            <h1>Vos régimes achetés</h1>
            <p>
                Retrouvez ici les combinaisons régime + sport déjà achetées dans votre compte.
            </p>
            <div style="margin-top: 18px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a class="btn btn-primary" href="<?= site_url('mon-regime/pdf') ?>" target="_blank" rel="noopener">
                    Exporter en PDF
                </a>
            </div>
        </div>

        <div class="regime-hero-stats">
            <div class="stat-box">
                <strong><?= esc((string) ($totalPackages ?? 0)) ?></strong>
                <span>Packages achetés</span>
            </div>
            <div class="stat-box">
                <strong><?= esc((string) ($totalDays ?? 0)) ?></strong>
                <span>Jours cumulés</span>
            </div>
            <div class="stat-box">
                <strong><?= esc(number_format((float) ($estimatedTotal ?? 0), 2, '.', '')) ?> €</strong>
                <span>Coût estimé</span>
            </div>
        </div>
    </section>

    <?php if ($selectedUser): ?>
        <p class="objectif-info">
            <strong>Utilisateur :</strong> <?= esc($selectedUser['email'] ?? $selectedUser['nom'] ?? '—') ?>
        </p>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="flash flash-error">
            <strong>❌ Erreur :</strong> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div class="flash flash-success">
            <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($purchasedRegimes)): ?>
        <table class="table regime-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Régime</th>
                    <th>Sport</th>
                    <th>Objectif</th>
                    <th>Durée</th>
                    <th>Prix / jour (€)</th>
                    <th>Impact</th>
                    <th>Coût estimé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchasedRegimes as $purchase):
                    $pourcentageViande = (float) ($purchase['pourcentage_viande'] ?? 0);
                    $pourcentageVolaille = (float) ($purchase['pourcentage_volaille'] ?? 0);
                    $pourcentagePoisson = (float) ($purchase['pourcentage_poisson'] ?? 0);
                    $prixParJour = (float) ($purchase['prix_par_jour'] ?? 0);
                    $duree = (int) ($purchase['duree'] ?? 0);
                    $impact = $purchase['impact_journalier'] ?? null;
                    $coutEstime = $prixParJour > 0 && $duree > 0 ? $prixParJour * $duree : null;
                    $regimeLabel = trim(
                        number_format($pourcentageViande, 0) . '% viande, ' .
                        number_format($pourcentageVolaille, 0) . '% volaille, ' .
                        number_format($pourcentagePoisson, 0) . '% poisson'
                    );
                    ?>
                    <tr>
                        <td data-label="Date"><?= esc((string) ($purchase['date_choix'] ?? '—')) ?></td>
                        <td data-label="Régime"><?= esc($regimeLabel ?: '—') ?></td>
                        <td data-label="Sport"><?= esc($purchase['sport_libelle'] ?? '—') ?></td>
                        <td data-label="Objectif"><?= esc($purchase['objectif_libelle'] ?? '—') ?></td>
                        <td data-label="Durée"><?= esc((string) $duree) ?> jours</td>
                        <td data-label="Prix / jour (€)"><?= esc(number_format($prixParJour, 2, '.', '')) ?> €</td>
                        <td data-label="Impact"><?= $impact === null ? '—' : esc(number_format((float) $impact, 3, '.', '')) ?>
                            kg</td>
                        <td data-label="Coût estimé">
                            <?= $coutEstime === null ? '—' : esc(number_format((float) $coutEstime, 2, '.', '')) . ' €' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state card">
            <h2>Aucun régime acheté pour le moment</h2>
            <p>
                Quand vous achetez un régime + sport, il apparaîtra ici automatiquement.
            </p>
            <a class="btn btn-primary" href="<?= site_url('regime-sport') ?>">Découvrir les régimes</a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>