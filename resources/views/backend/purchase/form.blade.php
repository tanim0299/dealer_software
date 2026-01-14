    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="">
        <form id="purchaseForm" data-submit-route="{{ $route }}">
            @if(isset($method))
            @method('PUT')
            @endif
            <!-- Supplier & Date -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Select Supplier</label>
                    <select class="form-select" name="supplier_id" id="supplier_id" required>
                        <option value="">Choose Supplier</option>
                        @forelse($suppliers as $supplier)
                        <option {{ @$purchase->supplier_id == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->name }} - {{ $supplier->phone }}</option>
                        @empty
                        <option  value="">No Supplier Found!</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date" value="{{ @$purchase->purchase_date ?? date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row">
                <!-- LEFT: Product Section -->
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">Products</div>
                        <div class="card-body">
                            <div class="row">
                                <input type="text" class="form-control mb-3" id="searchProduct" placeholder="Search product...">
                                <div class="col-8">
                                    <div class="row" id="productList">
                                        <!-- Products will load here -->
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="selected-product-section" id="selectedProductSection" style="display: none;">
                                        <h6>Selected Product: 
                                            <span id="selectedProductName" class="text-primary fw-bold"></span>
                                        </h6>
                                        <div class="row">
                                            <!-- Sub-Unit Selection -->
                                            <div class="col-md-6">
                                                <label class="form-label">Select Sub-Unit</label>
                                                <select id="subUnit" class="form-select" onchange="onSubUnitChange()" disabled>
                                                    <option value="">Loading sub-units...</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Price Display -->
                                            <div class="col-md-6">
                                                <label class="form-label">Price per Unit</label>
                                                <div class="input-group">
                                                    <input type="number" id="price" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quantity & Total -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Quantity</label>
                                                <div class="input-group">
                                                    <input type="number" id="qty" class="form-control" placeholder="Qty" min="1" value="1" oninput="updateTotalPrice()">
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Total Price</label>
                                                <div class="input-group">
                                                    <input type="number" id="totalPrice" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Add to Cart Button -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success w-100" onclick="addToCart()" disabled id="addToCartBtn">
                                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden fields for storing data -->
                                        <input type="hidden" id="selectedProductId">
                                        <input type="hidden" id="selectedSubUnitId">
                                        <input type="hidden" id="selectedSubUnitName">
                                        <input type="hidden" id="selectedConversionFactor" value="1">
                                    </div>

                                    <div class="no-selection" id="noSelectionSection">
                                        <p class="text-muted text-center mt-3">Select a product to continue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Cart -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Cart</span>
                            <span class="badge bg-primary" id="cartCount">0 items</span>
                        </div>
                        <div class="card-body p-0">
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm mb-0">
                                    <thead class="sticky-top" style="background: #f8f9fa;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Disc</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartBody">
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Items:</strong> <span id="totalItems">0</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <strong>Subtotal:</strong> $<span id="subtotal">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cartHiddenInputs"></div>
                </div>
            </div>

            <!-- Summary -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Grand Total</label>
                            <input type="text" id="grandTotal" name="total_amount" class="form-control" readonly value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Discount</label>
                            <input type="number" id="discount" class="form-control" oninput="calculateDue()" value="{{ @$purchase->discount ?? 0 }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paid Amount</label>
                            <input type="number" id="paid" class="form-control" oninput="calculateDue()" value="{{ @$purchase->paid_amount ?? 0 }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Due Amount</label>
                            <input type="text" id="due" class="form-control" readonly value="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachment & Note -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Attachment</label>
                    <input type="file" class="form-control" name="attachment" id="attachment">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Note</label>
                    <textarea class="form-control" name="note" id="note" rows="2"></textarea>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
                <button type="button" class="btn btn-primary" onclick="submitPurchase()">
                    <i class="fas fa-check me-2"></i>Submit Purchase
                </button>
            </div>

        </form>
    </div>

    <style>
    .selected-product-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .product-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #dee2e6;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #0d6efd;
    }

    .product-card.selected {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }

    #cartBody tr:last-child td {
        border-bottom: none;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1;
    }
    </style>
    <script>
    window.editEntries = @json($entries ?? []);
    if (!Array.isArray(window.editEntries)) {
        window.editEntries = [window.editEntries];
    }
    let selectedProduct = null;
    let cart = [];
    let currentPage = 1;
    let totalPages = 1;
    let isLoading = false;
    let hasMoreProducts = true;

    document.addEventListener('DOMContentLoaded', function () {
        loadProducts();

        console.log('Edit entries:', window.editEntries);

        if (Array.isArray(window.editEntries) && window.editEntries.length > 0) {
            loadEditCart(window.editEntries);
        }
    });



    function loadEditCart(entries) {
       
        if (!Array.isArray(entries) || entries.length === 0) return;

        cart = entries.map(item => ({
            product_id: item.product_id,
            product_name: item.product_name,
            sub_unit_id: item.sub_unit_id,
            sub_unit_name: item.sub_unit_name,
            unit_data: parseFloat(item.unit_data) || 1,
            quantity: parseFloat(item.qty) || 1,
            unit_price: parseFloat(item.unit_price) || 0,
            discount: parseFloat(item.discount) || 0,
            total_price: parseFloat(item.total_price) || 0,
            final_quantity: parseFloat(item.final_quantity) || 0
        }));

        updateCart();
        updateSummary();
    }


    // Load products with search functionality
    function loadProducts(search = '', reset = true) {
        if (reset) {
            currentPage = 1;
            hasMoreProducts = true;
            document.getElementById('productList').innerHTML = '<div class="col-12 text-center"><div class="spinner-border spinner-border-sm text-primary"></div> Loading products...</div>';
        }
        
        if (isLoading) return;
        isLoading = true;
        
        fetch(`/api/products?search=${search}&page=${currentPage}`)
            .then(res => res.json())
            .then(response => {
               
                
                if (!response || !response.data) {
                    console.error('Invalid response structure:', response);
                    document.getElementById('productList').innerHTML = '<div class="col-12 text-center text-danger">Error loading products</div>';
                    isLoading = false;
                    return;
                }
                
                totalPages = response.last_page || 1;
                hasMoreProducts = currentPage < totalPages;
                
                let html = '';
                
                if (response.data.length === 0) {
                    if (reset) {
                        html = '<div class="col-12 text-center text-muted">No products found</div>';
                    }
                } else {
                    response.data.forEach(p => {
                       
                        const imageUrl = p.image ? `/storage/${p.image}` : '/images/default-product.png';
                        const hasSubUnits = p.sub_units && p.sub_units.length > 0;
                        const purchasePrice = parseFloat(p.purchase_price) || 0;
                        
                        html += `
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center p-2 product-card" 
                                onclick="selectProduct(${JSON.stringify(p).replace(/"/g, '&quot;')})"
                                id="product-${p.id}">
                                <img src="${imageUrl}"
                                    class="mx-auto mb-1"
                                    style="height:60px;width:60px;object-fit:contain;"
                                    onerror="this.src='/images/default-product.png'">
                                <div class="fw-semibold text-truncate small">${p.name}</div>
                                <div class="text-muted small mt-1">
                                    Price: ${purchasePrice.toFixed(2)}
                                </div>
                                <div class="text-muted small">
                                    ${hasSubUnits ? `<span class="badge bg-info">${p.sub_units.length} units</span>` : 'Single unit'}
                                </div>
                            </div>
                        </div>`;
                    });
                }
                
                if (reset) {
                    document.getElementById('productList').innerHTML = html;
                } else {
                    document.getElementById('productList').innerHTML += html;
                }
                
                currentPage++;
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading products:', error);
                document.getElementById('productList').innerHTML = '<div class="col-12 text-center text-danger">Error loading products. Please try again.</div>';
                isLoading = false;
            });
    }

    // Load more products for infinite scroll
    function loadMoreProducts() {
        if (isLoading || !hasMoreProducts) return;
        
        const search = document.getElementById('searchProduct').value;
        loadProducts(search, false);
    }

    // Select product function
    function selectProduct(product) {
        console.log('Selected Product:', product);
        
        // Remove selected class from all products
        document.querySelectorAll('.product-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selected class to clicked product
        const productCard = document.getElementById(`product-${product.id}`);
        if (productCard) {
            productCard.classList.add('selected');
        }
        
        selectedProduct = product;
        
        // Show selected product section
        document.getElementById('selectedProductSection').style.display = 'block';
        document.getElementById('noSelectionSection').style.display = 'none';
        
        // Update product name
        document.getElementById('selectedProductName').textContent = product.name;
        document.getElementById('selectedProductId').value = product.id;
        
        // Load sub-units
        loadSubUnits(product);
        
        // Reset quantity and price
        document.getElementById('qty').value = 1;
        document.getElementById('addToCartBtn').disabled = true;
        document.getElementById('price').value = '';
        document.getElementById('totalPrice').value = '';
    }

    // Load sub-units dynamically
    function loadSubUnits(product) {
        const subUnitSelect = document.getElementById('subUnit');
        const purchasePrice = parseFloat(product.purchase_price) || 0;
      
        // Check if product has sub_units array
        if (product.sub_units && product.sub_units.length > 0) {
            subUnitSelect.innerHTML = '<option value="">Select Sub-Unit</option>';
            
            product.sub_units.forEach(subUnit => {
                subUnitSelect.innerHTML += `
                    <option value="${subUnit.id}" 
                            data-price="${purchasePrice}"
                            data-conversion="${subUnit.unit_data || 1}"
                            data-name="${subUnit.name}">
                        ${subUnit.name} 
                        ${(subUnit.unit_data && subUnit.unit_data > 1) ? `(${subUnit.unit_data} base units)` : ''}
                    </option>`;
            });
            
            subUnitSelect.disabled = false;
            
            // Auto-select first option if only one exists
            if (product.sub_units.length === 1) {
                setTimeout(() => {
                    subUnitSelect.value = product.sub_units[0].id;
                    onSubUnitChange();
                }, 100);
            }
        } else {
            // If no sub-units, create a default option
            subUnitSelect.innerHTML = `
                <option value="0" 
                        data-price="${purchasePrice}"
                        data-conversion="1"
                        data-name="Piece">
                    Piece
                </option>`;
            subUnitSelect.disabled = false;
            
            // Auto-select the only option
            setTimeout(() => {
                subUnitSelect.value = "0";
                onSubUnitChange();
            }, 100);
        }
    }

    // Handle sub-unit change
    function onSubUnitChange() {
        const subUnitSelect = document.getElementById('subUnit');
        const selectedOption = subUnitSelect.options[subUnitSelect.selectedIndex];
        
        console.log('Sub-unit changed. Selected option:', selectedOption);
        
        if (!selectedOption || !selectedOption.value || selectedOption.value === "") {
            document.getElementById('addToCartBtn').disabled = true;
            document.getElementById('price').value = '';
            return;
        }
        
        // Get data from selected option
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const conversionFactor = parseFloat(selectedOption.dataset.conversion) || 1;
        const subUnitName = selectedOption.dataset.name || 'Unit';
        
        console.log('Extracted data - Price:', price, 'Name:', subUnitName, 'Conversion:', conversionFactor);
        
        // Update hidden fields
        document.getElementById('selectedSubUnitId').value = selectedOption.value;
        document.getElementById('selectedSubUnitName').value = subUnitName;
        document.getElementById('selectedConversionFactor').value = conversionFactor;
        
        // Update price display
        document.getElementById('price').value = price.toFixed(2);
        
        // Enable add to cart button
        document.getElementById('addToCartBtn').disabled = false;
        
        // Update total price
        updateTotalPrice();
    }

    // Update total price
    function updateTotalPrice() {
        const qty = parseFloat(document.getElementById('qty').value) || 0;
        const price = parseFloat(document.getElementById('price').value) || 0;
        const total = qty * price;
        
        document.getElementById('totalPrice').value = total.toFixed(2);
    }

    // Add to cart
    function addToCart() {
        if (!selectedProduct) {
            alert('Please select a product first');
            return;
        }
        
        const qty = parseFloat(document.getElementById('qty').value);
        const price = parseFloat(document.getElementById('price').value);
        const subUnitId = document.getElementById('selectedSubUnitId').value;
        const subUnitName = document.getElementById('selectedSubUnitName').value;
        const conversionFactor = parseFloat(document.getElementById('selectedConversionFactor').value);
        
        console.log('Adding to cart:', {
            qty, price, subUnitId, subUnitName, conversionFactor
        });
        
        if (qty <= 0 || isNaN(qty)) {
            alert('Please enter a valid quantity');
            return;
        }
        
        if (price <= 0 || isNaN(price)) {
            alert('Price must be greater than 0');
            return;
        }
        
        if (!subUnitId) {
            alert('Please select a sub-unit');
            return;
        }
        
        // Calculate total price
        const total = qty * price;
        
        // Create cart item
        const item = {
            product_id: selectedProduct.id,
            product_name: selectedProduct.name,
            sub_unit_id: subUnitId,
            sub_unit_name: subUnitName,
            unit_data: conversionFactor,
            quantity: qty,
            unit_price: price,
            discount: 0,
            total_price: total,
            image: selectedProduct.image,

            // ✅ calculate immediately
            final_quantity: qty * (1 / conversionFactor)
        };


        
        console.log('Cart item created:', item);
        
        // Check if item already exists in cart (same product and same sub-unit)
        const existingIndex = cart.findIndex(
            cartItem => cartItem.product_id === item.product_id && 
                    cartItem.sub_unit_id === item.sub_unit_id
        );
        
        if (existingIndex > -1) {
            // Update existing item - ADD the new quantity to existing quantity
            cart[existingIndex].quantity += qty;

            // Recalculate everything
            recalculateCartItem(existingIndex);
            cart[existingIndex].total_price = cart[existingIndex].quantity * cart[existingIndex].unit_price;
            console.log('Updated existing item in cart. New quantity:', cart[existingIndex].quantity);
        } else {
            // Add new item
            cart.push(item);
            recalculateCartItem(cart.length - 1);
            console.log('Added new item to cart');
        }
        
        // Update cart display
        updateCart();
        
        // Update summary
        updateSummary();
        
        
        
        // Only reset the form inputs, not the product selection
        resetSelectionInputs();
    }

    // Reset only the form inputs (not the product selection)
    function resetSelectionInputs() {
        document.getElementById('qty').value = 1;
        document.getElementById('price').value = '';
        document.getElementById('totalPrice').value = '';
        document.getElementById('addToCartBtn').disabled = true;
        
        // Reset hidden fields except product id
        document.getElementById('selectedSubUnitId').value = '';
        document.getElementById('selectedSubUnitName').value = '';
        document.getElementById('selectedConversionFactor').value = '1';
        
        // Reset sub-unit dropdown
        document.getElementById('subUnit').selectedIndex = 0;
    }

    function updateCart() {
        const cartBody = document.getElementById('cartBody');
        const cartCount = document.getElementById('cartCount');
        const totalItemsSpan = document.getElementById('totalItems');

        cartBody.innerHTML = '';

        if (cart.length === 0) {
            cartBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Cart is empty
                    </td>
                </tr>`;
            cartCount.textContent = '0 items';
            totalItemsSpan.textContent = '0';
            document.getElementById('subtotal').textContent = '0.00';
            document.getElementById('grandTotal').value = '0.00';
            document.getElementById('due').value = '0.00';
            return;
        }

        let totalItems = 0;
        let subtotal = 0;

        cart.forEach((item, index) => {
            totalItems += item.quantity;
            subtotal += item.total_price;

            cartBody.innerHTML += `
                <tr>
                    <td>
                        ${item.product_name}<br>
                        <small class="text-muted">${item.sub_unit_name}</small>
                    </td>

                    <!-- Quantity -->
                    <td>
                        <input type="number" class="form-control form-control-sm"
                            value="${item.quantity}" min="1"
                            onchange="updateCartItem(${index}, 'quantity', this.value)">
                    </td>

                    <!-- Unit -->
                    <td>
                        <select class="form-select form-select-sm"
                            onchange="updateCartSubUnit(${index}, this)">
                            <option value="${item.sub_unit_id}" selected>
                                ${item.sub_unit_name}
                            </option>
                        </select>
                    </td>

                    <!-- Price -->
                    <td>
                        <input type="number" class="form-control form-control-sm"
                            value="${item.unit_price}"
                            onchange="updateCartItem(${index}, 'unit_price', this.value)">
                    </td>

                    <!-- Discount -->
                    <td>
                        <input type="number" class="form-control form-control-sm"
                            value="${item.discount}" min="0"
                            onchange="updateCartItem(${index}, 'discount', this.value)">
                    </td>

                    <!-- Total -->
                    <td class="fw-semibold">
                        ${item.total_price.toFixed(2)}
                    </td>

                    <td>
                        <button class="btn btn-sm btn-danger"
                            onclick="removeFromCart(${index})">✕</button>
                    </td>
                </tr>
                `;

        });

        cartCount.textContent = `${cart.length} items`;
        totalItemsSpan.textContent = totalItems;
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);

        syncCartHiddenInputs();

    }


    // Update cart quantity with plus/minus buttons
    function updateCartQuantity(index, change) {
        if (index >= 0 && index < cart.length) {
            const newQuantity = cart[index].quantity + change;
            if (newQuantity >= 1) {
                cart[index].quantity = newQuantity;
                cart[index].total_price = newQuantity * cart[index].unit_price;
                updateCart();
                updateSummary();
            } else {
                // If quantity becomes 0, remove the item
                removeFromCart(index);
            }
        }
    }

    // Update cart quantity via input field
    function updateCartQuantityInput(index, newQuantity) {
        newQuantity = parseInt(newQuantity);
        if (index >= 0 && index < cart.length && newQuantity >= 1) {
            cart[index].quantity = newQuantity;
            cart[index].total_price = newQuantity * cart[index].unit_price;
            updateCart();
            updateSummary();
        } else if (newQuantity < 1) {
            // If quantity is less than 1, set to 1
            document.querySelector(`#cart-item-${index} input[type="number"]`).value = 1;
            cart[index].quantity = 1;
            cart[index].total_price = cart[index].unit_price;
            updateCart();
            updateSummary();
        }
    }

    // Remove item from cart
    function removeFromCart(index) {
        if (confirm('Are you sure you want to remove this item from cart?')) {
            const removedItem = cart[index];
            cart.splice(index, 1);
            updateCart();
            updateSummary();
            
        }
    }

    // Reset selection (when changing product or clearing)
    function resetSelection() {
        selectedProduct = null;
        
        // Remove selected class from all products
        document.querySelectorAll('.product-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Hide selection section
        document.getElementById('selectedProductSection').style.display = 'none';
        document.getElementById('noSelectionSection').style.display = 'block';
        
        // Reset all form fields
        document.getElementById('selectedProductName').textContent = '';
        document.getElementById('subUnit').selectedIndex = 0;
        document.getElementById('subUnit').disabled = true;
        document.getElementById('price').value = '';
        document.getElementById('qty').value = 1;
        document.getElementById('totalPrice').value = '';
        document.getElementById('addToCartBtn').disabled = true;
        
        // Reset hidden fields
        document.getElementById('selectedProductId').value = '';
        document.getElementById('selectedSubUnitId').value = '';
        document.getElementById('selectedSubUnitName').value = '';
        document.getElementById('selectedConversionFactor').value = '1';
    }

    // Update summary
    function updateSummary() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.total_price;
        });
        
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const paid = parseFloat(document.getElementById('paid').value) || 0;
        const due = subtotal - discount - paid;
        
        document.getElementById('grandTotal').value = subtotal.toFixed(2);
        document.getElementById('due').value = Math.max(0, due).toFixed(2);
        
        // Auto-adjust paid amount if due is negative
        if (due < 0) {
            document.getElementById('paid').value = (paid + Math.abs(due)).toFixed(2);
            document.getElementById('due').value = '0.00';
        }
    }

    function updateCartItem(index, field, value) {
        value = parseFloat(value) || 0;

        if (field === 'quantity' && value < 1) value = 1;
        if (field === 'discount' && value < 0) value = 0;
        if (field === 'unit_price' && value < 0) value = 0;

        cart[index][field] = value;

        recalculateCartItem(index);
    }

    function recalculateCartItem(index) {
        const item = cart[index];

        // Gross price
        const gross = item.quantity * item.unit_price;
        const discount = item.discount || 0;

        // Total price
        item.total_price = Math.max(0, gross - discount);

        // ✅ Final Quantity (BASE UNIT)
        item.final_quantity = calculateFinalQuantity(item);

        updateCart();
        updateSummary();
    }


    function updateCartSubUnit(index, select) {
        const option = select.options[select.selectedIndex];

        cart[index].sub_unit_id = option.value;
        cart[index].sub_unit_name = option.text;

        // 🔴 IMPORTANT: get sub_unit_data
        cart[index].unit_data = parseFloat(option.dataset.conversion || 1);

        recalculateCartItem(index); // ✅ recalculates final_quantity
    }





    // Calculate due amount
    function calculateDue() {
        const grandTotal = parseFloat(document.getElementById('grandTotal').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const paid = parseFloat(document.getElementById('paid').value) || 0;
        const due = grandTotal - discount - paid;
        
        document.getElementById('due').value = Math.max(0, due).toFixed(2);
    }

    // Search with debounce
    let searchTimeout = null;
    document.getElementById('searchProduct').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadProducts(this.value, true);
        }, 500);
    });

    // Reset form
    function resetForm() {
        if (cart.length > 0 && !confirm('Are you sure you want to reset the form? All cart items will be lost.')) {
            return;
        }
        
        // Reset cart
        cart = [];
        updateCart();
        
        // Reset selection
        resetSelection();
        
        // Reset summary fields
        document.getElementById('discount').value = 0;
        document.getElementById('paid').value = 0;
        updateSummary();
        
        // Reset other form fields
        document.getElementById('supplier_id').selectedIndex = 0;
        document.getElementById('note').value = '';
        document.getElementById('attachment').value = '';
        
        // Reload products
        loadProducts('', true);
    }

    // Submit purchase
    function submitPurchase() {
        if (cart.length === 0) {
            alert('Please add at least one product to cart');
            return;
        }

        const supplierId = document.getElementById('supplier_id').value;
        if (!supplierId) {
            alert('Please select a supplier');
            return;
        }

        const formData = new FormData();
        const form = document.getElementById('purchaseForm');
        const submitRoute = form.dataset.submitRoute;

        formData.append('supplier_id', supplierId);
        formData.append('purchase_date', document.querySelector('input[name="purchase_date"]').value);
        formData.append('total_amount', document.getElementById('grandTotal').value);
        formData.append('discount', document.getElementById('discount').value);
        formData.append('paid', document.getElementById('paid').value);
        formData.append('note', document.getElementById('note').value);
        formData.append('cart_items', JSON.stringify(cart));

        @if(isset($method))
        formData.append('_method','PUT');
        @endif

        const attachment = document.getElementById('attachment').files[0];
        if (attachment) {
            formData.append('attachment', attachment);
        }

        fetch(submitRoute, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(res => {

            // Response format: [200, "Purchase Stored", purchase_id]
            if (res[0] === 200) {
                const purchaseId = res[2];

                // Open invoice in new tab
                window.open(`/purchase_invoice/${purchaseId}`, '_blank');

                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert(res[1] ?? 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the purchase');
        });
    }

    function syncCartHiddenInputs() {
        const container = document.getElementById('cartHiddenInputs');
        container.innerHTML = '';

        cart.forEach((item, index) => {
            container.innerHTML += `
                <input type="hidden" name="cart_items[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="cart_items[${index}][sub_unit_id]" value="${item.sub_unit_id}">
                <input type="hidden" name="cart_items[${index}][final_quantity]"  value="${item.final_quantity}">
                <input type="hidden" name="cart_items[${index}][quantity]" value="${item.quantity}">
                <input type="hidden" name="cart_items[${index}][unit_price]" value="${item.unit_price}">
                <input type="hidden" name="cart_items[${index}][discount]" value="${item.discount}">
                <input type="hidden" name="cart_items[${index}][total_price]" value="${item.total_price}">
                <input type="hidden" name="cart_items[${index}][unit_data]" value="${item.unit_data}">
            `;
        });
    }

    function calculateFinalQuantity(item) {
        const qty = parseFloat(item.quantity) || 0;
        const unitData = parseFloat(item.unit_data) || 1;

        // final_qty = qty * (1 / sub_unit_data)
        return qty * (1 / unitData);
    }


    // Handle quantity input validation
    document.getElementById('qty').addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
        updateTotalPrice();
    });

    // Handle discount input validation
    document.getElementById('discount').addEventListener('input', function() {
        const grandTotal = parseFloat(document.getElementById('grandTotal').value) || 0;
        if (parseFloat(this.value) > grandTotal) {
            this.value = grandTotal;
        }
        calculateDue();
    });

    // Handle paid amount input validation
    document.getElementById('paid').addEventListener('input', function() {
        const grandTotal = parseFloat(document.getElementById('grandTotal').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const maxPaid = grandTotal - discount;
        
        if (parseFloat(this.value) > maxPaid) {
            this.value = maxPaid;
        }
        calculateDue();
    });

    // Add event listeners for better debugging
    document.getElementById('qty').addEventListener('change', function() {
        console.log('Quantity changed to:', this.value);
        updateTotalPrice();
    });

    document.getElementById('subUnit').addEventListener('change', function() {
        console.log('Sub-unit changed to:', this.value);
        onSubUnitChange();
    });
    </script>