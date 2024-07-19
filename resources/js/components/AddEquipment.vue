<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add New Equipment</h1>
        <div v-for="(form, index) in forms" :key="index" class="mb-8 p-4 border rounded">
            <Form @submit="submitForm(index)" :validation-schema="schema" v-slot="{ errors }">
                <div class="space-y-4">
                    <div>
                        <label :for="'equipment_type_id_' + index" class="block mb-2">Equipment Type</label>
                        <Field :name="'equipment_type_id_' + index" as="select" v-model="form.equipment_type_id" class="w-full px-4 py-2 border rounded-lg" :class="{ 'border-red-500': errors['equipment_type_id_' + index] }">
                            <option value="">Select Equipment Type</option>
                            <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </Field>
                        <ErrorMessage :name="'equipment_type_id_' + index" class="text-red-500 text-sm mt-1" />
                    </div>

                    <div>
                        <label :for="'serial_number_' + index" class="block mb-2">Serial Numbers (one per line)</label>
                        <Field :name="'serial_number_' + index" as="textarea" v-model="form.serial_number" rows="5" class="w-full px-4 py-2 border rounded-lg" :class="{ 'border-red-500': errors['serial_number_' + index] }" />
                        <ErrorMessage :name="'serial_number_' + index" class="text-red-500 text-sm mt-1" />
                    </div>

                    <div>
                        <label :for="'desc_' + index" class="block mb-2">Description</label>
                        <Field :name="'desc_' + index" as="textarea" v-model="form.desc" rows="3" class="w-full px-4 py-2 border rounded-lg" :class="{ 'border-red-500': errors['desc_' + index] }" />
                        <ErrorMessage :name="'desc_' + index" class="text-red-500 text-sm mt-1" />
                    </div>
                </div>

                <div v-if="formErrors[index]" class="mt-4 p-2 bg-red-100 border border-red-400 text-red-700 rounded">
                    <p v-for="error in formErrors[index]" :key="error" class="text-sm">{{ error }}</p>
                </div>


            </Form>
        </div>

        <div class="mt-4 space-x-4">
            <button @click="addForm" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Add Another Form
            </button>
            <button @click="submitAllForms" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit All Forms
            </button>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';

export default {
    components: {
        Form,
        Field,
        ErrorMessage
    },
    setup() {
        const equipmentTypes = ref([]);
        const forms = ref([{ equipment_type_id: '', serial_number: '', desc: '' }]);
        const formErrors = ref([]);

        const schema = yup.object().shape({
            equipment_type_id: yup.string().required('Equipment type is required'),
            serial_number: yup.string().required('At least one serial number is required'),
            desc: yup.string().required('Description is required').max(255, 'Description must be at most 255 characters'),
        });

        const fetchEquipmentTypes = async () => {
            try {
                const response = await axios.get('/api/equipment-type');
                equipmentTypes.value = response.data.data.equipment_types;
            } catch (error) {
                console.error('Error fetching equipment types:', error);
            }
        };

        const addForm = () => {
            forms.value.push({ equipment_type_id: '', serial_number: '', desc: '' });
        };

        const submitAllForms = async () => {
            try {
                const formsData = forms.value.map(form => ({
                    equipment_type_id: form.equipment_type_id,
                    serial_number: form.serial_number,
                    desc: form.desc,
                }));

                const response = await axios.post('/api/equipment', formsData);

                const { data } = response.data;
                const errors = data.errors || {};
                const success = data.success || [];

                if (success.length > 0) {
                    alert(`Successfully added ${success.length} equipment(s)`);
                }

                formErrors.value = Object.keys(errors).reduce((acc, key) => {
                    acc[key] = errors[key];
                    return acc;
                }, {});

                const successIndices = success.map(item => forms.value.findIndex(form => form.serial_number.includes(item.serial_number)));
                forms.value = forms.value.filter((_, index) => !successIndices.includes(index));

                if (forms.value.length === 0) {
                    forms.value.push({ equipment_type_id: '', serial_number: '', desc: '' });
                    formErrors.value = {};
                }

            } catch (error) {
                console.error('Error submitting forms:', error);
                alert('An error occurred while connecting to the server. Please try again.');
            }
        };

        onMounted(fetchEquipmentTypes);

        return {
            equipmentTypes,
            schema,
            forms,
            formErrors,
            addForm,
            submitAllForms
        };
    }
}
</script>
