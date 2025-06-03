<template>
  <v-container class="py-4">
    <v-dialog
      v-model="internalDialog"
      max-width="600"
      transition="dialog-bottom-transition"
      persistent
      scrim="black"
    >
      <v-card class="rounded-xl elevation-10">
        <v-card-title class="text-h6 text-center d-flex align-center justify-center">
          <v-icon color="primary" class="me-2">mdi-plus</v-icon>
          ➕ إضافة مهمة جديدة
        </v-card-title>

        <v-divider class="my-2" />

        <v-card-text class="pt-0">
          <v-form @submit.prevent="submitTask" ref="formRef" validate-on="submit lazy">
            <v-text-field
              v-model="title"
              label="✏️ العنوان"
              :error-messages="titleError"
              variant="outlined"
              color="primary"
              rounded
              class="mb-4"
              required
            />

            <v-textarea
              v-model="description"
              label="📝 الوصف"
              :error-messages="descriptionError"
              variant="outlined"
              color="primary"
              rounded
              rows="3"
              class="mb-4"
              required
            />

            <v-text-field
              v-model="due_date"
              label="📅 تاريخ الاستحقاق"
              type="date"
              :error-messages="dueDateError"
              variant="outlined"
              color="primary"
              rounded
              class="mb-5"
              required
            />
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="error" @click="internalDialog = false">إلغاء</v-btn>
              <v-btn color="primary" type="submit" rounded elevation="2">💾 حفظ المهمة</v-btn>
            </v-card-actions>
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
<style>
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
