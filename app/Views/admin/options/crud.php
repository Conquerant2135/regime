<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
CRUD Options - Fitness Regime
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="dashboard-header">
    <h1>CRUD Options</h1>
    <span style="color: var(--muted); font-size: 14px;">Gestion des options d'abonnement</span>
</div>

<?php $errors = session()->get('errors') ?? []; ?>
<?php $successMessage = session()->getFlashdata('success'); ?>
<?php $errorMessage = session()->getFlashdata('error'); ?>
<?php $optionsRoute = 'admin/options'; ?>

<?php if (is_string($successMessage) && $successMessage !== ''): ?>
    <div class="table-section" style="border-left: 4px solid var(--primary-strong);">
        <span style="color: var(--primary-strong); font-weight: 600;"><?= esc($successMessage) ?></span>
    </div>
<?php endif; ?>

<?php if (is_string($errorMessage) && $errorMessage !== ''): ?>
    <div class="table-section" style="border-left: 4px solid var(--danger);">
        <span style="color: var(--danger); font-weight: 600;"><?= esc($errorMessage) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="table-section" style="border-left: 4px solid var(--danger);">
        <span class="table-title" style="margin-bottom: 10px;">Erreurs de validation</span>
        <ul style="padding-left: 18px; color: var(--danger); line-height: 1.6;">
            <?php foreach ($errors as $error): ?>
                <?php if (is_string($error) && $error !== ''): ?>
                    <li><?= esc($error) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="table-section">
    <span class="table-title">Creer une option</span>

    <form action="<?= site_url($optionsRoute) ?>" method="post" style="display:grid; gap:16px;">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div>
                <label for="libelle" style="display:block; margin-bottom:6px; color: var(--muted);">Libelle</label>
                <input type="text" name="libelle" id="libelle" maxlength="120" value="<?= old('libelle') ?>" required>
            </div>
            <div>
                <label for="remise" style="display:block; margin-bottom:6px; color: var(--muted);">Remise (%)</label>
                <input type="number" name="remise" id="remise" step="0.01" min="0" max="100"
                    value="<?= old('remise') ?>" required>
            </div>
            <div>
                <label for="prix_option" style="display:block; margin-bottom:6px; color: var(--muted);">Prix
                    option</label>
                <input type="number" name="prix_option" id="prix_option" step="0.01" min="0"
                    value="<?= old('prix_option') ?>" required>
            </div>
        </div>

        <div style="max-width: 240px;">
            <button class="btn-primary" type="submit">Creer l'option</button>
        </div>
    </form>
</div>

<div class="table-section">
    <div class="dashboard-header" style="margin-bottom: 18px;">
        <span class="table-title" style="margin-bottom: 0;">Liste des options</span>
        <form method="get" action="<?= site_url($optionsRoute) ?>" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="q" placeholder="Filtrer par libelle..." value="<?= esc($keyword ?? '') ?>"
                style="min-width: 260px;">
            <button type="submit" class="wallet-pill"
                style="cursor:pointer; background: rgba(42, 143, 214, 0.10); border: 1px solid rgba(42, 143, 214, 0.24);">Filtrer</button>
            <a href="<?= site_url($optionsRoute) ?>" class="wallet-pill">Reset</a>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="crosstab-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libelle</th>
                    <th>Remise</th>
                    <th>Prix option</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($options)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color: var(--muted);">Aucune option trouvee.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($options as $option): ?>
                        <?php $optionLibelle = is_string($option['libelle'] ?? null) ? $option['libelle'] : ''; ?>
                        <?php $confirmLibelle = addslashes($optionLibelle); ?>
                        <tr>
                            <form action="<?= site_url('admin/options/update/' . $option['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <td><?= (int) $option['id'] ?></td>
                                <td><input type="text" name="libelle" value="<?= esc($optionLibelle) ?>" maxlength="120"
                                        required style="width: 100%;"></td>
                                <td><input type="number" name="remise" step="0.01" min="0" max="100"
                                        value="<?= esc((string) $option['remise']) ?>" required style="width: 120px;"></td>
                                <td><input type="number" name="prix_option" step="0.01" min="0"
                                        value="<?= esc((string) $option['prix_option']) ?>" required style="width: 140px;"></td>
                                <td>
                                    <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                                        <button type="submit" class="wallet-pill"
                                            style="padding:8px 14px; cursor:pointer;">Save</button>
                            </form>
                            <form action="<?= site_url('admin/options/delete/' . $option['id']) ?>" method="post"
                                onsubmit="return confirm('Etes-vous vraiment sur de vouloir supprimer l\'option &quot;<?= esc($confirmLibelle) ?>&quot; ? Cette action est irreversible.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="wallet-pill"
                                    style="padding:8px 14px; cursor:pointer; border-color: rgba(217,79,79,0.35); color: var(--danger); background: rgba(217,79,79,0.08);">Delete</button>
                            </form>
            </div>
            </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    </table>
</div>
</div>

<?= $this->endSection() ?>