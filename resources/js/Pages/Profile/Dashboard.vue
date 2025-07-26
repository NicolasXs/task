<script setup>
import ConfirmModal from "@/Components/Dashboard/Modals/ConfirmModal.vue";
import ProjectModal from "@/Components/Dashboard/Modals/ProjectModal.vue";
import UserModal from "@/Components/Dashboard/Modals/UserModal.vue";
import AdminSection from "@/Components/Dashboard/Sections/AdminSection.vue";
import ProjectsSection from "@/Components/Dashboard/Sections/ProjectsSection.vue";
import Sidebar from "@/Components/Dashboard/Sections/Sidebar.vue";
import StatisticsSection from "@/Components/Dashboard/Sections/StatisticsSection.vue";
import TasksSection from "@/Components/Dashboard/Sections/TasksSection.vue";
import TaskModal from "@/Components/Dashboard/TaskModal.vue";
import { Head, router } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref, watch } from "vue";

const currentSection = ref("tasks");
const sidebarOpen = ref(false);
const tasks = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const tasksPerPage = ref(5);
const totalPages = ref(0);
const totalTasks = ref(0);
const searchTerm = ref("");
const filterValue = ref("all");
const sortValue = ref("newest");
const showTaskModal = ref(false);
const confirmModalOpen = ref(false);
const currentTask = ref({});
const confirmMessage = ref("");
const confirmAction = ref("");
const confirmItemId = ref(null);
const users = ref([]);
const loadingUsers = ref(false);
const userModalOpen = ref(false);
const userForm = ref({ id: null, name: "", email: "", role: "user" });
const loadingStats = ref(false);
const statistics = ref({});

const projects = ref([]);
const loadingProjects = ref(false);
const projectModalOpen = ref(false);
const currentProject = ref(null);
const selectedProjectId = ref("all");
const projectCurrentPage = ref(1);
const projectTotalPages = ref(0);
const projectSearchTerm = ref("");
const projectStatusFilter = ref("all");
const projectSortBy = ref("newest");

onMounted(() => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (token) {
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
    }
    loadTasks();
    loadStats();
    loadProjects();
});

watch([searchTerm, filterValue, sortValue], () => {
    currentPage.value = 1;
    loadTasks();
});

watch(currentPage, () => {
    loadTasks();
});

watch([projectSearchTerm, projectStatusFilter, projectSortBy], () => {
    projectCurrentPage.value = 1;
    loadProjects();
});

watch(projectCurrentPage, () => {
    loadProjects();
});

watch(selectedProjectId, () => {
    currentPage.value = 1;
    loadTasks();
});

const showSection = (section) => {
    currentSection.value = section;
    sidebarOpen.value = false;
    if (section === "admin") {
        loadUsers();
    } else if (section === "stats") {
        loadStats();
    } else if (section === "projects") {
        loadProjects();
    }
};
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const loadTasks = async () => {
    loading.value = true;
    try {
        const params = {
            page: currentPage.value,
            per_page: tasksPerPage.value,
        };

        if (searchTerm.value && searchTerm.value.trim() !== "") {
            params.search = searchTerm.value.trim();
        }

        if (filterValue.value && filterValue.value !== "all") {
            params.filter = filterValue.value;
        }

        if (sortValue.value && sortValue.value !== "newest") {
            params.sort = sortValue.value;
        }

        if (selectedProjectId.value && selectedProjectId.value !== "all") {
            params.project_id = selectedProjectId.value;
        }

        const response = await axios.get("/api/tasks", { params });
        tasks.value = response.data.data.map((task) => ({
            ...task,
            completed: task.status === "completed",
        }));
        const pagination = response.data.pagination || {};
        totalPages.value = pagination.last_page || pagination.total_pages || 1;
        totalTasks.value = pagination.total || tasks.value.length;

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value > 0 ? totalPages.value : 1;
            return;
        }
    } catch (error) {
        console.error("Error loading tasks:", error);
    } finally {
        loading.value = false;
    }
};

