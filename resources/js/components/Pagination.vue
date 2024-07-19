<template>
    <div class="flex justify-center mt-4">
        <button
            v-for="page in pages"
            :key="page"
            @click="$emit('page-changed', page)"
            :class="[
        'mx-1 px-3 py-2 rounded',
        page === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200'
      ]"
        >
            {{ page }}
        </button>
    </div>
</template>

<script>
export default {
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        lastPage: {
            type: Number,
            required: true
        }
    },
    emits: ['page-changed'],
    computed: {
        pages() {
            const range = [];
            for (let i = 1; i <= this.lastPage; i++) {
                range.push(i);
            }
            return range;
        }
    }
}
</script>
