
<?php
include ("connection.php");

// Make MySQLi throw exceptions on errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // (Optional) quick validation to fail early
    if (
        empty($_POST['customer']['firstName']) ||
        empty($_POST['customer']['lastName'])  ||
        empty($_POST['customer']['email'])     ||
        empty($_POST['summary']['delivery_zone_code']) ||
        !isset($_POST['items']) || !is_array($_POST['items']) || count($_POST['items']) === 0
    ) {
        http_response_code(400);
        exit("Bad request: Missing required fields or no items.");
    }

    // Ensure charset
    $connection->set_charset('utf8mb4');

    // Canonical lists (used to upsert FK targets)
    $DELIVERY_ZONES = [
        'MM'  => ['name' => 'Metro Manila',                     'fee' => 200.00],
        'LUZ' => ['name' => 'Luzon (outside Metro Manila)',     'fee' => 500.00],
        'VIS' => ['name' => 'Visayas',                          'fee' => 800.00],
        'MIN' => ['name' => 'Mindanao',                         'fee' => 1200.00],
    ];
    $PRODUCT_NAMES = [
        'SPUR_GEAR'         => 'SPUR GEAR',
        'HELICAL_GEAR'      => 'HELICAL GEAR',
        'BEVEL_GEAR'        => 'BEVEL GEAR',
        'WORM_GEAR'         => 'WORM GEAR',
        'SHAFT_COUPLING'    => 'SHAFT & COUPLING',
        'INDUSTRIAL_FLANGE' => 'INDUSTRIAL FLANGE',
        'BUSHING'           => 'BUSHING',
        'BEARING_HOUSING'   => 'BEARING HOUSING',
        'MOUNTING_BRACKET'  => 'MOUNTING BRACKET',
        'CUSTOM_CNC_PART'   => 'CUSTOM CNC METAL PART',
    ];
    $PRODUCT_BASE_PRICES = [
        'SPUR_GEAR'         => 500.00,
        'HELICAL_GEAR'      => 750.00,
        'BEVEL_GEAR'        => 900.00,
        'WORM_GEAR'         => 1200.00,
        'SHAFT_COUPLING'    => 600.00,
        'INDUSTRIAL_FLANGE' => 450.00,
        'BUSHING'           => 350.00,
        'BEARING_HOUSING'   => 800.00,
        'MOUNTING_BRACKET'  => 400.00,
        'CUSTOM_CNC_PART'   => 1000.00,
    ];

    $connection->begin_transaction();

    try {
        /* =====================
           CUSTOMER
        ===================== */
        $c = $_POST['customer'];

        $stmt = $connection->prepare(
            "INSERT INTO customers 
             (first_name, last_name, middle_name, company, email, phone)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssss",
            trim($c['firstName'] ?? ''),
            trim($c['lastName'] ?? ''),
            trim($c['middleName'] ?? ''),
            trim($c['company'] ?? ''),
            trim($c['email'] ?? ''),
            trim($c['phone'] ?? '')
        );
        $stmt->execute();
        $customer_id = $connection->insert_id;
        $stmt->close();

        /* =====================
           ORDER (ensure zone exists)
        ===================== */
        $s = $_POST['summary'];

        // Normalize values
        $delivery_zone_code = strtoupper(trim($s['delivery_zone_code'] ?? ''));  // 'MM', 'LUZ', 'VIS', 'MIN'
        $rush               = (int)($s['rush'] ?? 0);
        $subtotal           = (float)($s['subtotal'] ?? 0);
        $delivery_fee       = (float)($s['delivery_fee'] ?? 0);
        $rush_fee           = (float)($s['rush_fee'] ?? 0);
        $total              = (float)($s['total'] ?? 0);

        // DEV log: see what zone came in
        error_log("POST delivery_zone_code = [{$delivery_zone_code}]");

        // Validate zone against canonical list and upsert it to satisfy FK
        if (!isset($DELIVERY_ZONES[$delivery_zone_code])) {
            throw new mysqli_sql_exception("Invalid delivery zone code: {$delivery_zone_code}");
        }
        $zoneName = $DELIVERY_ZONES[$delivery_zone_code]['name'];
        $zoneFee  = $DELIVERY_ZONES[$delivery_zone_code]['fee'];

        $stmt = $connection->prepare(
            "INSERT INTO delivery_zones (code, name, fee)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE name = VALUES(name), fee = VALUES(fee)"
        );
        $stmt->bind_param("ssd", $delivery_zone_code, $zoneName, $zoneFee);
        $stmt->execute();
        $stmt->close();

        /* =====================
           Validate/upsert PRODUCTS to satisfy FK
        ===================== */
        $postedItems = $_POST['items'];
        $productIdsUnique = [];
        foreach ($postedItems as $it) {
            $pid = trim((string)($it['product'] ?? ''));
            if ($pid !== '') { $productIdsUnique[$pid] = true; }
        }
        // Upsert products used in this order (if products table is empty, FK will still pass)
        foreach (array_keys($productIdsUnique) as $pid) {
            $pname = $PRODUCT_NAMES[$pid]        ?? $pid;
            $pbase = (float)($PRODUCT_BASE_PRICES[$pid] ?? 0.00);

            $stmt = $connection->prepare(
                "INSERT INTO products (id, name, base_price)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE name = VALUES(name), base_price = VALUES(base_price)"
            );
            $stmt->bind_param("ssd", $pid, $pname, $pbase);
            $stmt->execute();
            $stmt->close();
        }

        /* =====================
           INSERT ORDER
        ===================== */
        $stmt = $connection->prepare(
            "INSERT INTO orders
             (customer_id, delivery_zone_code, rush, subtotal, delivery_fee, rush_fee, total)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isidddd",
            $customer_id,
            $delivery_zone_code,
            $rush,
            $subtotal,
            $delivery_fee,
            $rush_fee,
            $total
        );
        $stmt->execute();
        $order_id = $connection->insert_id;
        $stmt->close();

        /* =====================
           ORDER ITEMS
        ===================== */
        $stmt = $connection->prepare(
            "INSERT INTO order_items
             (order_id, product_id, quantity, unit_price, line_total)
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($postedItems as $item) {
            $product_id = (string)($item['product'] ?? '');
            $qty        = max(1, (int)($item['qty'] ?? 1));
            $price      = (float)($item['price'] ?? 0);
            $line_total = $qty * $price;

            // Basic guard
            if ($product_id === '') {
                throw new mysqli_sql_exception("Empty product_id in items.");
            }

            $stmt->bind_param("isidd", $order_id, $product_id, $qty, $price, $line_total);
            $stmt->execute();
        }
        $stmt->close();

        $connection->commit();

        // Redirect to the same page with success flag
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;

    } catch (mysqli_sql_exception $e) {
        $connection->rollback();
        http_response_code(500);

        // Log the exact DB error (keep for diagnostics)
        error_log("Order processing failed: " . $e->getMessage());

        // TEMPORARILY show the DB error on screen to diagnose:
        echo "<pre style='color:#b00;white-space:pre-wrap'>DB ERROR: " . htmlspecialchars($e->getMessage()) . "</pre>";
        exit("An error occurred while processing your order. Please try again later.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <heade>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order — Gerbag Industrial</title>
    main.css
    <link rel="stylesheet" href="order.css" />
  </heade>
  <body>
    <div class="site">
      <header class="site-header">
        <div class="container header-inner">
          <div class="brand">
            <img src="backatlogo/image.png" alt="GerBAG INDUSTRIAL TECHNOLOGIES</h1>
          </div>
          <nav class="site-nav" aria-label="Main navigation">
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contact</button>
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
              <label class="field">
                <span>First name</span>
                <input name="customer[firstName]" id="customerFirst" required placeholder="First name" />
              </label>

              <label class="field">
                <span>Last name</span>
                <input name="customer[lastName]" id="customerLast" required placeholder="Last name" />
              </label>

              <label class="field">
                <span>Middle name</span>
                <input name="customer[middleName]" id="customerMiddle" placeholder="Middle name or N/A" />
              </label>

              <label class="field">
                <span>Company</span>
                <input name="customer[company]" id="customerCompany" placeholder="Company (optional)" />
              </label>

              <label class="field">
                <span>Email</span>
                <input type="email" name="customer[email]" id="customerEmail" required placeholder="you@example.com" />
              </label>

              <label class="field">
                <span>Phone</span>
                <input name="customer[phone]" id="customerPhone" placeholder="Contact number" />
              </label>
            </div>

            <h3 class="items-title">Order Items</h3>
            <p class="muted">Choose product and quantity. Click Add item to include more rows.</p>

            <table class="items-table" aria-label="Order items">
              <thead>
                <tr>
                  <th>Product</th>
                  <th style="width:280px">Quantity</th>
                  <th style="width:270px">Unit price (PHP)</th>
                  <th style="width:110px">Remove</th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                <!-- initial row inserted by JS -->
              </tbody>
            </table>

            <div class="table-actions">
              <button type="button" id="addItemBtn" class="nav-btn">Add Item</button>
            </div>

            <section class="estimator">
              <h4>Estimate</h4>
              <div class="estimator-controls">
                <label>
                  Delivery zone
                  <select id="deliveryZone">
                    <option value="MM">Metro Manila</option>
                    <option value="LUZ">Luzon (outside Metro Manila)</option>
                    <option value="VIS">Visayas</option>
                    <option value="MIN">Mindanao</option>
                  </select>
                </label>

                <label style="margin-left:1rem;">
                  <input type="checkbox" id="rushToggle" /> Rush (20% surcharge)
                </label>
              </div>

              <div class="estimate-values">
                <div>Subtotal: <span id="subtotal">₱0.00</span></div>
                <div>Delivery fee: <span id="deliveryFee">₱0.00</span></div>
                <div>Rush fee: <span id="rushFee">₱0.00</span></div>
                <div style="margin-top:0.25rem"><strong>Total estimate: <span id="totalEstimate">₱0.00</span></strong></div>
                <div id="estimateNote" class="muted" style="margin-top:0.5rem">If any item has no unit price, enter an estimated price in the Unit price field.</div>
              </div>
            </section>

            <div class="form-actions">
              <button type="submit" class="primary-btn">Submit Order</button>
              <button type="reset" class="nav-btn">Reset</button>
            </div>

            <div id="orderMessage" class="form-message" role="status" aria-live="polite"></div>

            <!-- hidden totals -->
            <input type="hidden" name="summary[subtotal]" id="subtotalInput">
            <input type="hidden" name="summary[delivery_fee]" id="deliveryFeeInput">
            <input type="hidden" name="summary[rush_fee]" id="rushFeeInput">
            <input type="hidden" name="summary[total]" id="totalInput">

            <!-- NEW: delivery zone & rush -->
            <input type="hidden" name="summary[delivery_zone_code]" id="deliveryZoneInput">
            <input type="hidden" name="summary[rush]" id="rushInput">

            <!-- hidden items container -->
            <div id="hiddenItems"></div>
          </form>
        </section>
      </main>

      <footer class="site-footer">
        <div class="container footer-grid">
          <div>
            <strong>Gerbag Industrial Technologies Philippines</strong>
            <address>
              Phone: +(632) 838-2976 | +(632) 838-5976 | +(632) 838-4901
            </address>
          </div>

          <div class="footer-actions">
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contact</button>
          </div>
        </div>
        <div class="copyright">© 2010 Gerbag Industrial Technologies Philippines. All rights reserved.</div>
      </footer>
    </div>

    <script>
      // --- CNC Products ---
      const PRODUCTS = [
        {id: 'SPUR_GEAR',         name: 'SPUR GEAR'},
        {id: 'HELICAL_GEAR',      name: 'HELICAL GEAR'},
        {id: 'BEVEL_GEAR',        name: 'BEVEL GEAR'},
        {id: 'WORM_GEAR',         name: 'WORM GEAR'},
        {id: 'SHAFT_COUPLING',    name: 'SHAFT & COUPLING'},
        {id: 'INDUSTRIAL_FLANGE', name: 'INDUSTRIAL FLANGE'},
        {id: 'BUSHING',           name: 'BUSHING'},
        {id: 'BEARING_HOUSING',   name: 'BEARING HOUSING'},
        {id: 'MOUNTING_BRACKET',  name: 'MOUNTING BRACKET'},
        {id: 'CUSTOM_CNC_PART',   name: 'CUSTOM CNC METAL PART'}
      ];

      // Optional price list (PHP)
      const PRICES = {
        'SPUR_GEAR': 500,
        'HELICAL_GEAR': 750,
        'BEVEL_GEAR': 900,
        'WORM_GEAR': 1200,
        'SHAFT_COUPLING': 600,
        'INDUSTRIAL_FLANGE': 450,
        'BUSHING': 350,
        'BEARING_HOUSING': 800,
        'MOUNTING_BRACKET': 400,
        'CUSTOM_CNC_PART': 1000
      };

      // Delivery zones
      const DELIVERY_ZONES = {
        MM:  { name: 'Metro Manila',                    fee: 200 },
        LUZ: { name: 'Luzon (outside Metro Manila)',    fee: 500 },
        VIS: { name: 'Visayas',                          fee: 800 },
        MIN: { name: 'Mindanao',                        fee: 1200 }
      };

      const itemsBody         = document.getElementById("itemsBody");
      const addItemBtn        = document.getElementById("addItemBtn");
      const deliveryZone      = document.getElementById("deliveryZone");
      const rushToggle        = document.getElementById("rushToggle");

      const subtotalInput     = document.getElementById("subtotalInput");
      const deliveryFeeInput  = document.getElementById("deliveryFeeInput");
      const rushFeeInput      = document.getElementById("rushFeeInput");
      const totalInput        = document.getElementById("totalInput");

      const deliveryZoneInput = document.getElementById("deliveryZoneInput");
      const rushInput         = document.getElementById("rushInput");

      const form              = document.getElementById("orderForm");

      // Create item row (structure matches your table)
      function createItemRow() {
        const tr = document.createElement("tr");
        tr.className = "item-row";

        tr.innerHTML = `
          <td>
            <select class="product">
              ${PRODUCTS.map(p => `<option value="${p.id}">${p.name}</option>`).join("")}
            </select>
          </td>
          <td>
            <input type="number" class="qty" min="1" value="1">
          </td>
          <td>
            <input type="number" class="price" min="0" step="0.01" value="0">
          </td>
          <td>
            <button type="button" class="nav-btn remove">✖</button>
          </td>
        `;

        itemsBody.appendChild(tr);

        const product = tr.querySelector(".product");
        const price   = tr.querySelector(".price");

        product.onchange = () => {
          price.value = PRICES[product.value] || 0;
          updateEstimate();
        };

        tr.querySelectorAll("input, select").forEach(el =>
          el.addEventListener("input", updateEstimate)
        );

        tr.querySelector(".remove").onclick = () => {
          tr.remove();
          updateEstimate();
        };

        updateEstimate();
      }

      addItemBtn.onclick = createItemRow;
      createItemRow(); // first row

      // =========================
      // ESTIMATE + FORM SYNC
      // =========================
      function peso(v) {
        return "₱" + Number(v).toFixed(2);
      }

      function updateEstimate() {
        let subtotal = 0;
        const hiddenItems = document.getElementById("hiddenItems");
        hiddenItems.innerHTML = "";

        document.querySelectorAll(".item-row").forEach((row, i) => {
          const product = row.querySelector(".product").value;
          const qty     = Math.max(1, Number(row.querySelector(".qty").value));
          const price   = Math.max(0, Number(row.querySelector(".price").value));

          subtotal += qty * price;

          hiddenItems.innerHTML += `
            <input type="hidden" name="items[${i}][product]" value="${product}">
            <input type="hidden" name="items[${i}][qty]" value="${qty}">
            <input type="hidden" name="items[${i}][price]" value="${price}">
          `;
        });

        const zone     = (deliveryZone.value || '').trim().toUpperCase();
        const delivery = DELIVERY_ZONES[zone]?.fee ?? 0;
        const rush     = rushToggle.checked ? subtotal * 0.2 : 0;
        const total    = subtotal + delivery + rush;

        // Update UI
        document.getElementById("subtotal").textContent      = peso(subtotal);
        document.getElementById("deliveryFee").textContent   = peso(delivery);
        document.getElementById("rushFee").textContent       = peso(rush);
        document.getElementById("totalEstimate").textContent = peso(total);

        // Update hidden inputs (totals)
        subtotalInput.value     = subtotal.toFixed(2);
        deliveryFeeInput.value  = delivery.toFixed(2);
        rushFeeInput.value      = rush.toFixed(2);
        totalInput.value        = total.toFixed(2);

        // Update hidden inputs (order flags)
        deliveryZoneInput.value = zone;                 // 'MM', 'LUZ', 'VIS', 'MIN'
        rushInput.value         = rushToggle.checked ? 1 : 0;
      }

      // Keep estimate in sync on UI changes
      deliveryZone.onchange = updateEstimate;
      rushToggle.onchange   = updateEstimate;

      // Ensure hidden inputs are fresh right before submit
      form.addEventListener('submit', function() {
        updateEstimate();
        // Optional debug
        // console.log('Submitting delivery_zone_code:', deliveryZoneInput.value);
      });
    </script>
  </body>
</html>
