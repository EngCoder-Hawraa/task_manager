<template>
  <v-dialog
    v-model="dialog"
    max-width="560"
    scrollable
    persistent
    transition="dialog-bottom-transition"
    overlay-color="rgba(0, 0, 0, 0.4)"
  >
    <v-sheet
      elevation="10"
      rounded="xl"
      class="pa-6"
      style="max-height: 80vh; overflow-y: auto;"
    >
      <!-- رأس المودال -->
      <div class="d-flex justify-center align-center mb-5">
        <v-icon size="36" color="primary" class="me-3">mdi-pencil</v-icon>
        <h3 class="text-h5 font-weight-bold mb-0" style="font-family: 'Cairo', sans-serif;">
          تعديل المهمة
        </h3>
      </div>

      <!-- نموذج التعديل -->
      <v-form ref="editFormRef" @submit.prevent="submitEditTask" lazy-validation>
        <v-text-field
          v-model="editTitle"
          label="✏️ العنوان"
          :error="!!editTitleError"
          :error-messages="editTitleError"
          variant="outlined"
          rounded
          dense
          color="primary"
          class="mb-4"
          required
          autofocus
        />

        <v-textarea
          v-model="editDescription"
          label="📝 الوصف"
          :error="!!editDescriptionError"
          :error-messages="editDescriptionError"
          variant="outlined"
          rounded
          dense
          rows="4"
          color="primary"
          class="mb-4"
          required
        />

        <v-text-field
          v-model="editDueDate"
          label="📅 تاريخ الاستحقاق"
          type="date"
          :error="!!editDueDateError"
          :error-messages="editDueDateError"
          variant="outlined"
          rounded
          dense
          color="primary"
          class="mb-4"
          required
        />

        <v-select
          v-model="editStatus"
          :items="statusOptions"
          label="الحالة"
          variant="outlined"
          rounded
          dense
          color="primary"
          class="mb-4"
          required
        />

        <v-select
          v-model="editPriority"
          :items="priorityOptions"
          label="الأولوية"
          variant="outlined"
          rounded
          dense
          color="primary"
          class="mb-6"
          required
        />

        <!-- أزرار الإجراء -->
        <div class="d-flex justify-end gap-3">
          <v-btn
            variant="text"
            color="error"
            @click="closeDialog"
            rounded
          >
            إلغاء
          </v-btn>

          <v-btn
            color="primary"
            type="submit"
            rounded
            elevation="2"
            class="text-white"
          >
            💾 حفظ التعديل
          </v-btn>
        </div>
      </v-form>
    </v-sheet>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import Swal from 'sweetalert2'

// 🟦 الخصائص والدوال
const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['close', 'updated'])

const taskStore = useTaskStore()

const dialog = ref(true)

// 🟩 الحقول القابلة للتعديل
const editTitle = ref('')
const editDescription = ref('')
const editDueDate = ref('')
const editStatus = ref('مفتوحة')
const editPriority = ref('متوسطة')

// 🟥 أخطاء الحقول
const editTitleError = ref('')
const editDescriptionError = ref('')
const editDueDateError = ref('')

// 🟨 الخيارات
const statusOptions = ['مفتوحة', 'قيد التنفيذ', 'مكتملة', 'تم الإلغاء']
const priorityOptions = ['منخفضة', 'متوسطة', 'عالية']

const statusValueMap = {
  'مفتوحة': 'مفتوحة',
  'قيد التنفيذ': 'قيد التنفيذ',
  'مكتملة': 'مكتملة',
  'تم الإلغاء': 'تم الإلغاء',
}

const priorityValueMap = {
  'منخفضة': 'low',
  'متوسطة': 'medium',
  'عالية': 'high',
}

// ✅ تنظيف الأخطاء
const clearErrors = () => {
  editTitleError.value = ''
  editDescriptionError.value = ''
  editDueDateError.value = ''
}

// ✅ التحقق من صحة الحقول
const validateFields = () => {
  clearErrors()
  let valid = true

  if (!editTitle.value.trim()) {
    editTitleError.value = 'حقل العنوان مطلوب'
    valid = false
  }

  if (!editDescription.value.trim()) {
    editDescriptionError.value = 'حقل الوصف مطلوب'
    valid = false
  }

  if (!editDueDate.value) {
    editDueDateError.value = 'تاريخ الاستحقاق مطلوب'
    valid = false
  }

  return valid
}

// ✅ تنسيق التاريخ
const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

// ✅ تحميل بيانات المهمة عند التعديل
watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      editTitle.value = newTask.title || ''
      editDescription.value = newTask.description || ''
      editDueDate.value = formatDate(newTask.due_date)
      editStatus.value = newTask.status || 'مفتوحة'

      switch (newTask.priority) {
        case 'low':
          editPriority.value = 'منخفضة'
          break
        case 'high':
          editPriority.value = 'عالية'
          break
        default:
          editPriority.value = 'متوسطة'
      }
    }
  },
  { immediate: true }
)

// ✅ إغلاق نافذة التعديل
const closeDialog = () => {
  dialog.value = false
  emit('close')
}

// ✅ حفظ التعديلات
const submitEditTask = async () => {
  if (!validateFields()) {
    Swal.fire({
      icon: 'warning',
      title: '⚠️ تنبيه',
      text: 'الرجاء تعبئة جميع الحقول المطلوبة',
      confirmButtonText: 'حسنًا',
      confirmButtonColor: '#f59e0b'
    })
    return
  }

  try {
    await taskStore.updateTask(props.task.id, {
      title: editTitle.value.trim(),
      description: editDescription.value.trim(),
      due_date: editDueDate.value,
      status: statusValueMap[editStatus.value] || 'مفتوحة',
      priority: priorityValueMap[editPriority.value] || 'medium',
    })

    if (taskStore.error) {
      Swal.fire({
        icon: 'error',
        title: '❌ خطأ',
        text: `حدث خطأ: ${taskStore.error}`,
        confirmButtonText: 'موافق',
        confirmButtonColor: '#d33'
      })
    } else {
      await Swal.fire({
        icon: 'success',
        title: '✅ تم التعديل',
        text: 'تم تعديل المهمة بنجاح.',
        confirmButtonText: 'موافق',
        confirmButtonColor: '#3085d6'
      })
      emit('updated')
      closeDialog()
    }
  } catch (error) {
    // console.error(error)
    Swal.fire({
      icon: 'error',
      title: '⚠️ فشل الاتصال',
      text: 'فشل في الاتصال بالخادم.',
      confirmButtonText: 'حسنًا',
      confirmButtonColor: '#d33'
    })
  }
}
</script>


<style scoped>
/* خطوط ونصوص */
.v-sheet {
  background: #fff;
}

.v-dialog__content {
  backdrop-filter: blur(8px);
}

.v-btn {
  font-family: 'Cairo', sans-serif;
  font-weight: 600;
}

h3 {
  color: #1976d2; /* نفس لون primary */
}

/* حواف ناعمة للحقول */
.v-text-field,
.v-textarea,
.v-select {
  --v-border-radius: 12px;
  background-color: #f5f7fa;
}

/* رسائل الخطأ */
.v-messages__message {
  font-family: 'Cairo', sans-serif;
  font-size: 0.85rem;
  color: #d32f2f;
  margin-top: 2px;
}

/* زيادة المسافات */
.v-form > * + * {
  margin-top: 14px !important;
}
</style>
