<template>
  <v-container class="py-4">
    <!-- زر فتح النموذج -->
    <v-btn
      color="info"
      size="small"
      variant="outlined"
      @click="openEditDialog(task)"
    >
      ✏ تعديل
    </v-btn>

    <!-- مودال تعديل المهمة -->
    <v-dialog v-model="editDialog" max-width="600">
      <v-card>
        <v-card-title class="text-h6 text-center">✏ تعديل المهمة</v-card-title>
        <v-divider class="my-3"></v-divider>
        <v-card-text>
          <v-form @submit.prevent="submitEditTask" ref="editFormRef">
            <v-text-field
              v-model="editTitle"
              label="العنوان"
              :error-messages="editTitleError"
              required
              class="mb-3"
            />
            <v-textarea
              v-model="editDescription"
              label="الوصف"
              :error-messages="editDescriptionError"
              required
              rows="3"
              class="mb-3"
            />
            <v-text-field
              v-model="editDueDate"
              label="تاريخ الاستحقاق"
              type="date"
              :error-messages="editDueDateError"
              required
              class="mb-4"
            />
            <v-select
              v-model="editStatus"
              :items="statusOptions"
              label="الحالة"
              class="mb-4"
              required
            />
            <v-select
              v-model="editPriority"
              :items="priorityOptions"
              label="الأولوية"
              class="mb-4"
              required
            />

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text @click="editDialog = false">إلغاء</v-btn>
              <v-btn color="primary" type="submit">حفظ التعديل</v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import {ref} from 'vue'
import {useTaskStore} from '@/stores/taskStore'
import {useToast} from 'vue-toastification'
// --- تعديل المهمة ---

const editDialog = ref(false);
const editTaskId = ref(null);
const editTitle = ref("");
const editDescription = ref("");
const editDueDate = ref("");
const editStatus = ref("مفتوحة");
const editPriority = ref("متوسطة");
// استدعاء المتجر
const taskStore = useTaskStore()
// أخطاء التحقق من صحة الحقول
const editTitleError = ref("");
const editDescriptionError = ref("");
const editDueDateError = ref("");
const toast = useToast();

// خريطة عرض الأولوية (تطابق القيم المخزنة)
const priorityLabels = {
  low: "منخفضة",
  medium: "متوسطة",
  high: "عالية",
};
// فتح مودال التعديل مع تعبئة الحقول
const openEditDialog = (task) => {
  editTaskId.value = task.id;
  editTitle.value = task.title;
  editDescription.value = task.description;
  // تأكد من تحويل الحالة المخزنة (مثلاً "open" أو "مفتوحة") إلى قيمة عرض عربية متوافقة
  editStatus.value = task.status === "مكتملة" ? "مكتملة" : "مفتوحة";
  // تحويل أولوية المهمة إلى النص المعروض
  editPriority.value = priorityLabels[task.priority] || "متوسطة";
  editDueDate.value = task.due_date;
  clearEditErrors();
  editDialog.value = true;
};

// مسح الأخطاء
const clearEditErrors = () => {
  editTitleError.value = "";
  editDescriptionError.value = "";
  editDueDateError.value = "";
};

// التحقق من الحقول المطلوبة
const validateEditFields = () => {
  let valid = true;
  clearEditErrors();

  if (!editTitle.value.trim()) {
    editTitleError.value = "حقل العنوان مطلوب";
    valid = false;
  }
  if (!editDescription.value.trim()) {
    editDescriptionError.value = "حقل الوصف مطلوب";
    valid = false;
  }
  if (!editDueDate.value) {
    editDueDateError.value = "تاريخ الاستحقاق مطلوب";
    valid = false;
  }
  return valid;
};

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
const submitEditTask = async () => {
  if (!validateEditFields()) {
    toast.warning("⚠️ الرجاء تعبئة جميع الحقول المطلوبة");
    return;
  }

  try {
    await taskStore.updateTask({
      id: editTaskId.value,
      title: editTitle.value,
      description: editDescription.value,
      due_date: editDueDate.value,
      status: statusToValue(editStatus.value),
      priority: priorityToValue(editPriority.value),
    });

    if (taskStore.error) {
      toast.error(`❌ حدث خطأ: ${taskStore.error}`);
    } else {
      toast.success("✅ تم تعديل المهمة بنجاح");
      editDialog.value = false;
    }
  } catch (error) {
    toast.error("⚠️ فشل في الاتصال بالخادم");
    console.error(error);
  }
};

</script>
