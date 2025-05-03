import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';

// Get theme settings from localStorage or use defaults
const getThemeSettings = () => {
    const primaryColor = localStorage.getItem('primary_color') || '#1976D2';
    const secondaryColor = localStorage.getItem('secondary_color') || '#424242';
    const accentColor = localStorage.getItem('accent_color') || '#82B1FF';
    const theme = localStorage.getItem('theme') || 'light';
    const density = localStorage.getItem('density') || 'comfortable';

    return { primaryColor, secondaryColor, accentColor, theme, density };
};

const { primaryColor, secondaryColor, accentColor, theme, density } = getThemeSettings();

// Create a custom Vuetify instance
const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: theme,
        themes: {
            light: {
                dark: false,
                colors: {
                    primary: primaryColor,
                    secondary: secondaryColor,
                    accent: accentColor,
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                }
            },
            dark: {
                dark: true,
                colors: {
                    primary: primaryColor,
                    secondary: secondaryColor,
                    accent: accentColor,
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                }
            }
        }
    },
    defaults: {
        VBtn: {
            variant: 'flat',
        },
        VCard: {
            rounded: 'md',
        },
    },
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
    display: {
        thresholds: {
            xs: 0,
            sm: 600,
            md: 960,
            lg: 1280,
            xl: 1920,
        },
    },
    density: density,
});

// Listen for theme changes
window.addEventListener('theme-changed', (e) => {
    const { theme, primaryColor } = e.detail;
    vuetify.theme.global.name.value = theme;

    // Update theme colors
    vuetify.theme.themes.value.light.colors.primary = primaryColor;
    vuetify.theme.themes.value.dark.colors.primary = primaryColor;

    // Update density if provided
    if (e.detail.density) {
        vuetify.display.density.value = e.detail.density;
    }
});

export default vuetify;
