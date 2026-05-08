<div class="card">
    <h1><?= esc($title ?? 'Couples régimes - sports') ?></h1>

    <!-- Affiche l'objectif de l'utilisateur de session -->
    <?php if ($selectedUser && $userObjectif): ?>
        <p class="objectif-info">
            <strong>Utilisateur :</strong> <?= esc($selectedUser['email'] ?? $selectedUser['nom']) ?>
            | <strong>Objectif :</strong> <?= esc($userObjectif) ?>
        </p>
    <?php elseif ($selectedUser && !$userObjectif): ?>
        <p class="objectif-info">
            <strong>Utilisateur :</strong> <?= esc($selectedUser['email'] ?? $selectedUser['nom']) ?>
            | <strong>Objectif :</strong> Aucun objectif défini
        </p>
    <?php endif; ?>


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

    <!-- Liste des couples régime-sport -->
    <?php if (!empty($regimesSports)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Régime</th>
                    <th>Sport</th>
                    <th>Impact journalier (kg)</th>
                    <th>Durée (jours)</th>
                    <th>Cout total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regimesSports as $i => $rs):
                    if (is_array($rs)) {
                        $regime_id = $rs['regime_id'] ?? null;
                        $sport_id = $rs['sport_id'] ?? null;
                        $objectif_id = $rs['objectif_id'] ?? ($selectedUser && session()->get('user_id') ? null : null);
                        $regime = $rs['regime_nom'] ?? $rs['regime'] ?? $rs['regime_label'] ?? '';
                        $sport = $rs['sport_libelle'] ?? $rs['sport'] ?? $rs['sport_label'] ?? '';
                        $impact = $rs['impact_journalier'] ?? null;
                        $duree = $rs['duree_jours'] ?? null;
                        $cout_total = $rs['cout_total'] ?? null;
                    } else {
                        $regime_id = $rs->regime_id ?? null;
                        $sport_id = $rs->sport_id ?? null;
                        $objectif_id = $rs->objectif_id ?? null;
                        $regime = $rs->regime_nom ?? $rs->regime ?? $rs->regime_label ?? '';
                        $sport = $rs->sport_libelle ?? $rs->sport ?? $rs->sport_label ?? '';
                        $impact = $rs->impact_journalier ?? null;
                        $duree = property_exists($rs, 'duree_jours') ? $rs->duree_jours : null;
                        $cout_total = property_exists($rs, 'cout_total') ? $rs->cout_total : null;
                    }
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($regime ?: '—') ?></td>
                        <td><?= esc($sport ?: '—') ?></td>
                        <td><?= esc($impact ?: '—') ?></td>
                        <td><?= $duree === null ? '—' : esc($duree) ?></td>
                        <td>
                            <?php if ($cout_total === null): ?>
                                —
                            <?php else: ?>
                                <?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €
                            <?php endif; ?>
                        </td>
                    <td>
                        <form method="post" action="/acheter_regime_sport">
                            <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                            <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                            <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                            <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                            <input type="hidden" name="prix" value="<?= esc((string)($cout_total ?? '')) ?>">
                            <input type="submit" value="Procéder au paiement" <?= (!$regime_id || !$sport_id) ? 'disabled' : '' ?>>
                        </form>
                    </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun couple régime-sport
            trouvé<?php echo ($selectedUser && $userObjectif) ? ' pour l\'objectif "' . esc($userObjectif) . '"' : ''; ?>.
        </p>
    <?php endif; ?>
</div>

<style>
    .objectif-info {
        margin: 15px 0;
        padding: 10px 15px;
        background-color: #e8f4f8;
        border-left: 4px solid #0066cc;
        border-radius: 4px;
    }
</style>