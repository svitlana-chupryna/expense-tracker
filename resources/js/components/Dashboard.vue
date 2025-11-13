<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-3xl font-bold mb-2">Expense Tracker Dashboard</h1>
      <p class="text-gray-600">Track and analyze your expenses</p>
    </div>

    <!-- Date Range Filter -->
    <Card class="mb-6">
      <template #content>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Start Date</label>
            <Calendar v-model="filters.startDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">End Date</label>
            <Calendar v-model="filters.endDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div class="flex items-end">
            <Button label="Apply Filters" @click="loadDashboard" icon="pi pi-filter" class="w-full" />
          </div>
        </div>
      </template>
    </Card>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <Card>
        <template #content>
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">${{ totalExpenses.toFixed(2) }}</div>
            <div class="text-gray-600 mt-2">Total Expenses</div>
          </div>
        </template>
      </Card>
      <Card>
        <template #content>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ expensesByCategory.length }}</div>
            <div class="text-gray-600 mt-2">Categories</div>
          </div>
        </template>
      </Card>
      <Card>
        <template #content>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ expenses.length }}</div>
            <div class="text-gray-600 mt-2">Transactions</div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Chart -->
    <Card class="mb-6">
      <template #title>Expenses by Category</template>
      <template #content>
        <Chart type="pie" :data="chartData" :options="chartOptions" />
      </template>
    </Card>

    <!-- Recent Expenses -->
    <Card>
      <template #title>Recent Expenses</template>
      <template #content>
        <DataTable :value="recentExpenses" :paginator="true" :rows="10" class="p-datatable-sm">
          <Column field="name" header="Name" sortable></Column>
          <Column field="category" header="Category" sortable></Column>
          <Column field="amount" header="Amount" sortable>
            <template #body="slotProps">
              ${{ parseFloat(slotProps.data.amount).toFixed(2) }}
            </template>
          </Column>
          <Column field="date" header="Date" sortable>
            <template #body="slotProps">
              {{ formatDate(slotProps.data.date) }}
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Card from 'primevue/card';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import Chart from 'primevue/chart';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const filters = ref({
  startDate: null,
  endDate: null,
});

const expenses = ref([]);
const expensesByCategory = ref([]);
const totalExpenses = ref(0);
const recentExpenses = ref([]);

const chartData = computed(() => {
  return {
    labels: expensesByCategory.value.map(item => item.category),
    datasets: [
      {
        data: expensesByCategory.value.map(item => parseFloat(item.total)),
        backgroundColor: [
          '#42A5F5',
          '#66BB6A',
          '#FFA726',
          '#EF5350',
          '#AB47BC',
          '#26C6DA',
          '#FFCA28',
          '#5C6BC0',
        ],
      },
    ],
  };
});

const chartOptions = {
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
};

const loadDashboard = async () => {
  try {
    const params = {};
    if (filters.value.startDate) {
      params.start_date = formatDateForApi(filters.value.startDate);
    }
    if (filters.value.endDate) {
      params.end_date = formatDateForApi(filters.value.endDate);
    }

    // Load dashboard stats
    const statsResponse = await axios.get('/api/expenses/dashboard/stats', { params });
    expensesByCategory.value = statsResponse.data.byCategory || [];
    totalExpenses.value = parseFloat(statsResponse.data.total || 0);

    // Load recent expenses
    const expensesResponse = await axios.get('/api/expenses', { params });
    expenses.value = expensesResponse.data;
    recentExpenses.value = expensesResponse.data.slice(0, 10);
  } catch (error) {
    console.error('Error loading dashboard:', error);
  }
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatDateForApi = (date) => {
  if (!date) return null;
  const d = new Date(date);
  return d.toISOString().split('T')[0];
};

onMounted(() => {
  loadDashboard();
});
</script>

<style scoped>
</style>



