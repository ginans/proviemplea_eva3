<?php

namespace App\Http\Controllers\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "EmpresaInput",
    title: "Empresa Input",
    description: "Datos para crear o actualizar una empresa",
    required: ["nombre_empresa", "rut_empresa", "email", "tipo_empresa", "contacto_nombre", "contacto_email"]
)]
class EmpresaInputSchema
{
    #[OA\Property(property: "nombre_empresa", type: "string", example: "TechCorp SpA")]
    public string $nombre_empresa;

    #[OA\Property(property: "rut_empresa", type: "string", example: "76123456-7")]
    public string $rut_empresa;

    #[OA\Property(property: "email", type: "string", format: "email", example: "rrhh@techcorp.cl")]
    public string $email;

    #[OA\Property(property: "logo_url", type: "string", format: "url", nullable: true)]
    public ?string $logo_url;

    #[OA\Property(property: "rubro", type: "string", example: "Tecnología", nullable: true)]
    public ?string $rubro;

    #[OA\Property(property: "tipo_empresa", type: "string", enum: ["contratacion-directa","est","outsourcing"])]
    public string $tipo_empresa;

    #[OA\Property(property: "presentacion", type: "string", nullable: true)]
    public ?string $presentacion;

    #[OA\Property(property: "beneficios", type: "array", items: new OA\Items(type: "string"), nullable: true)]
    public ?array $beneficios;

    #[OA\Property(property: "contacto_nombre", type: "string", example: "Ana López")]
    public string $contacto_nombre;

    #[OA\Property(property: "contacto_email", type: "string", format: "email", example: "ana@techcorp.cl")]
    public string $contacto_email;

    #[OA\Property(property: "contacto_telefono", type: "string", nullable: true)]
    public ?string $contacto_telefono;
}
