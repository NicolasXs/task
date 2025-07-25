<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
        >
            <!-- Background overlay -->
            <div
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                @click="$emit('close')"
            ></div>

            <!-- Modal panel -->
            <div
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ project?.id ? "Editar Projeto" : "Novo Projeto" }}
                    </h3>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleSubmit">
                    <!-- Project Name -->
                    <div class="mb-4">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Nome do Projeto *
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            maxlength="255"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': errors.name }"
                            placeholder="Digite o nome do projeto"
                        />
                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">
                            {{ errors.name[0] }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Descrição
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            :class="{ 'border-red-500': errors.description }"
                            placeholder="Descreva o objetivo do projeto"
                        ></textarea>
                        <p
                            v-if="errors.description"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.description[0] }}
                        </p>
                    </div>

                    <!-- Color and Status Row -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Project Color -->
                        <div>
                            <label
                                for="color"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Cor
                            </label>
                            <div class="flex items-center gap-2">
                                <input
                                    id="color"
                                    v-model="form.color"
                                    type="color"
                                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                                    :class="{ 'border-red-500': errors.color }"
                                />
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                    :class="{ 'border-red-500': errors.color }"
                                    placeholder="#3B82F6"
                                    pattern="^#[a-fA-F0-9]{6}$"
                                />
                            </div>
                            <p
                                v-if="errors.color"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.color[0] }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label
                                for="status"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Status
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.status }"
                            >
                                <option value="active">Ativo</option>
                                <option value="completed">Concluído</option>
                                <option value="archived">Arquivado</option>
                            </select>
                            <p
                                v-if="errors.status"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.status[0] }}
                            </p>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <!-- Start Date -->
                        <div>
                            <label
                                for="start_date"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Data de Início
                            </label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.start_date }"
                            />
                            <p
                                v-if="errors.start_date"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.start_date[0] }}
                            </p>
                        </div>

                        <!-- End Date -->
                        <div>
                            <label
                                for="end_date"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Data de Fim
                            </label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                :min="form.start_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.end_date }"
                            />
                            <p
                                v-if="errors.end_date"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.end_date[0] }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                        >
                            <i
                                v-if="loading"
                                class="fas fa-spinner fa-spin"
                            ></i>
                            <i v-else class="fas fa-save"></i>
                            {{ loading ? "Salvando..." : "Salvar" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, reactive } from "vue";

// Props
const props = defineProps({
    isOpen: { type: Boolean, default: false },
    project: { type: Object, default: null },
    loading: { type: Boolean, default: false },
});

// Emits
const emit = defineEmits(["close", "save"]);

// Form data
const form = reactive({
    name: "",
    description: "",
    color: "#3B82F6",
    status: "active",
    start_date: "",
    end_date: "",
});

// Errors
const errors = ref({});

// Watch for project changes
watch(
    () => props.project,
    (newProject) => {
        if (newProject) {
            form.name = newProject.name || "";
            form.description = newProject.description || "";
            form.color = newProject.color || "#3B82F6";
            form.status = newProject.status || "active";
            form.start_date = newProject.start_date || "";
            form.end_date = newProject.end_date || "";
        } else {
            // Reset form for new project
            form.name = "";
            form.description = "";
            form.color = "#3B82F6";
            form.status = "active";
            form.start_date = "";
            form.end_date = "";
        }
        errors.value = {};
    },
    { immediate: true }
);

// Watch for modal open/close
watch(
    () => props.isOpen,
    (isOpen) => {
        if (!isOpen) {
            errors.value = {};
        }
    }
);

// Methods
const handleSubmit = async () => {
    errors.value = {};

    try {
        const projectData = { ...form };

        // Add ID if editing
        if (props.project?.id) {
            projectData.id = props.project.id;
        }

        await emit("save", projectData);
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    }
};
</script>
