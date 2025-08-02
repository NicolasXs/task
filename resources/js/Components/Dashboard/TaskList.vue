<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="tasks.length === 0" class="p-8 text-center text-gray-500">
            <i class="fas fa-tasks text-4xl mb-2 text-gray-300"></i>
            <p>No tasks found. Try adjusting your search or filters.</p>
        </div>

        <div v-else>
            <div
                v-for="task in tasks"
                :key="task.id"
                class="border-b border-gray-200 p-4 hover:bg-gray-50 transition fade-in"
                :class="{ 'bg-gray-50': task.completed }"
            >
                <!-- ...existing code... -->
                <div class="flex justify-between items-start">
                    <div class="flex items-center flex-1">
                        <input
                            type="checkbox"
                            :checked="task.completed"
                            @change="$emit('toggle-completion', task.id)"
                            class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 mr-3"
                        />
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3
                                    :class="[
                                        'font-medium',
                                        { 'task-completed': task.completed },
                                    ]"
                                >
                                    {{ task.title }}
                                </h3>
                                <span
                                    v-if="isOverdue(task)"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                >
                                    <i
                                        class="fas fa-exclamation-triangle mr-1"
                                    ></i>
                                    Atrasada
                                </span>
                            </div>
                            <p
                                v-if="task.description"
                                :class="[
                                    'text-sm text-gray-500 mt-1',
                                    { 'task-completed': task.completed },
                                ]"
                            >
                                {{ task.description }}
                            </p>
                        </div>
                    </div>
                    <span
                        :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            task.priority === 'high'
                                ? 'bg-red-100 text-red-800'
                                : task.priority === 'medium'
                                ? 'bg-yellow-100 text-yellow-800'
                                : 'bg-green-100 text-green-800',
                        ]"
                    >
                        {{
                            task.priority.charAt(0).toUpperCase() +
                            task.priority.slice(1)
                        }}
                    </span>
                </div>

                <div class="flex justify-between items-center mt-3">
                    <div
                        class="flex items-center text-sm text-gray-500 space-x-4"
                    >
                        <div v-if="task.project" class="flex items-center">
                            <i class="fas fa-folder mr-1"></i>
                            <span>{{ task.project.name }}</span>
                        </div>
                        <div v-if="task.due_date" class="flex items-center">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <span
                                :class="[
                                    isOverdue(task) && !task.completed
                                        ? 'text-red-600 font-medium'
                                        : '',
                                ]"
                            >
                                Due {{ formatDate(task.due_date) }}
                            </span>
                        </div>
                        <div
                            v-if="
                                task.assigned_user &&
                                task.assigned_user.id !== task.creator?.id
                            "
                            class="flex items-center"
                        >
                            <i class="fas fa-user mr-1"></i>
                            <span
                                >Assigned to {{ task.assigned_user.name }}</span
                            >
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="$emit('edit-task', task)"
                            class="text-blue-600 hover:text-blue-900"
                        >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button
                            @click="$emit('delete-task', task.id)"
                            class="text-red-600 hover:text-red-900"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <!-- ...existing code... -->
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

defineEmits(["toggle-completion", "edit-task", "delete-task"]);

const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const isOverdue = (task) => {
    if (!task.due_date || task.completed) return false;
    const today = new Date();
    const dueDate = new Date(task.due_date);
    today.setHours(0, 0, 0, 0);
    dueDate.setHours(0, 0, 0, 0);
    return dueDate < today;
};
</script>

<style scoped>
.task-completed {
    text-decoration: line-through;
    opacity: 0.7;
}

.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
