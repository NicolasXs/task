<template>
    <div
        v-show="isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">
                    {{ task.id ? "Edit Task" : "Add New Task" }}
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <form @submit.prevent="handleSubmit">
                    <div class="mb-4">
                        <label
                            for="taskTitle"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Title*</label
                        >
                        <input
                            type="text"
                            id="taskTitle"
                            v-model="form.title"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        />
                        <div
                            v-if="errors.title"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.title[0] }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            for="taskDescription"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Description</label
                        >
                        <textarea
                            id="taskDescription"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                        <div
                            v-if="errors.description"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.description[0] }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label
                                for="taskPriority"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Priority*</label
                            >
                            <select
                                id="taskPriority"
                                v-model="form.priority"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            <div
                                v-if="errors.priority"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.priority[0] }}
                            </div>
                        </div>
                        <div>
                            <label
                                for="taskDueDate"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Due Date</label
                            >
                            <input
                                type="date"
                                id="taskDueDate"
                                v-model="form.due_date"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <div
                                v-if="errors.due_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.due_date[0] }}
                            </div>
                        </div>
                    </div>
                    <!-- Project Selection -->
                    <div class="mb-4">
                        <label
                            for="taskProject"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Project</label
                        >
                        <select
                            id="taskProject"
                            v-model="form.project_id"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">No Project</option>
                            <option
                                v-for="project in projects"
                                :key="project.id"
                                :value="project.id"
                            >
                                {{ project.name }}
                            </option>
                        </select>
                        <div
                            v-if="errors.project_id"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.project_id[0] }}
                        </div>
                    </div>
                    <!-- Assign To User (only for admins) -->
                    <div v-if="isAdmin" class="mb-4">
                        <label
                            for="assignedTo"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Assign To</label
                        >
                        <select
                            id="assignedTo"
                            v-model="form.assigned_to"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Select User (Optional)</option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <div
                            v-if="errors.assigned_to"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.assigned_to[0] }}
                        </div>
                    </div>
                    <div class="flex items-center mb-4">
                        <input
                            type="checkbox"
                            id="taskCompleted"
                            v-model="form.completed"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label
                            for="taskCompleted"
                            class="ml-2 block text-sm text-gray-700"
                            >Completed</label
                        >
                    </div>
                </form>
            </div>
            <div class="flex justify-end border-t p-4">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 border rounded-lg mr-2 hover:bg-gray-50 transition"
                    :disabled="loading"
                >
                    Cancel
                </button>
                <button
                    @click="handleSubmit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    :disabled="loading"
                >
                    <span v-if="loading">Saving...</span>
                    <span v-else>Save Task</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
    isOpen: Boolean,
    task: {
        type: Object,
        default: () => ({}),
    },
    users: {
        type: Array,
        default: () => [],
    },
    projects: {
        type: Array,
        default: () => [],
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "save"]);

const form = ref({
    title: "",
    description: "",
    priority: "medium",
    due_date: "",
    completed: false,
    assigned_to: "",
    project_id: "",
});

const errors = ref({});
const loading = ref(false);

watch(
    () => props.task,
    (newTask) => {
        if (newTask && Object.keys(newTask).length > 0) {
            form.value = {
                title: newTask.title || "",
                description: newTask.description || "",
                priority: newTask.priority || "medium",
                due_date: newTask.due_date || "",
                completed: newTask.completed || false,
                assigned_to: newTask.assigned_to || "",
                project_id: newTask.project_id || "",
            };
        } else {
            form.value = {
                title: "",
                description: "",
                priority: "medium",
                due_date: "",
                completed: false,
                assigned_to: "",
                project_id: "",
            };
        }
        errors.value = {};
    },
    { immediate: true }
);

const handleSubmit = async () => {
    if (!form.value.title.trim()) {
        errors.value = { title: ["Title is required"] };
        return;
    }

    loading.value = true;
    errors.value = {};

    try {
        emit("save", {
            ...form.value,
            id: props.task.id || null,
        });
    } catch (error) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
};
</script>
