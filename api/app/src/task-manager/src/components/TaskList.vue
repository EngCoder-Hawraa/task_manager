<template>
  <v-container class="py-6">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pa-4" elevation="2">
          <!-- العنوان وزر إضافة مهمة -->
          <div class="d-flex justify-space-between align-center mb-4">
            <v-card-title class="text-h5">📝 قائمة المهام</v-card-title>
            <AddTask />
          </div>

          <v-divider class="my-3" />

          <!-- شريط تحميل -->
          <v-progress-linear
            v-if="taskStore.loading"
            indeterminate
            color="primary"
            class="mb-4"
          />

          <!-- رسالة عدم وجود مهام -->
          <v-alert
            v-else-if="taskStore.tasks.length === 0"
            type="info"
            variant="tonal"
            class="text-center"
          >
            لا توجد مهام حالياً.
          </v-alert>

          <!-- عرض المهام -->
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
                    <v-chip
                      size="small"
                      :color="task.status === 'مكتملة' ? 'green' : 'secondary'"
                      class="text-white"
                    >
                      {{ task.status }}
                    </v-chip>
                  </div>
                </v-card-title>

                <v-card-text>
                  <p class="mb-2 text-grey-darken-1">{{ task.description }}</p>
                  <p class="mb-1"><strong>الأولوية:</strong> {{ priorityLabels[task.priority] }}</p>
                  <p><strong>التاريخ:</strong> {{ formatDate(task.due_date) }}</p>
                </v-card-text>

                <v-card-actions>
                  <v-btn
                    v-if="task.status !== 'مكتملة'"
                    color="success"
                    size="small"
                    variant="flat"
                    @click="taskStore.markAsDone(task.id)"
                  >
                    ✔ تم الإنجاز
                  </v-btn>

                  <EditTask :task="task" />

                  <v-btn
                    color="error"
                    size="small"
                    variant="tonal"
                    @click="confirmDelete(task.id)"
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
import Swal from "sweetalert2";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";
import { useTaskStore } from "@/stores/taskStore";

import AddTask from "@/components/AddTask.vue";
import EditTask from "@/components/EditTask.vue";

const taskStore = useTaskStore();
const toast = useToast();

// خريطة عرض الأولوية بالنص العربي
const priorityLabels = {
  low: "منخفضة",
  medium: "متوسطة",
  high: "عالية",
};

// تحميل المهام عند تحميل المكون
onMounted(() => {
  taskStore.fetchTasks();
});

// دالة تأكيد الحذف
const confirmDelete = async (id) => {
  const result = await Swal.fire({
    title: "هل أنت متأكد؟",
    text: "لن تتمكن من استعادة المهمة بعد الحذف!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "نعم، احذف!",
    cancelButtonText: "إلغاء",
    reverseButtons: true,
  });

  if (result.isConfirmed) {
    await taskStore.deleteTask(id);
    toast.success("✅ تم حذف المهمة بنجاح");
  }
};

// دالة لتنسيق التاريخ بصيغة عربية
const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("ar-EG", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};
</script>
