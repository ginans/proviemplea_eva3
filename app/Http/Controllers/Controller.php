<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "ProviEmplea API",
    description: "API REST para la plataforma de empleo ProviEmplea de Providencia. Permite gestionar talentos, empresas y procesos de selección con curriculum ciego.\n\n### Rendimiento y Optimización\n- **Limitación de Tasa (Rate Limiting):** Todas las solicitudes de la API están sujetas a un límite de **60 peticiones por minuto** por dirección IP o usuario autenticado. Al exceder este límite, el servidor retornará un estado `429 Too Many Requests` acompañado de cabeceras de control (`X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After`).\n- **Estrategia de Caché:** El endpoint de estadísticas administrativas (`GET /api/admin/estadisticas`) implementa almacenamiento en caché del servidor con un tiempo de vida (TTL) de **5 minutos (300 segundos)**. La caché se invalida de forma proactiva y automática ante cualquier creación, modificación o desactivación de perfiles, empresas o intermediaciones.\n- **Guía de Consumo Eficiente:** Se recomienda a los sistemas consumidores almacenar localmente los datos de estadísticas para no generar peticiones redundantes antes de que expire el TTL del caché en el servidor, garantizando así un rendimiento óptimo de la aplicación."
)]
#[OA\Server(
    url: "http://localhost:8080/api",
    description: "Servidor de desarrollo local"
)]
#[OA\Tag(name: "Health",        description: "Endpoints de salud del sistema")]
#[OA\Tag(name: "Personas",      description: "Gestión de perfiles de talentos/vecinos")]
#[OA\Tag(name: "Empresas",      description: "Gestión de empresas empleadoras")]
#[OA\Tag(name: "Administración",description: "Gestión administrativa y seguimiento")]
abstract class Controller
{
    use ApiResponse;
}
