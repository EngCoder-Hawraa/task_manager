import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import TaskDashboard from '@/components/TaskDashboard.vue'
import CalendarView from "@/components/CalendarView.vue";
import { useAuthStore } from '@/stores/auth' // ⭐ استدعاء authStore

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  {
    path: '/taskDashboard',
    component: TaskDashboard,
    meta: { requiresAuth: true } // 🔐 الحماية
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: CalendarView,
    meta: { requiresAuth: true }
  }

]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ✅ حماية التنقل
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router
