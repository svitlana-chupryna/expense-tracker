<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold mb-2">{{ t('expenses.title') }}</h1>
        <p class="text-gray-600">{{ t('expenses.subtitle') }}</p>
      </div>
      <Button :label="t('common.addExpense')" icon="pi pi-plus" @click="showAddDialog = true" />
    </div>

    <!-- Filters -->
    <Card class="mb-6">
      <template #content>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">{{ t('common.startDate') }}</label>
            <Calendar v-model="filters.startDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ t('common.endDate') }}</label>
            <Calendar v-model="filters.endDate" dateFormat="yy-mm-dd" showIcon />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ t('common.category') }}</label>
            <InputText v-model="filters.category" :placeholder="t('form.categoryPlaceholder')" />
          </div>
          <div class="flex items-end">
            <Button :label="t('common.filter')" @click="loadExpenses" icon="pi pi-filter" class="w-full" />
            <Button :label="t('common.clear')" @click="clearFilters" icon="pi pi-times" severity="secondary" class="ml-2" />
          </div>
        </div>
      </template>
    </Card>

    <!-- Expenses Table -->
    <Card>
      <template #content>
        <DataTable :value="expenses" :paginator="true" :rows="10" :loading="loading" class="p-datatable-sm">
          <Column field="name" :header="t('expenses.table.name')" sortable></Column>
          <Column field="category" :header="t('expenses.table.category')" sortable></Column>
          <Column field="amount" :header="t('expenses.table.amount')" sortable>
            <template #body="slotProps">
              ${{ parseFloat(slotProps.data.amount).toFixed(2) }}
            </template>
          </Column>
          <Column field="date" :header="t('expenses.table.date')" sortable>
            <template #body="slotProps">
              {{ formatDate(slotProps.data.date) }}
            </template>
          </Column>
          <Column :header="t('expenses.table.actions')">
            <template #body="slotProps">
              <Button icon="pi pi-pencil" @click="editExpense(slotProps.data)" severity="info" class="mr-2" />
              <Button icon="pi pi-trash" @click="confirmDelete(slotProps.data)" severity="danger" />
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Add/Edit Dialog -->
    <Dialog
      v-model:visible="showAddDialog"
      :header="editingExpense ? t('common.editExpense') : t('common.addExpense')"
      :modal="true"
      :style="{ width: '500px' }"
    >
      <ExpenseForm :expense="editingExpense" @saved="handleSaved" @cancel="showAddDialog = false" />
    </Dialog>

    <!-- Delete Confirmation -->
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
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

const { t } = useI18n();

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
    toast.add({ severity: 'error', summary: t('notifications.loadExpensesError'), detail: '', life: 3000 });
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
    message: t('notifications.deleteConfirmMessage', { name: expense.name }),
    header: t('notifications.deleteConfirmHeader'),
    icon: 'pi pi-exclamation-triangle',
    accept: () => {
      deleteExpense(expense.id);
    },
  });
};

const deleteExpense = async (id) => {
  try {
    await axios.delete(`/api/expenses/${id}`);
    toast.add({ severity: 'success', summary: t('notifications.expenseDeleted'), detail: '', life: 3000 });
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

