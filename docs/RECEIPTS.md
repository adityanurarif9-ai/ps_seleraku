# PDF receipts and map

Authenticated order owners and admins can download A4 PDF receipts from rental details, food orders, payment results and admin order tables. Responses use application/pdf, attachment filenames and private/no-store headers. Names, item snapshots, quantities, totals, unit numbers, schedules and statuses come from the database. Untrusted text is escaped; PDF remote resources, PHP and JavaScript are disabled. Temporary renderer files and font caches use the system temporary directory.

Payments are still simulated. Every PDF prominently says DEMO and not proof of payment. Simulation amount and current order amount are separate so rental extensions cannot silently become paid. No real tax invoice or payment settlement is claimed.

The browser downloads the file, normally into Downloads/Unduhan or the location selected by the user. Open the PDF to print it. The app cannot choose a device folder silently.

The footer lazily embeds a Google map of SMK Telkom Purwokerto and links to Google Maps. The school is labeled as an area landmark, not the business address; configured business contact details are retained.
