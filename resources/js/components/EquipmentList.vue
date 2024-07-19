<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Equipment List</h1>
        <search-form
            :equipment-types="equipmentTypes"
            @search="performSearch"
        />
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white">
                <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Serial Number
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Equipment Type
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="equipment in equipments" :key="equipment.id">
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <span v-if="editingId !== equipment.id">{{ equipment.serial_number }}</span>
                        <div v-else>
                            <input v-model="editingEquipment.serial_number" class="border rounded px-2 py-1">
                            <p v-if="editingErrors.serial_number" class="text-red-500 text-xs mt-1">{{ editingErrors.serial_number[0] }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <span v-if="editingId !== equipment.id">{{ equipment.equipment_type.name }}</span>
                        <select v-else v-model="editingEquipment.equipment_type_id" class="border rounded px-2 py-1">
                            <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <span v-if="editingId !== equipment.id">{{ equipment.desc }}</span>
                        <div v-else>
                            <textarea v-model="editingEquipment.desc" class="border rounded px-2 py-1"></textarea>
                            <p v-if="editingErrors.desc" class="text-red-500 text-xs mt-1">{{ editingErrors.desc[0] }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <button v-if="editingId !== equipment.id" @click="startEditing(equipment)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mr-2">
                            Edit
                        </button>
                        <template v-else>
                            <button @click="saveEditing(equipment.id)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded mr-2">
                                Save
                            </button>
                            <button @click="cancelEditing" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded mr-2">
                                Cancel
                            </button>
                        </template>
                        <button @click="deleteEquipment(equipment.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                            Delete
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <pagination
            :current-page="currentPage"
            :last-page="lastPage"
            @page-changed="changePage"
        />
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SearchForm from './SearchForm.vue';
import Pagination from './Pagination.vue';

export default {
    components: {
        SearchForm,
        Pagination
    },
    setup() {
        const equipments = ref([]);
        const equipmentTypes = ref([]);
        const currentPage = ref(1);
        const lastPage = ref(1);
        const perPage = ref(15);
        const searchQuery = ref({});
        const editingId = ref(null);
        const editingEquipment = ref({});
        const editingErrors = ref({});

        const fetchEquipments = async () => {
            try {
                const response = await axios.get('/api/equipment', {
                    params: {
                        ...searchQuery.value,
                        page: currentPage.value,
                        per_page: perPage.value,
                    }
                });
                equipments.value = response.data.data.equipments;
                currentPage.value = response.data.data.meta.current_page;
                lastPage.value = response.data.data.meta.total_pages;
            } catch (error) {
                console.error('Error fetching equipments:', error);
            }
        };

        const fetchEquipmentTypes = async () => {
            try {
                const response = await axios.get('/api/equipment-type');
                equipmentTypes.value = response.data.data.equipment_types;
            } catch (error) {
                console.error('Error fetching equipment types:', error);
            }
        };

        const performSearch = (query) => {
            searchQuery.value = query;
            currentPage.value = 1;
            fetchEquipments();
        };

        const changePage = (page) => {
            currentPage.value = page;
            fetchEquipments();
        };

        const startEditing = (equipment) => {
            editingId.value = equipment.id;

            editingEquipment.value = {
                ...equipment,
                equipment_type_id: equipment.equipment_type.id
            };
            editingErrors.value = {};
        };

        const saveEditing = async (id) => {
            try {
                const dataToSend = {
                    equipment_type_id: editingEquipment.value.equipment_type_id,
                    serial_number: editingEquipment.value.serial_number,
                };

                if (editingEquipment.value.desc && editingEquipment.value.desc.trim() !== '') {
                    dataToSend.desc = editingEquipment.value.desc.trim();
                }

                const response = await axios.put(`/api/equipment/${id}`, dataToSend);
                if (response.status === 200) {
                    const index = equipments.value.findIndex(e => e.id === id);
                    if (index !== -1) {
                        equipments.value[index] = response.data.data;
                    }
                    editingId.value = null;
                    editingErrors.value = {};
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    editingErrors.value = error.response.data.errors;
                } else {
                    console.error('Error updating equipment:', error);
                    alert('An error occurred while updating the equipment.');
                }
            }
        };

        const cancelEditing = () => {
            editingId.value = null;
            editingErrors.value = {};
        };

        const deleteEquipment = async (id) => {
            if (confirm('Are you sure you want to delete this equipment?')) {
                try {
                    await axios.delete(`/api/equipment/${id}`);
                    fetchEquipments();
                } catch (error) {
                    console.error('Error deleting equipment:', error);
                }
            }
        };

        onMounted(() => {
            fetchEquipments();
            fetchEquipmentTypes();
        });

        return {
            equipments,
            equipmentTypes,
            currentPage,
            lastPage,
            performSearch,
            changePage,
            editingId,
            editingEquipment,
            editingErrors,
            startEditing,
            saveEditing,
            cancelEditing,
            deleteEquipment
        };
    }
}
</script>
