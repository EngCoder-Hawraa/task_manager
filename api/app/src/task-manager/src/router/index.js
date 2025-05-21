import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import TaskDashboard from "@/components/TaskDashboard.vue";

const routes = [
  { path: '/login', component: LoginView },
  { path: '/', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/taskDashboard', component: TaskDashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
