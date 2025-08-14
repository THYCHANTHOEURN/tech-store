import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { createI18n } from 'vue-i18n';
import en from './lang/en.json'; // Create this file for English translations
import km from './lang/km.json';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import vuetify from './Plugins/vuetify';
import RichTextEditor from './Components/RichTextEditor.vue';
import VueApexCharts from 'vue3-apexcharts';

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
        app.use(ZiggyVue);
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

        app.mount(el);

        // Correct way to persist locale changes
        // Use a watcher after mounting
        app.config.globalProperties.$nextTick(() => {
            // Use Vue's watch API
            import('vue').then(({ watch }) => {
                watch(
                    () => i18n.global.locale.value,
                    (newLocale) => {
                        localStorage.setItem('locale', newLocale);
                    }
                );
            });
        });

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
