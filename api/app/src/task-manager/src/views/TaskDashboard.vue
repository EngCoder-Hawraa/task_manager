<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-app>
    <v-slide-y-transition group>
<!--      <v-main>-->
        <div class="pa-6">
          <h2>مرحبًا بك في التطبيق</h2>
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
<!--      </v-main>-->
    </v-slide-y-transition>
  </v-app>
</template>

<script setup>
import {ref, onMounted, computed} from 'vue'
import {useRouter} from 'vue-router'
import {useAuthStore} from '@/stores/auth.js'
import {useTaskStore} from '@/stores/taskStore.js'

import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import TaskStats from "@/components/TaskStats.vue";
import TaskList from "@/components/TaskList.vue";

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
