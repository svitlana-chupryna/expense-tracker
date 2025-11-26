import { createI18n } from 'vue-i18n';

const messages = {
  en: {
    app: {
      title: 'Expense Tracker',
      dashboard: 'Dashboard',
      expenses: 'Expenses',
    },
    common: {
      startDate: 'Start Date',
      endDate: 'End Date',
      category: 'Category',
      date: 'Date',
      amount: 'Amount',
      actions: 'Actions',
      filter: 'Filter',
      clear: 'Clear',
      cancel: 'Cancel',
      save: 'Save',
      addExpense: 'Add Expense',
      editExpense: 'Edit Expense',
      name: 'Name',
    },
    dashboard: {
      title: 'Expense Tracker Dashboard',
      subtitle: 'Track and analyze your expenses',
      totalExpenses: 'Total Expenses',
      categories: 'Categories',
      transactions: 'Transactions',
      expensesByCategory: 'Expenses by Category',
      recentExpenses: 'Recent Expenses',
    },
    expenses: {
      title: 'Expenses',
      subtitle: 'Manage your expense transactions',
      table: {
        name: 'Name',
        category: 'Category',
        amount: 'Amount',
        date: 'Date',
        actions: 'Actions',
      },
    },
    form: {
      namePlaceholder: 'Enter expense name',
      categoryPlaceholder: 'Enter category',
    },
    notifications: {
      expenseCreated: 'Expense created successfully',
      expenseUpdated: 'Expense updated successfully',
      expenseDeleted: 'Expense deleted successfully',
      loadExpensesError: 'Failed to load expenses',
      saveExpenseError: 'Failed to save expense',
      deleteConfirmHeader: 'Delete Confirmation',
      deleteConfirmMessage: 'Are you sure you want to delete "{name}"?',
    },
    locale: {
      english: 'English',
      norwegian: 'Norwegian',
      label: 'Language',
    },
  },
  no: {
    app: {
      title: 'Utgiftsoversikt',
      dashboard: 'Oversikt',
      expenses: 'Utgifter',
    },
    common: {
      startDate: 'Startdato',
      endDate: 'Sluttdato',
      category: 'Kategori',
      date: 'Dato',
      amount: 'Beløp',
      actions: 'Handlinger',
      filter: 'Filtrer',
      clear: 'Tøm',
      cancel: 'Avbryt',
      save: 'Lagre',
      addExpense: 'Ny utgift',
      editExpense: 'Rediger utgift',
      name: 'Navn',
    },
    dashboard: {
      title: 'Utgiftsoversikt',
      subtitle: 'Følg og analyser utgiftene dine',
      totalExpenses: 'Totale utgifter',
      categories: 'Kategorier',
      transactions: 'Transaksjoner',
      expensesByCategory: 'Utgifter per kategori',
      recentExpenses: 'Siste utgifter',
    },
    expenses: {
      title: 'Utgifter',
      subtitle: 'Administrer utgiftstransaksjonene dine',
      table: {
        name: 'Navn',
        category: 'Kategori',
        amount: 'Beløp',
        date: 'Dato',
        actions: 'Handlinger',
      },
    },
    form: {
      namePlaceholder: 'Skriv inn navn på utgift',
      categoryPlaceholder: 'Skriv inn kategori',
    },
    notifications: {
      expenseCreated: 'Utgift opprettet',
      expenseUpdated: 'Utgift oppdatert',
      expenseDeleted: 'Utgift slettet',
      loadExpensesError: 'Kunne ikke laste utgifter',
      saveExpenseError: 'Kunne ikke lagre utgift',
      deleteConfirmHeader: 'Bekreft sletting',
      deleteConfirmMessage: 'Er du sikker på at du vil slette «{name}»?',
    },
    locale: {
      english: 'Engelsk',
      norwegian: 'Norsk',
      label: 'Språk',
    },
  },
};

const getInitialLocale = () => {
  if (typeof window === 'undefined') return 'en';
  const saved = localStorage.getItem('locale');
  return saved || 'en';
};

const i18n = createI18n({
  legacy: false,
  locale: getInitialLocale(),
  fallbackLocale: 'en',
  messages,
});

export default i18n;


