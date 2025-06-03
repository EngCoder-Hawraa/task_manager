<template>
  <v-row class="mb-6">
    <v-col cols="12" md="4">
      <v-select
        :items="statusOptions"
        label="فلترة حسب الحالة"
        v-model="selectedStatus"
        @change="emitFilters"
      />
    </v-col>
    <v-col cols="12" md="4">
      <v-select
        :items="priorityOptions"
        label="فلترة حسب الأولوية"
        v-model="selectedPriority"
        @change="emitFilters"
      />
    </v-col>
  </v-row>
</template>

<script setup>
import { ref } from 'vue'

const selectedStatus = ref(null)
const selectedPriority = ref(null)

const statusOptions = ['جميع الحالات', 'مكتملة', 'قيد التنفيذ', 'معلقة']
const priorityOptions = ['كل الأولويات', 'عالية', 'متوسطة', 'منخفضة']

const emit = defineEmits(['filter-change'])

function emitFilters() {
  emit('filter-change', {
    status: selectedStatus.value,
    priority: selectedPriority.value,
  })
}
</script>
