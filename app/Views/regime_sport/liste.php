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

    <?php if (empty($clientOption) || (isset($clientOption['libelle']) && strtolower((string) $clientOption['libelle']) !== 'gold')): ?>
        <div style="margin: 15px 0; padding: 14px 16px; background: linear-gradient(135deg, #fff7e6, #fff1d6); border: 1px solid #f0c36d; border-radius: 8px;">
            <strong>Option Gold :</strong>
            <?= esc(number_format((float) ($goldRemisePreview ?? ($goldOption['remise'] ?? 0)), 2, '.', '')) ?>% de remise sur tous les régimes
            <?php if (!empty($goldOptionPrice)): ?>
                pour <?= esc(number_format((float) $goldOptionPrice, 2, '.', '')) ?> €.
            <?php endif; ?>
            <form method="post" action="/options/souscrire-gold" style="display:inline-block; margin-left:16px;">
                <button type="submit" style="background:#f0c36d;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;">
                    Passer au Gold (<?= esc(number_format((float) $goldOptionPrice, 2, '.', '')) ?> €)
                </button>
            </form>
        </div>
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
                        $cout_total_original = $rs['cout_total_original'] ?? null;
                        $cout_total_gold = $rs['cout_total_gold'] ?? null;
                        $gold_active = (bool) ($rs['gold_active'] ?? false);
                        $gold_remise = (float) ($rs['gold_remise'] ?? 0);
                    } else {
                        $regime_id = $rs->regime_id ?? null;
                        $sport_id = $rs->sport_id ?? null;
                        $objectif_id = $rs->objectif_id ?? null;
                        $regime = $rs->regime_nom ?? $rs->regime ?? $rs->regime_label ?? '';
                        $sport = $rs->sport_libelle ?? $rs->sport ?? $rs->sport_label ?? '';
                        $impact = $rs->impact_journalier ?? null;
                        $duree = property_exists($rs, 'duree_jours') ? $rs->duree_jours : null;
                        $cout_total = property_exists($rs, 'cout_total') ? $rs->cout_total : null;
                        $cout_total_original = property_exists($rs, 'cout_total_original') ? $rs->cout_total_original : null;
                        $cout_total_gold = property_exists($rs, 'cout_total_gold') ? $rs->cout_total_gold : null;
                        $gold_active = property_exists($rs, 'gold_active') ? (bool) $rs->gold_active : false;
                        $gold_remise = property_exists($rs, 'gold_remise') ? (float) $rs->gold_remise : 0;
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
                                <?php if ($gold_active): ?>
                                    <?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €
                                <?php elseif ($cout_total_original !== null && $cout_total_gold !== null): ?>
                                    <span style="text-decoration: line-through; color: #888; margin-right: 8px;">
                                        <?= esc(number_format((float) $cout_total_original, 2, '.', '')) ?> €
                                    </span>
                                    <strong>
                                        <?= esc(number_format((float) $cout_total_gold, 2, '.', '')) ?> €
                                    </strong>
                                    <small style="display:block; color:#2e7d32;">
                                        -<?= esc(number_format($gold_remise, 2, '.', '')) ?>% avec Gold
                                    </small>
                                <?php else: ?>
                                    <?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <td>
                        <?php if ($gold_active): ?>
                            <form method="post" action="/acheter_regime_sport" style="display:inline-block;">
                                <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                <input type="hidden" name="prix" value="<?= esc((string)($cout_total ?? '')) ?>">
                                <input type="hidden" name="mode_achat" value="normal">
                                <button type="submit" <?= (!$regime_id || !$sport_id) ? 'disabled' : '' ?>>
                                    Acheter avec Gold
                                    <?php if ($cout_total !== null): ?>
                                        (<?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €)
                                    <?php endif; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/acheter_regime_sport" style="display:inline-block; margin-right:8px;">
                                <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                <input type="hidden" name="prix" value="<?= esc((string)($cout_total_original ?? $cout_total ?? '')) ?>">
                                <input type="hidden" name="mode_achat" value="normal">
                                <button type="submit" <?= (!$regime_id || !$sport_id) ? 'disabled' : '' ?>>
                                    Acheter au prix normal
                                    <?php if ($cout_total_original !== null): ?>
                                        (<?= esc(number_format((float) $cout_total_original, 2, '.', '')) ?> €)
                                    <?php endif; ?>
                                </button>
                            </form>

                            <form method="post" action="/acheter_regime_sport" style="display:inline-block;">
                                <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                <input type="hidden" name="prix" value="<?= esc((string)(isset($cout_total_gold) ? ((float)$goldOptionPrice + (float)$cout_total_gold) : '')) ?>">
                                <input type="hidden" name="mode_achat" value="gold">
                                <button type="submit" disabled title="Abonnez-vous à Gold en haut de la page pour activer cette option">
                                    Acheter avec Gold (nécessite Gold)
                                </button>
                            </form>
                        <?php endif; ?>

                    </td>
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