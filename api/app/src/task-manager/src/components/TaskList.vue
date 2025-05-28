<template>
  <v-container class="py-6">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pa-4" elevation="2">
          <!-- عنوان المهمة وزر إضافة مهمة -->
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

          <!-- رسالة لا توجد مهام -->
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

                  <EditTask />

<!--                  <v-btn-->
<!--                    color="info"-->
<!--                    size="small"-->
<!--                    variant="outlined"-->
<!--                    @click="openEditDialog(task)"-->
<!--                  >-->
<!--                    ✏ تعديل-->
<!--                  </v-btn>-->

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
import Swal from "sweetalert2";  // استيراد SweetAlert2
import AddTask from "@/components/AddTask.vue";
import { onMounted, ref } from "vue";
import { useTaskStore } from "@/stores/taskStore";
import { useToast } from "vue-toastification";
import EditTask from "@/components/EditTask.vue";

const taskStore = useTaskStore();
const toast = useToast();

// خيارات الحالة والأولوية موحدة
// const statusOptions = ["مفتوحة", "مكتملة"];
const priorityOptions = ["منخفضة", "متوسطة", "عالية"];

// خريطة عرض الأولوية (تطابق القيم المخزنة)
const priorityLabels = {
  low: "منخفضة",
  medium: "متوسطة",
  high: "عالية",
};

// تحميل المهام عند بدء الصفحة
onMounted(() => {
  taskStore.fetchTasks();
});

// --- تعديل المهمة ---

// const editDialog = ref(false);
// const editTaskId = ref(null);
// const editTitle = ref("");
// const editDescription = ref("");
// const editDueDate = ref("");
// const editStatus = ref("مفتوحة");
// const editPriority = ref("متوسطة");
//
// // أخطاء التحقق من صحة الحقول
// const editTitleError = ref("");
// const editDescriptionError = ref("");
// const editDueDateError = ref("");

// فتح مودال التعديل مع تعبئة الحقول
// const openEditDialog = (task) => {
//   editTaskId.value = task.id;
//   editTitle.value = task.title;
//   editDescription.value = task.description;
//   // تأكد من تحويل الحالة المخزنة (مثلاً "open" أو "مفتوحة") إلى قيمة عرض عربية متوافقة
//   editStatus.value = task.status === "مكتملة" ? "مكتملة" : "مفتوحة";
//   // تحويل أولوية المهمة إلى النص المعروض
//   editPriority.value = priorityLabels[task.priority] || "متوسطة";
//   editDueDate.value = task.due_date;
//   clearEditErrors();
//   editDialog.value = true;
// };

// مسح الأخطاء
// const clearEditErrors = () => {
//   editTitleError.value = "";
//   editDescriptionError.value = "";
//   editDueDateError.value = "";
// };

// // التحقق من الحقول المطلوبة
// const validateEditFields = () => {
//   let valid = true;
//   clearEditErrors();
//
//   if (!editTitle.value.trim()) {
//     editTitleError.value = "حقل العنوان مطلوب";
//     valid = false;
//   }
//   if (!editDescription.value.trim()) {
//     editDescriptionError.value = "حقل الوصف مطلوب";
//     valid = false;
//   }
//   if (!editDueDate.value) {
//     editDueDateError.value = "تاريخ الاستحقاق مطلوب";
//     valid = false;
//   }
//   return valid;
// };

// دالة لتحويل النص العربي للحالة والأولوية إلى القيم المخزنة
const statusToValue = (statusText) => (statusText === "مكتملة" ? "مكتملة" : "open");
const priorityToValue = (priorityText) => {
  switch (priorityText) {
    case "منخفضة":
      return "low";
    case "متوسطة":
      return "medium";
    case "عالية":
      return "high";
    default:
      return "medium";
  }
};

// حفظ التعديل
// const submitEditTask = async () => {
//   if (!validateEditFields()) {
//     toast.warning("⚠️ الرجاء تعبئة جميع الحقول المطلوبة");
//     return;
//   }

//   try {
//     await taskStore.updateTask({
//       id: editTaskId.value,
//       title: editTitle.value,
//       description: editDescription.value,
//       due_date: editDueDate.value,
//       status: statusToValue(editStatus.value),
//       priority: priorityToValue(editPriority.value),
//     });
//
//     if (taskStore.error) {
//       toast.error(`❌ حدث خطأ: ${taskStore.error}`);
//     } else {
//       toast.success("✅ تم تعديل المهمة بنجاح");
//       editDialog.value = false;
//     }
//   } catch (error) {
//     toast.error("⚠️ فشل في الاتصال بالخادم");
//     console.error(error);
//   }
// };

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

// دالة تنسيق التاريخ بالعربية (ar-EG)
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
