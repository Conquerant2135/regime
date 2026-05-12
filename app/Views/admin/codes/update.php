<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Éditer Code Promo - Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-content">
    <header class="admin-header">
        <h1>Éditer le code promo</h1>
        <a href="<?= site_url('admin/codes') ?>" class="btn btn-ghost">← Retour</a>
    </header>

    <div class="form-card card">
        <form method="post" action="<?= site_url('admin/codes/update/' . $code['id']) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="valeur">Code</label>
                <input type="text" id="valeur" name="valeur" placeholder="Ex: PROMO10"
                       value="<?= old('valeur', $code['valeur']) ?>" required>
                <?php if (isset($errors) && isset($errors['valeur'])): ?>
                    <small class="form-error"><?= $errors['valeur'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gain">Gain en Euro (€)</label>
                <input type="number" id="gain" name="gain" step="0.01" min="0"
                       placeholder="10.00" value="<?= old('gain', $code['gain']) ?>" required>
                <?php if (isset($errors) && isset($errors['gain'])): ?>
                    <small class="form-error"><?= $errors['gain'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="is_used">Statut</label>
                <select id="is_used" name="is_used" disabled>
                    <option value="0" <?= $code['is_used'] == 0 ? 'selected' : '' ?>>Disponible</option>
                    <option value="1" <?= $code['is_used'] == 1 ? 'selected' : '' ?>>Utilisé</option>
                </select>
                <small class="form-note">Le statut ne peut être modifié directement. Un code est marqué comme utilisé quand un client l'applique.</small>
            </div>

            <div class="form-group">
                <label for="date_creation">Créé le</label>
                <input type="text" id="date_creation" value="<?= !empty($code['date_creation']) ? date('d/m/Y H:i', strtotime($code['date_creation'])) : '—' ?>" disabled>
                <small class="form-note">Date de création du code (non modifiable)</small>
            </div>

            <div class="form-actions">
                <a href="<?= site_url('admin/codes') ?>" class="btn btn-ghost">Annuler</a>
                <button type="submit" class="btn btn-add">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-card {
        max-width: 500px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1em;
        font-family: inherit;
    }

    .form-group input:disabled,
    .form-group select:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .form-note {
        display: block;
        margin-top: 6px;
        font-size: 0.85em;
        color: #666;
    }

    .form-error {
        display: block;
        margin-top: 6px;
        color: var(--danger);
        font-size: 0.85em;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .form-actions .btn {
        flex: 1;
    }
</style>

<?= $this->endSection() ?>
