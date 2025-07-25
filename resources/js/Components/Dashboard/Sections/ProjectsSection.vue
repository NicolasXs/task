<template>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Projetos</h1>
                <p class="text-gray-600">
                    Gerencie seus projetos e organize suas tarefas
                </p>
            </div>
            <button
                @click="openProjectModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
                <i class="fas fa-plus"></i>
                Novo Projeto
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-[200px]">
                <input
                    v-model="searchTerm"
                    type="text"
                    placeholder="Buscar projetos..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
            <select
                v-model="statusFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="all">Todos os Status</option>
                <option value="active">Ativo</option>
                <option value="completed">Concluído</option>
                <option value="archived">Arquivado</option>
            </select>
            <select
                v-model="sortBy"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="newest">Mais Recente</option>
                <option value="oldest">Mais Antigo</option>
                <option value="name">Nome</option>
                <option value="progress">Progresso</option>
            </select>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
        </div>

        <!-- Projects Grid -->
        <div
            v-else-if="projects.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="project in projects"
                :key="project.id"
                class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border-l-4 cursor-pointer"
                :style="`border-left-color: ${project.color}`"
                @click="$emit('select-project', project)"
            >
                <div class="p-6">
                    <!-- Project Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                {{ project.name }}
                            </h3>
                            <p class="text-gray-600 text-sm line-clamp-2">
                                {{ project.description || "Sem descrição" }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <button
                                @click.stop="openProjectModal(project)"
                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                                title="Editar projeto"
                            >
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button
                                @click.stop="confirmDeleteProject(project.id)"
                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                title="Excluir projeto"
                            >
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Project Status -->
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium"
                            :class="getStatusClass(project.status)"
                        >
                            {{ getStatusLabel(project.status) }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ project.tasks_count || 0 }} tarefas
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Progresso</span>
                            <span class="text-sm font-medium text-gray-900"
                                >{{ project.progress || 0 }}%</span
                            >
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="h-2 rounded-full transition-all duration-300"
                                :style="`width: ${
                                    project.progress || 0
                                }%; background-color: ${project.color}`"
                            ></div>
                        </div>
                    </div>

                    <!-- Project Dates -->
                    <div
                        v-if="project.start_date || project.end_date"
                        class="text-xs text-gray-500"
                    >
                        <div
                            v-if="project.start_date"
                            class="flex items-center gap-1 mb-1"
                        >
                            <i class="fas fa-calendar-alt"></i>
                            Início: {{ formatDate(project.start_date) }}
                        </div>
                        <div
                            v-if="project.end_date"
                            class="flex items-center gap-1"
                        >
                            <i class="fas fa-calendar-check"></i>
                            Fim: {{ formatDate(project.end_date) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-folder-open text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                Nenhum projeto encontrado
            </h3>
            <p class="text-gray-600 mb-4">
                Comece criando seu primeiro projeto para organizar suas tarefas.
            </p>
            <button
                @click="openProjectModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors"
            >
                <i class="fas fa-plus mr-2"></i>
                Criar Primeiro Projeto
            </button>
        </div>

        <!-- Pagination -->
        <div
            v-if="totalPages > 1"
            class="flex justify-center items-center gap-2 mt-8"
        >
            <button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Anterior
            </button>

            <span class="text-sm text-gray-700">
                Página {{ currentPage }} de {{ totalPages }}
            </span>

            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Próxima
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";

// Props
const props = defineProps({
    projects: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    currentPage: { type: Number, default: 1 },
    totalPages: { type: Number, default: 1 },
});

// Emits
const emit = defineEmits([
    "update:searchTerm",
    "update:statusFilter",
    "update:sortBy",
    "open-project-modal",
    "delete-project",
    "change-page",
    "select-project",
]);

// Local state
const searchTerm = ref("");
const statusFilter = ref("all");
const sortBy = ref("newest");

// Watch for changes and emit
watch(searchTerm, (value) => emit("update:searchTerm", value));
watch(statusFilter, (value) => emit("update:statusFilter", value));
watch(sortBy, (value) => emit("update:sortBy", value));

// Methods
const openProjectModal = (project = null) => {
    emit("open-project-modal", project);
};

const confirmDeleteProject = (projectId) => {
    emit("delete-project", projectId);
};

const changePage = (page) => {
    emit("change-page", page);
};

const getStatusClass = (status) => {
    const classes = {
        active: "bg-green-100 text-green-800",
        completed: "bg-blue-100 text-blue-800",
        archived: "bg-gray-100 text-gray-800",
    };
    return classes[status] || classes.active;
};

const getStatusLabel = (status) => {
    const labels = {
        active: "Ativo",
        completed: "Concluído",
        archived: "Arquivado",
    };
    return labels[status] || status;
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-BR");
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
