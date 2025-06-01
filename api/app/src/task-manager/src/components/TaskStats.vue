<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTaskStore } from '@/stores/taskStore'

// استدعاء router و stores
const router = useRouter()
const authStore = useAuthStore()
const taskStore = useTaskStore()

// تحقق من حالة تسجيل الدخول
const isAuthenticated = computed(() => !!authStore.token)

// عند تحميل الصفحة
onMounted(() => {
  if (!isAuthenticated.value) {
    router.push('/login')
  } else {
    taskStore.fetchTasks()
  }
})

// حساب الإحصائيات
const totalTasks = computed(() => taskStore.tasks.length)

const completedTasks = computed(() =>
  taskStore.tasks.filter(task => task.status === 'مكتملة').length
)

const pendingTasks = computed(() =>
  taskStore.tasks.filter(task => task.status !== 'مكتملة').length
)
</script>

<template>
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

    <v-divider class="my-4"/>

    <h3>آخر المهام:</h3>
    <!-- يمكنك تفعيل القائمة لاحقًا -->
    <!--
    <v-list>
      <v-list-item v-for="task in latestTasks" :key="task.id">
        <v-list-item-content>
          <v-list-item-title>{{ task.title }}</v-list-item-title>
          <v-list-item-subtitle>{{ task.status }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    -->
  </div>
</template>

<style scoped>
</style>
