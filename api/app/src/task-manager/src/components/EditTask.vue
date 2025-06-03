<template>
  <v-container class="py-4">
    <v-btn
      color="info"
      size="small"
      variant="tonal"
      @click="openEditDialog(task)"
    >
      ✏ تعديل
    </v-btn>

    <v-dialog
      v-model="editDialog"
      max-width="600"
      transition="dialog-bottom-transition"
      persistent
      scrim="rgba(0,0,0,0.5)"
    >
      <v-card class="rounded-xl elevation-10">
        <v-card-title class="text-h6 text-center d-flex align-center justify-center">
          <v-icon color="primary" class="me-2">mdi-pencil</v-icon>
          ✏ تعديل المهمة
        </v-card-title>

        <v-divider class="my-2" />

        <v-card-text class="pt-0">
          <v-form @submit.prevent="submitEditTask" ref="editFormRef" validate-on="submit lazy">
            <v-text-field
              v-model="editTitle"
              label="✏️ العنوان"
              :error-messages="editTitleError"
              variant="outlined"
              color="primary"
              rounded
              class="mb-4"
              required
            />

            <v-textarea
              v-model="editDescription"
              label="📝 الوصف"
              :error-messages="editDescriptionError"
              variant="outlined"
              color="primary"
              rounded
              rows="3"
              class="mb-4"
              required
            />

            <v-text-field
              v-model="editDueDate"
              label="📅 تاريخ الاستحقاق"
              type="date"
              :error-messages="editDueDateError"
              variant="outlined"
              color="primary"
              rounded
              class="mb-4"
              required
            />

            <v-select
              v-model="editStatus"
              :items="statusOptions"
              label="الحالة"
              variant="outlined"
              color="primary"
              rounded
              class="mb-4"
              required
            />

            <v-select
              v-model="editPriority"
              :items="priorityOptions"
              label="الأولوية"
              variant="outlined"
              color="primary"
              rounded
              class="mb-5"
              required
            />

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="error" @click="editDialog = false">إلغاء</v-btn>
              <v-btn color="primary" type="submit" rounded elevation="2">
                💾 حفظ التعديل
              </v-btn>
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

const editDialog = ref(false)
const editTaskId = ref(null)
const editTitle = ref('')
const editDescription = ref('')
const editDueDate = ref('')
const editStatus = ref('مفتوحة')
const editPriority = ref('متوسطة')

const taskStore = useTaskStore()
const toast = useToast()

const editTitleError = ref('')
const editDescriptionError = ref('')
const editDueDateError = ref('')

// خيارات الحقول
const statusOptions = ['مفتوحة', 'قيد التنفيذ', 'مكتملة', 'تم الإلغاء']
const priorityOptions = ['منخفضة', 'متوسطة', 'عالية']

// تحويل القيم من النص العربي إلى قيمة مناسبة للـ backend
const statusValueMap = {
  'مفتوحة': 'مفتوحة',
  'قيد التنفيذ': 'قيد التنفيذ',
  'مكتملة': 'مكتملة',
  'تم الإلغاء': 'تم الإلغاء',
}

const statusTextMap = {
  open: 'مفتوحة',
  in_progress: 'قيد التنفيذ',
  completed: 'مكتملة',
  cancelled: 'تم الإلغاء',
}

const priorityValueMap = {
  'منخفضة': 'low',
  'متوسطة': 'medium',
  'عالية': 'high',
}

const priorityLabels = {
  low: 'منخفضة',
  medium: 'متوسطة',
  high: 'عالية',
}

defineProps({
  task: Object,
})

const openEditDialog = (task) => {
  editTaskId.value = task.id
  editTitle.value = task.title
  editDescription.value = task.description
  editDueDate.value = formatDate(task.due_date)
  editStatus.value = statusTextMap[task.status] || 'مفتوحة'
  editPriority.value = priorityLabels[task.priority] || 'متوسطة'
  clearEditErrors()
  editDialog.value = true
}

const formatDate = (date) => {
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const clearEditErrors = () => {
  editTitleError.value = ''
  editDescriptionError.value = ''
  editDueDateError.value = ''
}

const validateEditFields = () => {
  let valid = true
  clearEditErrors()

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

const statusToValue = (statusText) => statusValueMap[statusText] || 'open'
const priorityToValue = (priorityText) => priorityValueMap[priorityText] || 'medium'

const submitEditTask = async () => {
  if (!validateEditFields()) {
    toast.warning('⚠️ الرجاء تعبئة جميع الحقول المطلوبة')
    return
  }

  try {
    await taskStore.updateTask(editTaskId.value, {
      title: editTitle.value,
      description: editDescription.value,
      due_date: editDueDate.value,
      status: statusToValue(editStatus.value),
      priority: priorityToValue(editPriority.value),
    })

    if (taskStore.error) {
      toast.error(`❌ حدث خطأ: ${taskStore.error}`)
    } else {
      toast.success('✅ تم تعديل المهمة بنجاح')
      editDialog.value = false
    }
  } catch (error) {
    toast.error('⚠️ فشل في الاتصال بالخادم')
    console.error(error)
  }
}
</script>
<style scoped>
.v-dialog > .v-overlay__content {
  transition: all 0.35s ease-in-out;
}

.v-card-title {
  font-family: 'Cairo', sans-serif;
  font-weight: 700;
}

.v-card-text {
  font-family: 'Cairo', sans-serif;
}

.v-text-field input,
.v-textarea textarea {
  background-color: #f9f9f9;
}
</style>
