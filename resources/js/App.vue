<template>
  <div class="min-h-screen bg-gray-50">
    <Menubar :model="menuItems" class="mb-4 shadow-sm">
      <template #end>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-600">{{ t('locale.label') }}:</span>
          <Dropdown
            v-model="selectedLocale"
            :options="localeOptions"
            optionLabel="label"
            optionValue="value"
            class="w-36"
          />
        </div>
      </template>
    </Menubar>
    <router-view />
    <Toast />
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import Menubar from 'primevue/menubar';
import Dropdown from 'primevue/dropdown';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

const router = useRouter();
const { t, locale } = useI18n();

const menuItems = ref([
  {
    label: t('app.dashboard'),
    icon: 'pi pi-chart-bar',
    command: () => router.push('/'),
  },
  {
    label: t('app.expenses'),
    icon: 'pi pi-list',
    command: () => router.push('/expenses'),
  },
]);

const localeOptions = [
  { label: t('locale.english'), value: 'en' },
  { label: t('locale.norwegian'), value: 'no' },
];

const selectedLocale = computed({
  get: () => locale.value,
  set: (value) => {
    locale.value = value;
    localStorage.setItem('locale', value);
  },
});
</script>

<style scoped>
</style>


