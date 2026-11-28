package site.flexboxstudio.psseleraku

import org.junit.Assert.assertEquals
import org.junit.Test

class LaunchPolicyTest {
    @Test fun firstLaunchUsesTheLiveWebsite() {
        assertEquals(LaunchPolicy.START_URL, LaunchPolicy.approvedUrl(null))
        assertEquals(LaunchPolicy.START_URL, LaunchPolicy.approvedUrl(""))
    }
    @Test fun keepsBookingPathsAndSignedQrParametersUnchanged() {
        val booking = "https://ps.flexboxstudio.site/reservasi/42"
        val qr = "https://ps.flexboxstudio.site/demo-bayar/42?expires=1234567890&signature=abc"
        assertEquals(booking, LaunchPolicy.approvedUrl(booking))
        assertEquals(qr, LaunchPolicy.approvedUrl(qr))
        val httpsPort = "https://ps.flexboxstudio.site:443/rental-saya"
        assertEquals(httpsPort, LaunchPolicy.approvedUrl(httpsPort))
    }
    @Test fun rejectsOtherOriginsAndLookalikeHosts() {
        listOf("https://evil.example/", "https://ps.flexboxstudio.site.evil.example/",
            "https://ps.flexboxstudio.site@evil.example/", "https://evil@ps.flexboxstudio.site/",
            "https://ps.flexboxstudio.site:8443/", "https://flexboxstudio.site/")
            .forEach { assertEquals(LaunchPolicy.START_URL, LaunchPolicy.approvedUrl(it)) }
    }
    @Test fun rejectsCleartextFilesScriptsAndMalformedLinks() {
        listOf("http://ps.flexboxstudio.site/", "file:///etc/passwd", "content://private/photo",
            "javascript:alert(1)", "intent://ps.flexboxstudio.site/", "//ps.flexboxstudio.site/", "%broken")
            .forEach { assertEquals(LaunchPolicy.START_URL, LaunchPolicy.approvedUrl(it)) }
    }
}
