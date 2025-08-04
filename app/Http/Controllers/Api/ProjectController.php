<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Project\ListProjectsService;
use App\Services\Project\StoreProjectService;
use App\Services\Project\ShowProjectService;
use App\Services\Project\UpdateProjectService;
use App\Services\Project\DeleteProjectService;
use App\Services\Project\ProjectStatisticsService;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/projects",
     *     summary="Listar projetos",
     *     description="Retorna uma lista de projetos do usuário autenticado",
     *     operationId="getProjects",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de projetos recuperada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Project"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index(Request $request, ListProjectsService $listProjects): JsonResponse
    {
        try {
            $result = $listProjects->handle($request);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @OA\Post(
     *     path="/projects",
     *     summary="Criar um novo projeto",
     *     description="Cria um novo projeto",
     *     operationId="createProject",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *             @OA\Property(property="name", type="string", example="Projeto ABC"),
     *             @OA\Property(property="description", type="string", example="Descrição do projeto"),
     *             @OA\Property(property="color", type="string", example="#FF5733"),
     *             @OA\Property(property="status", type="string", enum={"active", "completed", "on_hold"}, example="active"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-07-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Projeto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Project created successfully"),
     *             @OA\Property(property="project", ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request, StoreProjectService $storeProject): JsonResponse
    {
        try {
            $project = $storeProject->handle($request);
            return response()->json([
                'message' => 'Project created successfully',
                'project' => $project
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @OA\Get(
     *     path="/projects/{id}",
     *     summary="Obter um projeto específico",
     *     description="Retorna os detalhes de um projeto específico",
     *     operationId="getProject",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do projeto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Projeto encontrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Projeto não encontrado"
     *     )
     * )
     */
    public function show(Project $project, ShowProjectService $showProject): JsonResponse
    {
        try {
            $project = $showProject->handle($project);
            return response()->json($project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, UpdateProjectService $updateProject): JsonResponse
    {
        try {
            $project = $updateProject->handle($project, $request);
            return response()->json([
                'message' => 'Project updated successfully',
                'project' => $project
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, DeleteProjectService $deleteProject): JsonResponse
    {
        try {
            $deleteProject->handle($project);
            return response()->json([
                'message' => 'Project deleted successfully'
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project statistics
     * 
     * @OA\Get(
     *     path="/projects/{id}/statistics",
     *     summary="Obter estatísticas do projeto",
     *     description="Retorna estatísticas detalhadas de um projeto específico",
     *     operationId="getProjectStatistics",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do projeto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas do projeto recuperadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_tasks", type="integer", example=20),
     *             @OA\Property(property="completed_tasks", type="integer", example=15),
     *             @OA\Property(property="pending_tasks", type="integer", example=3),
     *             @OA\Property(property="in_progress_tasks", type="integer", example=2),
     *             @OA\Property(property="completion_percentage", type="number", format="float", example=75.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Projeto não encontrado"
     *     )
     * )
     */
    public function statistics(Project $project, ProjectStatisticsService $projectStats): JsonResponse
    {
        try {
            $stats = $projectStats->handle($project);
            return response()->json($stats);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading project statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
