<?php
if (getenv('NODE_ENV') === 'development'): ?>
<!-- Development CMS Injection -->
<script type="module">
    // Inject Vite's client for HMR
    import { injectIntoGlobalHook } from 'http://localhost:5173/@vite/client'
    
    // Import and mount your Svelte app
    import { mount } from 'http://localhost:5173/src/main.ts'
    
    // Create mount point for CMS
    const mountPoint = document.createElement('div')
    mountPoint.id = 'cms-root'
    document.body.appendChild(mountPoint)
    
    // Initialize the CMS
    mount(mountPoint)
</script>
<?php endif; ?> 