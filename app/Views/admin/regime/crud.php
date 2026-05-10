<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
CRUD Regimes - Fitness Regime
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="dashboard-header">
    <h1>CRUD Regimes</h1>
    <span style="color: var(--muted); font-size: 14px;">Creation, recherche, mise a jour et suppression</span>
</div>

<?php $errors = session()->get('errors') ?? []; ?>
<?php $successMessage = session()->getFlashdata('success'); ?>
<?php $errorMessage = session()->getFlashdata('error'); ?>
<?php $regimesRoute = 'admin/regimes'; ?>

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
    <span class="table-title">Creer un regime</span>

    <form action="<?= site_url($regimesRoute) ?>" method="post" style="display:grid; gap:16px;">
        <?= csrf_field() ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div>
                <label for="nom" style="display:block; margin-bottom:6px; color: var(--muted);">Nom du regime</label>
                <input type="text" name="nom" id="nom" maxlength="255" value="<?= old('nom') ?>" required>
            </div>
            <div>
                <label for="pourcentage_viande" style="display:block; margin-bottom:6px; color: var(--muted);">% viande</label>
                <input type="number" name="pourcentage_viande" id="pourcentage_viande" step="0.01" min="0" max="100" value="<?= old('pourcentage_viande') ?>" required>
            </div>
            <div>
                <label for="pourcentage_volaille" style="display:block; margin-bottom:6px; color: var(--muted);">% volaille</label>
                <input type="number" name="pourcentage_volaille" id="pourcentage_volaille" step="0.01" min="0" max="100" value="<?= old('pourcentage_volaille') ?>" required>
            </div>
            <div>
                <label for="pourcentage_poisson" style="display:block; margin-bottom:6px; color: var(--muted);">% poisson</label>
                <input type="number" name="pourcentage_poisson" id="pourcentage_poisson" step="0.01" min="0" max="100" value="<?= old('pourcentage_poisson') ?>" required>
            </div>
            <div>
                <label for="prix_par_jour" style="display:block; margin-bottom:6px; color: var(--muted);">Prix par jour</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" step="0.01" min="0" value="<?= old('prix_par_jour') ?>" required>
            </div>
            <div>
                <label for="impact_journalier" style="display:block; margin-bottom:6px; color: var(--muted);">Impact journalier</label>
                <input type="number" name="impact_journalier" id="impact_journalier" step="0.001" value="<?= old('impact_journalier') ?>" required>
            </div>
        </div>

        <div style="max-width: 240px;">
            <button class="btn-primary" type="submit">Creer le regime</button>
        </div>
    </form>
</div>

<div class="table-section">
    <div class="dashboard-header" style="margin-bottom: 18px;">
        <span class="table-title" style="margin-bottom: 0;">Liste des regimes</span>
        <form method="get" action="<?= site_url($regimesRoute) ?>" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="q" placeholder="Filtrer par nom du regime..." value="<?= esc($keyword ?? '') ?>" style="min-width: 260px;">
            <button type="submit" class="wallet-pill" style="cursor:pointer; background: rgba(42, 143, 214, 0.10); border: 1px solid rgba(42, 143, 214, 0.24);">Filtrer</button>
            <a href="<?= site_url($regimesRoute) ?>" class="wallet-pill">Reset</a>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="crosstab-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>% viande</th>
                    <th>% volaille</th>
                    <th>% poisson</th>
                    <th>Prix / jour</th>
                    <th>Impact / jour</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($regimes)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color: var(--muted);">Aucun regime trouve.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($regimes as $regime): ?>
                        <?php $regimeNom = is_string($regime->nom ?? null) ? $regime->nom : ''; ?>
                        <?php $confirmNom = addslashes($regimeNom); ?>
                        <tr>
                            <td><?= (int) $regime->id ?></td>
                            <td><?= htmlspecialchars((string) $regimeNom, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((float) $regime->pourcentage_viande, 2, ',', ' ') ?></td>
                            <td><?= number_format((float) $regime->pourcentage_volaille, 2, ',', ' ') ?></td>
                            <td><?= number_format((float) $regime->pourcentage_poisson, 2, ',', ' ') ?></td>
                            <td><?= number_format((float) $regime->prix_par_jour, 2, ',', ' ') ?></td>
                            <td><?= number_format((float) $regime->impact_journalier, 3, ',', ' ') ?></td>
                            <td>
                                <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                                    <a href="<?= site_url('admin/regimes/update/' . $regime->id) ?>" class="wallet-pill" style="padding:8px 14px;">Update</a>
                                    <form action="<?= site_url('admin/regimes/delete/' . $regime->id) ?>" method="post" onsubmit="return confirm('Etes-vous vraiment sur de vouloir supprimer le regime &quot;<?= esc($confirmNom) ?>&quot; ? Cette action est irreversible.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="wallet-pill" style="padding:8px 14px; cursor:pointer; border-color: rgba(217,79,79,0.35); color: var(--danger); background: rgba(217,79,79,0.08);">Delete</button>
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
