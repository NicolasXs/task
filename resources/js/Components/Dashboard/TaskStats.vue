<template>
    <div class="space-y-6">
        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total de Tarefas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div
                        class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4"
                    >
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total de Tarefas</p>
                        <h3 class="text-2xl font-bold">{{ stats.total }}</h3>
                    </div>
                </div>
            </div>

            <!-- Concluídas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div
                        class="p-3 rounded-full bg-green-100 text-green-600 mr-4"
                    >
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Concluídas</p>
                        <h3 class="text-2xl font-bold">
                            {{ stats.completed }}
                        </h3>
                        <p class="text-xs text-green-600">
                            {{ stats.completionRate }}% completas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pendentes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div
                        class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4"
                    >
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pendentes</p>
                        <h3 class="text-2xl font-bold">{{ stats.pending }}</h3>
                    </div>
                </div>
            </div>

            <!-- Atrasadas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Atrasadas</p>
                        <h3 class="text-2xl font-bold">
                            {{ stats.overdue || 0 }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Vencem esta semana -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center">
                <div
                    class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4"
                >
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Vencem esta semana</p>
                    <h3 class="text-2xl font-bold">
                        {{ stats.dueThisWeek || 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Distribuição por Prioridade -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Tarefas por Prioridade
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">
                        {{ stats.urgent || 0 }}
                    </div>
                    <p class="text-sm text-red-600">Urgente</p>
                    <p class="text-xs text-gray-500">
                        {{ getPercentage(stats.urgent || 0) }}% do total
                    </p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ stats.highPriority || 0 }}
                    </div>
                    <p class="text-sm text-orange-600">Alta</p>
                    <p class="text-xs text-gray-500">
                        {{ getPercentage(stats.highPriority || 0) }}% do total
                    </p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ stats.mediumPriority || 0 }}
                    </div>
                    <p class="text-sm text-yellow-600">Média</p>
                    <p class="text-xs text-gray-500">
                        {{ getPercentage(stats.mediumPriority || 0) }}% do total
                    </p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        {{ stats.lowPriority || 0 }}
                    </div>
                    <p class="text-sm text-green-600">Baixa</p>
                    <p class="text-xs text-gray-500">
                        {{ getPercentage(stats.lowPriority || 0) }}% do total
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Status (se disponível) -->
        <div
            v-if="filteredTasksByStatus && filteredTasksByStatus.length > 0"
            class="bg-white rounded-lg shadow p-6"
        >
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Distribuição por Status
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div
                    v-for="statusItem in filteredTasksByStatus"
                    :key="statusItem.status"
                    class="text-center p-4 bg-gray-50 rounded-lg"
                >
                    <div class="text-xl font-bold text-gray-700">
                        {{ statusItem.count }}
                    </div>
                    <p class="text-sm text-gray-600">{{ statusItem.label }}</p>
                </div>
            </div>
        </div>

        <!-- Atividade dos últimos 7 dias -->
        <div
            v-if="stats.tasksLast7Days && stats.tasksLast7Days.length > 0"
            class="bg-white rounded-lg shadow p-6"
        >
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Atividade dos Últimos 7 Dias
            </h3>
            <div class="grid grid-cols-7 gap-2">
                <div
                    v-for="day in stats.tasksLast7Days"
                    :key="day.date"
                    class="text-center p-3 bg-blue-50 rounded-lg"
                >
                    <div class="text-lg font-bold text-blue-600">
                        {{ day.count }}
                    </div>
                    <p class="text-xs text-blue-600">{{ day.label }}</p>
                    <p class="text-xs text-gray-500">{{ day.day }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, computed } from "vue";

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const getPercentage = (value) => {
    if (!props.stats.total || props.stats.total === 0) return "0";
    return ((value / props.stats.total) * 100).toFixed(1);
};

const filteredTasksByStatus = computed(() => {
    if (!props.stats.tasksByStatus) return [];

    return props.stats.tasksByStatus.filter(
        (statusItem) =>
            statusItem.status === "completed" || statusItem.status === "pending"
    );
});
</script>