const loadStats = async () => {
    loadingStats.value = true;
    try {
        const response = await axios.get("/api/tasks/statistics");

        const apiData = response.data;

        statistics.value = {
            total: apiData.summary?.totalTasks || 0,
            completed: apiData.summary?.completedTasks || 0,
            pending: apiData.summary?.pendingTasks || 0,
            dueThisWeek: apiData.summary?.tasksDueThisWeek || 0,
            overdue: apiData.summary?.overdueTasks || 0,
            urgent:
                apiData.charts?.tasksByPriority?.find(
                    (p) => p.priority === "urgent"
                )?.count || 0,
            highPriority:
                apiData.charts?.tasksByPriority?.find(
                    (p) => p.priority === "high"
                )?.count || 0,
            mediumPriority:
                apiData.charts?.tasksByPriority?.find(
                    (p) => p.priority === "medium"
                )?.count || 0,
            lowPriority:
                apiData.charts?.tasksByPriority?.find(
                    (p) => p.priority === "low"
                )?.count || 0,
            tasksByStatus: apiData.charts?.tasksByStatus || [],
            tasksByPriority: apiData.charts?.tasksByPriority || [],
            tasksLast7Days: apiData.charts?.tasksLast7Days || [],
        };
    } catch (error) {
        console.error("Error loading stats:", error);
        statistics.value = {
            total: 0,
            completed: 0,
            pending: 0,
            dueThisWeek: 0,
            overdue: 0,
            completionRate: 0,
            urgent: 0,
            highPriority: 0,
            mediumPriority: 0,
            lowPriority: 0,
            tasksByStatus: [],
            tasksByPriority: [],
            tasksLast7Days: [],
        };
    } finally {
        loadingStats.value = false;
    }
};

const loadUsers = async () => {
    loadingUsers.value = true;
    try {
        const response = await axios.get("/api/users");
        users.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error loading users:", error);
    } finally {
        loadingUsers.value = false;
    }
};

const openTaskModal = (task = null) => {
    currentTask.value = task || {};
    showTaskModal.value = true;
    if (userType === "admin") {
        loadUsers();
    }
};

const closeTaskModal = () => {
    showTaskModal.value = false;
    currentTask.value = {};
};

const saveTask = async (taskData) => {
    try {
        if (taskData.id) {
            await axios.put(`/api/tasks/${taskData.id}`, taskData);
        } else {
            await axios.post("/api/tasks", taskData);
        }

        closeTaskModal();
        loadTasks();
        loadStats();
    } catch (error) {
        console.error("Error saving task:", error);
        if (error.response?.data?.errors) {
            throw error;
        } else {
            alert("Error saving task. Please try again.");
        }
    }
};

const toggleTaskCompletion = async (taskId) => {
    try {
        await axios.patch(`/api/tasks/${taskId}/toggle`);
        loadTasks();
        loadStats();
    } catch (error) {
        console.error("Error toggling task:", error);
    }
};

const confirmDeleteTask = (taskId) => {
    confirmAction.value = "deleteTask";
    confirmItemId.value = taskId;
    confirmMessage.value = "Are you sure you want to delete this task?";
    confirmModalOpen.value = true;
};

const deleteTask = async (taskId) => {
    try {
        await axios.delete(`/api/tasks/${taskId}`);
        loadTasks();
        loadStats();
    } catch (error) {
        console.error("Error deleting task:", error);
    }
};

const handleConfirm = async () => {
    if (confirmAction.value === "deleteTask") {
        await deleteTask(confirmItemId.value);
    } else if (confirmAction.value === "deleteProject") {
        await deleteProject(confirmItemId.value);
    }
    confirmModalOpen.value = false;
};

const openUserModal = (user = null) => {
    if (user) {
        userForm.value = {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.user_type || user.role,
        };
    } else {
        userForm.value = { id: null, name: "", email: "", role: "user" };
    }
    userModalOpen.value = true;
};

