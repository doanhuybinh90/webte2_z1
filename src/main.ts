import { createApp } from "vue";
import { createPinia } from "pinia";
import "./styles.css";
import "primevue/resources/themes/aura-dark-noir/theme.css";
import "primeflex/primeflex.scss";
import "primeicons/primeicons.css";

import ConfirmationService from "primevue/confirmationservice";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import Ripple from "primevue/ripple";
import App from "@/App.vue";
import vue3GoogleLogin from "vue3-google-login";
import { router } from "@/router";

const app = createApp(App);
const pinia = createPinia();
app.use(ToastService);

app.use(PrimeVue, { ripple: true });
app.use(router);
app.use(vue3GoogleLogin, {
  clientId:
    "626144583008-of37oah2psot6870eulmokn68047u01e.apps.googleusercontent.com",
});
app.use(ConfirmationService);
app.use(pinia);
app.directive("ripple", Ripple);

app.mount("#app");
