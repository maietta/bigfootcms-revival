<?php

namespace App\Utils;

class ViteAssets {
    public bool $isDev;
    public string $devServerUrl;

    public function __construct(
        ?bool $isDev = null,
        ?string $devServerUrl = null
    ) {
        $this->isDev = $isDev ?? ($_ENV['APP_ENV'] === 'development');
        // Use the host machine's IP/hostname since we're in a container
        $this->devServerUrl = $devServerUrl ?? ($_ENV['VITE_DEV_SERVER'] ?? 'http://192.168.50.126:5173');
        
        // Debug output
        error_log("ViteAssets initialized with isDev: " . ($this->isDev ? 'true' : 'false'));
        error_log("ViteAssets initialized with devServerUrl: " . $this->devServerUrl);
    }

    public function widgetTags(): string {
        error_log("Generating widget tags with isDev: " . ($this->isDev ? 'true' : 'false'));
        
        if ($this->isDev) {
            $tags = sprintf(
                '<div id="app" style="position:fixed;top:0;right:0;bottom:0;width:300px;background:white;box-shadow:-2px 0 5px rgba(0,0,0,0.1);z-index:1000;overflow-y:auto"></div>
                <script type="module">
                    import RefreshRuntime from "%s/@vite/client"
                    RefreshRuntime.injectIntoGlobalHook(window)
                    window.$RefreshReg$ = () => {}
                    window.$RefreshSig$ = () => (type) => type
                    window.__vite_plugin_react_preamble_installed__ = true
                </script>
                <script type="module" src="%s/src/main.ts"></script>',
                $this->devServerUrl,
                $this->devServerUrl
            );
            error_log("Generated dev tags: " . htmlspecialchars($tags));
            return $tags;
        }

        $tags = sprintf(
            '<div id="app" style="position:fixed;top:0;right:0;bottom:0;width:300px;background:white;box-shadow:-2px 0 5px rgba(0,0,0,0.1);z-index:1000;overflow-y:auto"></div>
            <script type="module" src="/admin/assets/main.js"></script>'
        );
        error_log("Generated prod tags: " . htmlspecialchars($tags));
        return $tags;
    }
} 