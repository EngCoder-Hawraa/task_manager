<template>
  <v-app-bar app color="primary" dark>
    <!-- زر فتح السايدبار -->
    <v-btn icon @click="$emit('toggle-sidebar')">
      <v-icon>mdi-menu</v-icon>
    </v-btn>
    <v-app-bar-title>Task Manager</v-app-bar-title>
    <v-spacer />
    <v-btn @click="logout" text>تسجيل الخروج</v-btn>
  </v-app-bar>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
defineEmits(['toggle-sidebar'])
function logout() {
  auth.logout()
  router.push('/login')
}
</script>
