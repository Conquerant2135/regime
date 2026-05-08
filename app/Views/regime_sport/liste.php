<div class="card">
    <h1><?= esc($title ?? 'Couples régimes - sports') ?></h1>

    <?php if (!empty($regimesSports)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Régime</th>
                    <th>Sport</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regimesSports as $i => $rs):
                    if (is_array($rs)) {
                        $regime = $rs['regime_nom'] ?? $rs['regime'] ?? $rs['regime_label'] ?? '';
                        $sport = $rs['sport_libelle'] ?? $rs['sport'] ?? $rs['sport_label'] ?? '';
                    } else {
                        $regime = $rs->regime_nom ?? $rs->regime ?? $rs->regime_label ?? '';
                        $sport = $rs->sport_libelle ?? $rs->sport ?? $rs->sport_label ?? '';
                    }
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($regime ?: '—') ?></td>
                        <td><?= esc($sport ?: '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun couple régime-sport trouvé.</p>
    <?php endif; ?>
</div>