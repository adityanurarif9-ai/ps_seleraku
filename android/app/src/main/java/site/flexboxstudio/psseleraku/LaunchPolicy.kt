package site.flexboxstudio.psseleraku

import java.net.URI

/** Limits links received from other apps to our HTTPS origin. */
object LaunchPolicy {
    const val HOST = "ps.flexboxstudio.site"
    const val START_URL = "https://ps.flexboxstudio.site/"

    fun approvedUrl(candidate: String?): String {
        if (candidate.isNullOrBlank()) return START_URL
        val parsed = runCatching { URI(candidate) }.getOrNull() ?: return START_URL
        return if (parsed.scheme.equals("https", ignoreCase = true) &&
            parsed.host.equals(HOST, ignoreCase = true) &&
            parsed.rawUserInfo == null && parsed.port in listOf(-1, 443)
        ) candidate else START_URL
    }
}
