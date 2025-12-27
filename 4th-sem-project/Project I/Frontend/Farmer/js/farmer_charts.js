document.addEventListener("DOMContentLoaded", () => {
  const reportDropdown = document.getElementById("dropdown");

  // Initial load
  loadReport("daily_sales");

  // Switch report type
  if (reportDropdown) {
    reportDropdown.addEventListener("change", (e) => {
      loadReport(e.target.value);
    });
  }
});

function loadReport(type) {
  const chartTitle = document.querySelector(".chart-card .card-title");

  if (type === "daily_sales") {
    if (chartTitle)
      chartTitle.innerText = "Daily Sales Overview (Last 30 Days)";
    const insightsCard = document.getElementById("insightsCard");
    if (insightsCard) insightsCard.style.display = "none";
    loadDailySalesChart();
  } else if (type === "stock_levels") {
    if (chartTitle) chartTitle.innerText = "Stock Levels Inventory";
    loadStockLevelsChart();
  }
}

function loadDailySalesChart() {
  fetch("../../Backend/farmer_get_monthly_earnings.php")
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        console.error(data.message);
        return;
      }

      Highcharts.chart("monthly-earnings-chart", {
        chart: { type: "areaspline" },
        title: { text: null },
        xAxis: {
          categories: data.dates,
          crosshair: true,
          title: { text: "Date" },
          labels: { rotation: -45 },
        },
        yAxis: {
          min: 0,
          title: { text: "Total Sales (Rs.)" },
        },
        tooltip: {
          shared: true,
          valueDecimals: 2,
          valuePrefix: "Rs. ",
        },
        series: [
          {
            name: "Your Daily Sales",
            data: data.amounts,
            color: "#2e7d32",
            marker: { radius: 4 },
            fillColor: {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, "rgba(46, 125, 50, 0.4)"],
                [1, "rgba(46, 125, 50, 0)"],
              ],
            },
          },
        ],
        credits: { enabled: false },
      });
    })
    .catch((err) => console.error("Sales chart error:", err));
}

function loadStockLevelsChart() {
  fetch("../../Backend/farmer_get_stock_levels.php")
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        console.error(data.message);
        return;
      }

      Highcharts.chart("monthly-earnings-chart", {
        chart: { type: "column" },
        title: { text: null },
        plotOptions: {
          column: {
            stacking: "normal",
            dataLabels: {
              enabled: true,
              formatter: function () {
                const idx = this.point.index;
                const total = data.stocks[idx];
                const threshold = data.thresholds[idx];

                // Show "REORDER" if strictly below target threshold
                // Only label the top-most visible segment of the bar
                const isTopVisible =
                  (total > threshold && this.series.name === "Surplus Stock") ||
                  (total <= threshold &&
                    this.series.name === "Safety Level (Target)");

                if (total < threshold && isTopVisible) {
                  return '<span style="color:#d32f2f; font-weight:bold;">⚠️ REORDER</span>';
                }
                return null;
              },
              useHTML: true,
              inside: false,
              crop: false,
              overflow: "none",
            },
          },
        },
        xAxis: {
          categories: data.names,
          title: { text: "Products" },
          crosshair: true,
        },
        yAxis: {
          min: 0,
          reversedStacks: false,
          title: { text: "Quantity (kg / units)" },
          stackLabels: {
            enabled: true,
            style: { fontWeight: "bold", color: "gray" },
          },
        },
        tooltip: {
          shared: true,
          headerFormat:
            '<span style="font-size: 14px; font-weight: bold;">{point.key}</span><br/>',
          pointFormat: "{series.name}: <b>{point.y} kg</b><br/>",
          footerFormat: "<hr/>Total Available: <b>{point.total} kg</b>",
          useHTML: true,
        },
        series: [
          {
            name: "Safety Level (Target)",
            data: data.stocks.map((val, idx) => {
              const threshold = data.thresholds[idx];
              return Math.min(val, threshold);
            }),
            color: "#d32f2f", // Red base for the safety zone
          },
          {
            name: "Surplus Stock",
            data: data.stocks.map((val, idx) => {
              const threshold = data.thresholds[idx];
              return Math.max(0, val - threshold);
            }),
            color: "#2e7d32", // Green for surplus
          },
        ],
        credits: { enabled: false },
      });

      generateActionableInsights(data);
    })
    .catch((err) => console.error("Stock chart error:", err));
}

function generateActionableInsights(data) {
  const container = document.getElementById("insightsCard");
  const list = document.getElementById("insightsList");

  if (!container || !list) return;

  const lowStockItems = [];
  data.stocks.forEach((stock, idx) => {
    const threshold = data.thresholds[idx];
    if (stock < threshold) {
      lowStockItems.push({
        name: data.names[idx],
        current: stock,
        target: threshold,
        needed: threshold - stock,
      });
    }
  });

  if (lowStockItems.length > 0) {
    container.style.display = "block";
    list.innerHTML = lowStockItems
      .map(
        (item) => `
      <div class="insight-item critical">
        <div class="insight-main">
          <div class="insight-icon">⚠️</div>
          <div class="insight-text">
            <h4>${item.name}</h4>
            <p>Stock is below your manual alert level of ${item.target}kg.</p>
          </div>
        </div>
        <div class="insight-action">
          <span class="restock-qty">+${item.needed.toFixed(1)}kg</span>
          <span class="restock-label">Needed to reach Safety</span>
        </div>
      </div>
    `
      )
      .join("");
  } else {
    container.style.display = "block";
    list.innerHTML = `
      <div class="insight-item" style="background: #e8f5e9; border-color: #a5d6a7;">
        <div class="insight-main">
          <div class="insight-icon">✅</div>
          <div class="insight-text">
            <h4>Inventory Healthy</h4>
            <p>All products are currently above your custom low-stock thresholds.</p>
          </div>
        </div>
      </div>
    `;
  }
}
