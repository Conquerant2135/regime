<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Codes Promo - Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-content">
    <header class="admin-header">
        <h1>Gestion des Codes Promo</h1>
        <a href="#" onclick="openCodeModal()" class="btn btn-add">+ Créer un code</a>
    </header>

    <?php if (session()->has('success')): ?>
        <div class="flash flash-success show">
            <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('errors') && is_array(session()->getFlashdata('errors'))): ?>
        <div class="flash flash-error show">
            <strong>❌ Erreurs :</strong>
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($codes)): ?>
        <div class="table-container card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Gain (€)</th>
                        <th>Utilisé</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($codes as $code): ?>
                        <tr>
                            <td><code class="code-badge"><?= $code['valeur'] ?></code></td>
                            <td><?= number_format((float) $code['gain'], 2) ?> €</td>
                            <td>
                                <span class="badge <?= $code['is_used'] ? 'badge-used' : 'badge-available' ?>">
                                    <?= $code['is_used'] ? '✓ Utilisé' : '○ Disponible' ?>
                                </span>
                            </td>
                            <td><?= !empty($code['date_creation']) ? date('d/m/Y H:i', strtotime($code['date_creation'])) : '—' ?></td>
                            <td class="table-actions">
                                <a href="<?= site_url('admin/codes/update/' . $code['id']) ?>" class="btn-icon btn-edit" title="Éditer">✏️</a>
                                <form method="post" action="<?= site_url('admin/codes/delete/' . $code['id']) ?>" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-icon btn-delete" title="Supprimer">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state card">
            <p>Aucun code promo pour le moment.</p>
            <a href="#" onclick="openCodeModal()" class="btn btn-add">Créer le premier code</a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de création -->
<div id="modal-code" class="modal-code">
    <div class="modal-content">
        <h2>Créer un code promo</h2>
        <form method="post" action="<?= site_url('admin/codes') ?>">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="valeur">Code</label>
                <input type="text" id="valeur" name="valeur" placeholder="Ex: PROMO10" 
                       value="<?= old('valeur') ?>" required>
                <?php if (isset($errors) && isset($errors['valeur'])): ?>
                    <small class="form-error"><?= $errors['valeur'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gain">Gain en Euro (€)</label>
                <input type="number" id="gain" name="gain" step="0.01" min="0" 
                       placeholder="10.00" value="<?= old('gain') ?>" required>
                <?php if (isset($errors) && isset($errors['gain'])): ?>
                    <small class="form-error"><?= $errors['gain'] ?></small>
                <?php endif; ?>
            </div>

            <div class="modal-buttons">
                <button type="button" class="btn btn-cancel" onclick="closeCodeModal()">Annuler</button>
                <button type="submit" class="btn btn-add">Créer le code</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCodeModal() {
        document.getElementById('modal-code').classList.add('show');
    }

    function closeCodeModal() {
        document.getElementById('modal-code').classList.remove('show');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modal-code');
        if (event.target === modal) {
            closeCodeModal();
        }
    }
</script>

<style>
    .code-badge {
        background-color: var(--primary-strong);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.9em;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .badge-available {
        background-color: rgba(42, 143, 214, 0.15);
        color: var(--primary);
    }

    .badge-used {
        background-color: rgba(217, 79, 79, 0.15);
        color: var(--danger);
    }

    .modal-code {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.2s;
    }

    .modal-code.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-code .modal-content {
        background-color: white;
        padding: 32px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        max-width: 400px;
        width: 90%;
        animation: slideUp 0.3s;
    }

    .modal-code h2 {
        margin-bottom: 20px;
        color: var(--text-primary);
    }

    .modal-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .modal-buttons .btn {
        flex: 1;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<?= $this->endSection() ?>
