<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
Modifier Sport - Fitness Regime
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php /** @var object $sport */ ?>

<div class="dashboard-header">
    <h1>Modifier un sport</h1>
    <a href="<?= site_url('admin/sports') ?>" class="wallet-pill">Retour a la liste</a>
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

    <form action="<?= site_url('admin/sports/update/' . $sport->id) ?>" method="post" style="display:grid; gap:16px; max-width: 520px;">
        <?= csrf_field() ?>
        <div>
            <label for="libelle" style="display:block; margin-bottom:6px; color: var(--muted);">Nom du sport</label>
            <input type="text" name="libelle" id="libelle" maxlength="120" value="<?= old('libelle', $sport->libelle ?? '') ?>" required>
        </div>

        <div style="max-width: 260px;">
            <button class="btn-primary" type="submit">Enregistrer les modifications</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
