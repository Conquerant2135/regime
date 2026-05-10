<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>
Dashboard Admin - Fitness Régime
<?= $this->endSection() ?>

<!-- HEADER -->
<?= $this->section('content') ?>

<div class="dashboard-header">
    <h1>📊 Dashboard Admin</h1>
    <span style="color: var(--muted); font-size: 14px;">Bienvenue dans votre espace de gestion</span>
</div>

<!-- STATS CARDS -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Utilisateurs Totaux</span>
        <span class="stat-value"><?= $totalClient ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Mediane IMC</span>
        <span class="stat-value"><?= number_format($imcMedian, 2, ",", " ") ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Revenus totaux</span>
        <span class="stat-value">Ar <?= number_format($totalCA, 2, ",", " ") ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Revenu Moyen Par Utilisateur</span>
        <span class="stat-value">Ar <?= number_format($revenuMoyen, 2, ",", " ") ?></span>
    </div>
</div>

<!-- CHARTS SECTION -->
<div class="charts-grid">
    <!-- BAR CHART -->
    <div class="chart-card">
        <span class="chart-title">📈 Utilisateurs par Mois</span>
        <div class="chart-container">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <!-- LINE CHART -->
    <div class="chart-card">
        <span class="chart-title">📉 Évolution des Revenus</span>
        <div class="chart-container">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <!-- PIE CHART -->
    <div class="chart-card">
        <span class="chart-title">🥧 Distribution des clients par type de compte</span>
        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- DOUGHNUT CHART -->
    <div class="chart-card">
        <span class="chart-title">🎯 Repartition des clients suivant leur IMC</span>
        <div class="chart-container">
            <canvas id="doughnutChart"></canvas>
        </div>
    </div>
</div>

<!-- CROSSTAB TABLE -->
<div class="table-section">
    <span class="table-title"> 📋 Tableau Croisé: Clients x Objectifs </span>
    <div class="form-group">
        <label for="annee_repartition">Voir la repartition suivant l'annee</label>
        <select name="annee_repartition" id="annee_repartition">

        </select>
    </div>

    <div class="table-wrapper">
        <table class="crosstab-table">
            <div id="loading-container"></div>
            <thead id="table-head"></thead>
            <tbody id="table-body">
                <tr id="table-loading-row">
                    <td colspan="13" style="text-align:center; padding:20px; color:#64748b;">
                        Chargement des données...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script src="/assets/scripts/dashboard.js">

</script>

<script>
    document.addEventListener('DOMContentLoaded', async () => {

        loadUsersInscriptionChart();
        gainArgentParMoisChart();
        repartitionClientsParOption();
        repartitionClientsParIMC();

        const select = document.getElementById('annee_repartition');

        await loadAnneePresente();

        loadObjectifsTable(select.value);

        select.addEventListener('change', (e) => {
            loadObjectifsTable(e.target.value);
        });
    });
</script>

<?= $this->endSection() ?>