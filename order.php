<?php
include ("connection.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// This only triggers when the user clicks 'Submit Order'
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['customer'])) {

    if (
        empty($_POST['customer']['firstName']) ||
        empty($_POST['customer']['lastName'])  ||
        empty($_POST['customer']['email'])     ||
        empty($_POST['customer']['address'])   ||
        !isset($_POST['items']) || !is_array($_POST['items']) || count($_POST['items']) === 0
    ) {
        http_response_code(400);
        exit("Bad request: Missing required fields.");
    }

    $connection->set_charset('utf8mb4');
    $connection->begin_transaction();

    try {
        /* =====================
           INSERT CUSTOMER
        ===================== */
        $c = $_POST['customer'];
        $stmt = $connection->prepare(
            "INSERT INTO customers (first_name, last_name, middle_name, company, address, email, phone)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", 
            trim($c['firstName']), trim($c['lastName']), trim($c['middleName'] ?? ''), 
            trim($c['company'] ?? ''), trim($c['address']), trim($c['email']), trim($c['phone'] ?? '')
        );
        $stmt->execute();
        $customer_id = $connection->insert_id;
        $stmt->close();

        /* =====================
           INSERT ORDER
        ===================== */
        $total = (float)($_POST['summary']['total'] ?? 0);
        $stmt = $connection->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
        $stmt->bind_param("id", $customer_id, $total);
        $stmt->execute();
        $order_id = $connection->insert_id;
        $stmt->close();

        /* =====================
           INSERT ORDER ITEMS
        ===================== */
        $stmt = $connection->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($_POST['items'] as $item) {
            $product_id = (string)$item['product'];
            $qty = max(1, (int)$item['qty']);
            $price = (float)$item['price'];
            $line_total = $qty * $price;
            $stmt->bind_param("isidd", $order_id, $product_id, $qty, $price, $line_total);
            $stmt->execute();
        }
        $stmt->close();

        $connection->commit();

        // REDIRECT TO PAYMENT INSTEAD OF SELF
        header("Location: payment.php?order_id=$order_id&amount=$total");
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        exit("DB ERROR: " . htmlspecialchars($e->getMessage()));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order — Gerbag Industrial</title>
    <link rel="stylesheet" href="order.css" />
</head>
<body>
    <div class="site">
      <header class="site-header">
        <div class="container header-inner">
          <div class="brand">
            <img src="backatlogo/image.png" alt="GerBAG INDUSTRIAL TECHNOLOGIES">
          </div>
          <nav class="site-nav">
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contact</button>
          </nav>
        </div>
      </header>

      <section class="hero hero-small">
        <div class="container">
          <h2>Place an Order</h2>
          <p class="lead">Fill customer details and select products below.</p>
        </div>
      </section>

      <main class="container order-layout">
        <section class="order-form-wrap">
          <h3>Customer Details</h3>
          <form id="orderForm" action="" method="post" class="order-form" novalidate>
            <div class="customer-grid">
              <label class="field"><span>First name</span><input name="customer[firstName]" required /></label>
              <label class="field"><span>Last name</span><input name="customer[lastName]" required /></label>
              <label class="field"><span>Middle name</span><input name="customer[middleName]" /></label>
              <label class="field"><span>Company</span><input name="customer[company]" /></label>
              
              <label class="field" style="grid-column: span 2;">
                <span>Exact Delivery Address</span>
                <textarea name="customer[address]" required placeholder="House No., Street, Brgy, City, Landmark" style="width: 100%; min-height: 80px; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"></textarea>
              </label>

              <label class="field"><span>Email</span><input type="email" name="customer[email]" required /></label>
              <label class="field"><span>Phone</span><input name="customer[phone]" /></label>
            </div>

            <h3 class="items-title">Order Items</h3>
            <table class="items-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th style="width:280px">Quantity</th>
                  <th style="width:270px">Unit price (PHP)</th>
                  <th style="width:110px">Remove</th>
                </tr>
              </thead>
              <tbody id="itemsBody"></tbody>
            </table>

            <div class="table-actions">
              <button type="button" id="addItemBtn" class="nav-btn">Add Item</button>
            </div>

            <section class="estimator">
              <h4>Order Summary</h4>
              <div class="estimate-values">
                <div><strong>Total Amount: <span id="totalEstimate">₱0.00</span></strong></div>
              </div>
            </section>

            <div class="form-actions">
              <button type="submit" class="primary-btn">Submit Order</button>
              <button type="reset" class="nav-btn">Reset</button>
            </div>

            <input type="hidden" name="summary[total]" id="totalInput">
            <div id="hiddenItems"></div>
          </form>
        </section>
      </main>

      <footer class="site-footer">
        <div class="container footer-grid">
          <div><strong>Gerbag Industrial Technologies Philippines</strong></div>
        </div>
        <div class="copyright">© 2010 Gerbag Industrial Technologies Philippines.</div>
      </footer>
    </div>

    <script>
      const PRODUCTS = [
        {id: 'SPUR_GEAR', name: 'SPUR GEAR'}, {id: 'HELICAL_GEAR', name: 'HELICAL GEAR'},
        {id: 'BEVEL_GEAR', name: 'BEVEL GEAR'}, {id: 'WORM_GEAR', name: 'WORM GEAR'},
        {id: 'CUSTOM_CNC_PART', name: 'CUSTOM CNC METAL PART'}
      ];

      const PRICES = { 'SPUR_GEAR': 500, 'HELICAL_GEAR': 750, 'BEVEL_GEAR': 900, 'WORM_GEAR': 1200, 'CUSTOM_CNC_PART': 1000 };

      const itemsBody = document.getElementById("itemsBody");
      const addItemBtn = document.getElementById("addItemBtn");
      const totalInput = document.getElementById("totalInput");

      function createItemRow() {
        const tr = document.createElement("tr");
        tr.className = "item-row";
        tr.innerHTML = `
          <td><select class="product">${PRODUCTS.map(p => `<option value="${p.id}">${p.name}</option>`).join("")}</select></td>
          <td><input type="number" class="qty" min="1" value="1"></td>
          <td><input type="number" class="price" min="0" step="0.01" value="0"></td>
          <td><button type="button" class="nav-btn remove">✖</button></td>`;

        itemsBody.appendChild(tr);
        const product = tr.querySelector(".product");
        const price = tr.querySelector(".price");

        product.onchange = () => { price.value = PRICES[product.value] || 0; updateEstimate(); };
        tr.querySelectorAll("input, select").forEach(el => el.addEventListener("input", updateEstimate));
        tr.querySelector(".remove").onclick = () => { tr.remove(); updateEstimate(); };
        product.onchange(); 
      }

      function updateEstimate() {
        let total = 0;
        const hiddenItems = document.getElementById("hiddenItems");
        hiddenItems.innerHTML = "";

        document.querySelectorAll(".item-row").forEach((row, i) => {
          const pid = row.querySelector(".product").value;
          const qty = Math.max(1, Number(row.querySelector(".qty").value));
          const prc = Math.max(0, Number(row.querySelector(".price").value));
          total += qty * prc;

          hiddenItems.innerHTML += `
            <input type="hidden" name="items[${i}][product]" value="${pid}">
            <input type="hidden" name="items[${i}][qty]" value="${qty}">
            <input type="hidden" name="items[${i}][price]" value="${prc}">`;
        });

        document.getElementById("totalEstimate").textContent = "₱" + total.toFixed(2);
        totalInput.value = total.toFixed(2);
      }

      addItemBtn.onclick = createItemRow;
      createItemRow();
    </script>
</body>
</html>