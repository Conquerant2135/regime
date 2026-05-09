<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Fitness Régime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* DASHBOARD LAYOUT */
        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            padding: 24px;
            min-height: calc(100vh - 200px);
            background: var(--bg);
        }

        /* SIDEBAR */
        .dashboard-sidebar {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: 0 4px 12px rgba(25, 49, 77, 0.08);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .sidebar-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            display: block;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background: var(--bg-alt);
            color: var(--primary);
            border-left-color: var(--primary);
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: rgba(42, 143, 214, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .sidebar-icon {
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
        }

        /* MAIN CONTENT */
        .dashboard-main {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 12px;
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: 0 4px 12px rgba(25, 49, 77, 0.08);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-change {
            font-size: 12px;
            color: var(--muted);
        }

        .stat-change.positive {
            color: var(--primary-strong);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* CHARTS SECTION */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
        }

        .chart-card {
            background: var(--surface);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: 0 4px 12px rgba(25, 49, 77, 0.08);
            border: 1px solid var(--border);
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 20px;
            display: block;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* TABLE SECTION */
        .table-section {
            background: var(--surface);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: 0 4px 12px rgba(25, 49, 77, 0.08);
            border: 1px solid var(--border);
        }

        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 20px;
            display: block;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .crosstab-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .crosstab-table thead {
            background: var(--bg-alt);
            border-bottom: 2px solid var(--border);
        }

        .crosstab-table th {
            padding: 14px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
        }

        .crosstab-table td {
            padding: 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        .crosstab-table tbody tr:hover {
            background: rgba(42, 143, 214, 0.05);
        }

        .table-center {
            text-align: center;
        }

        .table-value {
            font-weight: 600;
            color: var(--primary);
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .dashboard-sidebar {
                position: static;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 16px;
                gap: 16px;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body class="site-body">
    <!-- HEADER (réutilise la navbar existante) -->
    <header class="site-header">
        <div class="site-nav">
            <a class="brand" href="<?= site_url('/') ?>">
                <span class="brand-mark">FR</span>
                <span class="brand-text">Fitness Régime</span>
            </a>
            <nav class="site-links">
                <a href="<?= site_url('/') ?>">Accueil</a>
                <a href="<?= site_url('regime-sport') ?>">Régimes</a>
                <a href="<?= site_url('mon-compte') ?>">Mon compte</a>
                <a href="<?= site_url('logout') ?>">Déconnexion</a>
            </nav>
            <a class="wallet-pill" href="<?= site_url('portefeuille') ?>">
                Solde: <?= number_format((float) (session()->get('solde') ?? 0), 2) ?> €
            </a>
        </div>
    </header>

    <!-- MAIN DASHBOARD -->
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar">
            <span class="sidebar-title">⚙️ Gestion</span>
            <nav class="sidebar-menu">
                <a href="#" class="sidebar-link active">
                    <span class="sidebar-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">🏋️</span>
                    <span>Régimes</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">💪</span>
                    <span>Sports</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">👥</span>
                    <span>Utilisateurs</span>
                </a>
            </nav>

            <span class="sidebar-title" style="margin-top: 28px;">⚙️ Paramètres</span>
            <nav class="sidebar-menu">
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">🔧</span>
                    <span>Configuration</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">🎨</span>
                    <span>Thème</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">📧</span>
                    <span>Notifications</span>
                </a>
                <a href="#" class="sidebar-link">
                    <span class="sidebar-icon">🔐</span>
                    <span>Sécurité</span>
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="dashboard-main">
            <!-- HEADER -->
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
        </main>
    </div>

    <!-- JAVASCRIPT CHARTS -->
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

        // 1. BAR CHART - Utilisateurs par Mois
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Utilisateurs',
                    data: [145, 159, 175, 182, 195, 220],
                    backgroundColor: chartColors.primary,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: chartColors.muted },
                        grid: { color: 'rgba(92, 120, 146, 0.1)' }
                    },
                    x: {
                        ticks: { color: chartColors.muted },
                        grid: { display: false }
                    }
                }
            }
        });

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
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: chartColors.muted },
                        grid: { color: 'rgba(92, 120, 146, 0.1)' }
                    },
                    x: {
                        ticks: { color: chartColors.muted },
                        grid: { display: false }
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
                            font: { size: 12 }
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
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>