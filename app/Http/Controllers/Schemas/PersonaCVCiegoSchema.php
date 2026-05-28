<?php

namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PersonaCVCiego",
    title: "Persona CV Ciego",
    description: "Vista pública del talento sin datos personales identificables"
)]
class PersonaCVCiegoSchema
{
    #[OA\Property(property: "id", type: "string", format: "uuid", example: "550e8400-e29b-41d4-a716-446655440000")]
    public string $id;

    #[OA\Property(property: "codigo_talento", type: "string", example: "PROV-2026-A1B2")]
    public string $codigo_talento;

    #[OA\Property(property: "resumen", type: "string", nullable: true)]
    public ?string $resumen;

    #[OA\Property(property: "nivel_educacional", type: "string", nullable: true)]
    public ?string $nivel_educacional;

    #[OA\Property(property: "titulo_carrera", type: "string", nullable: true)]
    public ?string $titulo_carrera;

    #[OA\Property(property: "anio_egreso", type: "integer", nullable: true)]
    public ?int $anio_egreso;

    #[OA\Property(property: "anios_experiencia", type: "integer", example: 5)]
    public int $anios_experiencia;

    #[OA\Property(property: "tipo_jornada", type: "string", nullable: true)]
    public ?string $tipo_jornada;

    #[OA\Property(property: "modalidad", type: "string", nullable: true)]
    public ?string $modalidad;

    #[OA\Property(property: "persona_discapacidad", type: "boolean", example: false)]
    public bool $persona_discapacidad;
}
