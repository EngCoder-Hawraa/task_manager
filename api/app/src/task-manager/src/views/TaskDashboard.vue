<template>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { useTaskStore } from '@/stores/taskStore.js'

// ✅ المكونات
import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import TaskStats from '@/components/TaskStats.vue'
import TaskList from '@/components/TaskList.vue'

const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

// const isAuthenticated = computed(() => !!authStore.token)
// const sidebarRef = ref(null)
//
// // ✅ تحميل المهام عند الدخول
// onMounted(() => {
//   if (!isAuthenticated.value) {
//     router.push('/login')
//   } else {
//     taskStore.fetchTasks()
//   }
// })

// ✅ التحكم في إظهار/إخفاء السايدبار من AppHeader
function toggleDrawer() {
  if (sidebarRef.value?.drawer !== undefined) {
    sidebarRef.value.drawer = !sidebarRef.value.drawer
  }
}
</script>

<style scoped>
.v-main {
  background-color: #f8f9fa;
  min-height: 100vh;
}
</style>
