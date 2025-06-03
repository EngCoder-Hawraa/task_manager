<template>
<!--  <v-app>-->
    <!-- ✅ شاشة التحميل تغطي الصفحة -->
    <div v-if="loadingScreen" class="loading-overlay">
      <v-progress-circular indeterminate color="primary" size="70" />
    </div>

    <!-- ✅ بعد تحميل البيانات -->
    <div v-else>
      <!-- ✅ رأس الصفحة -->
      <AppHeader @toggle-sidebar="toggleDrawer" />

      <!-- ✅ السايدبار -->
      <AppSidebar ref="sidebarRef" />

      <v-slide-y-transition group>
        <div class="pa-6 main-background">
          <h2>مرحبًا بك في التطبيق</h2>

          <!-- ⏳ تحميل داخلي للمهام فقط -->
          <v-progress-circular
            indeterminate
            color="primary"
            v-if="taskStore.loading"
            class="mx-auto my-10"
          />

          <div v-if="isAuthenticated" class="mt-7">
            <TaskStats />
            <TaskList />
          </div>

          <div v-else>
            <p class="text-center my-10">
              الرجاء تسجيل الدخول للوصول إلى المهام.
            </p>
          </div>
        </div>
      </v-slide-y-transition>
    </div>
<!--  </v-app>-->
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { useTaskStore } from '@/stores/taskStore.js'

import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import TaskStats from "@/components/TaskStats.vue"
import TaskList from "@/components/TaskList.vue"

const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

const isAuthenticated = computed(() => !!authStore.token)
const sidebarRef = ref(null)

// شاشة تحميل البداية
const loadingScreen = ref(true)

onMounted(async () => {
  if (!isAuthenticated.value) {
    router.push('/login')
    loadingScreen.value = false
  } else {
    try {
      await taskStore.fetchTasks()
    } finally {
      // تأخير اختياري لإعطاء شعور تحميل مثل يوتيوب
      setTimeout(() => {
        loadingScreen.value = false
      }, 1000)
    }
  }
})

// ✅ التحكم في السايدبار
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

.loading-overlay {
  position: fixed;
  inset: 0;
  background-color: white;
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
