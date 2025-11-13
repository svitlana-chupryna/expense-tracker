<template>
  <div class="min-h-screen bg-gray-50">
    <Menubar :model="menuItems" class="mb-4 shadow-sm" />
    <router-view />
    <Toast />
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

const router = useRouter();

const menuItems = ref([
  {
    label: 'Dashboard',
    icon: 'pi pi-chart-bar',
    command: () => router.push('/'),
  },
  {
    label: 'Expenses',
    icon: 'pi pi-list',
    command: () => router.push('/expenses'),
  },
]);
</script>

<style scoped>
</style>


