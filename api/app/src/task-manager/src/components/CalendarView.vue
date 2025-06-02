<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="mt-6 mb-10">
    <v-card elevation="3" class="pa-5 rounded-xl">
      <!-- العنوان والتحكم في العرض -->
      <div class="d-flex justify-space-between align-center mb-4">
        <v-btn icon color="primary" @click="$router.back()" variant="flat">
          <v-icon>mdi-arrow-right</v-icon>
        </v-btn>
        <h2 class="text-h5 font-weight-bold">📅 تقويم المهام</h2>

        <!-- أزرار التبديل بين العروض -->
        <v-btn-toggle
          v-model="selectedView"
          class="rounded-lg"
          color="primary"
          variant="outlined"
          density="comfortable"
        >
          <v-btn value="day">يومي</v-btn>
          <v-btn value="week">أسبوعي</v-btn>
          <v-btn value="month">شهري</v-btn>
          <v-btn value="year">سنوي</v-btn>
        </v-btn-toggle>
      </div>

      <!-- تقويم المهام -->
      <vue-cal
        class="custom-calendar mt-6"
        :view="selectedView"
        :events="calendarEvents"
        locale="ar"
        time="24"
        :disable-views="['years']"
        style="height: 650px; border-radius: 12px"
      />
    </v-card>
  </v-container>
</template>

<script setup>
import 'vue-cal/dist/vuecal.css'
import VueCal from 'vue-cal'
import { ref, computed, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

const taskStore = useTaskStore()
const selectedView = ref('month') // الوضع الافتراضي
const sidebarRef = ref(null)

onMounted(() => {
  taskStore.fetchTasks()
})

// تحويل المهام إلى أحداث تقويم
const calendarEvents = computed(() =>
  taskStore.tasks.map(task => {
    const isOverdue = new Date(task.due_date) < new Date() && task.status !== 'مكتملة'
    return {
      title: `📌 ${task.title}`,
      start: task.due_date,
      end: task.due_date,
      content: task.description,
      class: isOverdue ? 'event-overdue' : getStatusClass(task.status)
    }
  })
)


function getStatusClass(status) {
  switch (status) {
    case 'مكتملة': return 'event-green'
    case 'قيد التنفيذ': return 'event-orange'
    case 'تم الإلغاء': return 'event-red'
    default: return 'event-blue'
  }
}

// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>

<style scoped>
/* تخصيص تصميم التقويم */
.custom-calendar {
  font-family: 'Cairo', sans-serif;
  border-radius: 12px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
}
.event-overdue {
  background: repeating-linear-gradient(45deg, #b71c1c, #d32f2f 10px, #b71c1c 20px);
  color: white;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.7; }
  100% { opacity: 1; }
}

.vuecal__event {
  border-radius: 10px !important;
  font-size: 0.85rem !important;
  font-weight: 500;
  padding: 6px 12px !important;
  margin: 2px 0;
  transition: transform 0.3s, box-shadow 0.3s;
}

.vuecal__event:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ألوان المهام */
.event-green {
  background: linear-gradient(45deg, #43a047, #66bb6a) !important;
  color: white;
}
.event-orange {
  background: linear-gradient(45deg, #fb8c00, #ffa726) !important;
  color: white;
}
.event-red {
  background: linear-gradient(45deg, #e53935, #ef5350) !important;
  color: white;
}
.event-blue {
  background: linear-gradient(45deg, #1e88e5, #42a5f5) !important;
  color: white;
}
</style>
