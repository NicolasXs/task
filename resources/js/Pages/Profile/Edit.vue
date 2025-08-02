<script setup>
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    auth: Object,
});

const currentSection = ref("profile");
const sidebarOpen = ref(false);

const userName = props.auth?.user?.name || "User";
const userType = props.auth?.user?.user_type || "user";
const userInitial = userName.charAt(0).toUpperCase();

const showSection = (section) => {
    if (section === "dashboard") {
        router.get("/dashboard");
    } else {
        currentSection.value = section;
        // Close sidebar on mobile when section is selected
        if (window.innerWidth <= 768) {
            sidebarOpen.value = false;
        }
    }
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const logout = () => {
    router.post("/logout");
};
</script>

<style scoped>
.sidebar {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 50;
    }
    .sidebar.open {
        transform: translateX(0);
    }
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

/* Custom button styles to match dashboard theme */
:deep(.bg-gray-800) {
    background-color: #2563eb !important;
}

:deep(.hover\:bg-gray-700:hover) {
    background-color: #1d4ed8 !important;
}

:deep(.focus\:bg-gray-700:focus) {
    background-color: #1d4ed8 !important;
}

:deep(.active\:bg-gray-900:active) {
    background-color: #1e40af !important;
}

:deep(.focus\:ring-indigo-500:focus) {
    --tw-ring-color: #3b82f6 !important;
}

/* Form input focus states */
:deep(.focus\:border-indigo-500:focus) {
    border-color: #3b82f6 !important;
}

:deep(.focus\:ring-indigo-500:focus) {
    --tw-ring-color: #3b82f6 !important;
}
</style>

<template>
    <Head title="Profile Settings" />

    <div class="flex h-screen overflow-hidden bg-gray-100">
        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
            @click="toggleSidebar"
        ></div>

        <!-- Sidebar -->
        <div
            :class="[
                'sidebar bg-blue-600 text-white w-64 flex-shrink-0 z-50',
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
                <button @click="toggleSidebar" class="md:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <button
                            @click="showSection('dashboard')"
                            class="w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left"
                        >
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span>Dashboard</span>
                        </button>
                    </li>
                    <li>
                        <button
                            @click="showSection('profile')"
                            :class="[
                                'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                                currentSection === 'profile'
                                    ? 'bg-blue-500'
                                    : '',
                            ]"
                        >
                            <i class="fas fa-user mr-3"></i>
                            <span>Profile Settings</span>
                        </button>
                    </li>
                    <li>
                        <button
                            @click="showSection('password')"
                            :class="[
                                'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                                currentSection === 'password'
                                    ? 'bg-blue-500'
                                    : '',
                            ]"
                        >
                            <i class="fas fa-lock mr-3"></i>
                            <span>Change Password</span>
                        </button>
                    </li>
                    <li>
                        <button
                            @click="showSection('security')"
                            :class="[
                                'w-full flex items-center p-2 rounded hover:bg-blue-500 transition text-left',
                                currentSection === 'security'
                                    ? 'bg-blue-500'
                                    : '',
                            ]"
                        >
                            <i class="fas fa-shield-alt mr-3"></i>
                            <span>Account Security</span>
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
                            <p class="text-blue-200 text-xs capitalize">
                                {{ userType }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="logout"
                        class="w-full bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded transition flex items-center justify-center"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Mobile Header -->
            <header
                class="bg-white shadow-sm md:hidden p-4 flex items-center justify-between"
            >
                <button @click="toggleSidebar">
                    <i class="fas fa-bars text-blue-600"></i>
                </button>
                <h1 class="text-xl font-bold text-blue-600">
                    Profile Settings
                </h1>
                <div class="w-6"></div>
            </header>

            <!-- Profile Information Section -->
            <section v-if="currentSection === 'profile'" class="p-6 fade-in">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user text-blue-600 text-2xl mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">
                            Profile Information
                        </h2>
                    </div>

                    <!-- Success Message -->
                    <div
                        v-if="status"
                        class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg"
                    >
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ status }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="max-w-2xl">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Change Password Section -->
            <section v-if="currentSection === 'password'" class="p-6 fade-in">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-lock text-blue-600 text-2xl mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">
                            Change Password
                        </h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="max-w-2xl">
                            <UpdatePasswordForm />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Account Security Section -->
            <section v-if="currentSection === 'security'" class="p-6 fade-in">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center mb-6">
                        <i
                            class="fas fa-shield-alt text-blue-600 text-2xl mr-3"
                        ></i>
                        <h2 class="text-2xl font-bold text-gray-800">
                            Account Security
                        </h2>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="max-w-2xl">
                            <div class="mb-6">
                                <h3
                                    class="text-lg font-semibold text-gray-800 mb-4"
                                >
                                    Danger Zone
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    Once you delete your account, all of its
                                    resources and data will be permanently
                                    deleted. Before deleting your account,
                                    please download any data or information that
                                    you wish to retain.
                                </p>
                            </div>
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
