<template>
  <v-container class="py-4">
    <v-btn
      color="info"
      size="small"
      variant="outlined"
      @click="openEditDialog(task)"
    >
      ✏ تعديل
    </v-btn>

    <v-dialog v-model="editDialog" max-width="600">
      <v-card>
        <v-card-title class="text-h6 text-center">✏ تعديل المهمة</v-card-title>
        <v-divider class="my-3" />
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
              required
              class="mb-4"
            />
            <v-select
              v-model="editPriority"
              :items="priorityOptions"
              label="الأولوية"
              required
              class="mb-4"
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
