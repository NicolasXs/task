<template>
    <section class="p-6">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Statistics</h2>
            <div
                v-if="loadingStats"
                class="bg-white rounded-lg shadow p-8 text-center"
            >
                <i
                    class="fas fa-spinner fa-spin text-blue-600 text-2xl mb-2"
                ></i>
                <p class="text-gray-500">Loading statistics...</p>
            </div>
            <TaskStats v-else :stats="statistics" />
        </div>
    </section>
</template>
<script setup>
import TaskStats from "@/Components/Dashboard/TaskStats.vue";
const props = defineProps({
    statistics: Object,
    loadingStats: Boolean,
});
</script>
