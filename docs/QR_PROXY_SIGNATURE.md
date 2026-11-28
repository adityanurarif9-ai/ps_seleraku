# Demo QR signature behind the production proxy

Reproduced on the live site: a newly generated absolute HTTPS scan URL returned 403 Invalid signature immediately. Production forceScheme generated HTTPS while the request reached FrankenPHP/Caddy over HTTP and the default Laravel TrustProxies middleware did not trust the Vercel TLS-termination hop. Laravel validates the absolute request URL, so the HMAC input differed.

The production middleware now trusts only X-Forwarded-Proto from loopback/RFC1918/IPv6-private forwarding hops in Vercel's private container topology; public client addresses are not trusted. Forwarded host, port, prefix and client IP are not enabled. Local direct HTTP is unchanged. Absolute signatures, the 30-minute expiry, CSRF, rate limits, amount snapshots and owner-only order/timer access remain intact. No signed parameter is ignored. Invalid/expired scan links still return 403, with instructions to refresh checkout instead of a blank framework error.

ScanProxyTest reproduces HTTP upstream plus HTTPS forwarding, asserts success for fresh links without mutating payment, and checks tampering/expiry still fail. It verifies forwarded host/port/IP do not change host, scheme-derived port or client IP. The ordinary HTTP scanner path remains covered too.
