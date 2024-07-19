<template>
    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <input
                v-model="searchQuery.q"
                @input="debounceSearch"
                type="text"
                placeholder="Search by serial number or description..."
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
        <div>
            <select
                v-model="searchQuery.equipment_type_id"
                @change="debounceSearch"
                class="w-full px-4 py-2 border rounded-lg"
            >
                <option value="">All Equipment Types</option>
                <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                </option>
            </select>
        </div>
        <div>
            <input
                v-model="searchQuery.serial_number"
                @input="debounceSearch"
                type="text"
                placeholder="Serial Number"
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
        <div>
            <input
                v-model="searchQuery.desc"
                @input="debounceSearch"
                type="text"
                placeholder="Description"
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
    </div>
</template>

<script>
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

export default {
    props: {
        equipmentTypes: {
            type: Array,
            required: true
        }
    },
    emits: ['search'],
    setup(props, {emit}) {
        const searchQuery = ref({
            q: '',
            equipment_type_id: '',
            serial_number: '',
            desc: ''
        });

        const debounceSearch = debounce(() => {
            emit('search', searchQuery.value);
        }, 300);

        watch(searchQuery, debounceSearch, {deep: true});

        return {
            searchQuery,
            debounceSearch
        };
    }
}
</script>
