<template>
    <div
        :class="[
            'sidebar bg-blue-600 text-white w-64 flex-shrink-0',
            { open: sidebarOpen },
        ]"
    >
        <div
            class="p-4 flex items-center justify-between border-b border-blue-500"
        >
            <div class="flex items-center">
                <i class="fas fa-tasks text-2xl mr-3"></i>
                <h1 class="text-xl font-bold">Task Manager</h1>
            </div>
            <button @click="$emit('toggle-sidebar')" class="md:hidden">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <button
                        @click="$emit('show-section', 'tasks')"
                        :class="[
                            'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                            currentSection === 'tasks' ? 'bg-blue-500' : '',
                        ]"
                    >
                        <i class="fas fa-tasks mr-3"></i>
                        <span>My Tasks</span>
                    </button>
                </li>
                <li>
                    <button
                        @click="$emit('show-section', 'projects')"
                        :class="[
                            'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                            currentSection === 'projects' ? 'bg-blue-500' : '',
                        ]"
                    >
                        <i class="fas fa-folder mr-3"></i>
                        <span>Projects</span>
                    </button>
                </li>
                <li v-if="userType === 'admin'">
                    <button
                        @click="$emit('show-section', 'admin')"
                        :class="[
                            'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                            currentSection === 'admin' ? 'bg-blue-500' : '',
                        ]"
                    >
                        <i class="fas fa-user-shield mr-3"></i>
                        <span>Admin Panel</span>
                    </button>
                </li>
                <li>
                    <button
                        @click="$emit('show-section', 'stats')"
                        :class="[
                            'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                            currentSection === 'stats' ? 'bg-blue-500' : '',
                        ]"
                    >
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Statistics</span>
                    </button>
                </li>
            </ul>
        </nav>
        <div class="mt-auto">
            <div class="p-4 border-t border-blue-500">
                <div class="flex items-center mb-4">
                    <div
                        class="h-10 w-10 rounded-full bg-blue-300 text-blue-800 flex items-center justify-center font-semibold"
                    >
                        {{ userInitial }}
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-sm">{{ userName }}</p>
                        <p class="text-xs text-blue-200">{{ userType }}</p>
                    </div>
                </div>
                <button
                    @click="$emit('logout')"
                    class="w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left text-blue-100 hover:text-white"
                >
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    currentSection: String,
    sidebarOpen: Boolean,
    userName: String,
    userType: String,
    userInitial: String,
});
</script>
