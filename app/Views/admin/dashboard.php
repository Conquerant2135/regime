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
        <span class="stat-value">1,234</span>
        <span class="stat-change positive">↑ 12% ce mois</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Régimes Actifs</span>
        <span class="stat-value">856</span>
        <span class="stat-change positive">↑ 8% ce mois</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Revenus Totaux</span>
        <span class="stat-value">€12,450</span>
        <span class="stat-change positive">↑ 5% ce mois</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Taux de Satisfaction</span>
        <span class="stat-value">94%</span>
        <span class="stat-change negative">↓ 2% ce mois</span>
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
        <span class="chart-title">🥧 Distribution des Régimes</span>
        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- DOUGHNUT CHART -->
    <div class="chart-card">
        <span class="chart-title">🎯 Statut des Régimes</span>
        <div class="chart-container">
            <canvas id="doughnutChart"></canvas>
        </div>
    </div>
</div>

<!-- CROSSTAB TABLE -->
<div class="table-section">
    <span class="table-title">📋 Tableau Croisé: Utilisateurs vs Régimes</span>
    <div class="table-wrapper">
        <table class="crosstab-table">
            <thead>
                <tr>
                    <th>Régime / Mois</th>
                    <th class="table-center">Janvier</th>
                    <th class="table-center">Février</th>
                    <th class="table-center">Mars</th>
                    <th class="table-center">Avril</th>
                    <th class="table-center">Mai</th>
                    <th class="table-center">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Cétogène</strong></td>
                    <td class="table-center"><span class="table-value">145</span></td>
                    <td class="table-center"><span class="table-value">152</span></td>
                    <td class="table-center"><span class="table-value">168</span></td>
                    <td class="table-center"><span class="table-value">185</span></td>
                    <td class="table-center"><span class="table-value">198</span></td>
                    <td class="table-center"><strong style="color: var(--primary-strong);">848</strong></td>
                </tr>
                <tr>
                    <td><strong>Paléo</strong></td>
                    <td class="table-center"><span class="table-value">98</span></td>
                    <td class="table-center"><span class="table-value">105</span></td>
                    <td class="table-center"><span class="table-value">112</span></td>
                    <td class="table-center"><span class="table-value">124</span></td>
                    <td class="table-center"><span class="table-value">136</span></td>
                    <td class="table-center"><strong style="color: var(--primary-strong);">575</strong></td>
                </tr>
                <tr>
                    <td><strong>Végétarien</strong></td>
                    <td class="table-center"><span class="table-value">210</span></td>
                    <td class="table-center"><span class="table-value">225</span></td>
                    <td class="table-center"><span class="table-value">242</span></td>
                    <td class="table-center"><span class="table-value">265</span></td>
                    <td class="table-center"><span class="table-value">284</span></td>
                    <td class="table-center"><strong style="color: var(--primary-strong);">1,226</strong></td>
                </tr>
                <tr>
                    <td><strong>Vegan</strong></td>
                    <td class="table-center"><span class="table-value">167</span></td>
                    <td class="table-center"><span class="table-value">178</span></td>
                    <td class="table-center"><span class="table-value">192</span></td>
                    <td class="table-center"><span class="table-value">215</span></td>
                    <td class="table-center"><span class="table-value">238</span></td>
                    <td class="table-center"><strong style="color: var(--primary-strong);">990</strong></td>
                </tr>
                <tr>
                    <td><strong>Sans Gluten</strong></td>
                    <td class="table-center"><span class="table-value">84</span></td>
                    <td class="table-center"><span class="table-value">91</span></td>
                    <td class="table-center"><span class="table-value">103</span></td>
                    <td class="table-center"><span class="table-value">118</span></td>
                    <td class="table-center"><span class="table-value">132</span></td>
                    <td class="table-center"><strong style="color: var(--primary-strong);">528</strong></td>
                </tr>
                <tr style="background: rgba(42, 143, 214, 0.08); font-weight: 600;">
                    <td><strong>TOTAL</strong></td>
                    <td class="table-center">704</td>
                    <td class="table-center">751</td>
                    <td class="table-center">817</td>
                    <td class="table-center">907</td>
                    <td class="table-center">988</td>
                    <td class="table-center" style="color: var(--primary); font-size: 16px;">4,167</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Configuration Chart.js avec les couleurs du thème
    const chartColors = {
        primary: '#2a8fd6',
        primaryStrong: '#22b8a6',
        accent: '#f59e0b',
        danger: '#d94f4f',
        muted: '#5f7185',
        bg: '#f4f7fb'
    };

    function transformDate(annee , mois){
        const date = new Date(annee , mois-1);
        return date.toLocaleString('fr-FR', { month: 'short' , year : 'numeric' });
    }

    async function loadUserInscriptionChart() {
        const response = await fetch('/api/userInscription');
        const data = await response.json();
        const labels = data.map(item => `${transformDate(item.annee , item.mois)}`);
        const total = data.map(item => item.total);
        const ctx = document.getElementById('barChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels : labels,
                datasets: [{
                    label: 'inscription',
                    data: total,
                    backgroundColor: chartColors.primary,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: chartColors.muted
                        },
                        grid: {
                            color: 'rgba(92, 120, 146, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: chartColors.muted
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        })
    }

    loadUserInscriptionChart();

    // 2. LINE CHART - Évolution des Revenus
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Revenus (€)',
                data: [2400, 2890, 3200, 3590, 4100, 4800],
                borderColor: chartColors.primaryStrong,
                backgroundColor: 'rgba(34, 184, 166, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: chartColors.primaryStrong,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: chartColors.muted
                    },
                    grid: {
                        color: 'rgba(92, 120, 146, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: chartColors.muted
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 3. PIE CHART - Distribution des Régimes
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Cétogène', 'Paléo', 'Végétarien', 'Vegan', 'Sans Gluten'],
            datasets: [{
                data: [848, 575, 1226, 990, 528],
                backgroundColor: [
                    chartColors.primary,
                    chartColors.primaryStrong,
                    chartColors.accent,
                    '#a78bfa',
                    '#60a5fa'
                ],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: chartColors.muted,
                        padding: 16,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // 4. DOUGHNUT CHART - Statut des Régimes
    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
    new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Actif', 'Suspendu', 'Complété', 'Abandonné'],
            datasets: [{
                data: [2850, 340, 580, 200],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#3b82f6',
                    '#ef4444'
                ],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: chartColors.muted,
                        padding: 16,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>