const editUser = (user) => {
    openUserModal(user);
};

const deleteUser = async (userId) => {
    if (confirm("Are you sure you want to delete this user?")) {
        try {
            await axios.delete(`/api/users/${userId}`);
            loadUsers();
        } catch (error) {
            console.error("Error deleting user:", error);
        }
    }
};

const saveUser = async () => {
    try {
        if (userForm.value.id) {
            await axios.put(`/api/users/${userForm.value.id}`, userForm.value);
        } else {
            await axios.post("/api/users", userForm.value);
        }

        userModalOpen.value = false;
        userForm.value = { id: null, name: "", email: "", role: "user" };
        loadUsers();
    } catch (error) {
        console.error("Error saving user:", error);
    }
};

const closeUserModal = () => {
    userModalOpen.value = false;
    userForm.value = { id: null, name: "", email: "", role: "user" };
};

const loadProjects = async () => {
    loadingProjects.value = true;
    try {
        const params = {
            page: projectCurrentPage.value,
            per_page: 10,
        };

        if (projectSearchTerm.value && projectSearchTerm.value.trim() !== "") {
            params.search = projectSearchTerm.value.trim();
        }

        if (projectStatusFilter.value && projectStatusFilter.value !== "all") {
            params.status = projectStatusFilter.value;
        }

        if (projectSortBy.value) {
            params.sort = projectSortBy.value;
        }

        const response = await axios.get("/api/projects", { params });
        projects.value = response.data.data || response.data;
        const pagination = response.data.pagination || response.data;
        projectTotalPages.value =
            pagination.last_page || pagination.total_pages || 1;
    } catch (error) {
        console.error("Error loading projects:", error);
    } finally {
        loadingProjects.value = false;
    }
};

const openProjectModal = (project = null) => {
    currentProject.value = project;
    projectModalOpen.value = true;
};

const closeProjectModal = () => {
    projectModalOpen.value = false;
    currentProject.value = null;
};

const saveProject = async (projectData) => {
    try {
        if (projectData.id) {
            await axios.put(`/api/projects/${projectData.id}`, projectData);
        } else {
            await axios.post("/api/projects", projectData);
        }

        closeProjectModal();
        loadProjects();
        if (selectedProjectId.value !== "all") {
            loadTasks();
        }
    } catch (error) {
        console.error("Error saving project:", error);
        if (error.response?.data?.errors) {
            throw error;
        } else {
            alert("Error saving project. Please try again.");
        }
    }
};

const confirmDeleteProject = (projectId) => {
    confirmAction.value = "deleteProject";
    confirmItemId.value = projectId;
    confirmMessage.value =
        "Are you sure you want to delete this project? All tasks in this project will be moved to 'No Project'.";
    confirmModalOpen.value = true;
};

const deleteProject = async (projectId) => {
    try {
        await axios.delete(`/api/projects/${projectId}`);
        loadProjects();
        if (selectedProjectId.value == projectId) {
            selectedProjectId.value = "all";
            loadTasks();
        }
    } catch (error) {
        console.error("Error deleting project:", error);
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        } else {
            alert("Error deleting project. Please try again.");
        }
    }
};

const changeProjectPage = (page) => {
    if (page >= 1 && page <= projectTotalPages.value) {
        projectCurrentPage.value = page;
    }
};

