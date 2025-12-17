document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.querySelector("table tbody");
  const editModal = document.getElementById("editModal");
  const closeBtn = document.querySelector(".close-btn");
  const editForm = document.getElementById("editForm");

  // Simple array to store our products
  let productList = [];

  // Fetch and Display Products
  function loadProducts() {
    fetch("../../Backend/get_products.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          // Store the data in our simple array
          productList = data.products;
          renderTable(productList);
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
            <!-- We add a class and the ID directly -->
            <button class="action-btn edit-btn" id="edit-${product.product_id}" style="background-color: #2196F3; margin-right: 5px;">Edit</button>
            <button class="action-btn delete-btn" id="delete-${product.product_id}" style="background-color: #ff4444;">Delete</button>
        </td>
      `;
      tableBody.appendChild(tr);
    });
  }

  // Handle Clicks (New Simple Way)
  tableBody.addEventListener("click", (e) => {
    // Check if we clicked an Edit button
    if (e.target.classList.contains("edit-btn")) {
      // Get the ID string (e.g., "edit-5")
      const fullId = e.target.id;
      // Split it to get just the number "5"
      const id = fullId.split("-")[1];

      // Find the product in our list
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
    const quantity = parseInt(formData.get("stock_quantity"));

    if (price < 0) {
      alert("Price cannot be negative.");
      return;
    }
    if (quantity < 0) {
      alert("Quantity cannot be negative.");
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
          loadProducts(); // Reload table
        } else {
          if (data.errors) {
            // Convert error object to readable string
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
    fetch("../../Backend/delete_product.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ product_id: id }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          loadProducts();
        } else {
          alert(data.message || "Delete failed");
        }
      })
      .catch((err) => console.error("Error deleting:", err));
  }

  // Initial Load
  loadProducts();
});
