<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmation = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<style scoped>
@keyframes float {
    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }

    100% {
        transform: translateY(0px);
    }
}

.floating {
    animation: float 4s ease-in-out infinite;
}

.gradient-bg {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%);
}

.input-focus:focus {
    box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
}

.form-hover:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
</style>

<template>
    <Head title="Task - Register" />

    <div class="gradient-bg min-h-screen flex items-center justify-center p-4">
        <!-- Floating elements -->
        <div
            class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none"
        >
            <div
                class="absolute top-20 left-10 w-16 h-16 rounded-full bg-blue-200 opacity-30 floating"
                style="animation-delay: 0s"
            ></div>
            <div
                class="absolute top-1/3 right-20 w-24 h-24 rounded-full bg-blue-300 opacity-20 floating"
                style="animation-delay: 1s"
            ></div>
            <div
                class="absolute bottom-1/4 left-1/4 w-20 h-20 rounded-full bg-blue-400 opacity-15 floating"
                style="animation-delay: 2s"
            ></div>
        </div>

        <div
            class="relative bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md z-10"
        >
            <!-- Header -->
            <div class="bg-blue-500 py-6 px-8 text-white">
                <div class="flex items-center justify-center mb-2">
                    <i class="fas fa-tasks text-3xl mr-3"></i>
                    <h1 class="text-3xl font-bold">Task</h1>
                </div>
                <p class="text-center text-blue-100">
                    Join us and organize your life
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="p-8 form-hover">
                <!-- Name field -->
                <div class="mb-6">
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Name</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <i class="fas fa-user text-blue-400"></i>
                        </div>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            class="input-focus pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200"
                            placeholder="Your full name"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email field -->
                <div class="mb-6">
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Email</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <i class="fas fa-envelope text-blue-400"></i>
                        </div>
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            class="input-focus pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200"
                            placeholder="seu@email.com"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Password field -->
                <div class="mb-6">
                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Password</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <i class="fas fa-lock text-blue-400"></i>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            class="input-focus pl-10 pr-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i
                                :class="
                                    showPassword
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                "
                                class="text-blue-400 hover:text-blue-600 cursor-pointer"
                            ></i>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password field -->
                <div class="mb-6">
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Confirm Password</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <i class="fas fa-lock text-blue-400"></i>
                        </div>
                        <input
                            :type="
                                showPasswordConfirmation ? 'text' : 'password'
                            "
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            class="input-focus pl-10 pr-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="togglePasswordConfirmation"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i
                                :class="
                                    showPasswordConfirmation
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                "
                                class="text-blue-400 hover:text-blue-600 cursor-pointer"
                            ></i>
                        </button>
                    </div>
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- Submit button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <span v-if="!form.processing">Register</span>
                    <span v-else>Creating account...</span>
                    <i
                        v-if="form.processing"
                        class="fas fa-spinner fa-spin ml-2"
                    ></i>
                </button>

                <!-- Login link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <Link
                            :href="route('login')"
                            class="text-blue-600 hover:text-blue-800 font-medium"
                            >Sign in</Link
                        >
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Font Awesome CDN -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</template>
