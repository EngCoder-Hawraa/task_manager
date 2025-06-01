<template>
  <v-app>
    <!-- ✅ رأس الصفحة -->
    <AppHeader @toggle-sidebar="toggleDrawer" />

    <!-- ✅ السايدبار -->
    <AppSidebar ref="sidebarRef" />

    <!-- ✅ المحتوى -->
    <v-main class="main-background">
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import {ref, onMounted, computed} from 'vue'
import {useRouter} from 'vue-router'
import {useAuthStore} from '@/stores/auth.js'
import {useTaskStore} from '@/stores/taskStore.js'

import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'

const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

const isAuthenticated = computed(() => !!authStore.token)
const sidebarRef = ref(null)

onMounted(() => {
  if (!isAuthenticated.value) {
    router.push('/login')
  } else {
    taskStore.fetchTasks()
  }
})

// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>

<style scoped>
.main-background {
  background-color: #f8f9fa;
  min-height: 100vh;
}
</style>
