/**
* @OA\Schema(
* title="LoginResponse",
* description="Resposta do login",
* @OA\Property(property="access_token", type="string", example="1|abcd1234efgh5678ijkl"),
* @OA\Property(property="token_type", type="string", example="Bearer"),
* @OA\Property(property="user", ref="#/components/schemas/User")
* )
*/