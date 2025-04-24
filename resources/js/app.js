import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import vuetify from './Plugins/vuetify';
import RichTextEditor from './Components/RichTextEditor.vue';
import VueApexCharts from 'vue3-apexcharts';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

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

        // Register ApexCharts component globally
        app.component('apexchart', VueApexCharts);

        app.component('Head', Head);
        app.component('Link', Link);
        app.component('RichTextEditor', RichTextEditor);

        app.mount(el);

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
