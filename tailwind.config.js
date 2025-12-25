import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts',
    ],
    prefix: '',
    theme: {
        container: {
            center: true,
            padding: '2rem',
            screens: {
                '2xl': '1400px',
            },
        },
        extend: {
            fontFamily: {
                sans: ['DM Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['Space Mono', 'ui-monospace', 'monospace'],
            },
            colors: {
                border: 'hsl(var(--border))',
                input: 'hsl(var(--input))',
                ring: 'hsl(var(--ring))',
                background: 'hsl(var(--background))',
                foreground: 'hsl(var(--foreground))',
                primary: {
                    DEFAULT: 'hsl(var(--primary))',
                    foreground: 'hsl(var(--primary-foreground))',
                },
                secondary: {
                    DEFAULT: 'hsl(var(--secondary))',
                    foreground: 'hsl(var(--secondary-foreground))',
                },
                destructive: {
                    DEFAULT: 'hsl(var(--destructive))',
                    foreground: 'hsl(var(--destructive-foreground))',
                },
                muted: {
                    DEFAULT: 'hsl(var(--muted))',
                    foreground: 'hsl(var(--muted-foreground))',
                },
                accent: {
                    DEFAULT: 'hsl(var(--accent))',
                    foreground: 'hsl(var(--accent-foreground))',
                },
                success: {
                    DEFAULT: 'hsl(var(--success))',
                    foreground: 'hsl(var(--success-foreground))',
                },
                warning: {
                    DEFAULT: 'hsl(var(--warning))',
                    foreground: 'hsl(var(--warning-foreground))',
                },
                popover: {
                    DEFAULT: 'hsl(var(--popover))',
                    foreground: 'hsl(var(--popover-foreground))',
                },
                card: {
                    DEFAULT: 'hsl(var(--card))',
                    foreground: 'hsl(var(--card-foreground))',
                },
                sidebar: {
                    DEFAULT: 'hsl(var(--sidebar-background))',
                    foreground: 'hsl(var(--sidebar-foreground))',
                    primary: 'hsl(var(--sidebar-primary))',
                    'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
                    accent: 'hsl(var(--sidebar-accent))',
                    'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
                    border: 'hsl(var(--sidebar-border))',
                    ring: 'hsl(var(--sidebar-ring))',
                },
                bay: {
                    idle: 'hsl(var(--bay-idle))',
                    'idle-foreground': 'hsl(var(--bay-idle-foreground))',
                    active: 'hsl(var(--bay-active))',
                    'active-foreground': 'hsl(var(--bay-active-foreground))',
                    maintenance: 'hsl(var(--bay-maintenance))',
                    'maintenance-foreground': 'hsl(var(--bay-maintenance-foreground))',
                    completed: 'hsl(var(--bay-completed))',
                    'completed-foreground': 'hsl(var(--bay-completed-foreground))',
                },
                queue: {
                    waiting: 'hsl(var(--queue-waiting))',
                    'waiting-foreground': 'hsl(var(--queue-waiting-foreground))',
                    'in-progress': 'hsl(var(--queue-in-progress))',
                    'in-progress-foreground': 'hsl(var(--queue-in-progress-foreground))',
                    completed: 'hsl(var(--queue-completed))',
                    'completed-foreground': 'hsl(var(--queue-completed-foreground))',
                },
                tier: {
                    regular: 'hsl(var(--tier-regular))',
                    'regular-foreground': 'hsl(var(--tier-regular-foreground))',
                    silver: 'hsl(var(--tier-silver))',
                    'silver-foreground': 'hsl(var(--tier-silver-foreground))',
                    gold: 'hsl(var(--tier-gold))',
                    'gold-foreground': 'hsl(var(--tier-gold-foreground))',
                    platinum: 'hsl(var(--tier-platinum))',
                    'platinum-foreground': 'hsl(var(--tier-platinum-foreground))',
                },
                'active-cyan': {
                    DEFAULT: 'hsl(var(--active-cyan))',
                    foreground: 'hsl(var(--active-cyan-foreground))',
                },
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)',
            },
            keyframes: {
                'accordion-down': {
                    from: {
                        height: '0',
                    },
                    to: {
                        height: 'var(--radix-accordion-content-height)',
                    },
                },
                'accordion-up': {
                    from: {
                        height: 'var(--radix-accordion-content-height)',
                    },
                    to: {
                        height: '0',
                    },
                },
                'fade-in': {
                    from: {
                        opacity: '0',
                    },
                    to: {
                        opacity: '1',
                    },
                },
                'fade-in-fast': {
                    from: {
                        opacity: '0',
                    },
                    to: {
                        opacity: '1',
                    },
                },
                'slide-up': {
                    from: {
                        transform: 'translateY(10px)',
                        opacity: '0',
                    },
                    to: {
                        transform: 'translateY(0)',
                        opacity: '1',
                    },
                },
                'slide-in-right': {
                    from: {
                        transform: 'translateX(20px)',
                        opacity: '0',
                    },
                    to: {
                        transform: 'translateX(0)',
                        opacity: '1',
                    },
                },
                'slide-out-left': {
                    from: {
                        transform: 'translateX(0)',
                        opacity: '1',
                    },
                    to: {
                        transform: 'translateX(-20px)',
                        opacity: '0',
                    },
                },
                'scale-in': {
                    from: {
                        transform: 'scale(0.95)',
                        opacity: '0',
                    },
                    to: {
                        transform: 'scale(1)',
                        opacity: '1',
                    },
                },
                'scale-success': {
                    '0%': {
                        transform: 'scale(0)',
                    },
                    '50%': {
                        transform: 'scale(1.1)',
                    },
                    '100%': {
                        transform: 'scale(1)',
                    },
                },
                'pulse-slow': {
                    '0%, 100%': {
                        opacity: '1',
                    },
                    '50%': {
                        opacity: '0.7',
                    },
                },
            },
            animation: {
                'accordion-down': 'accordion-down 0.2s ease-out',
                'accordion-up': 'accordion-up 0.2s ease-out',
                'fade-in': 'fade-in 0.5s ease-out',
                'fade-in-fast': 'fade-in-fast 0.15s ease-out',
                'slide-up': 'slide-up 0.3s ease-out',
                'slide-in-right': 'slide-in-right 0.2s ease-out',
                'slide-out-left': 'slide-out-left 0.25s ease-out',
                'scale-in': 'scale-in 0.2s ease-out',
                'scale-success': 'scale-success 0.2s ease-out',
                'pulse-slow': 'pulse-slow 2s ease-in-out infinite',
            },
        },
    },
    plugins: [forms, require('tailwindcss-animate')],
};
