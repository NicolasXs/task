<template>
    <div
        v-show="userModalOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click="handleBackdropClick"
    >
        <div
            class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4"
            @click.stop
        >
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">
                    {{ userForm.id ? "Edit User" : "Add New User" }}
                </h3>
                <button
                    @click="handleClose"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <form @submit.prevent="$emit('save-user')">
                    <div class="mb-4">
                        <label
                            for="userName"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Name*</label
                        >
                        <input
                            type="text"
                            id="userName"
                            v-model="userForm.name"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="userEmail"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Email*</label
                        >
                        <input
                            type="email"
                            id="userEmail"
                            v-model="userForm.email"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <label
                            for="userRole"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Role*</label
                        >
                        <select
                            id="userRole"
                            v-model="userForm.role"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="flex justify-end border-t p-4">
                <button
                    @click="handleClose"
                    class="px-4 py-2 border rounded-lg mr-2 hover:bg-gray-50 transition"
                >
                    Cancel
                </button>
                <button
                    @click="handleSaveUser"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Save User
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    userModalOpen: Boolean,
    userForm: Object,
});

const emit = defineEmits(["close", "save-user"]);

const handleClose = () => {
    emit("close");
};

const handleSaveUser = () => {
    emit("save-user");
};

const handleBackdropClick = () => {
    handleClose();
};

const handleEscapeKey = (event) => {
    if (event.key === "Escape" && props.userModalOpen) {
        event.preventDefault();
        handleClose();
    }
};

watch(
    () => props.userModalOpen,
    (isOpen) => {
        if (isOpen) {
            document.addEventListener("keydown", handleEscapeKey);
        } else {
            document.removeEventListener("keydown", handleEscapeKey);
        }
    }
);

onUnmounted(() => {
    document.removeEventListener("keydown", handleEscapeKey);
});
</script>
