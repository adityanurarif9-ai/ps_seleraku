package site.flexboxstudio.psseleraku

import android.content.Intent
import android.net.Uri
import com.google.androidbrowserhelper.trusted.LauncherActivity

/**
 * Android entry point. The verified website supplies the existing UI and backend.
 * Chrome owns login cookies, file selection, downloads and website permissions.
 * No API secrets, credentials, WebView bridge or certificate bypass are included.
 */
class MainActivity : LauncherActivity() {
    override fun getLaunchingUrl(): Uri = Uri.parse(LaunchPolicy.approvedUrl(intent?.dataString))

    // Sanitize the original intent too, so an untrusted URL is never forwarded
    // through the helper library as a secondary/original launch URL.
    override fun getUrlForIntent(intent: Intent): Uri? =
        intent.dataString?.let { Uri.parse(LaunchPolicy.approvedUrl(it)) }
}
