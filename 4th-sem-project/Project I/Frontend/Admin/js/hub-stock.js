// Collection Hub Stock JavaScript

let hubInventory = [];

document.addEventListener("DOMContentLoaded", () => {
  loadHubData();
  // Auto-refresh every 30 seconds
  setInterval(loadHubData, 30000);
});

async function loadHubData() {
  try {
    // Fetch summary and inventory data in parallel
    const [summaryResponse, inventoryResponse] = await Promise.all([
      fetch("../../Backend/Admin/admin_get_hub_summary.php"),
      fetch("../../Backend/Admin/admin_get_hub_stock.php"),
    ]);

    const summaryData = await summaryResponse.json();
    const inventoryData = await inventoryResponse.json();

    if (summaryData.success) {
      updateSummaryCards(summaryData.summary);
      updateStatusBreakdown(summaryData.summary);
    }

    if (inventoryData.success) {
      hubInventory = inventoryData.inventory;
      renderInventoryTable(hubInventory);
      renderStockChart(hubInventory);
      renderCategoryChart(hubInventory);
    }
  } catch (error) {
    console.error("Failed to load hub data:", error);
    showError("Failed to load collection hub data. Please refresh the page.");
  }
}

function updateSummaryCards(summary) {
  document.getElementById("total-value").textContent =
    "Rs. " + summary.total_value.toLocaleString();
  document.getElementById("product-types").textContent = summary.product_types;
  document.getElementById("ready-pickup").textContent =
    summary.ready_for_pickup;
  document.getElementById("awaiting-packaging").textContent =
    summary.awaiting_packaging;
}

function updateStatusBreakdown(summary) {
  document.getElementById("dispatched-count").textContent =
    summary.dispatched_qty;
  document.getElementById("received-count").textContent =
    summary.awaiting_packaging;
  document.getElementById("ready-count").textContent = summary.ready_for_pickup;
}

function renderInventoryTable(inventory) {
  const tbody = document.getElementById("inventory-tbody");

  if (inventory.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="9" class="empty-state">
          <i class="fa-solid fa-box-open"></i>
          <h3>No items currently in collection hub</h3>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = inventory
    .map(
      (item) => `
    <tr>
      <td><strong>${item.product_name}</strong></td>
      <td><span class="category-badge">${item.category}</span></td>
      <td class="total-qty">${formatQuantity(item.total_quantity)}</td>
      <td><span class="qty-badge">${formatQuantity(item.dispatched_qty)}</span></td>
      <td><span class="qty-badge">${formatQuantity(item.received_qty)}</span></td>
      <td><span class="qty-badge">${formatQuantity(item.ready_qty)}</span></td>
      <td>${item.order_count}</td>
      <td>${formatDateTime(item.last_updated)}</td>
    </tr>
  `,
    )
    .join("");
}

function renderStockChart(inventory) {
  if (!inventory || inventory.length === 0) {
    document.getElementById("stock-chart").innerHTML = `
      <div class="empty-state">
        <i class="fa-solid fa-chart-column"></i>
        <h3>No data to display</h3>
      </div>
    `;
    return;
  }

  if (typeof Highcharts === "undefined") return;

  // Group by category for better visualization & Handle nulls
  const categories = [
    ...new Set(inventory.map((item) => item.category || "Uncategorized")),
  ];

  const seriesData = categories.map((category) => {
    const items = inventory.filter(
      (item) => (item.category || "Uncategorized") === category,
    );
    return {
      name: category,
      data: items.map((item) => ({
        name: item.product_name,
        y: item.total_quantity,
        dispatched: item.dispatched_qty,
        received: item.received_qty,
        ready: item.ready_qty,
      })),
    };
  });

  Highcharts.chart("stock-chart", {
    chart: { type: "column" },
    title: { text: null },
    xAxis: {
      type: "category",
      labels: {
        rotation: -45,
        style: { fontSize: "12px" },
      },
    },
    yAxis: {
      min: 0,
      title: { text: "Quantity" },
    },
    legend: { enabled: true },
    plotOptions: {
      column: {
        dataLabels: {
          enabled: true,
          formatter: function () {
            return this.y > 0 ? this.y + "kgs" : "";
          },
        },
      },
    },
    tooltip: {
      formatter: function () {
        return (
          '<span style="font-size:11px">' +
          this.series.name +
          "</span><br>" +
          "<b>" +
          this.point.name +
          "</b><br/>" +
          "Total Quantity: <b>" +
          (this.y > 0 ? this.y + "kgs" : "-") +
          "</b><br/>" +
          "Dispatched: " +
          (this.point.dispatched > 0 ? this.point.dispatched + "kgs" : "-") +
          "<br/>" +
          "Received: " +
          (this.point.received > 0 ? this.point.received + "kgs" : "-") +
          "<br/>" +
          "Ready for Pickup: " +
          (this.point.ready > 0 ? this.point.ready + "kgs" : "-")
        );
      },
    },
    series: seriesData,
    credits: { enabled: false },
  });
}

function renderCategoryChart(inventory) {
  if (!inventory || inventory.length === 0) return;

  // Calculate total quantity per category
  const categoryTotals = {};
  inventory.forEach((item) => {
    const cat = item.category || "Uncategorized";
    categoryTotals[cat] = (categoryTotals[cat] || 0) + item.total_quantity;
  });

  const pieData = Object.keys(categoryTotals).map((cat) => ({
    name: cat,
    y: categoryTotals[cat],
  }));

  Highcharts.chart("category-chart", {
    chart: { type: "pie" },
    title: { text: null },
    tooltip: {
      formatter: function () {
        return (
          "<b>" +
          this.point.name +
          "</b>: " +
          (this.y > 0 ? this.y + "kgs" : "-") +
          " total items"
        );
      },
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: "pointer",
        dataLabels: {
          enabled: true,
          format: "<b>{point.name}</b>: {point.percentage:.1f} %",
        },
      },
    },
    series: [
      {
        name: "Quantity",
        colorByPoint: true,
        data: pieData,
      },
    ],
    credits: { enabled: false },
  });
}

function formatQuantity(qty) {
  return qty > 0 ? `${qty}kgs` : "-";
}

function formatDateTime(dateString) {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) return "Just now";
  if (diffMins < 60) return `${diffMins} min ago`;
  if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
  if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? "s" : ""} ago`;

  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function showError(message) {
  const tbody = document.getElementById("inventory-tbody");
  tbody.innerHTML = `
    <tr>
      <td colspan="9" style="text-align: center; padding: 40px; color: #d32f2f;">
        <i class="fa-solid fa-exclamation-triangle"></i><br/>
        ${message}
      </td>
    </tr>
  `;
}
