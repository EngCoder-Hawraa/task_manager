<template>
  <v-container
    fluid
    dir="rtl"
    class="d-flex justify-center align-center"
    style="min-height: 100vh;"
  >
    <v-card class="pa-6" width="450" elevation="10">
      <v-card-title class="text-h5 text-center mb-4">تسجيل الدخول</v-card-title>

      <v-form @submit.prevent="login" class="d-flex flex-column gap-4">
        <v-text-field
          label="البريد الإلكتروني"
          v-model="email"
          type="email"
          :rules="emailRules"
          required
          append-inner-icon="mdi-email"
          outlined
        />

        <v-text-field
          label="كلمة المرور"
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          :rules="passwordRules"
          required
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="togglePasswordVisibility"
          outlined
        />

        <v-btn type="submit" color="primary" block :loading="loading">
          دخول
        </v-btn>

        <v-btn text to="/register" color="secondary" class="mt-2" block>
          ليس لديك حساب؟ سجل الآن
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth' // <-- تأكدي من المسار

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)

const router = useRouter()
const authStore = useAuthStore()

const emailRules = [
  v => !!v || 'البريد الإلكتروني مطلوب',
  v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'يرجى إدخال بريد إلكتروني صالح'
]

const passwordRules = [
  v => !!v || 'كلمة المرور مطلوبة',
  v => v.length >= 6 || 'كلمة المرور يجب أن تكون على الأقل 6 أحرف'
]

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

async function login() {
  loading.value = true

  try {
    await authStore.login({ email: email.value, password: password.value })

    Swal.fire({
      icon: 'success',
      title: 'تم تسجيل الدخول بنجاح',
      confirmButtonText: 'موافق',
    })

    router.push('/TaskDashboard')
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'فشل تسجيل الدخول',
      text: error.message || 'حدث خطأ ما',
      confirmButtonText: 'موافق',
    })
  } finally {
    loading.value = false
  }
}
</script>


<style>
.swal2-confirm {
  color: white !important;
}
</style>
