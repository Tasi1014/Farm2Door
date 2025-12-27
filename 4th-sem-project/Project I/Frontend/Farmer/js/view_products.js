document.addEventListener("DOMContentLoaded", () => {
  console.log("✓ JS file loaded successfully");
  const tableBody = document.querySelector("table tbody");
  const editModal = document.getElementById("editModal");
  const closeBtn = document.querySelector(".close-btn");
  const editForm = document.getElementById("editForm");

  // Pagination State
  let currentPage = 1;
  const itemsPerPage = 5;
  let totalProducts = 0;

  // Simple array to store our products
  let productList = [];

  // Fetch and Display Products
  function loadProducts(page = 1) {
    currentPage = page;
    fetch(
      `../../Backend/get_products.php?page=${currentPage}&limit=${itemsPerPage}`
    )
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          productList = data.products;
          totalProducts = data.total;
          renderTable(productList);
          updatePaginationControls();
        } else {
          if (data.message === "Unauthorized") {
            window.location.href = "../Login/login.html";
          } else {
            console.error(data.message);
          }
        }
      })
      .catch((err) => console.error("Error loading products:", err));
  }

  function renderTable(products) {
    tableBody.innerHTML = "";
    if (products.length === 0) {
      tableBody.innerHTML =
        "<tr><td colspan='5'>No products found. Add some!</td></tr>";
      return;
    }

    products.forEach((product) => {
      const tr = document.createElement("tr");
      const imgSrc = `../../Images/products/${product.image}`;

      tr.innerHTML = `
        <td>
            <img src="${imgSrc}" alt="${product.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" 
            onerror="this.src='../../Images/logo.png'">
        </td>
        <td>${product.name}</td>
        <td>Rs. ${product.price}/kg</td>
        <td>${product.stock_quantity} kg</td>
        <td>
            <button class="action-btn edit-btn" id="edit-${product.product_id}" style="background-color: #2196F3; margin-right: 5px;">Edit</button>
            <button class="action-btn delete-btn" id="delete-${product.product_id}" style="background-color: #ff4444;">Delete</button>
        </td>
      `;
      tableBody.appendChild(tr);
    });
  }

  function updatePaginationControls() {
    const totalPages = Math.ceil(totalProducts / itemsPerPage);
    const prevBtn = document.getElementById("prevPageBtn");
    const nextBtn = document.getElementById("nextPageBtn");
    const pageInfo = document.getElementById("pageInfo");

    if (pageInfo) {
      pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
    }

    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
  }

  // --- Pagination Button Listeners ---
  document.getElementById("prevPageBtn")?.addEventListener("click", () => {
    if (currentPage > 1) {
      loadProducts(currentPage - 1);
    }
  });

  document.getElementById("nextPageBtn")?.addEventListener("click", () => {
    const totalPages = Math.ceil(totalProducts / itemsPerPage);
    if (currentPage < totalPages) {
      loadProducts(currentPage + 1);
    }
  });

  // Handle Clicks (New Simple Way)
  tableBody.addEventListener("click", (e) => {
    // Check if we clicked an Edit button
    if (e.target.classList.contains("edit-btn")) {
      const fullId = e.target.id;
      const id = fullId.split("-")[1];
      const product = productList.find((p) => p.product_id == id);
      if (product) {
        openEditModal(product);
      }
    }

    // Check if we clicked a Delete button
    if (e.target.classList.contains("delete-btn")) {
      const fullId = e.target.id;
      const id = fullId.split("-")[1];

      if (confirm("Are you sure you want to delete this product?")) {
        deleteProduct(id);
      }
    }
  });

  // --- Modal Logic ---
  function openEditModal(product) {
    document.getElementById("edit-id").value = product.product_id;
    document.getElementById("edit-name").value = product.name;
    document.getElementById("edit-category").value = product.category;
    document.getElementById("edit-price").value = product.price;
    document.getElementById("edit-quantity").value = product.stock_quantity;
    document.getElementById("edit-threshold").value = product.threshold || 5;
    document.getElementById("edit-description").value =
      product.description || "";

    editModal.style.display = "block";
  }

  closeBtn.addEventListener("click", () => {
    editModal.style.display = "none";
  });

  window.addEventListener("click", (e) => {
    if (e.target === editModal) {
      editModal.style.display = "none";
    }
  });

  // Handle Update
  editForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(editForm);

    // Validation
    const price = parseFloat(formData.get("price"));
    const quantity = parseInt(formData.get("quantity"));
    const threshold = parseInt(formData.get("lowStockThreshold"));

    if (price < 0) {
      alert("Price cannot be negative.");
      return;
    }
    if (quantity < 0) {
      alert("Quantity cannot be negative.");
      return;
    }
    if (threshold < 0) {
      alert("Threshold cannot be negative.");
      return;
    }

    fetch("../../Backend/update_product.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          editModal.style.display = "none";
          loadProducts(currentPage); // Reload current page
        } else {
          if (data.errors) {
            const errorMsg = Object.values(data.errors).join("\n");
            alert(errorMsg);
          } else {
            alert(data.message || "Update failed");
          }
        }
      })
      .catch((err) => console.error("Error updating:", err));
  });

  // Handle Delete
  function deleteProduct(id) {
    fetch("../../Backend/delete_products.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ product_id: id }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          // If this was the last item on the page, go to prev page
          if (productList.length === 1 && currentPage > 1) {
            loadProducts(currentPage - 1);
          } else {
            loadProducts(currentPage);
          }
        } else {
          alert(data.message || "Delete failed");
        }
      })
      .catch((err) => console.error("Error deleting:", err));
  }

  loadProducts(1);
});
