<template>
  <v-container class="py-6">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pa-4" elevation="2">
          <v-card-title class="text-h5 text-center">📝 قائمة المهام</v-card-title>

          <v-divider class="my-3"></v-divider>

          <v-progress-linear
            v-if="taskStore.loading"
            indeterminate
            color="primary"
            class="mb-4"
          ></v-progress-linear>

          <v-alert
            v-else-if="taskStore.tasks.length === 0"
            type="info"
            variant="tonal"
            class="text-center"
          >
            لا توجد مهام حالياً.
          </v-alert>

          <v-row v-else dense>
            <v-col
              v-for="task in taskStore.tasks"
              :key="task.id"
              cols="12"
              md="6"
            >
              <v-card elevation="1" class="mb-3">
                <v-card-title>
                  <div class="d-flex justify-space-between align-center w-100">
                    <span>{{ task.title }}</span>
                    <v-chip size="small" color="secondary" class="text-white">{{ task.status }}</v-chip>
                  </div>
                </v-card-title>

                <v-card-text>
                  <p class="mb-2 text-grey-darken-1">{{ task.description }}</p>
                  <p class="mb-1"><strong>الأولوية:</strong> {{ task.priority }}</p>
                  <p><strong>التاريخ:</strong> {{ new Date(task.due_date).toLocaleDateString() }}</p>
                </v-card-text>

                <v-card-actions>
                  <v-btn
                    color="success"
                    size="small"
                    variant="flat"
                    @click="taskStore.markAsDone(task.id)"
                  >
                    ✔ تم الإنجاز
                  </v-btn>
                  <v-btn
                    color="error"
                    size="small"
                    variant="tonal"
                    @click="taskStore.deleteTask(task.id)"
                  >
                    🗑 حذف
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'

const taskStore = useTaskStore()

onMounted(() => {
  taskStore.fetchTasks()
})
</script>
