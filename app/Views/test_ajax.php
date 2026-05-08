<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX Debug</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        button { padding: 10px 20px; margin: 10px 0; cursor: pointer; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Test AJAX Request</h1>
    
    <button onclick="testAjax()">Tester la requête AJAX</button>
    
    <h2>Résultat du serveur:</h2>
    <pre id="result">En attente...</pre>

    <script>
        function testAjax() {
            const code = "PROMO10";
            
            console.log("Envoi de la requête AJAX...");
            console.log("URL: /portefeuille/utiliser-code");
            console.log("Payload:", { code: code });
            
            fetch('/portefeuille/utiliser-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => {
                console.log("Status:", response.status);
                console.log("Headers:", {
                    'content-type': response.headers.get('content-type')
                });
                return response.json();
            })
            .then(data => {
                console.log("Response data:", data);
                document.getElementById('result').innerHTML = 
                    '<span class="' + (data.success ? 'success' : 'error') + '">' + 
                    JSON.stringify(data, null, 2) + 
                    '</span>';
            })
            .catch(error => {
                console.error("Erreur:", error);
                document.getElementById('result').innerHTML = 
                    '<span class="error">Erreur: ' + error + '</span>';
            });
        }
    </script>
</body>
</html>
