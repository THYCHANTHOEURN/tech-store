import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { createI18n } from 'vue-i18n';
import en from './Lang/en.json';
import km from './Lang/km.json';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, watch, nextTick } from 'vue';
import vuetify from './Plugins/vuetify';
import RichTextEditor from './Components/RichTextEditor.vue';
import VueApexCharts from 'vue3-apexcharts';
import { Ziggy } from './ziggy';
import route from 'ziggy-js';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
// Use document.querySelector to get the site name from a meta tag that we'll add
const appName = document.querySelector('title')?.innerText || 'Laravel';

const savedLocale = localStorage.getItem('locale') || 'en';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(vuetify);

        // Set up vue-i18n
        const i18n = createI18n({
            legacy: false,
            locale: savedLocale,
            fallbackLocale: 'en',
            messages: { en, km },
        });
        app.use(i18n);

        // Register ApexCharts component globally
        app.component('apexchart', VueApexCharts);

        app.component('Head', Head);
        app.component('Link', Link);
        app.component('RichTextEditor', RichTextEditor);

        // Watch for locale changes and persist to localStorage
        watch(
            () => i18n.global.locale.value,
            (newLocale) => {
                localStorage.setItem('locale', newLocale);
            }
        );

        // Expose Ziggy and the `route()` helper globally so compiled bundles and
        // components can call `route('name')` without runtime errors.
        if (typeof window !== 'undefined') {
            try {
                window.Ziggy = Object.assign(typeof window.Ziggy !== 'undefined' ? window.Ziggy : {}, Ziggy || {});
            } catch (e) {
                window.Ziggy = Ziggy || window.Ziggy || {};
            }
            window.route = route;
        }

        // Make `route` available inside Vue components via globalProperties
        app.config.globalProperties.route = route;

        app.mount(el);

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
