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

// تكوين Swiper
// import "swiper/css";
// import "swiper/css/pagination";
// import "swiper/css/navigation";

// تكوين Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { aliases, mdi } from "vuetify/iconsets/mdi"; // استخدام أيقونات Material Design
import "@mdi/font/css/materialdesignicons.css";

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: "mdi", // التأكد من تعريف مجموعة الأيقونات الافتراضية
    aliases,
    sets: {
      mdi,
    },
  },
});

const app = createApp(App);

// طريقة Composition API
app.use(createPinia());
app.provide("Emitter", Emitter); // توفير Emitter بشكل عام

app.use(router);
app.use(vuetify);

// تثبيت التطبيق
app.mount("#app");
