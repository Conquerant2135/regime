<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>

<div class="wallet-page">
    <h1>🔍 Debug: Enregistrements client_objectifs</h1>

    <section class="card">
        <h2>Objectifs disponibles</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($objectives as $obj): ?>
                    <tr>
                        <td><?= $obj['id'] ?></td>
                        <td><?= esc($obj['libelle']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Enregistrements client_objectifs (<?= count($records) ?> total)</h2>
        <?php if (empty($records)): ?>
            <p style="color: var(--muted); font-style: italic;">Aucun enregistrement pour le moment.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Objectif ID</th>
                        <th>Objectif</th>
                        <th>Date Choix</th>
                        <th>Action Poids</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= $record['client_id'] ?></td>
                            <td><?= $record['objectif_id'] ?></td>
                            <td><strong><?= esc($record['objectif_libelle']) ?></strong></td>
                            <td><?= $record['date_choix'] ?></td>
                            <td><?= $record['action_poids'] ?? '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>

<?= $this->endSection() ?>
