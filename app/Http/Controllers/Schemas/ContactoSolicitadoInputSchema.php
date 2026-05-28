<?php

namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ContactoSolicitadoInput",
    title: "Contacto Solicitado Input",
    required: ["empresa_id", "persona_id"]
)]
class ContactoSolicitadoInputSchema
{
    #[OA\Property(property: "empresa_id", type: "string", format: "uuid", example: "550e8400-e29b-41d4-a716-446655440000")]
    public string $empresa_id;

    #[OA\Property(property: "persona_id", type: "string", format: "uuid", example: "550e8400-e29b-41d4-a716-446655440000")]
    public string $persona_id;

    #[OA\Property(property: "notas_admin", type: "string", nullable: true)]
    public ?string $notas_admin;
}
