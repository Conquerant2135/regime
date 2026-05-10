<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>
<section class="hero-section">
    <div class="hero-copy">
        <span class="eyebrow">Votre parcours, plus simple</span>
        <h1>Un espace accueillant pour suivre votre régime et votre progression.</h1>
        <p>
            Découvrez des régimes adaptés à votre objectif, calculez votre IMC en quelques secondes et gardez votre
            compte à portée de main.
        </p>

        <div class="hero-stats">
            <div>
                <strong><?= esc((string) $regimesPrise) ?></strong>
                <span>Régimes prise de poids</span>
            </div>
            <div>
                <strong><?= esc((string) $regimesPerte) ?></strong>
                <span>Régimes perte de poids</span>
            </div>
        </div>
    </div>

    <div class="hero-card">
        <h2>Calculer mon IMC</h2>
        <p>Renseignez vos informations pour obtenir une estimation rapide.</p>

        <form id="imc-form" class="imc-form">
            <?= csrf_field() ?>
            <label>
                Poids (kg)
                <input type="number" name="poids" min="1" step="0.1" placeholder="Ex. 72">
            </label>
            <label>
                Taille (cm ou m)
                <input type="number" name="taille" min="1" step="0.01" placeholder="Ex. 175">
            </label>
            <button type="submit" class="btn-primary">Calculer mon IMC</button>
        </form>

        <div id="imc-result" class="imc-result">Votre résultat apparaîtra ici.</div>
    </div>
</section>

<section class="category-section">
    <div class="section-head">
        <span class="eyebrow">Catégories</span>
        <h2>Des solutions pour chaque objectif</h2>
    </div>

    <div class="category-grid">
        <article class="category-card">
            <h3>+ Poids</h3>
            <p>Des programmes équilibrés pour reprendre du poids progressivement.</p>
        </article>
        <article class="category-card">
            <h3>- Poids</h3>
            <p>Des régimes orientés perte de poids, avec un suivi clair et progressif.</p>
        </article>
    </div>
</section>

<section class="extra-section">
    <div class="extra-card">
        <h2>Pourquoi c’est plus confortable ?</h2>
        <p>
            Une interface plus douce, plus lisible, avec des repères simples pour avancer sans stress.
        </p>
    </div>
</section>

<script>
    (() => {
        const form = document.getElementById('imc-form');
        const result = document.getElementById('imc-result');

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            result.classList.remove('error');
            result.textContent = 'Calcul en cours...';

            try {
                const response = await fetch('<?= site_url('imc/calculer') ?>', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await response.json();

                if (!data.success) {
                    result.classList.add('error');
                    result.textContent = data.message || 'Impossible de calculer l’IMC.';
                    return;
                }

                result.innerHTML = `<strong>IMC :</strong> ${data.imc} - <span>${data.categorie}</span>`;
            } catch (error) {
                result.classList.add('error');
                result.textContent = 'Impossible de calculer l’IMC pour le moment.';
            }
        });
    })();
</script>
<?= $this->endSection() ?>