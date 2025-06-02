import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import TaskDashboard from '@/views/TaskDashboard.vue'
import CalendarView from "@/components/CalendarView.vue";
import { useAuthStore } from '@/stores/auth'
import MyTasks from "@/components/MyTasks.vue";
// import AddTask from "@/components/AddTask.vue";
import AddTask2 from "@/components/AddTask2.vue";
import TeamTasks from "@/components/TeamTasks.vue";

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
    path: '/my-tasks',
    component: MyTasks,
    meta: { requiresAuth: true } //🔐 الحماية
  },
  {
    path: '/add-task',
    component: AddTask2,
    meta: { requiresAuth: true } //🔐 الحماية
  },
  {
    path: '/team-tasks',
    component: TeamTasks,
    meta: { requiresAuth: true } //🔐 الحماية
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
