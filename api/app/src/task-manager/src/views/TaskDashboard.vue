<template>
  <AppHeader />
  <div v-if="isAuthenticated">
    <v-row>
      <v-col cols="12" md="4">
        <v-card color="blue-lighten-4">
          <v-card-title>إجمالي المهام</v-card-title>
          <v-card-text>{{ totalTasks }}</v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="green-lighten-4">
          <v-card-title>المهام المكتملة</v-card-title>
          <v-card-text>{{ completedTasks }}</v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="red-lighten-4">
          <v-card-title>المهام المتبقية</v-card-title>
          <v-card-text>{{ pendingTasks }}</v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-divider class="my-4" />

    <h3>آخر المهام:</h3>
<!--    <v-list>-->
<!--      <v-list-item v-for="task in latestTasks" :key="task.id">-->
<!--        <v-list-item-content>-->
<!--          <v-list-item-title>{{ task.title }}</v-list-item-title>-->
<!--          <v-list-item-subtitle>{{ task.status }}</v-list-item-subtitle>-->
<!--        </v-list-item-content>-->
<!--      </v-list-item>-->
<!--    </v-list>-->
  </div>

  <TaskList v-if="isAuthenticated" />
  <div v-else>
    <p class="text-center my-10">الرجاء تسجيل الدخول للوصول إلى المهام.</p>
  </div>
</template>

<script setup>
import AppHeader from "@/components/AppHeader.vue";
import TaskList from "@/components/TaskList.vue";
import { onMounted, computed } from 'vue'
import { useTaskStore } from '@/stores/taskStore.js'
import { useAuthStore } from '@/stores/auth.js' // 🟡 استدعاء authStore
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

const isAuthenticated = computed(() => !!authStore.token)

// إعادة توجيه إن لم يكن مسجل دخول
onMounted(() => {
  if (!isAuthenticated.value) {
    router.push('/login')
  } else {
    taskStore.fetchTasks()
  }
})

// حساب إجمالي المهام
const totalTasks = computed(() => taskStore.tasks.length)

// حساب المهام المكتملة
const completedTasks = computed(() =>
  taskStore.tasks.filter(task => task.status === 'مكتملة').length
)

// حساب المهام المتبقية
const pendingTasks = computed(() =>
  taskStore.tasks.filter(task => task.status !== 'مكتملة').length
)

// استخراج آخر المهام (مثلاً 5 مهام)
const latestTasks = computed(() => {
  return [...taskStore.tasks]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5)
})
</script>
