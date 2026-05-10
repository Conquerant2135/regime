<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
CRUD Sports - Fitness Regime
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="dashboard-header">
    <h1>CRUD Sports</h1>
    <span style="color: var(--muted); font-size: 14px;">Creation, recherche, mise a jour et suppression</span>
</div>

<?php $errors = session()->get('errors') ?? []; ?>
<?php $successMessage = session()->getFlashdata('success'); ?>
<?php $errorMessage = session()->getFlashdata('error'); ?>
<?php $sportsRoute = 'admin/sports'; ?>

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
    <span class="table-title">Creer un sport</span>

    <form action="<?= site_url($sportsRoute) ?>" method="post" style="display:grid; gap:16px; max-width: 520px;">
        <?= csrf_field() ?>
        <div>
            <label for="libelle" style="display:block; margin-bottom:6px; color: var(--muted);">Nom du sport</label>
            <input type="text" name="libelle" id="libelle" maxlength="120" value="<?= old('libelle') ?>" required>
        </div>

        <div style="max-width: 240px;">
            <button class="btn-primary" type="submit">Creer le sport</button>
        </div>
    </form>
</div>

<div class="table-section">
    <div class="dashboard-header" style="margin-bottom: 18px;">
        <span class="table-title" style="margin-bottom: 0;">Liste des sports</span>
        <form method="get" action="<?= site_url($sportsRoute) ?>" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="q" placeholder="Filtrer par nom du sport..." value="<?= esc($keyword ?? '') ?>" style="min-width: 260px;">
            <button type="submit" class="wallet-pill" style="cursor:pointer; background: rgba(42, 143, 214, 0.10); border: 1px solid rgba(42, 143, 214, 0.24);">Filtrer</button>
            <a href="<?= site_url($sportsRoute) ?>" class="wallet-pill">Reset</a>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="crosstab-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sports)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color: var(--muted);">Aucun sport trouve.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sports as $sport): ?>
                        <?php $sportLibelle = is_string($sport->libelle ?? null) ? $sport->libelle : ''; ?>
                        <?php $confirmLibelle = addslashes($sportLibelle); ?>
                        <tr>
                            <td><?= (int) $sport->id ?></td>
                            <td><?= htmlspecialchars((string) $sportLibelle, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                                    <a href="<?= site_url('admin/sports/update/' . $sport->id) ?>" class="wallet-pill" style="padding:8px 14px;">Update</a>
                                    <form action="<?= site_url('admin/sports/delete/' . $sport->id) ?>" method="post" onsubmit="return confirm('Etes-vous vraiment sur de vouloir supprimer le sport &quot;<?= esc($confirmLibelle) ?>&quot; ? Cette action est irreversible.');">
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
