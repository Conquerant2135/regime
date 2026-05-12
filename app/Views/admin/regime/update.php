<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
Modifier Regime - Fitness Regime
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php /** @var object $regime */ ?>

<div class="dashboard-header">
    <h1>Modifier un regime</h1>
    <a href="<?= site_url('admin/regimes') ?>" class="wallet-pill">Retour a la liste</a>
</div>

<?php $errors = session()->get('errors') ?? []; ?>
<?php $errorMessage = session()->getFlashdata('error'); ?>

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
    <span class="table-title">Formulaire de mise a jour</span>

    <form action="<?= site_url('admin/regimes/update/' . $regime->id) ?>" method="post" style="display:grid; gap:16px;">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div>
                <label for="nom" style="display:block; margin-bottom:6px; color: var(--muted);">Nom du regime</label>
                <input type="text" name="nom" id="nom" maxlength="255" value="<?= old('nom', $regime->nom ?? '') ?>" required>
            </div>
            <div>
                <label for="pourcentage_viande" style="display:block; margin-bottom:6px; color: var(--muted);">% viande</label>
                <input type="number" name="pourcentage_viande" id="pourcentage_viande" step="0.01" min="0" max="100" value="<?= old('pourcentage_viande', $regime->pourcentage_viande) ?>" required>
            </div>
            <div>
                <label for="pourcentage_volaille" style="display:block; margin-bottom:6px; color: var(--muted);">% volaille</label>
                <input type="number" name="pourcentage_volaille" id="pourcentage_volaille" step="0.01" min="0" max="100" value="<?= old('pourcentage_volaille', $regime->pourcentage_volaille) ?>" required>
            </div>
            <div>
                <label for="pourcentage_poisson" style="display:block; margin-bottom:6px; color: var(--muted);">% poisson</label>
                <input type="number" name="pourcentage_poisson" id="pourcentage_poisson" step="0.01" min="0" max="100" value="<?= old('pourcentage_poisson', $regime->pourcentage_poisson) ?>" required>
            </div>
            <div>
                <label for="prix_par_jour" style="display:block; margin-bottom:6px; color: var(--muted);">Prix par jour (€)</label>
                <input type="number" name="prix_par_jour" id="prix_par_jour" step="0.01" min="0" value="<?= old('prix_par_jour', $regime->prix_par_jour) ?>" required>
            </div>
            <div>
                <label for="impact_journalier" style="display:block; margin-bottom:6px; color: var(--muted);">Impact journalier</label>
                <input type="number" name="impact_journalier" id="impact_journalier" step="0.001" value="<?= old('impact_journalier', $regime->impact_journalier) ?>" required>
            </div>
        </div>

        <div style="max-width: 260px;">
            <button class="btn-primary" type="submit">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
