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
          title: { text: "Revenue (Rs.)" },
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
        xAxis: {
          categories: data.names,
          title: { text: "Products" },
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: { text: "Quantity (kg / units)" },
        },
        tooltip: {
          headerFormat:
            '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat:
            '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
          footerFormat: "</table>",
          shared: true,
          useHTML: true,
        },
        series: [
          {
            name: "Current Stock",
            data: data.stocks.map((val) => ({
              y: val,
              color: val < 5 ? "#e53935" : "#2e7d32", // Red if < 5, else theme green
            })),
          },
        ],
        credits: { enabled: false },
      });
    })
    .catch((err) => console.error("Stock chart error:", err));
}
