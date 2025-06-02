<template>
  <v-container class="py-4">
    <!-- نافذة المودال -->
    <v-dialog v-model="internalDialog" max-width="600">
      <v-card>
        <v-card-title class="text-h6 text-center">➕ إضافة مهمة جديدة</v-card-title>
        <v-divider class="my-3"></v-divider>

        <v-card-text>
          <v-form @submit.prevent="submitTask" ref="formRef">
            <v-text-field
              v-model="title"
              label="العنوان"
              :error-messages="titleError"
              required
              class="mb-3"
            />

            <v-textarea
              v-model="description"
              label="الوصف"
              :error-messages="descriptionError"
              required
              rows="3"
              class="mb-3"
            />

            <v-text-field
              v-model="due_date"
              label="تاريخ الاستحقاق"
              type="date"
              :error-messages="dueDateError"
              required
              class="mb-4"
            />

            <!-- زر الإرسال -->
            <v-btn type="submit" color="success" block>
              حفظ المهمة
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import { useToast } from 'vue-toastification'

// props and emits for dialog control
const props = defineProps({ dialog: Boolean })
const emit = defineEmits(['update:dialog'])

// sync local state with parent
const internalDialog = ref(props.dialog)
watch(() => props.dialog, val => internalDialog.value = val)
watch(internalDialog, val => emit('update:dialog', val))

// form fields
const title = ref('')
const description = ref('')
const due_date = ref('')
const status = ref('open')
const priority = ref('medium')

// error fields
const titleError = ref('')
const descriptionError = ref('')
const dueDateError = ref('')
const formRef = ref(null)

const toast = useToast()
const taskStore = useTaskStore()

const validateFields = () => {
  let valid = true
  titleError.value = ''
  descriptionError.value = ''
  dueDateError.value = ''

  if (!title.value.trim()) {
    titleError.value = 'حقل العنوان مطلوب'
    valid = false
  }

  if (!description.value.trim()) {
    descriptionError.value = 'حقل الوصف مطلوب'
    valid = false
  }

  if (!due_date.value) {
    dueDateError.value = 'تاريخ الاستحقاق مطلوب'
    valid = false
  }

  return valid
}

const submitTask = async () => {
  if (!validateFields()) {
    toast.warning('⚠️ الرجاء تعبئة جميع الحقول المطلوبة')
    return
  }

  try {
    await taskStore.addTask({
      title: title.value,
      description: description.value,
      status: status.value,
      priority: priority.value,
      due_date: due_date.value
    })

    if (taskStore.error) {
      toast.error(`❌ حدث خطأ: ${taskStore.error}`)
    } else {
      toast.success('✅ تم إضافة المهمة بنجاح')
      // Reset fields
      title.value = ''
      description.value = ''
      due_date.value = ''
      internalDialog.value = false
    }
  } catch (err) {
    console.error(err)
    toast.error('⚠️ فشل في الاتصال بالخادم')
  }
}
</script>
