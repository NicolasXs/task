<?php

namespace App\Http\Controllers;

use App\Services\Task\DeleteTaskAction;
use App\Services\Task\ExportTasksAction;
use App\Services\Task\ListTasksAction;
use App\Services\Task\ShowTaskAction;
use App\Services\Task\StoreTaskAction;
use App\Services\Task\TaskStatisticsAction;
use App\Services\Task\ToggleTaskAction;
use App\Services\Task\UpdateTaskAction;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of tasks
     * 
     * @OA\Get(
     *     path="/tasks",
     *     summary="Listar todas as tarefas",
     *     description="Retorna uma lista paginada de tarefas do usuário autenticado",
     *     operationId="getTasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas recuperada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $result = app(ListTasksAction::class)->handle($request);
        return response()->json($result);
    }

    /**
     * Store a newly created task
     * 
     * @OA\Post(
     *     path="/tasks",
     *     summary="Criar uma nova tarefa",
     *     description="Cria uma nova tarefa",
     *     operationId="createTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string", example="Minha nova tarefa"),
     *             @OA\Property(property="description", type="string", example="Descrição da tarefa"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="medium"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-07-30"),
     *             @OA\Property(property="project_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarefa criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $task = app(StoreTaskAction::class)->handle($request);
        return response()->json($task, 201);
    }

    /**
     * Display the specified task
     * 
     * @OA\Get(
     *     path="/tasks/{id}",
     *     summary="Obter uma tarefa específica",
     *     description="Retorna os detalhes de uma tarefa específica",
     *     operationId="getTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa encontrada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task = app(ShowTaskAction::class)->handle($task);
        return response()->json($task);
    }

    /**
     * Update the specified task
     * 
     * @OA\Put(
     *     path="/tasks/{id}",
     *     summary="Atualizar uma tarefa",
     *     description="Atualiza uma tarefa existente",
     *     operationId="updateTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Tarefa atualizada"),
     *             @OA\Property(property="description", type="string", example="Nova descrição"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="high"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-08-01"),
     *             @OA\Property(property="assigned_to", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado"
     *     )
     * )
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = app(UpdateTaskAction::class)->handle($request, $task);
        return response()->json($task);
    }

    /**
     * Remove the specified task
     * 
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     summary="Excluir uma tarefa",
     *     description="Remove uma tarefa do sistema",
     *     operationId="deleteTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa excluída com sucesso",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Tarefa excluída com sucesso"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado"
     *     )
     * )
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $result = app(DeleteTaskAction::class)->handle($task);
        return response()->json($result);
    }

    /**
     * Toggle task completion status
     * 
     * @OA\Patch(
     *     path="/tasks/{id}/toggle",
     *     summary="Alternar status da tarefa",
     *     description="Alterna o status de uma tarefa entre pendente e concluída",
     *     operationId="toggleTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status da tarefa alterado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado"
     *     )
     * )
     */
    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        $task = app(ToggleTaskAction::class)->handle($task);
        return response()->json($task);
    }

    /**
     * Get task statistics
     * 
     * @OA\Get(
     *     path="/tasks/statistics",
     *     summary="Obter estatísticas das tarefas",
     *     description="Retorna estatísticas sobre as tarefas do usuário",
     *     operationId="getTaskStatistics",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas recuperadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=50),
     *             @OA\Property(property="completed", type="integer", example=30),
     *             @OA\Property(property="pending", type="integer", example=15),
     *             @OA\Property(property="in_progress", type="integer", example=5),
     *             @OA\Property(property="overdue", type="integer", example=3)
     *         )
     *     )
     * )
     */
    public function statistics()
    {
        $result = app(TaskStatisticsAction::class)->handle();
        return response()->json($result);
    }

    /**
     * Export tasks to CSV
     * 
     * @OA\Get(
     *     path="/tasks/export/csv",
     *     summary="Exportar tarefas para CSV",
     *     description="Exporta todas as tarefas do usuário para um arquivo CSV",
     *     operationId="exportTasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo CSV gerado com sucesso",
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     )
     * )
     */
    public function export()
    {
        return app(ExportTasksAction::class)->handle();
    }
}
