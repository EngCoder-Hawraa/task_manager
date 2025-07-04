<template>
  <v-container class="py-6">
    <v-row justify="center">
      <v-col cols="12" md="11">
        <v-card class="pa-4" elevation="2">
          <!-- ✅ العنوان والأزرار -->
          <div class="d-flex justify-space-between align-center mb-4 flex-wrap gap-2">
            <v-card-title class="text-h5">📝 قائمة المهام</v-card-title>

            <div class="d-flex align-center flex-wrap gap-2">
              <AddTask v-model:dialog="dialog" />
              <v-btn color="primary" class="me-3" @click="dialog = true" prepend-icon="mdi-plus">
                إضافة مهمة جديدة
              </v-btn>
              <v-btn to="/calendar" color="primary" prepend-icon="mdi-calendar">
                عرض التقويم
              </v-btn>
            </div>
          </div>

          <!-- ✅ الفلترة -->
          <v-btn-toggle
            v-model="filterStatus"
            class="mb-3"
            color="primary"
            dense
            rounded
            mandatory
          >
            <v-btn value="الكل">الكل</v-btn>
            <v-btn value="مفتوحة">مفتوحة</v-btn>
            <v-btn value="قيد التنفيذ">قيد التنفيذ</v-btn>
            <v-btn value="مكتملة">مكتملة</v-btn>
            <v-btn value="تم الإلغاء">تم الإلغاء</v-btn>
          </v-btn-toggle>

          <v-divider class="my-3" />

          <!-- ✅ Skeleton Loader أثناء التحميل -->
          <v-row v-if="taskStore.loading" dense>
            <v-col v-for="n in 6" :key="n" cols="12" md="6">
              <v-card elevation="1" class="mb-3 pa-3">
                <v-skeleton-loader
                  :loading="true"
                  boilerplate
                  type="card-avatar, heading, text, text, actions"
                  class="mx-auto slow-fade"
                  height="170"
                  elevation="0"
                />
              </v-card>
            </v-col>
          </v-row>

          <!-- ⛔ لا توجد مهام -->
          <v-alert
            v-else-if="filteredTasks.length === 0"
            type="info"
            variant="tonal"
            class="text-center"
          >
            لا توجد مهام حسب الفلتر الحالي.
          </v-alert>

          <!-- ✅ عرض المهام مع pagination -->
          <v-fade-transition mode="out-in">
            <div key="task-list">
              <v-row dense>
                <v-col
                  v-for="task in paginatedTasks"
                  :key="task.id"
                  cols="12"
                  md="6"
                >
                  <v-card elevation="1" class="mb-3 task-card-fade">
                    <v-card-title>
                      <div class="d-flex justify-space-between align-center w-100">
                        <span>{{ task.title }}</span>
                        <v-chip
                          size="small"
                          :color="statusLabels[task.status]?.color || 'grey'"
                          class="text-white"
                        >
                          {{ statusLabels[task.status]?.text || task.status }}
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
                        @click="markAsDone(task.id)"
                      >
                        ✔ تم الإنجاز
                      </v-btn>

                      <EditTask :task="task" />

                      <v-btn
                        color="error"
                        size="small"
                        variant="tonal"
                        class="me-4"
                        @click="confirmDelete(task.id)"
                      >
                        🗑 حذف
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>

              <!-- ✅ Pagination -->
              <v-pagination
                v-model="currentPage"
                :length="totalPages"
                :total-visible="5"
                color="primary"
                size="large"
                rounded
                class="custom-pagination mt-4 justify-center"
                prev-icon="mdi-chevron-right"
                next-icon="mdi-chevron-left"
              />
            </div>
          </v-fade-transition>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import Swal from "sweetalert2";
import { ref, computed, onMounted, watch } from "vue";
import { useToast } from "vue-toastification";
import { useTaskStore } from "@/stores/taskStore";

import AddTask from "@/components/AddTask.vue";
import EditTask from "@/components/EditTask.vue";

const taskStore = useTaskStore();
const toast = useToast();

const dialog = ref(false);
const filterStatus = ref("الكل");

// ✅ Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

const statusLabels = {
  "مفتوحة": { text: "مفتوحة", color: "blue" },
  "قيد التنفيذ": { text: "قيد التنفيذ", color: "orange" },
  "مكتملة": { text: "مكتملة", color: "green" },
  "تم الإلغاء": { text: "تم الإلغاء", color: "red" },
};

const priorityLabels = {
  low: "منخفضة",
  medium: "متوسطة",
  high: "عالية",
};

const filteredTasks = computed(() => {
  if (filterStatus.value === "الكل") {
    return taskStore.tasks;
  }
  return taskStore.tasks.filter(task => task.status === filterStatus.value);
});

const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredTasks.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => {
  return Math.ceil(filteredTasks.value.length / itemsPerPage);
});

// ✅ Reset pagination when filter changes
watch(filterStatus, () => {
  currentPage.value = 1;
});

onMounted(() => {
  taskStore.fetchTasks();
});

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

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("ar-EG", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const markAsDone = async (id) => {
  try {
    await taskStore.markAsDone(id);
    toast.success("✅ تم تحديث حالة المهمة إلى مكتملة");
  } catch (error) {
    toast.error("❌ حدث خطأ أثناء تحديث الحالة");
  }
};
</script>

<style scoped>
.slow-fade {
  position: relative;
  overflow: hidden;
  background-color: #f2f2f2;
  border-radius: 12px;
}

.slow-fade::after {
  content: "";
  position: absolute;
  top: 0;
  left: -150%;
  height: 100%;
  width: 150%;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.4) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: slow-shimmer 4s ease-in-out infinite;
  z-index: 1;
}

@keyframes slow-shimmer {
  0% {
    left: -150%;
  }
  100% {
    left: 100%;
  }
}

.task-card-fade {
  animation: fadeInCard 0.8s ease forwards;
  opacity: 0;
}

@keyframes fadeInCard {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.custom-pagination {
  direction: rtl; /* لضبط الاتجاه حسب اللغة */
  justify-content: center;
  gap: 6px;
}

.custom-pagination .v-pagination__item {
  background-color: #e3f2fd; /* أزرق فاتح */
  color: #1565c0; /* أزرق متوسط */
  font-weight: bold;
  border-radius: 12px;
  padding: 8px 12px;
  transition: all 0.3s ease;
}

.custom-pagination .v-pagination__item--is-active {
  background-color: #1565c0 !important; /* أزرق غامق عند التحديد */
  color: white !important;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.custom-pagination .v-pagination__more {
  color: #90caf9;
  font-weight: bold;
}


</style>
