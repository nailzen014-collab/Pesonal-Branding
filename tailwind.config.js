import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Design system website personal branding.
 * Token warna mengikuti PRD bagian 11.1 (tema gelap, aksen merah).
 *
 * Konsep: "design token" = nama warna yang dipakai konsisten di seluruh
 * website. Dengan begitu, mengganti tema cukup di file ini, tidak perlu
 * mengedit tiap halaman.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#E11D2E',
                    dark: '#B3121F',
                    deep: '#7A0C16',
                    soft: '#FF4D5E',
                },
                ink: {
                    DEFAULT: '#08080A',
                    soft: '#0D0D10',
                },
                surface: {
                    DEFAULT: '#121215',
                    2: '#1A1A1F',
                    3: '#23232A',
                },
                line: {
                    DEFAULT: '#26262E',
                    soft: '#1C1C22',
                },
                text: '#F7F7F8',
                muted: '#A1A1AA',
                success: '#22C55E',
                warning: '#F59E0B',
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Space Grotesk"', 'Poppins', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },

            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],
            },

            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },

            boxShadow: {
                card: '0 1px 0 0 rgb(255 255 255 / 0.04) inset, 0 18px 40px -24px rgb(0 0 0 / 0.9)',
                'card-lg': '0 1px 0 0 rgb(255 255 255 / 0.05) inset, 0 32px 60px -28px rgb(0 0 0 / 0.95)',
                'glow-primary': '0 0 0 1px rgb(225 29 46 / 0.4), 0 22px 50px -20px rgb(225 29 46 / 0.6)',
                'glow-soft': '0 0 30px -8px rgb(225 29 46 / 0.5)',
            },

            backgroundImage: {
                'grid-fade':
                    'linear-gradient(to bottom, rgb(8 8 10 / 0), rgb(8 8 10 / 1)), radial-gradient(circle at 50% 0%, rgb(225 29 46 / 0.16), transparent 55%)',
                'hero-glow':
                    'radial-gradient(55% 55% at 50% 0%, rgb(225 29 46 / 0.25), transparent 70%)',
                'aurora':
                    'radial-gradient(45% 45% at 15% 10%, rgb(225 29 46 / 0.22), transparent 60%), radial-gradient(40% 40% at 85% 20%, rgb(122 12 22 / 0.35), transparent 60%), radial-gradient(50% 50% at 50% 90%, rgb(225 29 46 / 0.12), transparent 65%)',
                'text-shine':
                    'linear-gradient(100deg, #FFFFFF 0%, #FFFFFF 40%, #FF8A94 60%, #FFFFFF 100%)',
                'border-gradient':
                    'linear-gradient(135deg, rgb(225 29 46 / 0.55), rgb(255 255 255 / 0.06) 45%, rgb(225 29 46 / 0.15))',
            },

            spacing: {
                18: '4.5rem',
                22: '5.5rem',
                30: '7.5rem',
            },

            maxWidth: {
                '8xl': '88rem',
            },

            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                drift: {
                    '0%, 100%': { transform: 'translate3d(0, 0, 0) scale(1)' },
                    '50%': { transform: 'translate3d(0, -18px, 0) scale(1.04)' },
                },
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                'pulse-ring': {
                    '0%': { boxShadow: '0 0 0 0 rgb(225 29 46 / 0.5)' },
                    '70%': { boxShadow: '0 0 0 16px rgb(225 29 46 / 0)' },
                    '100%': { boxShadow: '0 0 0 0 rgb(225 29 46 / 0)' },
                },
                'shine-sweep': {
                    '0%': { transform: 'translateX(-120%)' },
                    '100%': { transform: 'translateX(220%)' },
                },
            },

            animation: {
                'fade-up': 'fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fade-in 0.8s ease-out both',
                'scale-in': 'scale-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) both',
                float: 'float 6s ease-in-out infinite',
                drift: 'drift 14s ease-in-out infinite',
                marquee: 'marquee 32s linear infinite',
                'pulse-ring': 'pulse-ring 2.6s ease-out infinite',
                'shine-sweep': 'shine-sweep 1.6s ease-out',
            },

            transitionTimingFunction: {
                smooth: 'cubic-bezier(0.22, 1, 0.36, 1)',
            },

            transitionDuration: {
                400: '400ms',
            },
        },
    },

    plugins: [forms],
};