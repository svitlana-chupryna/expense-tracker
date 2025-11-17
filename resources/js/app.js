import './bootstrap';
import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import App from './App.vue';
import router from './router';

// PrimeVue base styles
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';

// Load theme based on saved preference or system preference
const getInitialTheme = () => {
  const saved = localStorage.getItem('darkMode');
  if (saved !== null) {
    return saved === 'true';
  }
  return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const isDark = getInitialTheme();

// Import the appropriate theme
if (isDark) {
  import('primevue/resources/themes/arya-blue/theme.css');
} else {
  import('primevue/resources/themes/saga-blue/theme.css');
}

// Initialize dark mode on page load
const initializeDarkMode = () => {
  if (isDark) {
    document.documentElement.classList.add('dark');
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.setAttribute('data-theme', 'light');
  }
};

initializeDarkMode();

const app = createApp(App);
app.use(PrimeVue);
app.use(ToastService);
app.use(ConfirmationService);
app.use(router);
app.mount('#app');
