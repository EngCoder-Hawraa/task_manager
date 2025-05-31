<template>
  <v-container class="py-6">
    <v-row justify="center">
      <v-col cols="12" md="11">
        <v-card class="pa-4" elevation="2">

          <!-- العنوان وزر إضافة مهمة -->
          <div class="d-flex justify-space-between align-center mb-4">
            <v-card-title class="text-h5">📝 قائمة المهام</v-card-title>
            <AddTask />
            <v-btn
              to="/calendar"
              color="primary"
              prepend-icon="mdi-calendar"
            >
              عرض التقويم
            </v-btn>
          </div>
          <!-- فلترة حسب الحالة -->
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

          <!-- شريط تحميل -->
          <v-progress-linear
            v-if="taskStore.loading"
            indeterminate
            color="primary"
            class="mb-4"
          />

          <!-- رسالة عدم وجود مهام -->
          <v-alert
            v-else-if="filteredTasks.length === 0"
            type="info"
            variant="tonal"
            class="text-center"
          >
            لا توجد مهام حسب الفلتر الحالي.
          </v-alert>

          <!-- عرض المهام -->
          <v-row v-else dense>
            <v-col
              v-for="task in filteredTasks"
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
                  <!-- زر "تم الإنجاز" يظهر فقط للمهام غير المكتملة -->
                  <v-btn
                    v-if="task.status !== 'مكتملة'"
                    color="success"
                    size="small"
                    variant="flat"
                    @click="markAsDone(task.id)"
                  >
                    ✔ تم الإنجاز
                  </v-btn>

                  <!-- زر تعديل المهمة -->
                  <EditTask :task="task" />

                  <!-- زر حذف المهمة -->
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
import { ref, computed, onMounted } from "vue";
import { useToast } from "vue-toastification";
import { useTaskStore } from "@/stores/taskStore";

import AddTask from "@/components/AddTask.vue";
import EditTask from "@/components/EditTask.vue";

const taskStore = useTaskStore();
const toast = useToast();

// الحالة الافتراضية للفلتر (كل المهام)
const filterStatus = ref("الكل");

// خريطة ترجمة الحالة إلى اللون والنص
const statusLabels = {
  "مفتوحة": { text: "مفتوحة", color: "blue" },
  "قيد التنفيذ": { text: "قيد التنفيذ", color: "orange" },
  "مكتملة": { text: "مكتملة", color: "green" },
  "تم الإلغاء": { text: "تم الإلغاء", color: "red" },
};

// خريطة عرض الأولوية بالنص العربي
const priorityLabels = {
  low: "منخفضة",
  medium: "متوسطة",
  high: "عالية",
};

// دالة لتصفية المهام حسب حالة الفلتر
const filteredTasks = computed(() => {
  if (filterStatus.value === "الكل") {
    return taskStore.tasks;
  }
  return taskStore.tasks.filter(task => task.status === filterStatus.value);
});

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

// دالة تنسيق التاريخ إلى صيغة عربية
const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("ar-EG", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

// دالة وضع المهمة كمكتملة (يمكن تعديلها حسب طريقة التخزين)
const markAsDone = async (id) => {
  try {
    await taskStore.markAsDone(id);
    toast.success("✅ تم تحديث حالة المهمة إلى مكتملة");
  } catch (error) {
    toast.error("❌ حدث خطأ أثناء تحديث الحالة");
  }
};
</script>
