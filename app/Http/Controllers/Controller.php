<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="TODO API",
 *     version="1.0.0",
 *     description="API para gerenciamento de tarefas, projetos e usuários",
 *     @OA\Contact(
 *         email="admin@todoapp.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8001/api",
 *     description="Servidor de desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
