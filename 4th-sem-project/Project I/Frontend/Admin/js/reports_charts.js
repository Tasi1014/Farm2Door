document.addEventListener("DOMContentLoaded", () => {
  const generateBtn = document.getElementById("generateReportBtn");
  const reportType = document.getElementById("reportType");
  const startDateInput = document.getElementById("startDate");
  const endDateInput = document.getElementById("endDate");

  const fetchReport = () => {
    const type = reportType.value;
    const start = startDateInput.value;
    const end = endDateInput.value;

    if (!start || !end) return; // Don't alert on change, only on button click

    let endpoint = "";
    if (type === "daily") endpoint = "../../Backend/admin_get_sales_range.php";
    else if (type === "stock_levels")
      endpoint = "../../Backend/admin_get_stock_levels.php";
    else if (type === "farmers")
      endpoint = "../../Backend/admin_get_top_farmers.php";

    fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ start_date: start, end_date: end }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.success) {
          console.error(data.message);
          return;
        }
        renderChart(type, data, start, end);
        renderReportTable(type, data);
      })
      .catch((err) => console.error("Report generation error:", err));
  };

  generateBtn.addEventListener("click", () => {
    const start = startDateInput.value;
    const end = endDateInput.value;
    if (!start || !end) {
      alert("Please select both start and end dates.");
      return;
    }
    fetchReport();
  });

  reportType.addEventListener("change", fetchReport);
});

function renderChart(type, data, start, end) {
  let chartOptions = {
    title: { text: null },
    credits: { enabled: false },
    yAxis: { min: 0 },
    tooltip: { shared: true },
  };

  if (type === "daily") {
    chartOptions = {
      ...chartOptions,
      chart: { type: "areaspline" },
      title: { text: `Daily Sales from ${start} to ${end}` },
      xAxis: {
        categories: data.dates,
        title: { text: "Date" },
        labels: { rotation: -45 },
      },
      yAxis: { title: { text: "Revenue (Rs.)" } },
      series: [
        {
          name: "Revenue",
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
    };
  } else if (type === "stock_levels") {
    chartOptions = {
      ...chartOptions,
      chart: { type: "column" },
      title: { text: "Stock Levels Inventory Status" },
      xAxis: {
        categories: data.names,
        title: { text: "Products" },
      },
      yAxis: { title: { text: "Quantity" } },
      series: [
        {
          name: "Current Stock",
          data: data.stocks.map((v) => ({
            y: v,
            color: v < 5 ? "#e53935" : "#2e7d32",
          })),
        },
      ],
    };
  } else if (type === "farmers") {
    chartOptions = {
      ...chartOptions,
      chart: { type: "bar" },
      title: { text: `Top Farmers by Revenue (${start} to ${end})` },
      xAxis: {
        categories: data.names,
        title: { text: "Farmers" },
      },
      yAxis: { title: { text: "Total Revenue (Rs.)" } },
      series: [
        {
          name: "Revenue",
          data: data.amounts,
          color: "#43a047",
        },
      ],
    };
  }

  Highcharts.chart("range-sales-chart", chartOptions);
}

function renderReportTable(type, data) {
  const container = document.getElementById("reportResult");
  const header = document.getElementById("reportTableHeader");
  const tbody = document.getElementById("reportTableBody");
  const title = document.getElementById("reportTitle");

  container.style.display = "block";
  tbody.innerHTML = "";

  if (type === "daily") {
    title.innerText = "Detailed Sales Report";
    header.innerHTML = `<tr><th>Date</th><th>Revenue</th><th>Admin Commission (10%)</th></tr>`;
    data.dates.forEach((date, index) => {
      const amt = data.amounts[index];
      tbody.innerHTML += `<tr><td>${date}</td><td>Rs. ${amt.toLocaleString()}</td><td>Rs. ${(
        amt * 0.1
      ).toLocaleString()}</td></tr>`;
    });
  } else if (type === "stock_levels") {
    title.innerText = "Product Stock Inventory Report";
    header.innerHTML = `<tr><th>Product Name</th><th>Stock Quantity</th><th>Status</th></tr>`;
    data.names.forEach((name, index) => {
      const stock = data.stocks[index];
      const status =
        stock < 5
          ? '<span style="color:#e53935; font-weight:bold">LOW STOCK</span>'
          : "Healthy";
      tbody.innerHTML += `<tr><td>${name}</td><td>${stock} kg/units</td><td>${status}</td></tr>`;
    });
  } else if (type === "farmers") {
    title.innerText = "Farmer Performance Report";
    header.innerHTML = `<tr><th>Farmer Name</th><th>Revenue Generated</th></tr>`;
    data.names.forEach((name, index) => {
      tbody.innerHTML += `<tr><td>${name}</td><td>Rs. ${data.amounts[
        index
      ].toLocaleString()}</td></tr>`;
    });
  }
}
