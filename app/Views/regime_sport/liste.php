<div class="card">
    <h1><?= esc($title ?? 'Couples régimes - sports') ?></h1>

    <!-- Filtre utilisateur -->
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label for="user_filter">Sélectionner un utilisateur :</label>
            <select name="user_id" id="user_filter" class="form-control" onchange="this.form.submit()">
                <option value="">-- Tous les couples --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= ($selectedUserId == $user['id']) ? 'selected' : '' ?>>
                        <?= esc($user['email'] ?? $user['nom'] ?? 'Utilisateur') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Affiche l'objectif de l'utilisateur sélectionné -->
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
                    <?php if ($selectedUser): ?>
                        <th>Durée (jours)</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regimesSports as $i => $rs):
                    if (is_array($rs)) {
                        $regime = $rs['regime_nom'] ?? $rs['regime'] ?? $rs['regime_label'] ?? '';
                        $sport = $rs['sport_libelle'] ?? $rs['sport'] ?? $rs['sport_label'] ?? '';
                        $impact = $rs['impact_journalier'] ?? null;
                        $duree = $rs['duree_jours'] ?? null;
                    } else {
                        $regime = $rs->regime_nom ?? $rs->regime ?? $rs->regime_label ?? '';
                        $sport = $rs->sport_libelle ?? $rs->sport ?? $rs->sport_label ?? '';
                        $impact = $rs->impact_journalier ?? null;
                        $duree = property_exists($rs, 'duree_jours') ? $rs->duree_jours : null;
                    }
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($regime ?: '—') ?></td>
                        <td><?= esc($sport ?: '—') ?></td>
                        <td><?= esc($impact ?: '—') ?></td>
                        <?php if ($selectedUser): ?>
                            <td><?= $duree === null ? '—' : esc($duree) ?></td>
                        <?php endif; ?>
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
    .filter-form {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 4px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .objectif-info {
        margin: 15px 0;
        padding: 10px 15px;
        background-color: #e8f4f8;
        border-left: 4px solid #0066cc;
        border-radius: 4px;
    }
</style>