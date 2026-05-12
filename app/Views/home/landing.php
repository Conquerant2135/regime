<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>
<section class="hero-section">
    <div class="hero-copy">
        <span class="eyebrow">Votre parcours, plus simple</span>
        <h1>Un espace accueillant pour suivre votre régime et votre progression.</h1>
        <p style="margin-top: 25px">
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
        <p>Choisissez le régime qui correspond à vos besoins et atteignez vos objectifs fitness.</p>
    </div>

    <div class="category-grid">
        <article class="category-card">
            <div class="category-image">
                <img src="<?= base_url('assets/images/gain.jpg') ?>" alt="Prise de poids progressive" loading="lazy">
                <div class="category-overlay"></div>
                <span class="category-badge">+ Poids</span>
            </div>
            <div class="category-content">
                <h3>Prise de Poids</h3>
                <p>Des programmes équilibrés pour reprendre du poids progressivement et sainement.</p>
                <a href="<?= site_url('regime-sport') ?>" class="category-link">Découvrir →</a>
            </div>
        </article>

        <article class="category-card">
            <div class="category-image">
                <img src="<?= base_url('assets/images/perte.jpg') ?>" alt="Perte de poids efficace" loading="lazy">
                <div class="category-overlay"></div>
                <span class="category-badge">- Poids</span>
            </div>
            <div class="category-content">
                <h3>Perte de Poids</h3>
                <p>Des régimes orientés perte de poids, avec un suivi clair et des résultats progressifs.</p>
                <a href="<?= site_url('regime-sport') ?>" class="category-link">Découvrir →</a>
            </div>
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
<section class="testimonials-section">
    <div class="section-head">
        <span class="eyebrow">Témoignages</span>
        <h2>Leurs succès, notre fierté</h2>
        <p>Des utilisateurs qui ont transformé leur parcours fitness grâce à notre plateforme.</p>
    </div>

    <div class="testimonials-grid">
        <article class="testimonial-card">
            <div class="testimonial-content">
                <p class="testimonial-text">
                    "J'ai enfin trouvé une application simple et efficace pour suivre mes objectifs. L'interface est très intuitive et l'équipe est réactive."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">MD</div>
                    <div>
                        <strong>Marie Dupont</strong>
                        <span class="author-meta">Perte de poids • 8 mois</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
        </article>

        <article class="testimonial-card">
            <div class="testimonial-content">
                <p class="testimonial-text">
                    "Les régimes proposés correspondent vraiment à mes besoins. J'ai pu progresser à mon rythme sans stress."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">JM</div>
                    <div>
                        <strong>Jean Martin</strong>
                        <span class="author-meta">Prise de poids • 5 mois</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
        </article>

        <article class="testimonial-card">
            <div class="testimonial-content">
                <p class="testimonial-text">
                    "L'option Gold vaut vraiment le coup ! Les réductions et les fonctionnalités premium m'ont vraiment aidé à accélérer mes résultats."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">SB</div>
                    <div>
                        <strong>Sophie Bernard</strong>
                        <span class="author-meta">Perte de poids • Membre Gold</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
        </article>

        <article class="testimonial-card">
            <div class="testimonial-content">
                <p class="testimonial-text">
                    "L'équipe a été très attentive à mes retours. C'est rare de voir une appli aussi responsive et centrée sur l'utilisateur."
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">PL</div>
                    <div>
                        <strong>Pierre Leclerc</strong>
                        <span class="author-meta">Prise de poids • 12 mois</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-rating">⭐⭐⭐⭐⭐</div>
        </article>
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