# Eunoia IA - Project Roadmap & TODO

## Phase 1: Core Architecture & Security 🛡️
- [x] **Centralize Layouts**: Create `header.php` and `footer.php` using Tailwind CSS.
- [x] **Security Audit**: Convert inline SQL to **Prepared Statements** in login and registration.
- [x] **Enhanced Session Management**: Navigation dynamically updates based on `$_SESSION['role']`.

## Phase 2: Authentication & Onboarding 👤
- [x] **Database Schema**: Unified `users` table with roles (Customer, Seller, Admin).
- [x] **Smart Registration**: Updated `register.php` with role selection.
- [x] **Role-Based Redirects**: Login accurately sends users to Admin, Seller, or Customer home.

## Phase 3: Seller Ecosystem 🏪
- [x] **Seller Dashboard (`seller_dashboard.php`)**:
    - [x] Statistics: Total products and units.
    - [x] Product Management: Add and Delete products scoped to the seller.
    - [x] Order Tracking: View list of orders containing seller items.
- [ ] **Inventory Control**: Real-time stock updates (Next Phase).

## Phase 4: Customer Experience & Storefront 🛍️
- [x] **Interactive Gallery**: AJAX category filtering implemented in `index.php`.
- [x] **Product Details**: Dedicated layout in `product_details.php` with seller integration.
- [x] **AJAX Cart System**:
    - [x] Enhanced `cart.php` with DOM-based updates.
    - [x] Persistent storage via `localStorage`.

## Phase 5: Transaction & Checkout Flow 💸
- [ ] **Multi-Step Checkout**:
    - [ ] Step 1: Shipping Information.
    - [ ] Step 2: GCash/Payment Integration (Simulated).
- [ ] **Order Processing (`process_order.php`)**:
    - [ ] Use AJAX to send cart data to the server.
    - [ ] Insert into `orders` and `order_items` tables.
    - [ ] Deduct stock from the `products` table.
- [ ] **Dynamic Receipts**: Generate a printable HTML receipt after successful checkout.

## Phase 6: Admin Oversight ⚖️
- [ ] **Global Dashboard**: Manage all users (activate/deactivate accounts).
- [ ] **Platform Analytics**: View total revenue and platform-wide top-selling products.
- [ ] **Dispute Management**: View order histories to resolve issues between customers and sellers.

## Phase 7: Polish & Optimization ✨
- [ ] **Responsive Design**: Final pass with Tailwind to ensure the site is perfect on mobile devices.
- [ ] **Loading States**: Add skeleton loaders or spinners for AJAX requests.
- [ ] **Image Optimization**: Implement logic to resize/compress uploaded product images.

---
*Current Status: Foundation set. Moving toward Phase 1 & 2 integration.*
