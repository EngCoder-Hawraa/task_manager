<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-5">
    <h1 class="text-h5 font-weight-bold mb-6">⚙️ الإعدادات</h1>

    <v-card class="mb-6">
      <v-card-title class="text-h6">👤 الإعدادات الشخصية</v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-text-field v-model="user.name" label="الاسم الكامل" />
        <v-text-field v-model="user.email" label="البريد الإلكتروني" type="email" />
        <v-btn color="primary" @click="saveProfile">💾 حفظ</v-btn>
      </v-card-text>
    </v-card>

    <v-card class="mb-6">
      <v-card-title class="text-h6">🔐 تغيير كلمة المرور</v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-text-field v-model="password.old" label="كلمة المرور الحالية" type="password" />
        <v-text-field v-model="password.new" label="كلمة المرور الجديدة" type="password" />
        <v-btn color="success" @click="changePassword">🔁 تحديث</v-btn>
      </v-card-text>
    </v-card>

    <v-card class="mb-6">
      <v-card-title class="text-h6">🎨 المظهر</v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-switch v-model="darkMode" label="الوضع الليلي" />
      </v-card-text>
    </v-card>

    <v-card class="mb-6">
      <v-card-title class="text-h6">🔔 الإشعارات</v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-switch v-model="notifications.email" label="إشعارات البريد الإلكتروني" />
        <v-switch v-model="notifications.sms" label="إشعارات الرسائل النصية" />
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title class="text-h6">🌐 اللغة</v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-select
          v-model="language"
          :items="languages"
          label="اختر اللغة"
        />
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

const toast = useToast()
const sidebarRef = ref(null)

const user = ref({
  name: 'أحمد علي',
  email: 'ahmad@example.com'
})

const password = ref({
  old: '',
  new: ''
})

const darkMode = ref(false)

const notifications = ref({
  email: true,
  sms: false
})

const language = ref('العربية')
const languages = ['العربية', 'English', 'Français']

const saveProfile = () => {
  toast.success('✅ تم حفظ الإعدادات الشخصية')
}

const changePassword = () => {
  if (!password.old || !password.new) {
    toast.warning('⚠️ الرجاء إدخال جميع الحقول')
    return
  }
  toast.success('🔐 تم تغيير كلمة المرور بنجاح')
}

// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>
<style scoped>
.v-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
