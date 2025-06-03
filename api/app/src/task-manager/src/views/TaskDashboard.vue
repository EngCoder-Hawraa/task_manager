<template>
<!--  <v-app>-->
    <!-- ✅ شاشة التحميل تغطي الصفحة -->
  <!-- ✅ شاشة التحميل المحسّنة -->
  <div v-if="loadingScreen" class="loading-overlay fade-in">
    <div class="loader-content">
      <v-progress-circular
        indeterminate
        color="primary"
        size="80"
        width="6"
      />
      <p class="loading-text">جاري تحميل البيانات...</p>
    </div>
  </div>


  <!-- ✅ بعد تحميل البيانات -->
    <div v-else>
      <!-- ✅ رأس الصفحة -->
      <AppHeader @toggle-sidebar="toggleDrawer" />

      <!-- ✅ السايدبار -->
      <AppSidebar ref="sidebarRef" />

      <transition name="fade" mode="out-in">
        <div class="pa-6 main-background fade-in-container" key="main-content">
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
      </transition>
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
      }, 50)
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
.fade-enter-active, .fade-leave-active {
  transition: all 0.6s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

.fade-enter-to, .fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.main-background {
  background-color: #f8f9fa;
  min-height: 100vh;
}

/* خلفية الشاشة */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: linear-gradient(135deg, #ffffff, #f0f4f8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 1s ease-in-out;
}

/* المحتوى الداخلي */
.loader-content {
  text-align: center;
  animation: pulse 2s infinite ease-in-out;
}

/* نص التحميل */
.loading-text {
  margin-top: 20px;
  font-size: 1.25rem;
  color: #1976d2;
  font-weight: 500;
  letter-spacing: 1px;
}

/* تأثير الظهور البطيء */
@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* نبض لطيف */
@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.215);
  }
  100% {
    transform: scale(1);
  }
}
</style>
