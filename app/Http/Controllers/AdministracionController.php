<?php

namespace App\Http\Controllers;

use App\Models\ContactoSolicitado;
use App\Models\Empresa;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class AdministracionController extends Controller
{
    #[OA\Get(
        path: "/admin/contactos",
        operationId: "getContactosSolicitados",
        tags: ["Administración"],
        summary: "Listar contactos solicitados"
    )]
    #[OA\Parameter(name: "estado", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["pendiente","contactado","entrevista","seleccionado","no-seleccionado","proceso-cerrado"]))]
    #[OA\Response(
        response: 200,
        description: "Listado exitoso",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/ContactoSolicitado"))
    )]
    #[OA\Response(
        response: 429,
        description: "Demasiadas peticiones (Rate Limiting). Se permite un máximo de 60 peticiones por minuto por IP/Usuario.",
        headers: [
            new OA\Header(header: "X-RateLimit-Limit", schema: new OA\Schema(type: "integer"), description: "Límite máximo permitido por minuto"),
            new OA\Header(header: "X-RateLimit-Remaining", schema: new OA\Schema(type: "integer"), description: "Peticiones restantes disponibles en el bloque actual"),
            new OA\Header(header: "Retry-After", schema: new OA\Schema(type: "integer"), description: "Segundos a esperar antes de reintentar")
        ]
    )]
    public function listarContactos(Request $request): JsonResponse
    {
        $query = ContactoSolicitado::with(['empresa', 'persona']);
        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }
        return $this->successResponse($query->orderBy('created_at', 'desc')->get());
    }

    #[OA\Post(
        path: "/admin/contactos",
        operationId: "crearContactoSolicitado",
        tags: ["Administración"],
        summary: "Registrar solicitud de contacto",
        description: "Una empresa solicita contactar a un talento. No puede existir una solicitud activa previa."
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/ContactoSolicitadoInput")
    )]
    #[OA\Response(
        response: 201,
        description: "Contacto registrado",
        content: new OA\JsonContent(ref: "#/components/schemas/ContactoSolicitado")
    )]
    #[OA\Response(response: 409, description: "Ya existe una solicitud activa")]
    #[OA\Response(response: 422, description: "Errores de validación")]
    #[OA\Response(response: 429, description: "Demasiadas peticiones")]
    public function crearContacto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'empresa_id'  => 'required|exists:empresas,id',
            'persona_id'  => 'required|exists:personas,id',
            'notas_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $existente = ContactoSolicitado::where('empresa_id', $request->empresa_id)
            ->where('persona_id', $request->persona_id)
            ->whereNotIn('estado', ['no-seleccionado', 'proceso-cerrado'])
            ->first();

        if ($existente) {
            return $this->errorResponse('Ya existe una solicitud activa entre esta empresa y talento.', 409);
        }

        $contacto = ContactoSolicitado::create($validator->validated());
        return $this->successResponse($contacto->load(['empresa', 'persona']), 201);
    }

    #[OA\Patch(
        path: "/admin/contactos/{id}/estado",
        operationId: "actualizarEstadoContacto",
        tags: ["Administración"],
        summary: "Actualizar estado de contacto",
        description: "Cambia el estado del proceso. Las fechas se registran automáticamente según el estado."
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["estado"],
            properties: [
                new OA\Property(property: "estado", type: "string", enum: ["pendiente","contactado","entrevista","seleccionado","no-seleccionado","proceso-cerrado"]),
                new OA\Property(property: "notas_admin", type: "string", nullable: true)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Estado actualizado",
        content: new OA\JsonContent(ref: "#/components/schemas/ContactoSolicitado")
    )]
    #[OA\Response(response: 404, description: "Contacto no encontrado")]
    #[OA\Response(response: 429, description: "Demasiadas peticiones")]
    public function actualizarEstado(Request $request, string $contacto): JsonResponse
    {
        $model = ContactoSolicitado::find($contacto);
        if (!$model) {
            return $this->errorResponse('Contacto no encontrado.', 404);
        }

        $validator = Validator::make($request->all(), [
            'estado'      => 'required|in:pendiente,contactado,entrevista,seleccionado,no-seleccionado,proceso-cerrado',
            'notas_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();

        if ($data['estado'] === 'contactado' && !$model->fecha_contacto) {
            $data['fecha_contacto'] = now();
        } elseif ($data['estado'] === 'entrevista' && !$model->fecha_entrevista) {
            $data['fecha_entrevista'] = now();
        } elseif (in_array($data['estado'], ['seleccionado', 'no-seleccionado']) && !$model->fecha_resultado) {
            $data['fecha_resultado'] = now();
        }

        $model->update($data);
        return $this->successResponse($model->load(['empresa', 'persona']));
    }

    #[OA\Get(
        path: "/admin/estadisticas",
        operationId: "getEstadisticas",
        tags: ["Administración"],
        summary: "Estadísticas generales de la plataforma (Cached)",
        description: "Retorna el resumen acumulado de perfiles, empresas y estados de intermediación en la plataforma. Para optimizar el rendimiento y disminuir cargas en el servidor, este endpoint almacena los resultados en una caché del lado del servidor durante 5 minutos (300 segundos). Recomendación al consumidor: evitar llamadas repetitivas en intervalos menores a este periodo para un rendimiento óptimo. La caché se invalida proactivamente al registrar o editar personas, empresas o intermediaciones."
    )]
    #[OA\Response(
        response: 200,
        description: "Estadísticas generadas exitosamente",
        headers: [
            new OA\Header(header: "Cache-Control", schema: new OA\Schema(type: "string", example: "public, max-age=300"), description: "Directiva de caché que indica que la respuesta es almacenable")
        ],
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "total_personas", type: "integer", example: 45),
                new OA\Property(property: "personas_validadas", type: "integer", example: 38),
                new OA\Property(property: "total_empresas", type: "integer", example: 12),
                new OA\Property(property: "empresas_validadas", type: "integer", example: 10),
                new OA\Property(property: "contactos_pendientes", type: "integer", example: 5),
                new OA\Property(property: "contactos_en_proceso", type: "integer", example: 8),
                new OA\Property(property: "contactos_exitosos", type: "integer", example: 15)
            ]
        )
    )]
    #[OA\Response(
        response: 429,
        description: "Demasiadas peticiones (Rate Limiting)",
        headers: [
            new OA\Header(header: "X-RateLimit-Limit", schema: new OA\Schema(type: "integer"), description: "Límite por minuto"),
            new OA\Header(header: "X-RateLimit-Remaining", schema: new OA\Schema(type: "integer"), description: "Restantes")
        ]
    )]
    public function estadisticas(): JsonResponse
    {
        $stats = Cache::remember('admin_estadisticas', 300, function () {
            return [
                'total_personas'       => Persona::count(),
                'personas_validadas'   => Persona::where('validado', true)->count(),
                'total_empresas'       => Empresa::count(),
                'empresas_validadas'   => Empresa::where('validado', true)->count(),
                'contactos_pendientes' => ContactoSolicitado::where('estado', 'pendiente')->count(),
                'contactos_en_proceso' => ContactoSolicitado::whereIn('estado', ['contactado', 'entrevista'])->count(),
                'contactos_exitosos'   => ContactoSolicitado::where('estado', 'seleccionado')->count(),
            ];
        });

        return $this->successResponse($stats);
    }
}
