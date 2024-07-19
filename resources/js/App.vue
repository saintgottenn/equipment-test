<template>
    <div>
        <nav class="bg-gray-800 p-4">
            <div class="container mx-auto flex justify-between">
                <router-link to="/" class="text-white font-bold">Equipment List</router-link>
                <router-link to="/add" class="text-white">Add Equipment</router-link>
            </div>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App'
}
</script>
