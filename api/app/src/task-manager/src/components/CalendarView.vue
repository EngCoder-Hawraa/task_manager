<template>
  <v-container>
    <h2 class="text-h5 mb-4">📅 تقويم المهام</h2>

    <vue-cal
      :events="calendarEvents"
      default-view="month"
      style="height: 600px"
      :disable-views="['years', 'year']"
      time="24"
      locale="ar"
    />
  </v-container>
</template>

<script setup>
import 'vue-cal/dist/vuecal.css'
import VueCal from 'vue-cal'
import { computed, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'

const taskStore = useTaskStore()

onMounted(() => {
  taskStore.fetchTasks()
})

// تحويل المهام إلى صيغة يدعمها Vue Cal
const calendarEvents = computed(() => {
  return taskStore.tasks.map(task => ({
    title: task.title,
    start: task.due_date,
    end: task.due_date,
    content: task.description,
    class: getStatusClass(task.status),
  }))
})

// تحديد اللون حسب الحالة
function getStatusClass(status) {
  switch (status) {
    case 'مكتملة':
      return 'event-green'
    case 'قيد التنفيذ':
      return 'event-orange'
    case 'تم الإلغاء':
      return 'event-red'
    default:
      return 'event-blue'
  }
}
</script>

<style>
/* ألوان المهام */
.event-green {
  background-color: #4caf50 !important;
  color: white;
}
.event-orange {
  background-color: #ff9800 !important;
  color: white;
}
.event-red {
  background-color: #f44336 !important;
  color: white;
}
.event-blue {
  background-color: #2196f3 !important;
  color: white;
}
</style>
