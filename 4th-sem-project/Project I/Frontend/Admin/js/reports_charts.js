document.addEventListener("DOMContentLoaded", () => {
  const generateBtn = document.getElementById("generateReportBtn");
  const reportType = document.getElementById("reportType");
  const viewBy = document.getElementById("viewBy");
  const startDateInput = document.getElementById("startDate");
  const endDateInput = document.getElementById("endDate");

  // Disable future dates
  const today = new Date().toISOString().split("T")[0];
  startDateInput.max = today;
  endDateInput.max = today;

  // Set default dates
  const monthAgo = new Date();
  monthAgo.setMonth(monthAgo.getMonth() - 1);
  startDateInput.value = monthAgo.toISOString().split("T")[0];
  endDateInput.value = today;

  // Populate Farmer Dropdown
  fetch("../../Backend/Admin/get_farmers_list.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        data.farmers.forEach((f) => {
          const opt = document.createElement("option");
          opt.value = f.id;
          opt.innerText = f.name;
          viewBy.appendChild(opt);
        });
      }
    })
    .catch((err) => console.error("Error fetching farmers:", err));

  const fetchReport = () => {
    const type = reportType.value;
    const start = startDateInput.value;
    const end = endDateInput.value;
    const farmerId = viewBy.value;

    if (!start || !end) return;

    let endpoint = "";
    if (type === "daily") endpoint = "../../Backend/admin_get_sales_range.php";
    else if (type === "stock_levels")
      endpoint = "../../Backend/admin_get_stock_levels.php";
    else if (type === "farmers")
      endpoint = "../../Backend/admin_get_top_farmers.php";
    else if (type === "payments")
      endpoint = "../../Backend/admin_get_payments_report.php";

    // Hide specialized report elements by default
    // Hide specialized report elements by default
    const financialSummary = document.getElementById("financialSummary");
    const reportDesc = document.getElementById("reportDescription");
    const insightsCard = document.getElementById("insightsCard");

    if (financialSummary) financialSummary.style.display = "none";
    if (reportDesc) reportDesc.style.display = "none";
    if (insightsCard) insightsCard.style.display = "none";

    fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        start_date: start,
        end_date: end,
        farmer_id: farmerId,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.success) {
          console.error(data.message);
          return;
        }

        if (type === "payments") {
          loadFinancialReport(data, start, end);
        } else {
          renderChart(type, data, start, end);
          if (type === "stock_levels") {
            generateAdminActionableInsights(data);
          }
        }
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
  viewBy.addEventListener("change", fetchReport);
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
      plotOptions: {
        column: { stacking: "normal" },
      },
      xAxis: {
        categories: data.names,
        title: { text: "Products" },
      },
      yAxis: {
        title: { text: "Quantity" },
        reversedStacks: false,
        stackLabels: {
          enabled: true,
          style: { fontWeight: "bold", color: "gray" },
        },
      },
      tooltip: {
        shared: true,
        headerFormat:
          '<span style="font-size: 14px; font-weight: bold;">{point.key}</span><br/>',
        pointFormat: "{series.name}: <b>{point.y} kg/units</b><br/>",
        footerFormat: "<hr/>Total Available: <b>{point.total} kg/units</b>",
        useHTML: true,
      },
      series: [
        {
          name: "Safety Level (Target)",
          data: data.stocks.map((val, idx) => {
            const threshold = data.thresholds[idx];
            return Math.min(val, threshold);
          }),
          color: "#d32f2f",
        },
        {
          name: "Surplus Stock",
          data: data.stocks.map((val, idx) => {
            const threshold = data.thresholds[idx];
            return Math.max(0, val - threshold);
          }),
          color: "#2e7d32",
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

function loadFinancialReport(data, start, end) {
  // Show summary cards
  const summary = document.getElementById("financialSummary");
  if (summary) {
    summary.style.display = "grid";
    document.getElementById(
      "grossPaid"
    ).innerText = `Rs. ${data.summary.total_paid.toLocaleString()}`;
    document.getElementById(
      "totalRefunded"
    ).innerText = `Rs. ${data.summary.total_refunded.toLocaleString()}`;
    document.getElementById(
      "adminCommission"
    ).innerText = `Rs. ${data.summary.commission.toLocaleString()}`;
    document.getElementById(
      "netFlow"
    ).innerText = `Rs. ${data.summary.net_flow.toLocaleString()}`;
  }

  // Show Description
  const desc = document.getElementById("reportDescription");
  if (desc) {
    desc.style.display = "block";
    desc.innerText = `Financial trend from ${start} to ${end}. Includes only successful payments from consumers in the selected period; refunds are subtracted from gross paid to calculate net flow.`;
  }

  // Render Line Chart (Paid vs Refunded)
  Highcharts.chart("range-sales-chart", {
    chart: { type: "areaspline" },
    title: { text: "Payment Record(Daily)" },
    xAxis: {
      categories: data.labels,
      title: { text: "Date" },
      labels: { rotation: -45 },
    },
    yAxis: {
      title: { text: "Amount (Rs.)" },
      min: 0,
    },
    tooltip: {
      shared: true,
      headerFormat: "<b>{point.x}</b><br/>",
      pointFormat:
        '<span style="color:{series.color}">●</span> {series.name}: <b>Rs. {point.y:,.2f}</b><br/>',
      useHTML: true,
    },
    series: [
      {
        name: "Successful Payments",
        data: data.paid_amounts,
        color: "#2e7d32",
      },
      {
        name: "Refunded Amounts",
        data: data.refunded_amounts,
        color: "#d32f2f",
      },
    ],
    credits: { enabled: false },
  });
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
    const selectedView = document.getElementById("viewBy").value;

    // Overall: Remove Target Safety
    // Specific Farmer: Remove Farmer AND Target Safety
    let tableHeader = `<tr><th>Product Name</th><th>Stock Quantity</th>`;
    if (selectedView === "overall") {
      tableHeader += `<th>Farmer</th>`;
    }
    tableHeader += `<th>Status</th></tr>`;
    header.innerHTML = tableHeader;

    data.names.forEach((name, index) => {
      const stock = data.stocks[index];
      const farmer = data.farmer_names[index];
      const threshold = data.thresholds[index];

      let status = "";
      if (stock < threshold) {
        status =
          '<span style="color:#d32f2f; font-weight:bold">🔴 Low Stock</span>';
      } else {
        status =
          '<span style="color:#388e3c; font-weight:bold">🟢 Sufficient Stock</span>';
      }

      let row = `<tr><td>${name}</td><td>${stock} kg</td>`;
      if (selectedView === "overall") {
        row += `<td>${farmer}</td>`;
      }
      row += `<td>${status}</td></tr>`;
      tbody.innerHTML += row;
    });

    if (type === "stock_levels") {
      generateAdminActionableInsights(data);
    } else {
      const insightsCard = document.getElementById("insightsCard");
      if (insightsCard) insightsCard.style.display = "none";
    }
  } else if (type === "farmers") {
    title.innerText = "Farmer Performance Report";
    header.innerHTML = `<tr><th>Farmer Name</th><th>Revenue Generated</th></tr>`;
    data.names.forEach((name, index) => {
      tbody.innerHTML += `<tr><td>${name}</td><td>Rs. ${data.amounts[
        index
      ].toLocaleString()}</td></tr>`;
    });
  } else if (type === "payments") {
    title.innerText = "Payments Report";
    header.innerHTML = `<tr>
        <th>Date</th>
        <th>
          <span class="has-tooltip" data-tooltip="Total gross money collected from customers (Gross Inflow).">Total Payments received</span>
        </th>
        <th>
          <span class="has-tooltip" data-tooltip="Total money refunded to customers.">Total Refunds</span>
        </th>
        <th>Admin Profit (10%)</th>
        <th>
          <span class="has-tooltip" data-tooltip="The actual cash remaining in the platform after deducting refunds.">Total Net Flow</span>
        </th>
      </tr>`;

    data.labels.forEach((date, index) => {
      const paid = data.paid_amounts[index];
      const refunded = data.refunded_amounts[index];

      // Skip empty days for the table to reduce clutter
      if (paid === 0 && refunded === 0) return;

      const commission = paid * 0.1;
      const net = paid - refunded;

      tbody.innerHTML += `
          <tr>
            <td>${date}</td>
            <td style="color:#2e7d32;">Rs. ${paid.toLocaleString()}</td>
            <td style="color:#d32f2f;">Rs. ${refunded.toLocaleString()}</td>
            <td style="color:#ffa000;">Rs. ${commission.toLocaleString()}</td>
            <td><strong>Rs. ${net.toLocaleString()}</strong></td>
          </tr>
        `;
    });
  }
}

function generateAdminActionableInsights(data) {
  const container = document.getElementById("insightsCard");
  const list = document.getElementById("insightsList");

  if (!container || !list) return;

  const lowStockItems = [];
  data.stocks.forEach((stock, idx) => {
    const threshold = data.thresholds[idx];
    if (stock < threshold) {
      lowStockItems.push({
        name: data.names[idx],
        farmer: data.farmer_names[idx],
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
            <h4>${item.name} <small style="color:#888;">(Farmer: ${
          item.farmer
        })</small></h4>
            <p>Stock is below the manual alert level of ${item.target}kg.</p>
          </div>
        </div>
        <div class="insight-action">
          <span class="restock-qty">+${item.needed.toFixed(1)}kg</span>
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
            <h4>All inventory is sufficient</h4>
            <p>No products are currently below their safety targets.</p>
          </div>
        </div>
      </div>
    `;
  }
}
