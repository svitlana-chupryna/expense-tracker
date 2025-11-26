<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <Menubar :model="menuItems" class="mb-4 shadow-sm dark:bg-gray-800 dark:border-gray-700">
      <template #end>
        <Button
          :icon="isDarkMode ? 'pi pi-sun' : 'pi pi-moon'"
          :label="isDarkMode ? 'Light Mode' : 'Dark Mode'"
          @click="toggleDarkMode"
          severity="secondary"
          text
          class="ml-2"
        />
      </template>
    </Menubar>
    <router-view />
    <Toast />
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import { useDarkMode } from './composables/useDarkMode';

const router = useRouter();
const { isDarkMode, toggleDarkMode } = useDarkMode();

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

// Track if this is the initial mount to avoid reload on first render
let isInitialMount = true;

const updatePrimeVueTheme = (dark) => {
  // Update data attribute for theme switching
  if (dark) {
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.setAttribute('data-theme', 'light');
  }
  
  // Only reload if this is not the initial mount (i.e., user toggled the theme)
  if (!isInitialMount) {
    // For PrimeVue theme switching, we need to reload the page
    // because CSS imports are bundled by Vite and can't be easily swapped
    // This ensures a clean theme switch
    window.location.reload();
  }
};

// Watch for dark mode changes to update PrimeVue theme
watch(isDarkMode, (dark) => {
  updatePrimeVueTheme(dark);
  // Mark that initial mount is complete
  if (isInitialMount) {
    isInitialMount = false;
  }
}, { immediate: true });
</script>

<style scoped>
</style>


