document.addEventListener("DOMContentLoaded", () => {
  loadMonthlySalesChart();
  loadTopFarmersChart();
  loadStockLevelsChart();
});

function loadMonthlySalesChart() {
  fetch("../../Backend/admin_get_monthly_sales.php")
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        console.error(data.message);
        return;
      }

      Highcharts.chart("monthly-sales-chart", {
        chart: { type: "areaspline" },
        title: { text: "Daily Sales (Current Month)" },
        xAxis: {
          categories: data.months,
          crosshair: true,
          labels: {
            rotation: -45,
          },
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
            name: "Total Sales",
            data: data.amounts,
            color: "#2e7d32",
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
    .catch((err) => console.error("Monthly sales error:", err));
}
