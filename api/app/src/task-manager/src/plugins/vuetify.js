// plugins/vuetify.js أو main.js حسب مشروعك

import { createVuetify } from 'vuetify'
import 'vuetify/styles'
import { aliases, mdi } from 'vuetify/iconsets/mdi'

const vuetify = createVuetify({
  rtl: true, // <-- هذا مهم لتفعيل اتجاه RTL
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light',
  },
  locale: {
    rtl: {
      ar: true
    },
    current: 'ar'
  }
})

export default vuetify
