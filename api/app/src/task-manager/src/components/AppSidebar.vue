<template>
  <v-navigation-drawer
    v-model="drawer"
    app
    location="right"
    color="primary"
    theme="dark"
    width="320"
    class="custom-drawer elevation-6"
  >
    <v-list nav dense>
      <!-- حساب المستخدم -->
      <v-list-item class="mt-6 mb-4" prepend-avatar="https://randomuser.me/api/portraits/women/75.jpg">
          <v-list-item-title class="text-white font-weight-bold headline">مرحبًا، حوراء</v-list-item-title>
          <v-list-item-subtitle class="text-white-50">حسابك الشخصي</v-list-item-subtitle>
      </v-list-item>

      <v-divider class="mb-4" />

      <!-- الأقسام -->
      <template v-for="(section, sectionIndex) in sections" :key="sectionIndex">
        <v-list-group
          v-model="openSection"
          :value="section.title"
          :prepend-icon="section.icon || 'mdi-menu-down'"
          no-action
          class="mx-2 mb-2 rounded-lg group-section"
        >
          <template #activator="{ props }">
            <v-list-item
              v-bind="props"
              class="text-white"
            >
                <v-list-item-title class="font-weight-bold">{{ section.title }}</v-list-item-title>
            </v-list-item>
          </template>

          <!-- ✅ Drag-and-drop list -->
          <draggable
            v-model="section.items"
            item-key="value"
            :group="{ name: 'menu', pull: true, put: true }"
            ghost-class="ghost"
            animation="200"
          >
            <template #item="{ element: item }">
              <v-list-item
                :key="item.value"
                :prepend-icon="item.icon"
                class="mx-4 my-1 rounded"
                link
                @click="handleMenuClick(item)"
                :active="activeRoute === item.route"
                :class="{ 'active-item': activeRoute === item.route }"
              >
                  <v-list-item-title class="text-white font-weight-medium">{{ item.title }}</v-list-item-title>
              </v-list-item>
            </template>
          </draggable>
        </v-list-group>
      </template>

      <v-spacer></v-spacer>

      <v-divider class="mt-6" />

      <v-list-item
        class="mx-5 mt-6 logout"
        link
        @click="logout"
        density="compact"
      >
        <template #prepend>
          <v-icon color="lighten-1" class="me-2">mdi-logout</v-icon>
        </template>
        <template #default>
          <span class="font-weight-bold text-white">تسجيل الخروج</span>
        </template>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import {ref, computed, onMounted, defineExpose} from 'vue'
import {useRouter} from 'vue-router'
import {useAuthStore} from '@/stores/auth.js'
import {useTaskStore} from '@/stores/taskStore.js'
import draggable from 'vuedraggable'

const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

const drawer = ref(true)

function toggleDrawer() {
  drawer.value = !drawer.value
}

defineExpose({drawer, toggleDrawer})

const isAuthenticated = computed(() => !!authStore.token)

onMounted(() => {
  if (!isAuthenticated.value) {
    router.push('/login')
  } else {
    taskStore.fetchTasks()
  }
})

const openSection = ref(null)

const sections = ref([
  {
    title: 'القائمة الرئيسية',
    items: [
      {
        title: 'لوحة المهام',
        icon: 'mdi-view-dashboard',
        value: 'dashboard',
        route: '/taskDashboard'
      },
      {title: 'مهامي', icon: 'mdi-format-list-checkbox', value: 'my-tasks', route: '/my-tasks'},
      {title: 'إضافة مهمة', icon: 'mdi-plus-box', value: 'add-task', route: '/add-task'},
      {
        title: 'المهام الجماعية',
        icon: 'mdi-account-multiple-check',
        value: 'team-tasks',
        route: '/team-tasks'
      },
    ]
  },
  {
    title: 'الإدارة',
    items: [
      {title: 'إدارة المستخدمين', icon: 'mdi-account-cog', value: 'users', route: '/users'},
      {title: 'التقارير', icon: 'mdi-chart-bar', value: 'reports', route: '/reports'},
      {title: 'التنبيهات', icon: 'mdi-bell-alert', value: 'notifications', route: '/notifications'},
      {title: 'الإعدادات', icon: 'mdi-cog', value: 'settings', route: '/app-settings'},
    ]
  },
  {
    title: 'التدريب والتطوير',
    items: [
      { title: 'التقويم', icon: 'mdi-calendar-month', value: 'calendar', route: '/calendar' },
      { title: 'الوحدات النمطية', icon: 'mdi-view-module', value: 'modules', route: '/modules' },
      { title: 'الأرشيف', icon: 'mdi-archive', value: 'archive', route: '/archive' },
      { title: 'الملفات', icon: 'mdi-file-document-box', value: 'files', route: '/files' },
      { title: 'الفريق', icon: 'mdi-account-group', value: 'team', route: '/team' },
      { title: 'الصلاحيات', icon: 'mdi-shield-account', value: 'roles', route: '/roles-permissions' },
      { title: 'المحادثات', icon: 'mdi-message-text', value: 'chat', route: '/chat' },
      { title: 'الإشعارات المخصصة', icon: 'mdi-bell-plus', value: 'custom-notifications', route: '/custom-notifications' },
      { title: 'الدروس التفاعلية', icon: 'mdi-school', value: 'lessons', route: '/lessons' },
      { title: 'الأدوات', icon: 'mdi-tools', value: 'tools', route: '/tools' },
      { title: 'السجل والأنشطة', icon: 'mdi-history', value: 'activity-log', route: '/activity-log' },
      { title: 'الترجمة', icon: 'mdi-translate', value: 'i18n', route: '/translations' },
    ]
  }
])

function handleMenuClick(item) {
  if (item.action === 'logout') {
    logout()
  } else if (item.route) {
    router.push(item.route)
  }
}

function logout() {
  authStore.logout()
  router.push('/login')
}

const activeRoute = computed(() => router.currentRoute.value.path)
</script>

<style scoped>
.custom-drawer {
  font-family: 'Cairo', sans-serif;
  transition: all 0.3s ease-in-out;
  overflow: hidden;
}

.group-section {
  background-color: rgba(255, 255, 255, 0.05);
  padding-top: 4px;
  padding-bottom: 4px;
  border-radius: 8px;
}

.v-list-group__header {
  padding-left: 12px !important;
  padding-right: 12px !important;
}

.active-item {
  background-color: rgba(255, 255, 255, 0.15);
}

.v-list-item {
  transition: background-color 0.2s ease, transform 0.2s ease;
  cursor: move;
}

.v-list-item:hover {
  transform: translateX(-2px);
  background-color: rgba(255, 255, 255, 0.08);
}

.ghost {
  opacity: 0.4;
}

.text-white {
  color: white !important;
}
</style>
