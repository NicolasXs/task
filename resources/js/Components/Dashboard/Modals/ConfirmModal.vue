<template>
    <div
        v-show="confirmModalOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">Confirm Action</h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <p>{{ confirmMessage }}</p>
            </div>
            <div class="flex justify-end border-t p-4">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 border rounded-lg mr-2 hover:bg-gray-50 transition"
                >
                    Cancel
                </button>
                <button
                    @click="$emit('confirm')"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    Confirm
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    confirmModalOpen: Boolean,
    confirmMessage: String,
});
</script>
