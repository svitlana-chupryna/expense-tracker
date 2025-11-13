import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Dashboard.vue';
import ExpenseList from './components/ExpenseList.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: ExpenseList,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;



