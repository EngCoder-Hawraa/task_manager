<template>
  <v-app-bar app color="primary" dark>
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

function logout() {
  auth.logout()
  router.push('/login')
}
</script>
