<template>
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label
                    for="search"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Search</label
                >
                <div class="relative">
                    <input
                        type="text"
                        id="search"
                        :value="searchTerm"
                        @input="$emit('update:searchTerm', $event.target.value)"
                        placeholder="Search tasks..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <i
                        class="fas fa-search absolute left-3 top-3 text-gray-400"
                    ></i>
                </div>
            </div>
            <div v-if="projects && projects.length > 0">
                <label
                    for="project"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Project</label
                >
                <select
                    id="project"
                    :value="selectedProjectId"
                    @change="
                        $emit('update:selectedProjectId', $event.target.value)
                    "
                    class="w-full py-2 px-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="all">All Projects</option>
                    <option value="null">No Project</option>
                    <option
                        v-for="project in projects"
                        :key="project.id"
                        :value="project.id"
                    >
                        {{ project.name }}
                    </option>
                </select>
            </div>
            <div>
                <label
                    for="filter"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Filter</label
                >
                <select
                    id="filter"
                    :value="filterValue"
                    @change="$emit('update:filterValue', $event.target.value)"
                    class="w-full py-2 px-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="all">All Tasks</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="low">Low Priority</option>
                </select>
            </div>
            <div>
                <label
                    for="sort"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Sort By</label
                >
                <select
                    id="sort"
                    :value="sortValue"
                    @change="$emit('update:sortValue', $event.target.value)"
                    class="w-full py-2 px-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="priority">Priority</option>
                    <option value="dueDate">Due Date</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
    searchTerm: String,
    filterValue: String,
    sortValue: String,
    projects: { type: Array, default: () => [] },
    selectedProjectId: { type: [String, Number], default: "all" },
});

defineEmits([
    "update:searchTerm",
    "update:filterValue",
    "update:sortValue",
    "update:selectedProjectId",
]);
</script>
