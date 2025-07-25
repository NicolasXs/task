<template>
    <div
        v-if="totalPages > 1"
        class="flex justify-between items-center mt-6 bg-white p-4 border border-gray-200 rounded-lg shadow-sm"
    >
        <div class="flex items-center space-x-2">
            <button
                @click="$emit('change-page', currentPage - 1)"
                :disabled="currentPage === 1"
                :class="[
                    'px-4 py-2 border rounded-lg font-medium text-sm',
                    currentPage === 1
                        ? 'text-gray-400 cursor-not-allowed bg-gray-100 border-gray-200'
                        : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400',
                ]"
            >
                <i class="fas fa-chevron-left mr-1"></i>
            </button>

            <div class="flex items-center space-x-1">
                <template v-for="page in totalPages" :key="page">
                    <button
                        @click="$emit('change-page', page)"
                        :class="[
                            'px-3 py-2 rounded border text-sm font-medium min-w-[40px]',
                            page === currentPage
                                ? 'bg-blue-500 text-white border-blue-500 hover:bg-blue-600'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 hover:border-gray-400',
                        ]"
                        :aria-current="page === currentPage ? 'page' : null"
                    >
                        {{ page }}
                    </button>
                </template>
            </div>

            <button
                @click="$emit('change-page', currentPage + 1)"
                :disabled="currentPage === totalPages"
                :class="[
                    'px-4 py-2 border rounded-lg font-medium text-sm',
                    currentPage === totalPages
                        ? 'text-gray-400 cursor-not-allowed bg-gray-100 border-gray-200'
                        : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400',
                ]"
            >
                <i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>

        <div class="text-sm text-gray-500">
            Showing {{ (currentPage - 1) * itemsPerPage + 1 }}-{{
                Math.min(currentPage * itemsPerPage, totalItems)
            }}
            of {{ totalItems }} tasks
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
    currentPage: Number,
    totalPages: Number,
    totalItems: Number,
    itemsPerPage: Number,
});

defineEmits(["change-page"]);
</script>
