<template>
  <v-app>
    <!-- الشريط الجانبي (السايدبار) -->
    <v-navigation-drawer
      v-model="drawer"
      app
      permanent
      location="right"
      color="primary"
      theme="dark"
      width="260"
      class="elevation-3 rounded-start custom-drawer"
    >
      <v-list nav dense>
        <!-- حساب المستخدم -->
        <v-list-item
          class="mt-4 mb-2"
          prepend-avatar="https://randomuser.me/api/portraits/women/75.jpg"
        >
          <v-list-item-title class="text-white font-weight-medium">
            مرحبًا، حوراء
          </v-list-item-title>
        </v-list-item>

        <v-divider class="mb-2" />

        <!-- عناصر التنقل -->
        <v-list-item
          v-for="item in menuItems"
          :key="item.value"
          :prepend-icon="item.icon"
          class="rounded-lg mx-2 my-1"
          link
          @click="handleMenuClick(item)"
        >
          <v-list-item-title class="text-white">
            {{ item.title }}
          </v-list-item-title>
        </v-list-item>

        <!--          <v-list-item-title class="text-white">{{ item.title }}</v-list-item-title>-->
<!--        </v-list-item>-->
      </v-list>
    </v-navigation-drawer>

    <!-- المحتوى الرئيسي -->
    <v-main>
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
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "@/stores/auth.js"
import { useTaskStore } from "@/stores/taskStore.js"
import TaskStats from "@/components/TaskStats.vue"
import TaskList from "@/components/TaskList.vue"

// تعريف المتغيرات
const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()
const auth = useAuthStore()

const drawer = ref(true)

const menuItems = [
  {
    title: "لوحة المهام",
    icon: "mdi-view-dashboard",
    value: "dashboard",
    route: "/taskDashboard"
  },
  {
    title: "مهامي",
    icon: "mdi-format-list-checkbox",
    value: "my-tasks",
    route: "/my-tasks"
  },
  {
    title: "إضافة مهمة",
    icon: "mdi-plus-box",
    value: "add-task",
    route: "/add-task"
  },
  {
    title: "المهام الجماعية",
    icon: "mdi-account-multiple-check",
    value: "team-tasks",
    route: "/team-tasks"
  },
  {
    title: "إدارة المستخدمين",
    icon: "mdi-account-cog",
    value: "users",
    route: "/users"
  },
  {
    title: "التقارير",
    icon: "mdi-chart-bar",
    value: "reports",
    route: "/reports"
  },
  {
    title: "التنبيهات",
    icon: "mdi-bell-alert",
    value: "notifications",
    route: "/notifications"
  },
  {
    title: "الإعدادات",
    icon: "mdi-cog",
    value: "settings",
    route: "/settings"
  },
  {
    title: "تسجيل الخروج",
    icon: "mdi-logout",
    action: "logout" // 🔴 تحديد أن هذه ليست صفحة، بل إجراء
  }
]


const isAuthenticated = computed(() => !!authStore.token)

// إعادة التوجيه عند عدم تسجيل الدخول
onMounted(() => {
  if (!isAuthenticated.value) {
    router.push("/login")
  } else {
    taskStore.fetchTasks()
  }
})

// دالة للتنقل إلى الصفحة المطلوبة عند الضغط على عنصر من القائمة
function navigateTo(route) {
  router.push(route)
}

function handleMenuClick(item) {
  if (item.action === 'logout') {
    auth.logout()           // 🔓 تسجيل الخروج من المتجر
    router.push('/login')   // 🔁 إعادة التوجيه
  } else if (item.route) {
    router.push(item.route)
  }
}
</script>

<style scoped>
.custom-drawer {
  font-family: 'Cairo', sans-serif;
  transition: all 0.3s ease-in-out;
}

.v-list-item {
  transition: background-color 0.2s ease;
  cursor: pointer;
}
.v-list-item--nav .v-list-item-title {
  font-size: 16px !important;
  font-family: 'Cairo', sans-serif;
  font-weight: bold;
}
.v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.text-white {
  color: white !important;
}
</style>
