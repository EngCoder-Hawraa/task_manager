<template>
  <AppHeader />
  <AppSidebar />

  <v-container class="py-9">
    <v-row>
      <v-col cols="12">
        <!-- 🔹 عنوان الصفحة -->
        <div class="d-flex align-center justify-space-between mb-6">
          <h2 class="text-h5 font-weight-bold">📋 مهامي</h2>
          <v-chip size="small" color="primary" class="px-3" text-color="white">
            {{ filteredTasks.length }} مهمة
          </v-chip>
        </div>

        <!-- 🔹 الفلاتر -->
        <v-btn-toggle
          v-model="selectedStatus"
          class="mb-6"
          rounded
          density="comfortable"
          color="primary"
          divided
        >
          <v-btn value="all">الكل</v-btn>
          <v-btn value="مفتوحة">مفتوحة</v-btn>
          <v-btn value="قيد التنفيذ">قيد التنفيذ</v-btn>
          <v-btn value="مكتملة">مكتملة</v-btn>
          <v-btn value="تم الإلغاء">تم الإلغاء</v-btn>
        </v-btn-toggle>

        <!-- 🔹 قائمة المهام -->
        <v-slide-y-transition group>
          <v-card
            v-for="task in filteredTasks"
            :key="task.id"
            class="mb-4 pa-4 elevation-2 rounded-xl"
          >
            <v-row>
              <v-col cols="12" sm="8">
                <h3 class="text-h6 font-weight-medium mb-1">{{ task.title }}</h3>
                <p class="text-body-2 text-grey-darken-1 mb-2">{{ task.description }}</p>
                <v-chip :color="statusColor(task.status)" size="small" class="mt-1" text-color="white">
                  {{ statusLabel(task.status) }}
                </v-chip>
              </v-col>
              <v-col
                cols="12"
                sm="4"
                class="d-flex justify-end align-center flex-wrap"
              >
                <v-tooltip text="تعديل المهمة" location="top">
                  <template #activator="{ props }">
                    <v-btn icon color="primary" class="me-2" v-bind="props" @click="editTask(task)">
                      <v-icon size="22">mdi-pencil</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>

                <v-tooltip text="حذف المهمة" location="top">
                  <template #activator="{ props }">
                    <v-btn icon color="red" v-bind="props" @click="deleteTask(task.id)">
                      <v-icon size="22">mdi-delete</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-card>
        </v-slide-y-transition>

        <!-- 🔹 لا توجد مهام -->
        <v-alert
          v-if="filteredTasks.length === 0"
          type="info"
          border="start"
          color="blue-lighten-4"
          class="mt-10 text-center"
        >
          لا توجد مهام ضمن هذا التصنيف.
        </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import AppSidebar from "@/components/AppSidebar.vue"
import AppHeader from "@/components/AppHeader.vue"

const taskStore = useTaskStore()
const selectedStatus = ref('all')

onMounted(() => {
  taskStore.fetchTasks()
})

const filteredTasks = computed(() => {
  if (selectedStatus.value === 'all') {
    return taskStore.tasks
  }
  return taskStore.tasks.filter(task => task.status === selectedStatus.value)
})

function deleteTask(id) {
  taskStore.deleteTask(id)
}

function editTask(task) {
  console.log('تعديل المهمة:', task)
}

function statusLabel(status) {
  switch (status) {
    case 'مفتوحة':
      return 'مفتوحة'
    case 'قيد التنفيذ':
      return 'قيد التنفيذ'
    case 'مكتملة':
      return 'مكتملة'
    case 'تم الإلغاء':
      return 'تم الإلغاء'
    default:
      return 'غير معروف'
  }
}

function statusColor(status) {
  switch (status) {
    case 'done':
      return 'green'
    case 'pending':
      return 'blue'
    case 'delayed':
      return 'orange'
    default:
      return 'grey'
  }
}
</script>

<style scoped>
h2 {
  font-family: 'Cairo', sans-serif;
}
</style>
