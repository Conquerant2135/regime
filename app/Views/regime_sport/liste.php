<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>

<div class="regime-page">
    <section class="regime-hero card">
        <div class="regime-hero-copy">
            <span class="eyebrow">Catalogue des régimes</span>
            <h1><?= esc($title ?? 'Couples régimes - sports') ?></h1>
            <p>
                Parcourez les combinaisons régime + sport disponibles, comparez les prix et trouvez la solution la plus adaptée à votre objectif.
            </p>
        </div>

        <div class="regime-hero-stats">
            <div class="stat-box">
                <strong><?= esc((string) ($totalCombinaisons ?? count($regimesSports ?? []))) ?></strong>
                <span>Combinaisons</span>
            </div>
            <div class="stat-box">
                <strong><?= esc((string) ($priseCount ?? 0)) ?></strong>
                <span>Prise de poids</span>
            </div>
            <div class="stat-box">
                <strong><?= esc((string) ($perteCount ?? 0)) ?></strong>
                <span>Perte de poids</span>
            </div>
        </div>
    </section>

    <!-- Affiche l'objectif de l'utilisateur de session -->
    <?php if ($selectedUser && $userObjectif): ?>
        <p class="objectif-info">
            <strong>Utilisateur :</strong> <?= esc($selectedUser['email'] ?? $selectedUser['nom']) ?>
            | <strong>Objectif :</strong> <?= esc($userObjectif) ?>
        </p>
    <?php elseif ($selectedUser && !$userObjectif): ?>
        <p class="objectif-info">
            <strong>Utilisateur :</strong> <?= esc($selectedUser['email'] ?? $selectedUser['nom']) ?>
            | <strong>Objectif :</strong> Aucun objectif défini
        </p>
    <?php endif; ?>

    <!-- Sélecteur d'objectif si l'utilisateur n'en a pas -->
    <?php if ($showObjectiveSelector && !empty($allObjectives)): ?>
        <section class="objectif-selector card">
            <div class="objectif-selector-content">
                <div>
                    <span class="eyebrow">Sélectionnez votre objectif</span>
                    <h2>Quel est votre objectif ?</h2>
                    <p>Choisir un objectif pour voir les régimes et sports les plus adaptés à vos besoins.</p>
                </div>
                
                <form method="post" action="/regime-sport/set-objectif" class="objectif-form" id="objectifForm">
                    <?= csrf_field() ?>
                    <div class="objectif-radios">
                        <?php foreach ($allObjectives as $obj): ?>
                            <label class="radio-label">
                                <input 
                                    type="radio" 
                                    name="objectif_id" 
                                    value="<?= esc($obj['id']) ?>" 
                                    data-libelle="<?= esc($obj['libelle']) ?>"
                                    required>
                                <span class="radio-text"><?= esc($obj['libelle']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Champ Action Poids (affiché dynamiquement) -->
                    <div class="action-poids-container" id="actionPoidsContainer" style="display: none;">
                        <div class="form-group">
                            <label for="actionPoids">
                                Poids à <span id="actionLabel">gagner</span> (kg)
                            </label>
                            <input 
                                type="number" 
                                id="actionPoids" 
                                name="action_poids" 
                                placeholder="Ex: 5, 10, 15..." 
                                min="0.1" 
                                step="0.1"
                                class="input-poids">
                            <small>Indiquez le poids que vous souhaitez <span id="actionLabelSmall">gagner</span> en kg.</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Confirmer mon objectif
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('objectifForm');
                const radios = form.querySelectorAll('input[type="radio"]');
                const actionPoidsContainer = document.getElementById('actionPoidsContainer');
                const actionLabel = document.getElementById('actionLabel');
                const actionLabelSmall = document.getElementById('actionLabelSmall');
                const actionPoidsInput = document.getElementById('actionPoids');
                
                function toggleActionPoids() {
                    const selected = form.querySelector('input[type="radio"]:checked');
                    
                    if (selected) {
                        const libelle = selected.dataset.libelle.toLowerCase();
                        
                        // Affiche le champ si c'est perte ou gain de poids
                        if (libelle.includes('perte') || libelle.includes('gain') || libelle.includes('prise')) {
                            actionPoidsContainer.style.display = 'block';
                            actionPoidsInput.required = true;
                            
                            // Adapte le label selon le type
                            if (libelle.includes('perte')) {
                                actionLabel.textContent = 'perdre';
                                actionLabelSmall.textContent = 'perdre';
                            } else if (libelle.includes('gain') || libelle.includes('prise')) {
                                actionLabel.textContent = 'gagner';
                                actionLabelSmall.textContent = 'gagner';
                            }
                        } else {
                            actionPoidsContainer.style.display = 'none';
                            actionPoidsInput.required = false;
                            actionPoidsInput.value = '';
                        }
                    } else {
                        actionPoidsContainer.style.display = 'none';
                        actionPoidsInput.required = false;
                        actionPoidsInput.value = '';
                    }
                }
                
                // Événement au changement de radio
                radios.forEach(radio => {
                    radio.addEventListener('change', toggleActionPoids);
                });
                
                // Vérification initiale
                toggleActionPoids();
            });
        </script>
    <?php endif; ?>

    <section class="gold-banner">
        <div>
            <span class="eyebrow">Option Gold</span>
            <strong><?= esc(number_format((float) ($goldRemisePreview ?? ($goldOption['remise'] ?? 0)), 2, '.', '')) ?>% de remise sur tous les régimes</strong>
            <p>
                <?php if (!empty($goldOptionPrice)): ?>
                    Disponible pour <?= esc(number_format((float) $goldOptionPrice, 2, '.', '')) ?> €.
                <?php else: ?>
                    Activez Gold pour bénéficier de tarifs plus doux.
                <?php endif; ?>
            </p>
        </div>
        <?php if (empty($clientOption) || (isset($clientOption['libelle']) && strtolower((string) $clientOption['libelle']) !== 'gold')): ?>
            <form method="post" action="/options/souscrire-gold" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-gold">
                    Passer au Gold
                </button>
            </form>
        <?php endif; ?>
    </section>


    <?php if (session()->has('error')): ?>
        <div class="flash flash-error">
            <strong>❌ Erreur :</strong> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div class="flash flash-success">
            <strong>✅ Succès :</strong> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Liste des couples régime-sport -->
    <?php if (!empty($regimesSports)): ?>
        <table class="table regime-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Régime</th>
                    <th>Sport</th>
                    <th>Impact journalier (kg)</th>
                    <th>Durée (jours)</th>
                    <th>Cout total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regimesSports as $i => $rs):
                    if (is_array($rs)) {
                        $regime_id = $rs['regime_id'] ?? null;
                        $sport_id = $rs['sport_id'] ?? null;
                        $objectif_id = $rs['objectif_id'] ?? ($selectedUser && session()->get('user_id') ? null : null);
                        $regime = $rs['regime_nom'] ?? $rs['regime'] ?? $rs['regime_label'] ?? '';
                        $sport = $rs['sport_libelle'] ?? $rs['sport'] ?? $rs['sport_label'] ?? '';
                        $impact = $rs['impact_journalier'] ?? null;
                        $duree = $rs['duree_jours'] ?? null;
                        $cout_total = $rs['cout_total'] ?? null;
                        $cout_total_original = $rs['cout_total_original'] ?? null;
                        $cout_total_gold = $rs['cout_total_gold'] ?? null;
                        $gold_active = (bool) ($rs['gold_active'] ?? false);
                        $gold_remise = (float) ($rs['gold_remise'] ?? 0);
                    } else {
                        $regime_id = $rs->regime_id ?? null;
                        $sport_id = $rs->sport_id ?? null;
                        $objectif_id = $rs->objectif_id ?? null;
                        $regime = $rs->regime_nom ?? $rs->regime ?? $rs->regime_label ?? '';
                        $sport = $rs->sport_libelle ?? $rs->sport ?? $rs->sport_label ?? '';
                        $impact = $rs->impact_journalier ?? null;
                        $duree = property_exists($rs, 'duree_jours') ? $rs->duree_jours : null;
                        $cout_total = property_exists($rs, 'cout_total') ? $rs->cout_total : null;
                        $cout_total_original = property_exists($rs, 'cout_total_original') ? $rs->cout_total_original : null;
                        $cout_total_gold = property_exists($rs, 'cout_total_gold') ? $rs->cout_total_gold : null;
                        $gold_active = property_exists($rs, 'gold_active') ? (bool) $rs->gold_active : false;
                        $gold_remise = property_exists($rs, 'gold_remise') ? (float) $rs->gold_remise : 0;
                    }
                    ?>
                    <tr>
                        <td data-label="#"><?= $i + 1 ?></td>
                        <td data-label="Régime"><?= esc($regime ?: '—') ?></td>
                        <td data-label="Sport"><?= esc($sport ?: '—') ?></td>
                        <td data-label="Impact journalier (kg)"><?= esc($impact ?: '—') ?></td>
                        <td data-label="Durée (jours)"><?= $duree === null ? '—' : esc($duree) ?></td>
                        <td data-label="Cout total">
                            <?php if ($cout_total === null): ?>
                                —
                            <?php else: ?>
                                <?php if ($gold_active): ?>
                                    <?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €
                                <?php elseif ($cout_total_original !== null && $cout_total_gold !== null): ?>
                                    <span class="price-old">
                                        <?= esc(number_format((float) $cout_total_original, 2, '.', '')) ?> €
                                    </span>
                                    <strong class="price-now">
                                        <?= esc(number_format((float) $cout_total_gold, 2, '.', '')) ?> €
                                    </strong>
                                    <small class="price-note">
                                        -<?= esc(number_format($gold_remise, 2, '.', '')) ?>% avec Gold
                                    </small>
                                <?php else: ?>
                                    <?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td data-label="Action">
                            <?php if (empty($isLoggedIn)): ?>
                                <a class="btn btn-ghost" href="<?= site_url('login') ?>">Se connecter pour acheter</a>
                            <?php elseif ($gold_active): ?>
                                <form method="post" action="/acheter_regime_sport" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                    <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                    <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                    <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                    <input type="hidden" name="prix" value="<?= esc((string)($cout_total ?? '')) ?>">
                                    <input type="hidden" name="mode_achat" value="normal">
                                    <button type="submit" class="btn btn-primary" <?= (!$regime_id || !$sport_id) ? 'disabled' : '' ?>>
                                        Acheter avec Gold
                                        <?php if ($cout_total !== null): ?>
                                            (<?= esc(number_format((float) $cout_total, 2, '.', '')) ?> €)
                                        <?php endif; ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="/acheter_regime_sport" class="inline-form inline-form-gap">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                    <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                    <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                    <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                    <input type="hidden" name="prix" value="<?= esc((string)($cout_total_original ?? $cout_total ?? '')) ?>">
                                    <input type="hidden" name="mode_achat" value="normal">
                                    <button type="submit" class="btn btn-primary" <?= (!$regime_id || !$sport_id) ? 'disabled' : '' ?>>
                                        Acheter au prix normal
                                        <?php if ($cout_total_original !== null): ?>
                                            (<?= esc(number_format((float) $cout_total_original, 2, '.', '')) ?> €)
                                        <?php endif; ?>
                                    </button>
                                </form>

                                <form method="post" action="/acheter_regime_sport" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="regime_id" value="<?= esc((string)($regime_id ?? '')) ?>">
                                    <input type="hidden" name="sport_id" value="<?= esc((string)($sport_id ?? '')) ?>">
                                    <input type="hidden" name="objectif_id" value="<?= esc((string)($objectif_id ?? session()->get('user_id') ?? '')) ?>">
                                    <input type="hidden" name="duree" value="<?= esc((string)($duree ?? '')) ?>">
                                    <input type="hidden" name="prix" value="<?= esc((string)(isset($cout_total_gold) ? ((float)$goldOptionPrice + (float)$cout_total_gold) : '')) ?>">
                                    <input type="hidden" name="mode_achat" value="gold">
                                    <button type="submit" class="btn btn-ghost" disabled title="Abonnez-vous à Gold en haut de la page pour activer cette option">
                                        Acheter avec Gold (nécessite Gold)
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state card">
            <h2>Aucune combinaison trouvée</h2>
            <p>
                Aucun couple régime-sport n’est disponible<?php echo ($selectedUser && $userObjectif) ? ' pour l\'objectif "' . esc($userObjectif) . '"' : ''; ?>.
            </p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

