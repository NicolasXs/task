<template>
    <section class="p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">My Tasks</h2>
                <button
                    @click="$emit('open-task-modal')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i> Add Task
                </button>
            </div>
            <TaskFilters
                :searchTerm="searchTerm"
                :filterValue="filterValue"
                :sortValue="sortValue"
                :projects="projects"
                :selectedProjectId="selectedProjectId"
                @update:searchTerm="$emit('update:searchTerm', $event)"
                @update:filterValue="$emit('update:filterValue', $event)"
                @update:sortValue="$emit('update:sortValue', $event)"
                @update:selectedProjectId="
                    $emit('update:selectedProjectId', $event)
                "
            />
            <div
                v-if="loading"
                class="bg-white rounded-lg shadow p-8 text-center"
            >
                <i
                    class="fas fa-spinner fa-spin text-blue-600 text-2xl mb-2"
                ></i>
                <p class="text-gray-500">Loading tasks...</p>
            </div>
            <TaskList
                v-else
                :tasks="tasks"
                @toggle-completion="$emit('toggle-completion', $event)"
                @edit-task="$emit('edit-task', $event)"
                @delete-task="$emit('delete-task', $event)"
            />
            <TaskPagination
                :currentPage="currentPage"
                :totalPages="totalPages"
                :totalItems="totalTasks"
                :itemsPerPage="tasksPerPage"
                @change-page="$emit('change-page', $event)"
            />
            <div class="mt-6 flex justify-end">
                <button
                    @click="$emit('export-csv')"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center"
                >
                    <i class="fas fa-file-export mr-2"></i> Export to CSV
                </button>
            </div>
        </div>
    </section>
</template>
<script setup>
import TaskList from "@/Components/Dashboard/TaskList.vue";
import TaskFilters from "@/Components/Dashboard/TaskFilters.vue";
import TaskPagination from "@/Components/Dashboard/TaskPagination.vue";
const props = defineProps({
    tasks: Array,
    loading: Boolean,
    currentPage: Number,
    totalPages: Number,
    totalTasks: Number,
    tasksPerPage: Number,
    searchTerm: String,
    filterValue: String,
    sortValue: String,
    projects: { type: Array, default: () => [] },
    selectedProjectId: { type: [String, Number], default: "all" },
});
const emit = defineEmits([
    "update:searchTerm",
    "update:filterValue",
    "update:sortValue",
    "update:selectedProjectId",
    "open-task-modal",
    "toggle-completion",
    "edit-task",
    "delete-task",
    "change-page",
    "export-csv",
]);
</script>
