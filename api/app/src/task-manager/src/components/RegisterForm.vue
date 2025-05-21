<template>
  <v-container
    fluid
    dir="rtl"
    class="d-flex justify-center align-center text-right"
    style="min-height: 100vh;"
  >
    <v-card class="pa-6" width="450" elevation="10">
      <v-card-title class="text-h5 text-center mb-4">إنشاء حساب</v-card-title>

      <v-form @submit.prevent="submit" class="d-flex flex-column gap-4">
        <v-text-field
          label="الاسم"
          v-model="name"
          :rules="nameRules"
          required
          append-inner-icon="mdi-account"
          outlined
        />

        <v-text-field
          label="البريد الإلكتروني"
          v-model="email"
          type="email"
          :rules="emailRules"
          required
          append-inner-icon="mdi-email"
          outlined
        />

        <!-- كلمة المرور مع زر إظهار/إخفاء -->
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

        <!-- تأكيد كلمة المرور مع زر إظهار/إخفاء -->
        <v-text-field
          label="تأكيد كلمة المرور"
          v-model="confirmPassword"
          :type="showConfirmPassword ? 'text' : 'password'"
          :rules="confirmPasswordRules"
          required
          :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="toggleConfirmPasswordVisibility"
          outlined
        />

        <v-btn type="submit" color="primary" block :loading="loading">
          تسجيل
        </v-btn>

        <v-btn text to="/login" color="secondary" class="mt-2" block>
          لديك حساب بالفعل؟ تسجيل الدخول
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const router = useRouter()
const auth = useAuthStore()

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPasswordVisibility = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

const nameRules = [v => !!v || 'الاسم مطلوب']
const emailRules = [
  v => !!v || 'البريد الإلكتروني مطلوب',
  v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'يرجى إدخال بريد إلكتروني صالح'
]

const passwordRules = [
  v => !!v || 'كلمة المرور مطلوبة',
  v => v.length >= 8 || 'كلمة المرور يجب أن تكون على الأقل 8 أحرف'
]
const confirmPasswordRules = [
  v => !!v || 'يرجى تأكيد كلمة المرور',
  v => v === password.value || 'كلمة المرور غير متطابقة'
]

const submit = async () => {
  loading.value = true

  // تحقق من تطابق كلمة المرور وتأكيدها قبل الإرسال
  if (password.value !== confirmPassword.value) {
    loading.value = false
    Swal.fire({
      icon: 'error',
      title: 'خطأ في كلمة المرور',
      text: 'كلمة المرور وتأكيدها غير متطابقين',
      confirmButtonText: 'موافق',
      customClass: {
        confirmButton: 'white--text'
      }
    })
    return
  }

  try {
    const response = await auth.register({
      name: name.value,
      email: email.value,
      password: password.value
    })

    if (response && response.message === 'تم التسجيل بنجاح') {
      Swal.fire({
        icon: 'success',
        title: 'تم تسجيل الحساب بنجاح',
        text: 'تمت عملية التسجيل بنجاح، يمكنك الآن تسجيل الدخول',
        confirmButtonText: 'موافق',
        customClass: {
          confirmButton: 'swal2-confirm'
        }
      })
      router.push('/taskDashboard')
    } else {
      throw new Error(response.error || 'حدث خطأ أثناء التسجيل')
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'حدث خطأ',
      text: error.message || 'حدث خطأ أثناء التسجيل، حاول مجددًا.',
      confirmButtonText: 'موافق',
      customClass: {
        confirmButton: 'swal2-confirm'
      }
    })
  } finally {
    loading.value = false
  }
}
</script>

<style>
/* لتغيير لون زر SweetAlert إلى أبيض */
.swal2-confirm {
  color: white !important;
}
</style>
