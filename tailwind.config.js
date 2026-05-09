import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                'bg-beige': '#F7ECD0',
                'paper-white': '#FFFDF8',
                'text-dark': '#4E342E',
                'text-muted': '#8D6E63',
                'cherry-red': '#C24641',
                'butter-yellow': '#FFFD74',
                'tape-color': 'rgba(255, 255, 230, 0.7)',
            },
            fontFamily: {
                caveat: ['Caveat', 'cursive'],
                lora: ['Lora', 'serif'],
                montserrat: ['Montserrat', 'sans-serif'],
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
