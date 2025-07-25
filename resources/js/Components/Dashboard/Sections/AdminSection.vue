<template>
    <section class="p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    User Management
                </h2>
                <button
                    v-if="isAdmin"
                    @click="$emit('open-user-modal')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i> Add User
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center"
                        >
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Total Users
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ users.length }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center"
                        >
                            <i class="fas fa-user-check text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Regular Users
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{
                                    users.filter((u) => u.user_type === "user")
                                        .length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center"
                        >
                            <i
                                class="fas fa-user-shield text-white text-xl"
                            ></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">
                                Admins
                            </h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{
                                    users.filter((u) => u.user_type === "admin")
                                        .length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="loadingUsers"
                class="bg-white rounded-lg shadow p-8 text-center"
            >
                <i
                    class="fas fa-spinner fa-spin text-blue-600 text-2xl mb-2"
                ></i>
                <p class="text-gray-500">Loading users...</p>
            </div>
            <div v-else class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                User
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Role
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Tasks
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Joined
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center"
                                    >
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ user.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        user.user_type === 'admin'
                                            ? 'bg-purple-100 text-purple-800'
                                            : 'bg-blue-100 text-blue-800',
                                    ]"
                                >
                                    {{ user.user_type }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <div class="flex flex-col">
                                    <span class="font-medium">{{
                                        user.total_tasks || 0
                                    }}</span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <div class="flex space-x-2">
                                    <button
                                        @click="$emit('edit-user', user)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        v-if="user.id !== currentUserId"
                                        @click="$emit('delete-user', user.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
<script setup>
const props = defineProps({
    users: Array,
    loadingUsers: Boolean,
    isAdmin: Boolean,
    currentUserId: Number,
    formatDate: Function,
});
</script>
