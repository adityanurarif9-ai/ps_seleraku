# Payment simulation

The owner requested test mode because no Midtrans merchant account is active. Checkout is a local simulator, not a Midtrans sandbox connection. QRIS, DANA, GoPay, OVO and bank are test choices only. There is no QR code, provider request, real charge, COD option, or proof of settlement. A successful test records simulated_paid separately from reservation and food order status. Admin approval and stock checks remain required.

PAYMENT_DEMO_ENABLED defaults to true for this requested demo. Set it to false to disable new simulations. Activate and verify a merchant account before implementing real checkout, signed notifications, reconciliation and server-side provider status checks. Never treat test_payments as real receipts.

The expiry alert requires the rental page to stay open. The customer explicitly enables audio and optional notifications. Alarm volume follows the device, repeats for at most about one minute, and can be stopped. Browser or operating system background restrictions can delay or suppress audio. No closed-app alarm is promised.
