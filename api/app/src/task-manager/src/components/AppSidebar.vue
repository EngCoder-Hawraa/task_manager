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
          :title="item.title"
          class="rounded-lg mx-2 my-1"
          link
          @click="navigateTo(item.route)"
        >
          <v-list-item-title class="text-white">{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- المحتوى الرئيسي -->
    <v-main>
      <div class="pa-6">
        <h2>مرحبًا بك في التطبيق</h2>
        <p>هذا هو المحتوى الرئيسي.</p>
        <div v-if="isAuthenticated">
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

const drawer = ref(true)

const menuItems = [
  { title: "الرئيسية", icon: "mdi-view-dashboard", value: "dashboard", route: "/" },
  { title: "الرسائل", icon: "mdi-forum", value: "messages", route: "/messages" },
  { title: "جهات الاتصال", icon: "mdi-account-group", value: "contacts", route: "/contacts" },
  { title: "الإعدادات", icon: "mdi-cog", value: "settings", route: "/settings" },
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
</script>

<style scoped>
.custom-drawer {
  transition: all 0.3s ease-in-out;
}

.v-list-item {
  transition: background-color 0.2s ease;
  cursor: pointer;
}

.v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.text-white {
  color: white !important;
}
</style>
