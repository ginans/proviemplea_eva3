<?php

namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PersonaInput",
    title: "Persona Input",
    description: "Datos para crear o actualizar una persona/talento",
    required: ["email"]
)]
class PersonaInputSchema
{
    #[OA\Property(property: "email", type: "string", format: "email", example: "juan@example.com")]
    public string $email;

    #[OA\Property(property: "telefono", type: "string", example: "+56912345678", nullable: true)]
    public ?string $telefono;

    #[OA\Property(property: "resumen", type: "string", nullable: true)]
    public ?string $resumen;

    #[OA\Property(property: "nivel_educacional", type: "string", enum: ["basica","media","tecnica","universitaria","postgrado"], nullable: true)]
    public ?string $nivel_educacional;

    #[OA\Property(property: "titulo_carrera", type: "string", nullable: true)]
    public ?string $titulo_carrera;

    #[OA\Property(property: "anio_egreso", type: "integer", example: 2020, nullable: true)]
    public ?int $anio_egreso;

    #[OA\Property(property: "anios_experiencia", type: "integer", example: 3, nullable: true)]
    public ?int $anios_experiencia;

    #[OA\Property(property: "areas_experiencia", type: "array", items: new OA\Items(type: "string"), example: ["Desarrollo Web"], nullable: true)]
    public ?array $areas_experiencia;

    #[OA\Property(property: "competencias", type: "array", items: new OA\Items(type: "string"), example: ["PHP", "Laravel"], nullable: true)]
    public ?array $competencias;

    #[OA\Property(property: "rango_renta", type: "string", example: "500k-800k", nullable: true)]
    public ?string $rango_renta;

    #[OA\Property(property: "tipo_jornada", type: "string", enum: ["completa","part-time","por-horas"], nullable: true)]
    public ?string $tipo_jornada;

    #[OA\Property(property: "modalidad", type: "string", enum: ["presencial","remoto","hibrido"], nullable: true)]
    public ?string $modalidad;

    #[OA\Property(property: "persona_discapacidad", type: "boolean", example: false, nullable: true)]
    public ?bool $persona_discapacidad;

    #[OA\Property(property: "portafolio_url", type: "string", format: "url", nullable: true)]
    public ?string $portafolio_url;
}
