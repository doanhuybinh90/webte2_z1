import { createApp } from "vue";
import { createPinia } from "pinia";
import "./styles.css";
import "primevue/resources/themes/aura-dark-noir/theme.css";
import "primeflex/primeflex.scss";
import "primeicons/primeicons.css";

import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import Ripple from "primevue/ripple";
import App from "@/App.vue";

import { router } from "@/router";

const app = createApp(App);
const pinia = createPinia();
app.use(ToastService);

app.use(PrimeVue, { ripple: true });
app.use(router);
app.use(pinia);
app.directive("ripple", Ripple);

app.mount("#app");
