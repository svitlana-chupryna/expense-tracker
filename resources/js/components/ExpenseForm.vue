<template>
  <div class="p-fluid">
    <div class="mb-4">
      <label for="name" class="block text-sm font-medium mb-2">Name *</label>
      <InputText id="name" v-model="form.name" placeholder="Enter expense name" :class="{ 'p-invalid': errors.name }" />
      <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
    </div>

    <div class="mb-4">
      <label for="amount" class="block text-sm font-medium mb-2">Amount *</label>
      <InputNumber id="amount" v-model="form.amount" mode="decimal" :min="0" :maxFractionDigits="2" placeholder="0.00" :class="{ 'p-invalid': errors.amount }" />
      <small v-if="errors.amount" class="p-error">{{ errors.amount[0] }}</small>
    </div>

    <div class="mb-4">
      <label for="category" class="block text-sm font-medium mb-2">Category *</label>
      <AutoComplete
        id="category"
        v-model="form.category"
        :suggestions="categorySuggestions"
        @complete="searchCategories"
        placeholder="Enter category"
        :class="{ 'p-invalid': errors.category }"
      />
      <small v-if="errors.category" class="p-error">{{ errors.category[0] }}</small>
    </div>

    <div class="mb-4">
      <label for="date" class="block text-sm font-medium mb-2">Date *</label>
      <Calendar id="date" v-model="form.date" dateFormat="yy-mm-dd" showIcon :class="{ 'p-invalid': errors.date }" />
      <small v-if="errors.date" class="p-error">{{ errors.date[0] }}</small>
    </div>

    <div class="flex justify-end gap-2 mt-4">
      <Button label="Cancel" @click="$emit('cancel')" severity="secondary" />
      <Button label="Save" @click="save" :loading="saving" />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import AutoComplete from 'primevue/autocomplete';
import Button from 'primevue/button';

const props = defineProps({
  expense: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();

const form = reactive({
  name: '',
  amount: null,
  category: '',
  date: null,
});

const errors = ref({});
const saving = ref(false);
const categorySuggestions = ref([]);

const commonCategories = [
  'Food',
  'Transportation',
  'Shopping',
  'Bills',
  'Entertainment',
  'Healthcare',
  'Education',
  'Travel',
  'Other',
];

const searchCategories = (event) => {
  const query = event.query.toLowerCase();
  categorySuggestions.value = commonCategories.filter(cat => cat.toLowerCase().includes(query));
};

const save = async () => {
  saving.value = true;
  errors.value = {};

  try {
    const data = {
      name: form.name,
      amount: form.amount,
      category: form.category,
      date: form.date ? new Date(form.date).toISOString().split('T')[0] : null,
    };

    if (props.expense && props.expense.id) {
      await axios.put(`/api/expenses/${props.expense.id}`, data);
      toast.add({ severity: 'success', summary: 'Success', detail: 'Expense updated successfully', life: 3000 });
    } else {
      await axios.post('/api/expenses', data);
      toast.add({ severity: 'success', summary: 'Success', detail: 'Expense created successfully', life: 3000 });
    }

    emit('saved');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {};
    } else {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save expense', life: 3000 });
    }
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  if (props.expense) {
    form.name = props.expense.name || '';
    form.amount = parseFloat(props.expense.amount) || null;
    form.category = props.expense.category || '';
    form.date = props.expense.date ? new Date(props.expense.date) : null;
  }
});
</script>

<style scoped>
</style>



