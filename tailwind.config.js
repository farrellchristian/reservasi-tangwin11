import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // INI TAMBAHANNYA: Warna khusus untuk desain Tangwin Cut
            colors: {
                'tangwin-dark': '#1a1a1a', 
                'tangwin-accent': '#3b82f6',
            }
        },
    },
    plugins: [],
};