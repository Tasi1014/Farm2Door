document.addEventListener("DOMContentLoaded", () => {
  console.log("Add Product Script Loaded");
  // alert("Debug: Script Loaded"); // Uncomment if needed for visible test
  const addProductForm = document.querySelector("form");
  const addBtn = document.querySelector(".add-btn");

  if (addProductForm) {
    addProductForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const nameInput = document.getElementById("productName");
      const priceInput = document.getElementById("price");
      const quantityInput = document.getElementById("quantity");
      const imageInput = document.getElementById("image");

      let errors = [];

      // Frontend Validation
      if (!nameInput.value.trim()) {
        errors.push("Product name is required.");
      }

      if (!priceInput.value || priceInput.value <= 0) {
        errors.push("Valid price is required.");
      }

      if (!quantityInput.value || quantityInput.value < 0) {
        errors.push("Valid quantity is required.");
      }

      if (!imageInput.files || imageInput.files.length === 0) {
        errors.push("Product image is required.");
      }

      if (errors.length > 0) {
        alert("Please fix the following errors:\n\n" + errors.join("\n"));
        return; // STOP execution here if invalid
      }

      // Disable button to prevent double submit
      addBtn.disabled = true;
      addBtn.textContent = "Adding...";

      const formData = new FormData(addProductForm);
      // Append checkbox/radio values if needed, or specific handling
      // The form fields match the PHP expectation names:
      // productName, category, price, quantity, description, image

      // Clear previous alerts if any (optional)

      fetch("../../Backend/add_product.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message); // Simple alert as requested for simplicity
            addProductForm.reset();
            // Optional: Redirect to view products
            // window.location.href = "view-products.html";
          } else {
            // Check if errors object has any keys
            if (data.errors && Object.keys(data.errors).length > 0) {
              // Construct error message
              let errorMsg = "Please fix the following errors:\n";
              for (const [key, value] of Object.entries(data.errors)) {
                errorMsg += `- ${value}\n`;
              }
              alert(errorMsg);
            } else {
              // Fallback to generic message (e.g. database error)
              alert(data.message || "An error occurred.");
            }
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred connecting to the server.");
        })
        .finally(() => {
          addBtn.disabled = false;
          addBtn.textContent = "Add Product";
        });
    });
  }
});
