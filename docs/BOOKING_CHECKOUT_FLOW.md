# Checkout and admin management

Customer booking creates a pending order and test payment, then redirects to checkout. In demo mode, pending unpaid orders show Awaiting demo payment. Server-side guards refuse rental confirmation/start and food preparation until simulated_paid. After paying, the customer returns to the waiting order, not an automatically started session. Admin confirmation and start are separate actions with availability checks. Walk-in sessions started directly by an admin remain supported.

The QR contains a Laravel signed URL expiring after 30 minutes. It opens in a normal phone browser, not a DANA or bank QRIS scanner. The public page reveals only demo reference, amount and result. Anyone holding that scoped link can complete only that simulated payment. POST still requires CSRF. Completion is idempotent, locks the order before payment, refuses inactive orders, ignores posted amounts, and notifies admins once. The original authenticated checkout polls an owner-only status endpoint and redirects after success. No merchant gateway, wallet balance, PIN or real money is involved.

Catalog supports create/edit/delete for food, drinks, snacks and console models. Stock determines numbered console cards. Referenced products cannot be deleted; admins can hide them via Edit to preserve receipts and history.

Inventory/lifecycle tests explicitly use the legacy/manual configuration; PaymentFlowTest covers the payment-required production demo path, signed link expiry, CSRF, anonymity, idempotency, the full booking/payment/approval/timer story and catalog CRUD synchronization.