const selectProject = (project) => {
    selectedProjectId.value = project.id;
    currentSection.value = "tasks";
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-BR");
};
const logout = () => {
    router.post("/logout");
};
const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};
const exportToCSV = async () => {
    try {
        const params = {
            search: searchTerm.value,
            filter: filterValue.value,
        };
        const response = await axios.get("/api/tasks/export/csv", {
            params,
            responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute(
            "download",
            `tasks_export_${new Date().toISOString().slice(0, 10)}.csv`
        );
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Error exporting CSV:", error);
    }
};

const props = defineProps({ auth: Object });
const userName = props.auth?.user?.name || "User";
const userType = props.auth?.user?.user_type || "user";
const userInitial = userName.charAt(0).toUpperCase();
const currentUserId = props.auth?.user?.id;
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

.gradient-bg {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%);
}
</style>

<template>
    <Head title="Task Dashboard" />

    <div class="flex h-screen overflow-hidden bg-gray-100">
        <Sidebar
            :currentSection="currentSection"
            :sidebarOpen="sidebarOpen"
            :userName="userName"
            :userType="userType"
            :userInitial="userInitial"
            @show-section="showSection"
            @toggle-sidebar="toggleSidebar"
            @logout="logout"
        />

        <div class="flex-1 overflow-auto">
            <header
                class="bg-white shadow-sm md:hidden p-4 flex items-center justify-between"
            >
                <button @click="toggleSidebar">
                    <i class="fas fa-bars text-blue-600"></i>
                </button>
                <h1 class="text-xl font-bold text-blue-600">Task Manager</h1>
                <div class="w-6"></div>
            </header>
            <TasksSection
                v-if="currentSection === 'tasks'"
                :tasks="tasks"
                :loading="loading"
                :currentPage="currentPage"
                :totalPages="totalPages"
                :totalTasks="totalTasks"
                :tasksPerPage="tasksPerPage"
                :searchTerm="searchTerm"
                :filterValue="filterValue"
                :sortValue="sortValue"
                :projects="projects"
                :selectedProjectId="selectedProjectId"
                @update:searchTerm="(val) => (searchTerm = val)"
                @update:filterValue="(val) => (filterValue = val)"
                @update:sortValue="(val) => (sortValue = val)"
                @update:selectedProjectId="(val) => (selectedProjectId = val)"
                @open-task-modal="openTaskModal"
                @toggle-completion="toggleTaskCompletion"
                @edit-task="openTaskModal"
                @delete-task="confirmDeleteTask"
                @change-page="changePage"
                @export-csv="exportToCSV"
            />
            <AdminSection
                v-if="currentSection === 'admin' && userType === 'admin'"
                :users="users"
                :loadingUsers="loadingUsers"
                :isAdmin="userType === 'admin'"
                :currentUserId="currentUserId"
                :formatDate="formatDate"
                @open-user-modal="openUserModal"
                @edit-user="editUser"
                @delete-user="deleteUser"
            />
            <StatisticsSection
                v-if="currentSection === 'stats'"
                :statistics="statistics"
                :loadingStats="loadingStats"
            />
            <ProjectsSection
                v-if="currentSection === 'projects'"
                :projects="projects"
                :loading="loadingProjects"
                :currentPage="projectCurrentPage"
                :totalPages="projectTotalPages"
                @update:searchTerm="(val) => (projectSearchTerm = val)"
                @update:statusFilter="(val) => (projectStatusFilter = val)"
                @update:sortBy="(val) => (projectSortBy = val)"
                @open-project-modal="openProjectModal"
                @delete-project="confirmDeleteProject"
                @change-page="changeProjectPage"
                @select-project="selectProject"
            />
        </div>
    </div>

    <TaskModal
        :isOpen="showTaskModal"
        :task="currentTask"
        :users="users"
        :projects="projects"
        :isAdmin="userType === 'admin'"
        @close="closeTaskModal"
        @save="saveTask"
    />
    <UserModal
        :userModalOpen="userModalOpen"
        :userForm="userForm"
        @close="closeUserModal"
        @save-user="saveUser"
    />
    <ProjectModal
        :isOpen="projectModalOpen"
        :project="currentProject"
        @close="closeProjectModal"
        @save="saveProject"
    />
    <ConfirmModal
        :confirmModalOpen="confirmModalOpen"
        :confirmMessage="confirmMessage"
        @close="() => (confirmModalOpen.value = false)"
        @confirm="handleConfirm"
    />

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</template>
