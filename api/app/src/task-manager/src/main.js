// هذا يمنع ظهور الخطأ "ResizeObserver loop limit exceeded" في بيئة التطوير
// if (process.env.NODE_ENV === "development") {
//   window.addEventListener("error", (e) => {
//     if (e.message.includes("ResizeObserver loop")) {
//       e.stopImmediatePropagation();
//     }
//   });
// }

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

// تكوين Pinia
import { createPinia } from "pinia";

// تكوين Emitter
import mitt from "mitt";
const Emitter = mitt();

// تكوين Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { aliases, mdi } from "vuetify/iconsets/mdi"; // استخدام أيقونات Material Design
import "@mdi/font/css/materialdesignicons.css";

// تكوين Toastification
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: "mdi",
    aliases,
    sets: {
      mdi,
    },
  },
});

const app = createApp(App);

// تثبيت الحزم
app.use(createPinia());
app.provide("Emitter", Emitter);
app.use(router);
app.use(vuetify);

// ✅ تفعيل Toastification
app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: "button",
  icon: true,
});

app.mount("#app");
