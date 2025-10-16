<!DOCTYPE html>
<html>
<head>
    <title>{$page_title|default:"EasyStream"}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Your existing head content -->
</head>
<body>
    <!-- Your existing body content -->
    
    <!-- Browser Fingerprinting Script -->
    {literal}
    <script>
    function generateFingerprint() {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        ctx.textBaseline = "top";
        ctx.font = "14px Arial";
        ctx.fillText("Browser fingerprint", 2, 2);
        
        const fingerprint = {
            screen_resolution: screen.width + "x" + screen.height,
            screen_color_depth: screen.colorDepth,
            timezone_offset: new Date().getTimezoneOffset(),
            language: navigator.language,
            languages: navigator.languages ? navigator.languages.join(",") : "",
            platform: navigator.platform,
            cookie_enabled: navigator.cookieEnabled,
            do_not_track: navigator.doNotTrack,
            canvas_fingerprint: canvas.toDataURL(),
            webgl_vendor: getWebGLVendor(),
            webgl_renderer: getWebGLRenderer(),
            plugins: getPluginsList(),
            fonts: detectFonts(),
            hardware_concurrency: navigator.hardwareConcurrency,
            device_memory: navigator.deviceMemory,
            connection_type: navigator.connection ? navigator.connection.effectiveType : "",
            touch_support: "ontouchstart" in window,
            local_storage: typeof(Storage) !== "undefined",
            session_storage: typeof(sessionStorage) !== "undefined",
            indexed_db: typeof(indexedDB) !== "undefined"
        };
        
        return fingerprint;
    }

    function getWebGLVendor() {
        try {
            const canvas = document.createElement("canvas");
            const gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
            if (gl) {
                const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
                return debugInfo ? gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL) : "";
            }
        } catch (e) {}
        return "";
    }

    function getWebGLRenderer() {
        try {
            const canvas = document.createElement("canvas");
            const gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
            if (gl) {
                const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
                return debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : "";
            }
        } catch (e) {}
        return "";
    }

    function getPluginsList() {
        const plugins = [];
        for (let i = 0; i < navigator.plugins.length; i++) {
            plugins.push(navigator.plugins[i].name);
        }
        return plugins.join(",");
    }

    function detectFonts() {
        const testFonts = ["Arial", "Helvetica", "Times", "Courier", "Verdana", "Georgia", "Palatino", "Garamond"];
        const detectedFonts = [];
        
        const testString = "mmmmmmmmmmlli";
        const testSize = "72px";
        const h = document.getElementsByTagName("body")[0];
        
        const baseFonts = ["monospace", "sans-serif", "serif"];
        const testDiv = document.createElement("div");
        testDiv.style.position = "absolute";
        testDiv.style.left = "-9999px";
        testDiv.style.fontSize = testSize;
        testDiv.innerHTML = testString;
        
        const defaultWidths = {};
        for (let i = 0; i < baseFonts.length; i++) {
            testDiv.style.fontFamily = baseFonts[i];
            h.appendChild(testDiv);
            defaultWidths[baseFonts[i]] = testDiv.offsetWidth;
            h.removeChild(testDiv);
        }
        
        for (let i = 0; i < testFonts.length; i++) {
            let detected = false;
            for (let j = 0; j < baseFonts.length; j++) {
                testDiv.style.fontFamily = testFonts[i] + "," + baseFonts[j];
                h.appendChild(testDiv);
                const matched = (testDiv.offsetWidth !== defaultWidths[baseFonts[j]]);
                h.removeChild(testDiv);
                detected = detected || matched;
            }
            if (detected) {
                detectedFonts.push(testFonts[i]);
            }
        }
        
        return detectedFonts.join(",");
    }

    // Send fingerprint to server
    function sendFingerprint() {
        const fingerprint = generateFingerprint();
        
        fetch("/fingerprint_handler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(fingerprint)
        }).then(response => {
            if (response.status === 403) {
                // Fingerprint is banned
                window.location.href = '/error';
            }
        }).catch(console.error);
    }

    // Auto-send fingerprint when page loads
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", sendFingerprint);
    } else {
        sendFingerprint();
    }
    </script>
    {/literal}
</body>
</html>