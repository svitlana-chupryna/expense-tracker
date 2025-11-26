import { ref } from 'vue';

// Initialize from localStorage or system preference
const getInitialDarkMode = () => {
  if (typeof window === 'undefined') return false;
  const saved = localStorage.getItem('darkMode');
  if (saved !== null) {
    return saved === 'true';
  }
  return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const isDarkMode = ref(getInitialDarkMode());

export function useDarkMode() {
  const applyDarkMode = (dark) => {
    if (typeof window === 'undefined') return;
    if (dark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  // Apply initial state
  applyDarkMode(isDarkMode.value);

  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    localStorage.setItem('darkMode', isDarkMode.value.toString());
    applyDarkMode(isDarkMode.value);
  };

  const setDarkMode = (dark) => {
    isDarkMode.value = dark;
    localStorage.setItem('darkMode', dark.toString());
    applyDarkMode(dark);
  };

  // Watch for system preference changes
  if (typeof window !== 'undefined') {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
      // Only apply if user hasn't set a preference
      if (localStorage.getItem('darkMode') === null) {
        isDarkMode.value = e.matches;
        applyDarkMode(e.matches);
      }
    });
  }

  return {
    isDarkMode,
    toggleDarkMode,
    setDarkMode,
  };
}

