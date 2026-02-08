<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order — Gerbag Industrial</title>
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="order.css" />
  </head>
  <body>
    <div class="site">
      <header class="site-header">
        <div class="container header-inner">
          <div class="brand">
            <img src="backatlogo/image.png" alt="Gerbag logo" class="logo" />
            <h1>GERBAG INDUSTRIAL TECHNOLOGIES</h1>
          </div>
          <nav class="site-nav" aria-label="Main navigation">
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contacts</button>
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
          <form id="orderForm" action="/api/orders" method="post" class="order-form" novalidate>
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
            <button class="primary-btn" onclick="location.href='products.php'">Products</button>
          </div>
        </div>
        <div class="copyright">© 2010 Gerbag Industrial Technologies Philippines. All rights reserved.</div>
      </footer>
    </div>

    <script>
      // --- CNC Products ---
      const PRODUCTS = [
        {id: 'SPUR_GEAR', name: 'SPUR GEAR'},
        {id: 'HELICAL_GEAR', name: 'HELICAL GEAR'},
        {id: 'BEVEL_GEAR', name: 'BEVEL GEAR'},
        {id: 'WORM_GEAR', name: 'WORM GEAR'},
        {id: 'SHAFT_COUPLING', name: 'SHAFT & COUPLING'},
        {id: 'INDUSTRIAL_FLANGE', name: 'INDUSTRIAL FLANGE'},
        {id: 'BUSHING', name: 'BUSHING'},
        {id: 'BEARING_HOUSING', name: 'BEARING HOUSING'},
        {id: 'MOUNTING_BRACKET', name: 'MOUNTING BRACKET'},
        {id: 'CUSTOM_CNC_PART', name: 'CUSTOM CNC METAL PART'}
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
        MM: { name: 'Metro Manila', fee: 200 },
        LUZ: { name: 'Luzon (outside Metro Manila)', fee: 500 },
        VIS: { name: 'Visayas', fee: 800 },
        MIN: { name: 'Mindanao', fee: 1200 }
      };
      
      
const itemsBody = document.getElementById("itemsBody");
const addItemBtn = document.getElementById("addItemBtn");

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

  // auto price
  const product = tr.querySelector(".product");
  const price = tr.querySelector(".price");

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
    const qty = Number(row.querySelector(".qty").value);
    const price = Number(row.querySelector(".price").value);

    subtotal += qty * price;

    hiddenItems.innerHTML += `
      <input type="hidden" name="items[${i}][product]" value="${product}">
      <input type="hidden" name="items[${i}][qty]" value="${qty}">
      <input type="hidden" name="items[${i}][price]" value="${price}">
    `;
  });

  const zone = deliveryZone.value;
  const delivery = DELIVERY_ZONES[zone].fee;
  const rush = rushToggle.checked ? subtotal * 0.2 : 0;
  const total = subtotal + delivery + rush;

  // Update UI
  document.getElementById("subtotal").textContent = peso(subtotal);
  document.getElementById("deliveryFee").textContent = peso(delivery);
  document.getElementById("rushFee").textContent = peso(rush);
  document.getElementById("totalEstimate").textContent = peso(total);

  // Update hidden inputs (totals)
  subtotalInput.value     = subtotal;
  deliveryFeeInput.value  = delivery;
  rushFeeInput.value      = rush;
  totalInput.value        = total;

  // Update hidden inputs (order flags)
  deliveryZoneInput.value = zone;                 // 'MM', 'LUZ', etc.
  rushInput.value         = rushToggle.checked ? 1 : 0;
}

  