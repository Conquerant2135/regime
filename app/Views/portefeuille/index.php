<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Portefeuille</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .solde-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .solde-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .solde-value {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        button:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-add {
            background: #4CAF50;
        }
        .btn-add:hover {
            background: #45a049;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }
        .alert.error {
            background: #ffebee;
            color: #d32f2f;
            border: 2px solid #d32f2f;
        }
        .alert.success {
            background: #e8f5e9;
            color: #388e3c;
            border: 2px solid #388e3c;
        }
        .alert.show {
            display: block;
        }

        .modal-code {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            animation: fadeIn 0.3s ease;
        }
        .modal-code.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 90%;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-cancel {
            background: #ccc;
        }
        .btn-cancel:hover {
            background: #999;
        }

        .historique {
            margin-top: 40px;
        }
        .historique h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .transaction {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }
        .transaction:hover {
            background: #f5f5f5;
        }
        .transaction:last-child {
            border-bottom: none;
        }
        .transaction-info {
            flex: 1;
        }
        .transaction-type {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .transaction-date {
            font-size: 12px;
            color: #999;
        }
        .transaction-amount {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        .amount-credit {
            color: #4CAF50;
        }
        .amount-debit {
            color: #d32f2f;
        }
        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Mon Portefeuille</h1>

        <?php if (session()->has('error')): ?>
            <div class="alert error show">
                <strong>❌ Erreur :</strong> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->has('success')): ?>
            <div class="alert success show">
                <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div id="alert-message" class="alert"></div>

        <div class="solde-box">
            <div class="solde-label">Solde actuel</div>
            <div class="solde-value" id="solde-display"><?= number_format($solde, 2) ?> €</div>
            <div class="btn-group">
                <button class="btn-add" onclick="openCodeModal()">+ Ajouter de l'argent</button>
            </div>
        </div>

        <div class="historique">
            <h2>📊 Historique des transactions</h2>
            <?php if (!empty($historique)): ?>
                <div id="historique-list">
                    <?php foreach ($historique as $transaction): ?>
                        <div class="transaction">
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
        </div>
    </div>

    <!-- Modal pour saisir le code -->
    <div id="modal-code" class="modal-code">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Ajouter du crédit</h2>
            <div class="form-group">
                <label for="code-input">Nous vous avons envoyé un CODE Veuillez le saisir ici :</label>
                <input type="text" id="code-input" placeholder="Entrez votre code" style="text-transform: uppercase;">
            </div>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeCodeModal()">Annuler</button>
                <button onclick="verifierCode()">Utiliser le code</button>
            </div>
        </div>
    </div>

    <script>
        function openCodeModal() {
            document.getElementById('modal-code').classList.add('show');
            document.getElementById('code-input').focus();
        }

        function closeCodeModal() {
            document.getElementById('modal-code').classList.remove('show');
            document.getElementById('code-input').value = '';
        }

        function verifierCode() {
            const code = document.getElementById('code-input').value.trim();
            
            if (!code) {
                showAlert('Veuillez entrer un code', 'error');
                return;
            }

            // Appel AJAX
            fetch('/portefeuille/utiliser-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Mettre à jour le solde
                    document.getElementById('solde-display').textContent = 
                        new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(parseFloat(data.nouveau_solde)) + ' €';
                    
                    // Ajouter la transaction à l'historique
                    addTransactionToList({
                        type: 'credit',
                        montant: data.montant,
                        date: new Date()
                    });
                    
                    closeCodeModal();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Erreur: ' + error, 'error');
            });
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert-message');
            alert.textContent = message;
            alert.className = 'alert ' + type + ' show';
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        function addTransactionToList(transaction) {
            const list = document.getElementById('historique-list');
            if (!list) return;
            
            const montant = parseFloat(transaction.montant);
            const div = document.createElement('div');
            div.className = 'transaction';
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

        // Fermer modal en cliquant en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('modal-code');
            if (event.target === modal) {
                closeCodeModal();
            }
        }

        // Appuyer sur Entrée pour valider
        document.getElementById('code-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifierCode();
            }
        });
    </script>
</body>
</html>
