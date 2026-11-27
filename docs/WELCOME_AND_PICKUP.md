# Welcome, cart menu and take-home requirements

The public-site welcome appears once per browser-tab session for three seconds. It is independent of network completion, has a no-JavaScript fallback, and is dismissed on bfcache restore. Normal navigation retains the short progress bar. Reduced-motion preferences disable movement.

The cart offers current visible food, drinks and snacks with stock-aware plus/minus controls. All changes use CSRF-protected existing cart routes and server prices; console reservation and cafe checkout remain separate.

New take-home booking HTTP requests require an identity image, permission for rental review, and acknowledgement to bring the original identity document and agreed guarantee to the shop. No guarantee amount or identity-number collection is invented. Student cards are supported and younger renters are directed to bring a parent/guardian. Lounge bookings do not collect identity photos.

JPG/PNG/WebP images are checked by MIME, dimensions, size (2 MiB), and decoded-pixel limit. GD normalizes them to JPEG up to 1600px and strips source metadata. Images are encrypted with Laravel Crypt and stored in a separate longText database field, hidden from model JSON and never written to public storage. The signed demo payment page, PDFs, admin notifications and polling do not include identity images. The authenticated image route authorizes the owner or admin, sends no-store/nosniff, and refuses expired/terminal records. Back up APP_KEY securely; changing it prevents decryption of existing photos.

Photos are deleted on reservation completion/cancellation/rejection. Access expires three days after the scheduled end. Expired ciphertext is purged when the admin reservation table is loaded or a new identity is attached, and via rentals:purge-identities (also registered in Laravel's daily schedule; this does not presume a Vercel cron runner). Admin checks original identity and guarantee at pickup before starting new take-home sessions. Old reservations retain their previous flow. Only synthetic images are used for automated and browser QA.
