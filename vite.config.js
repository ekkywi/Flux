import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // PENTING: Mengizinkan akses dari luar kontainer (Windows/Host)
        host: '0.0.0.0', 
        port: 5173,
        strictPort: true,
        hmr: {
            // Memastikan Hot Module Replacement mengarah ke localhost komputer Anda
            host: 'localhost',
        },
        watch: {
            usePolling: true, // Kadang dibutuhkan di Windows agar deteksi perubahan file lancar
            ignored: ['**/storage/framework/views/**'],
        },
    },
});