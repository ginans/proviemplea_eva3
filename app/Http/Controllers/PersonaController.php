<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class PersonaController extends Controller
{
    #[OA\Get(
        path: "/personas",
        operationId: "getPersonas",
        tags: ["Personas"],
        summary: "Listar personas (CV ciego)",
        description: "Obtiene talentos activos en formato de CV ciego (sin datos personales identificables)."
    )]
    #[OA\Parameter(name: "validado", in: "query", required: false, description: "Filtrar por validación", schema: new OA\Schema(type: "boolean"))]
    #[OA\Parameter(name: "nivel_educacional", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["basica","media","tecnica","universitaria","postgrado"]))]
    #[OA\Response(
        response: 200,
        description: "Listado exitoso",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/PersonaCVCiego"))
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Persona::where('activo', true);

        if ($request->has('validado')) {
            $query->where('validado', $request->boolean('validado'));
        }
        if ($request->has('nivel_educacional')) {
            $query->where('nivel_educacional', $request->input('nivel_educacional'));
        }

        return $this->successResponse($query->get()->map(fn($p) => $p->getCvCiego()));
    }

    #[OA\Post(
        path: "/personas",
        operationId: "createPersona",
        tags: ["Personas"],
        summary: "Registrar nueva persona/talento",
        description: "Crea un perfil de talento. El código se genera automáticamente."
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/PersonaInput")
    )]
    #[OA\Response(
        response: 201,
        description: "Persona creada",
        content: new OA\JsonContent(ref: "#/components/schemas/Persona")
    )]
    #[OA\Response(response: 422, description: "Errores de validación")]
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'                => 'required|email|unique:personas,email',
            'telefono'             => 'nullable|string|max:15',
            'resumen'              => 'nullable|string',
            'nivel_educacional'    => 'nullable|in:basica,media,tecnica,universitaria,postgrado',
            'titulo_carrera'       => 'nullable|string',
            'anio_egreso'          => 'nullable|integer|min:1950|max:' . date('Y'),
            'anios_experiencia'    => 'nullable|integer|min:0',
            'areas_experiencia'    => 'nullable|array',
            'competencias'         => 'nullable|array',
            'rango_renta'          => 'nullable|string',
            'tipo_jornada'         => 'nullable|in:completa,part-time,por-horas',
            'modalidad'            => 'nullable|in:presencial,remoto,hibrido',
            'cursos'               => 'nullable|array',
            'idiomas'              => 'nullable|array',
            'portafolio_url'       => 'nullable|url',
            'persona_discapacidad' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $data['codigo_talento'] = $this->generarCodigoTalento();
        $data['porcentaje_completitud'] = $this->calcularCompletitud($data);

        return $this->successResponse(Persona::create($data), 201);
    }

    #[OA\Get(
        path: "/personas/{id}",
        operationId: "getPersona",
        tags: ["Personas"],
        summary: "Obtener persona por ID"
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "Persona encontrada",
        content: new OA\JsonContent(ref: "#/components/schemas/Persona")
    )]
    #[OA\Response(response: 404, description: "No encontrada")]
    public function show(string $persona): JsonResponse
    {
        $model = Persona::find($persona);
        if (!$model) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }
        return $this->successResponse($model);
    }

    #[OA\Put(
        path: "/personas/{id}",
        operationId: "updatePersona",
        tags: ["Personas"],
        summary: "Actualizar persona"
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/PersonaInput")
    )]
    #[OA\Response(
        response: 200,
        description: "Persona actualizada",
        content: new OA\JsonContent(ref: "#/components/schemas/Persona")
    )]
    #[OA\Response(response: 404, description: "No encontrada")]
    public function update(Request $request, string $persona): JsonResponse
    {
        $model = Persona::find($persona);
        if (!$model) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }

        $validator = Validator::make($request->all(), [
            'email'                => 'sometimes|email|unique:personas,email,' . $model->id,
            'telefono'             => 'nullable|string|max:15',
            'resumen'              => 'nullable|string',
            'nivel_educacional'    => 'nullable|in:basica,media,tecnica,universitaria,postgrado',
            'titulo_carrera'       => 'nullable|string',
            'anio_egreso'          => 'nullable|integer|min:1950|max:' . date('Y'),
            'anios_experiencia'    => 'nullable|integer|min:0',
            'areas_experiencia'    => 'nullable|array',
            'competencias'         => 'nullable|array',
            'rango_renta'          => 'nullable|string',
            'tipo_jornada'         => 'nullable|in:completa,part-time,por-horas',
            'modalidad'            => 'nullable|in:presencial,remoto,hibrido',
            'cursos'               => 'nullable|array',
            'idiomas'              => 'nullable|array',
            'portafolio_url'       => 'nullable|url',
            'persona_discapacidad' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Los datos enviados no son válidos.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $data['porcentaje_completitud'] = $this->calcularCompletitud(array_merge($model->toArray(), $data));
        $model->update($data);

        return $this->successResponse($model->fresh());
    }

    #[OA\Patch(
        path: "/personas/{id}/validar",
        operationId: "validarPersona",
        tags: ["Personas"],
        summary: "Validar persona (solo administración)",
        description: "Marca a una persona como validada para que aparezca en la vitrina."
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "Persona validada",
        content: new OA\JsonContent(ref: "#/components/schemas/Persona")
    )]
    #[OA\Response(response: 404, description: "No encontrada")]
    public function validar(string $persona): JsonResponse
    {
        $model = Persona::find($persona);
        if (!$model) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }
        $model->update(['validado' => true]);
        return $this->successResponse($model->fresh());
    }

    #[OA\Delete(
        path: "/personas/{id}",
        operationId: "deletePersona",
        tags: ["Personas"],
        summary: "Desactivar persona",
        description: "Desactiva el perfil sin eliminarlo de la base de datos."
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Persona desactivada")]
    #[OA\Response(response: 404, description: "No encontrada")]
    public function destroy(string $persona): JsonResponse
    {
        $model = Persona::find($persona);
        if (!$model) {
            return $this->errorResponse('Persona no encontrada.', 404);
        }
        $model->update(['activo' => false]);
        return $this->successResponse(['message' => 'Persona desactivada exitosamente.']);
    }

    private function generarCodigoTalento(): string
    {
        do {
            $codigo = 'PROV-' . date('Y') . '-' . strtoupper(Str::random(4));
        } while (Persona::where('codigo_talento', $codigo)->exists());
        return $codigo;
    }

    private function calcularCompletitud(array $data): int
    {
        $campos = ['email','telefono','resumen','nivel_educacional','titulo_carrera',
                   'anio_egreso','anios_experiencia','competencias','rango_renta',
                   'tipo_jornada','modalidad'];
        $completados = count(array_filter($campos, fn($c) => !empty($data[$c])));
        return (int) round(($completados / count($campos)) * 100);
    }
}
