<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold mb-2">Expenses</h1>
        <p class="text-gray-600">Manage your expense transactions</p>
      </div>
      <Button label="Add Expense" icon="pi pi-plus" @click="showAddDialog = true" />
    </div>

    <!-- Filters -->
    <Card class="mb-6">
      <template #content>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Start Date</label>
            <Calendar v-model="filters.startDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">End Date</label>
            <Calendar v-model="filters.endDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Category</label>
            <InputText v-model="filters.category" placeholder="Filter by category" />
          </div>
          <div class="flex items-end">
            <Button label="Filter" @click="loadExpenses" icon="pi pi-filter" class="w-full" />
            <Button label="Clear" @click="clearFilters" icon="pi pi-times" severity="secondary" class="ml-2" />
          </div>
        </div>
      </template>
    </Card>

    <!-- Expenses Table -->
    <Card>
      <template #content>
        <DataTable :value="expenses" :paginator="true" :rows="10" :loading="loading" class="p-datatable-sm">
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
          <Column header="Actions">
            <template #body="slotProps">
              <Button icon="pi pi-pencil" @click="editExpense(slotProps.data)" severity="info" class="mr-2" />
              <Button icon="pi pi-trash" @click="confirmDelete(slotProps.data)" severity="danger" />
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Add/Edit Dialog -->
    <Dialog v-model:visible="showAddDialog" :header="editingExpense ? 'Edit Expense' : 'Add Expense'" :modal="true" :style="{ width: '500px' }">
      <ExpenseForm :expense="editingExpense" @saved="handleSaved" @cancel="showAddDialog = false" />
    </Dialog>

    <!-- Delete Confirmation -->
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import ExpenseForm from './ExpenseForm.vue';

const confirmService = useConfirm();
const toast = useToast();

const expenses = ref([]);
const loading = ref(false);
const showAddDialog = ref(false);
const editingExpense = ref(null);

const filters = ref({
  startDate: null,
  endDate: null,
  category: '',
});

const loadExpenses = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filters.value.startDate) {
      params.start_date = formatDateForApi(filters.value.startDate);
    }
    if (filters.value.endDate) {
      params.end_date = formatDateForApi(filters.value.endDate);
    }
    if (filters.value.category) {
      params.category = filters.value.category;
    }

    const response = await axios.get('/api/expenses', { params });
    expenses.value = response.data;
  } catch (error) {
    console.error('Error loading expenses:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load expenses', life: 3000 });
  } finally {
    loading.value = false;
  }
};

const clearFilters = () => {
  filters.value = {
    startDate: null,
    endDate: null,
    category: '',
  };
  loadExpenses();
};

const editExpense = (expense) => {
  editingExpense.value = { ...expense };
  showAddDialog.value = true;
};

const confirmDelete = (expense) => {
  confirmService.require({
    message: `Are you sure you want to delete "${expense.name}"?`,
    header: 'Delete Confirmation',
    icon: 'pi pi-exclamation-triangle',
    accept: () => {
      deleteExpense(expense.id);
    },
  });
};

const deleteExpense = async (id) => {
  try {
    await axios.delete(`/api/expenses/${id}`);
    toast.add({ severity: 'success', summary: 'Success', detail: 'Expense deleted successfully', life: 3000 });
    loadExpenses();
  } catch (error) {
    console.error('Error deleting expense:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete expense', life: 3000 });
  }
};

const handleSaved = () => {
  showAddDialog.value = false;
  editingExpense.value = null;
  loadExpenses();
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
  loadExpenses();
});
</script>

<style scoped>
</style>

