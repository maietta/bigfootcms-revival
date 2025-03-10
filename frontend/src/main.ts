import { mount } from 'svelte'
import './app.css'
import App from './App.svelte'

// Get config from PHP
declare global {
    interface Window {
        ADMIN_CONFIG: {
            apiEndpoint: string;
            currentPath: string;
            pageData: {
                path: string;
                title: string;
                content: string;
            };
        };
    }
}

mount(App, {
  target: document.getElementById('app')!
})
