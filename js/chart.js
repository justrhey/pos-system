document.addEventListener("DOMContentLoaded", function () {
  const salesLabels = window.reportData.salesLabels;
  const salesTotals = window.reportData.salesTotals;
  const bestSellerLabels = window.reportData.bestSellerLabels;
  const bestSellerData = window.reportData.bestSellerData;

  const colorList = [
    "#ff6384", "#36a2eb", "#ffce56", "#8bc34a", "#f44336",
    "#9c27b0", "#00bcd4", "#ffc107", "#795548", "#607d8b"
  ];

  // Line chart for sales
  const salesCtx = document.getElementById("salesChart").getContext("2d");
  new Chart(salesCtx, {
    type: "line",
    data: {
      labels: salesLabels,
      datasets: [
        {
          label: "Total Sales (₱)",
          data: salesTotals,
          borderColor: "#3e95cd",
          backgroundColor: "rgba(62,149,205,0.2)",
          fill: true,
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      }
    },
  });

  // Doughnut chart for best sellers
  const bestSellerCtx = document.getElementById("bestSellerChart").getContext("2d");
  new Chart(bestSellerCtx, {
    type: "doughnut",
    data: {
      labels: bestSellerLabels,
      datasets: [
        {
          data: bestSellerData,
          backgroundColor: colorList,
        },
      ],
    },
    options: {
      responsive: true,
      cutout: '70%',
      layout: {
        padding: 10
      },
      plugins: {
        legend: {
          position: "right",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || '';
              const value = context.parsed;
              return `${label}: ${value.toLocaleString()}`;
            }
          }
        }
      }
    },
  });

  // Render best seller list with percentages
  const total = bestSellerData.reduce((sum, val) => sum + val, 0);
  const list = document.getElementById("bestsellerList");
  list.innerHTML = "";

  bestSellerLabels.forEach((label, index) => {
    const percent = ((bestSellerData[index] / total) * 100).toFixed(1);
    const li = document.createElement("li");
    li.style.display = "flex";
    li.style.justifyContent = "space-between";
    li.style.alignItems = "center";
    li.style.color = colorList[index % colorList.length];
    li.style.fontWeight = "bold";
    li.style.fontSize = "1.5rem";
    li.innerHTML = `
      <span class="label-text">${label}</span>
      <span class="percent-text">${percent}%</span>
    `;
    list.appendChild(li);
  });
});
