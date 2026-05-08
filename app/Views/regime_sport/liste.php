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
                        $regime = $rs['regime_nom'] ?? $rs['regime'] ?? $rs['regime_label'] ?? '';
                        $sport = $rs['sport_libelle'] ?? $rs['sport'] ?? $rs['sport_label'] ?? '';
                        $impact = $rs['impact_journalier'] ?? null;
                        $duree = $rs['duree_jours'] ?? null;
                        $cout_total = $rs['cout_total'] ?? null;
                    } else {
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
                                <?= esc(number_format((float) $cout_total, 2, '.', '')) ?>
                            <?php endif; ?>
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