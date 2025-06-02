<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-5">
    <v-row class="align-center mb-4">
      <v-col>
        <h1 class="text-h5 font-weight-bold">📊 التقارير</h1>
        <p class="text-body-2 text-grey">عرض وتحليل البيانات المتعلقة بالمهام والمستخدمين</p>
      </v-col>
    </v-row>

    <v-row class="mb-4">
      <v-col cols="12" sm="4">
        <v-select :items="reportTypes" v-model="selectedReport" label="نوع التقرير" />
      </v-col>
      <v-col cols="12" sm="4">
        <v-text-field v-model="dateFrom" label="من تاريخ" type="date" />
      </v-col>
      <v-col cols="12" sm="4">
        <v-text-field v-model="dateTo" label="إلى تاريخ" type="date" />
      </v-col>
    </v-row>

    <v-btn color="primary" @click="generateReport">
      <v-icon left>mdi-file-chart</v-icon> توليد التقرير
    </v-btn>

    <v-divider class="my-6" />

    <v-row v-if="reportData">
      <v-col cols="12">
        <h2 class="text-h6 font-weight-bold mb-3">نتائج التقرير</h2>
        <div v-if="selectedReport === 'عدد المهام حسب الحالة'">
          <BarChart :chart-data="barChartData" :chart-options="barChartOptions" />
        </div>
        <div v-else-if="selectedReport === 'عدد المستخدمين حسب الدور'">
          <PieChart :chart-data="pieChartData" :chart-options="pieChartOptions" />
        </div>
        <div v-else>
          <p>⚠️ نوع التقرير غير مدعوم حاليًا.</p>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { BarChart, PieChart } from 'vue-chart-3'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarController,     // ✅ إضافة
  PieController      // ✅ إضافة
} from 'chart.js'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

// ✅ تسجيل كل ما يلزم لتفادي الخطأ
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarController,
  PieController
)

const reportTypes = ['عدد المهام حسب الحالة', 'عدد المستخدمين حسب الدور']
const selectedReport = ref('عدد المهام حسب الحالة')
const dateFrom = ref('')
const dateTo = ref('')

const reportData = ref(null)
const barChartData = ref(null)
const pieChartData = ref(null)
const sidebarRef = ref(null)

const barChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' },
    title: { display: true, text: 'عدد المهام حسب الحالة' }
  }
}

const pieChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'right' },
    title: { display: true, text: 'عدد المستخدمين حسب الدور' }
  }
}

const generateReport = () => {
  if (selectedReport.value === 'عدد المهام حسب الحالة') {
    const data = [
      { status: 'مفتوحة', count: 15 },
      { status: 'قيد التنفيذ', count: 8 },
      { status: 'مكتملة', count: 22 },
      { status: 'تم الإلغاء', count: 2 }
    ]

    reportData.value = data
    barChartData.value = {
      labels: data.map(d => d.status),
      datasets: [
        {
          label: 'عدد المهام',
          data: data.map(d => d.count),
          backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350']
        }
      ]
    }
  } else if (selectedReport.value === 'عدد المستخدمين حسب الدور') {
    const data = [
      { role: 'مدير', count: 3 },
      { role: 'موظف', count: 12 },
      { role: 'مشرف', count: 5 }
    ]
    reportData.value = data
    pieChartData.value = {
      labels: data.map(d => d.role),
      datasets: [
        {
          label: 'عدد المستخدمين',
          data: data.map(d => d.count),
          backgroundColor: ['#AB47BC', '#29B6F6', '#FF7043']
        }
      ]
    }
  } else {
    reportData.value = null
    barChartData.value = null
    pieChartData.value = null
  }
}
// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>

<style scoped>
.text-grey {
  color: #757575;
}
</style>
