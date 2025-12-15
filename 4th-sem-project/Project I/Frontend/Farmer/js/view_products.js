document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.querySelector("table tbody");
  const editModal = document.getElementById("editModal");
  const closeBtn = document.querySelector(".close-btn");
  const editForm = document.getElementById("editForm");

  // Fetch and Display Products
  function loadProducts() {
    fetch("../../Backend/get_products.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          renderTable(data.products);
        } else {
          // If simply no products, that's fine, handle unauthorized or error
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
      // Use fallback image if specific one fails
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
            <button class="action-btn edit-btn" data-id="${product.product_id}" style="background-color: #2196F3; margin-right: 5px;">Edit</button>
            <button class="action-btn delete-btn" data-id="${product.product_id}" style="background-color: #ff4444;">Delete</button>
        </td>
      `;

      // Store product data on row
      tr.dataset.product = JSON.stringify(product);
      tableBody.appendChild(tr);
    });

    // No need to re-attach listeners if we use delegation on the parent!
  }

  // Event Delegation for Edit and Delete
  tableBody.addEventListener("click", (e) => {
    // Handle Edit
    const editBtn = e.target.closest(".edit-btn");

    if (editBtn) {
      const tr = editBtn.closest("tr");

      if (tr && tr.dataset.product) {
        try {
          const product = JSON.parse(tr.dataset.product);
          openEditModal(product);
        } catch (err) {
          console.error("Error parsing product data:", err);
          alert("Error reading product data. Check console.");
        }
      }
      return;
    }

    // Handle Delete
    const deleteBtn = e.target.closest(".delete-btn");
    if (deleteBtn) {
      const id = deleteBtn.dataset.id;
      if (confirm("Are you sure you want to delete this product?")) {
        deleteProduct(id);
      }
      return;
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
            alert("Error: " + JSON.stringify(data.errors));
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
