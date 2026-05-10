// Configuration Chart.js avec les couleurs du thème
const chartColors = {
  primary: "#2a8fd6",
  primaryStrong: "#22b8a6",
  accent: "#f59e0b",
  danger: "#d94f4f",
  muted: "#5f7185",
  bg: "#f4f7fb",
};

function transformDate(annee, mois) {
  const date = new Date(annee, mois - 1);
  return date.toLocaleString("fr-FR", {
    month: "short",
    year: "numeric",
  });
}

function transformMonth(mois) {
  const date = new Date(2026, mois - 1);
  return date.toLocaleString("fr-FR", {
    month: "short",
  });
}

async function loadUsersInscriptionChart() {
  const response = await fetch("/api/userInscription");
  const data = await response.json();
  const labels = data.map((item) => `${transformDate(item.annee, item.mois)}`);
  const total = data.map((item) => item.total);
  const ctx = document.getElementById("barChart").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "inscription",
          data: total,
          backgroundColor: chartColors.primary,
          borderRadius: 8,
          borderSkipped: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: chartColors.muted,
          },
          grid: {
            color: "rgba(92, 120, 146, 0.1)",
          },
        },
        x: {
          ticks: {
            color: chartColors.muted,
          },
          grid: {
            display: false,
          },
        },
      },
    },
  });
}
async function gainArgentParMoisChart() {
  const response = await fetch("/api/userDepenses");
  const data = await response.json();
  const labels = data.map((item) => `${transformDate(item.annee, item.mois)}`);
  const total = data.map((item) => item.total_depenses);
  const lineCtx = document.getElementById("lineChart").getContext("2d");
  new Chart(lineCtx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Revenus (Ar)",
          data: total,
          borderColor: chartColors.primaryStrong,
          backgroundColor: "rgba(34, 184, 166, 0.1)",
          fill: true,
          tension: 0.4,
          pointBackgroundColor: chartColors.primaryStrong,
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: chartColors.muted,
          },
          grid: {
            color: "rgba(92, 120, 146, 0.1)",
          },
        },
        x: {
          ticks: {
            color: chartColors.muted,
          },
          grid: {
            display: false,
          },
        },
      },
    },
  });
}

async function repartitionClientsParOption() {
  const response = await fetch("/api/userRepartition/typeCompte");
  const data = await response.json();
  const labels = data.map((item) => item.option_type);
  const total = data.map((item) => item.total);
  const pieCtx = document.getElementById("pieChart").getContext("2d");
  new Chart(pieCtx, {
    type: "pie",
    data: {
      labels: labels,
      datasets: [
        {
          data: total,
          backgroundColor: [
            chartColors.primary,
            chartColors.primaryStrong,
            chartColors.accent,
            "#a78bfa",
            "#60a5fa",
          ],
          borderColor: "#fff",
          borderWidth: 3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: chartColors.muted,
            padding: 16,
            font: {
              size: 12,
            },
          },
        },
      },
    },
  });
}

async function repartitionClientsParIMC() {
  const reponse = await fetch("/api/userRepartition/imc");
  const data = await reponse.json();
  const labels = data.map((item) => item.categorie_imc);
  const total = data.map((item) => item.total);
  const doughnutCtx = document.getElementById("doughnutChart").getContext("2d");
  new Chart(doughnutCtx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [
        {
          data: total,
          backgroundColor: ["#10b981", "#f59e0b", "#3b82f6", "#ef4444"],
          borderColor: "#fff",
          borderWidth: 3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: chartColors.muted,
            padding: 16,
            font: {
              size: 12,
            },
          },
        },
      },
    },
  });
}

async function loadObjectifsTable(annee) {
  const tbody = document.getElementById("table-body");
  tbody.innerHTML = "";
  const response = await fetch(`/api/userRepartition/objectif/${annee}`);
  const data = await response.json();

  const objectifs = [...new Set(data.map((d) => d.objectif))];
  const mois = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

  const map = {};

  data.forEach((d) => {
    const obj = d.objectif;
    const m = Number(d.mois);
    const total = Number(d.total);

    if (!map[obj]) map[obj] = {};
    map[obj][m] = (map[obj][m] || 0) + total;
  });

  const thead = document.getElementById("table-head");

  thead.innerHTML = `
        <tr>
            <th>Objectif</th>
            ${mois.map((m) => `<th class="table-center">${transformMonth(m)}</th>`).join("")}
            <th>Total</th>
        </tr>
    `;

  let html = "";

  objectifs.forEach((obj) => {
    let total = 0;

    html += `<tr><td><strong>${obj}</strong></td>`;

    mois.forEach((m) => {
      const value = map[obj]?.[m] ?? 0;
      total += value;

      html += `<td class="table-center"><span class="table-value">${value}</span></td>`;
    });

    html += `<td class="table-center"><strong>${total}</strong></td></tr>`;
  });

  tbody.innerHTML = html;
}

async function loadAnneePresente() {
  const response = await fetch("/api/annee");
  const annee = await response.json();

  const select = document.getElementById("annee_repartition");
  select.innerHTML = "";

  annee.forEach((d) => {
    const option = document.createElement("option");
    option.value = d.annee;
    option.innerText = d.annee;
    select.appendChild(option);
  });
}

document
  .getElementById("annee_repartition")
  .addEventListener("change", async (event) => {
    event.preventDefault();
    loadObjectifsTable(event.target.value);
  });
