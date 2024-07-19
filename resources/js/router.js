import { createRouter, createWebHistory } from 'vue-router';
import EquipmentList from './components/EquipmentList.vue';
import AddEquipment from './components/AddEquipment.vue';

const routes = [
    { path: '/', component: EquipmentList },
    { path: '/add', component: AddEquipment }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
