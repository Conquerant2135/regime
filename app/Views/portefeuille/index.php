<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>

<div class="wallet-page">
    <header class="wallet-header">
        <span class="eyebrow">Solde & rechargement</span>
        <h1>Mon Portefeuille</h1>
        <p>Consultez votre solde, rechargez avec un code et gardez un historique clair.</p>
    </header>

    <?php if (session()->has('error')): ?>
        <div class="flash flash-error show">
            <strong>❌ Erreur :</strong> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div class="flash flash-success show">
            <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div id="alert-message" class="alert"></div>

    <section class="wallet-card">
        <div class="solde-label">Solde actuel</div>
        <div class="solde-value" id="solde-display"><?= number_format((float) $solde, 2) ?> €</div>
        <p class="wallet-help-text">
            Le solde affiché ici est utilisé pour vos achats de régimes, de sport et d’options.
        </p>
        <div class="btn-group">
            <button class="btn btn-add" onclick="openCodeModal()">+ Ajouter de l'argent</button>
            <a class="btn btn-ghost" href="<?= site_url('mon-compte') ?>">Voir mon compte</a>
        </div>
    </section>

    <section class="history-section card">
        <div class="section-head">
            <h2>Historique des transactions</h2>
            <p>Retrouvez les crédits et débits liés à votre portefeuille.</p>
        </div>
        <?php if (!empty($historique)): ?>
            <div id="historique-list" class="history-list">
                <?php foreach ($historique as $transaction): ?>
                    <div class="transaction-row">
                        <div class="transaction-info">
                            <div class="transaction-type">
                                <?= ucfirst($transaction['type_transaction']) ?>
                            </div>
                            <div class="transaction-date">
                                <?= date('d/m/Y H:i', strtotime($transaction['date_mouvement'])) ?>
                            </div>
                        </div>
                        <div class="transaction-amount <?= $transaction['type_transaction'] === 'crédit' ? 'amount-credit' : 'amount-debit' ?>">
                            <?= $transaction['type_transaction'] === 'crédit' ? '+' : '-' ?><?= number_format($transaction['montant'], 2) ?> €
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">
                Aucune transaction pour le moment
            </div>
        <?php endif; ?>
    </section>
</div>

<div id="modal-code" class="modal-code">
    <div class="modal-content">
        <?= csrf_field() ?>
        <h2>Ajouter du crédit</h2>
        <p class="modal-help-text">
            Saisissez le code reçu par email ou SMS. Il sert à créditer votre portefeuille et votre solde sera mis à jour juste après validation.
        </p>
        <div id="code-log" class="modal-log modal-log-info" role="status" aria-live="polite">
            Entrez votre code puis cliquez sur "Utiliser le code".
        </div>
        <div class="form-group">
            <label for="code-input">Code de recharge</label>
            <input type="text" id="code-input" placeholder="Entrez votre code" class="code-input">
            <small class="form-note">Astuce: collez ici le code exactement comme il vous a été envoyé.</small>
        </div>
        <div class="modal-buttons">
            <button class="btn btn-cancel" onclick="closeCodeModal()">Annuler</button>
            <button class="btn btn-add" onclick="verifierCode()">Utiliser le code</button>
        </div>
    </div>
</div>

<script>
    function openCodeModal() {
        document.getElementById('modal-code').classList.add('show');
        setCodeLog('Entrez votre code puis cliquez sur "Utiliser le code".', 'info');
        document.getElementById('code-input').focus();
    }

    function closeCodeModal() {
        document.getElementById('modal-code').classList.remove('show');
        document.getElementById('code-input').value = '';
    }

    function verifierCode() {
        const code = document.getElementById('code-input').value.trim();

        if (!code) {
            setCodeLog('Veuillez saisir le code de recharge reçu avant de valider.', 'error');
            return;
        }

        setCodeLog('Vérification du code en cours, veuillez patienter...', 'info');

        // Récupérer le token CSRF depuis l'input caché
        const csrfToken = document.querySelector('input[name="csrf_test_name"]')?.value || '';

        if (!csrfToken) {
            setCodeLog('Erreur de sécurité: token CSRF manquant', 'error');
            return;
        }

        fetch('/portefeuille/utiliser-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => {
            // Vérifier le statut HTTP
            if (!response.ok && response.status !== 200) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                setCodeLog(data.message || 'Code appliqué avec succès!', 'success');

                document.getElementById('solde-display').textContent =
                    new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(parseFloat(data.nouveau_solde)) + ' €';

                addTransactionToList({
                    montant: data.montant,
                    date: new Date()
                });

                setTimeout(() => closeCodeModal(), 1500);
            } else {
                setCodeLog(data.message || 'Erreur lors de l\'application du code', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            setCodeLog('Erreur: ' + error.message, 'error');
        });
    }

    function setCodeLog(message, type) {
        const log = document.getElementById('code-log');
        if (!log) return;

        log.textContent = message;
        log.className = 'modal-log modal-log-' + type;
    }

    function addTransactionToList(transaction) {
        const list = document.getElementById('historique-list');
        if (!list) return;

        const montant = parseFloat(transaction.montant);
        const div = document.createElement('div');
        div.className = 'transaction-row';
        div.innerHTML = `
            <div class="transaction-info">
                <div class="transaction-type">Crédit</div>
                <div class="transaction-date">À l'instant</div>
            </div>
            <div class="transaction-amount amount-credit">
                +${montant.toFixed(2)} €
            </div>
        `;
        list.insertBefore(div, list.firstChild);
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modal-code');
        if (event.target === modal) {
            closeCodeModal();
        }
    }

    document.getElementById('code-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            verifierCode();
        }
    });
</script>

<?= $this->endSection() ?>
