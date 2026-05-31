# 🏢 ProviEmplea Backend API — Plataforma de Búsqueda Inversa de Talentos

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker)](https://www.docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://www.mysql.com)
[![Nginx](https://img.shields.io/badge/Nginx-1.27-009639?style=for-the-badge&logo=nginx)](https://nginx.org)
[![OpenAPI/Swagger](https://img.shields.io/badge/OpenAPI-3.0-89BF04?style=for-the-badge&logo=openapi-initiative)](https://swagger.io)

**ProviEmplea** es una plataforma pionera de **búsqueda inversa de empleo** desarrollada para el Departamento de Empleo de la Municipalidad de Providencia. A diferencia de las bolsas de trabajo tradicionales, en ProviEmplea son las empresas empleadoras validadas quienes buscan proactivamente a los candidatos idóneos para sus vacantes. 

Para garantizar la no discriminación arbitraria, transparencia y confidencialidad en los procesos de selección, la API implementa el principio de **CV Ciego**, ocultando datos sociodemográficos sensibles (nombres, género, edad, comuna de residencia) en las consultas públicas de reclutamiento, exponiendo únicamente habilidades, competencias, experiencia y preferencias laborales del talento.

🔗 Repositorio del Frontend: [FLAMEXone/proviemplea_eva3_frontend](https://github.com/FLAMEXone/proviemplea_eva3_frontend)

🔗 Deploy en Vercel: [https://proviemplea.vercel.app/](https://proviemplea.vercel.app/)

---

## 🚀 Requisitos y Setup Rápido

### 🛠️ Tecnologías y Versiones Utilizadas

La solución está construida sobre un stack moderno y optimizado utilizando las siguientes tecnologías y versiones específicas:

*   **Framework Backend:** PHP `8.2.x` (FPM) y Laravel `11.x`
*   **Base de Datos:** MySQL `8.0` (Almacenamiento relacional)
*   **Servidor Web Nginx:** Nginx `1.27-alpine` (Como servidor web proxy inverso)
*   **Orquestación de Contenedores:** Docker `20.10+` y Docker Compose `v2.x`
*   **Especificación de API:** OpenAPI `3.0` / Swagger UI (Implementado vía `darkaonline/l5-swagger` versión `8.6+` compatible con Laravel 11)

---

La infraestructura de desarrollo está completamente dockerizada, aislando cada uno de estos servicios para un despliegue libre de conflictos de dependencias locales.

### 📋 Requisitos Previos en el Host

Antes de intentar levantar la aplicación, asegúrate de tener instalado y configurado en tu máquina local:
1.  **Docker Desktop** (con soporte para contenedores Linux) instalado, iniciado y ejecutándose en segundo plano. *(Esencial, no se requiere tener instalado PHP ni MySQL localmente en tu sistema operativo host, todo corre dentro del contenedor).*
2.  **Git** instalado en tu consola local para poder clonar el repositorio.

---

### Paso 1: Clonar el Repositorio e Ingresar al Directorio
1.  Abre una terminal en tu máquina local y clona el repositorio del proyecto:
    ```bash
    git clone https://github.com/ginans/proviemplea_eva3.git
    ```
2.  Accede a la carpeta raíz del proyecto:
    ```bash
    cd proviemplea_eva3
    ```

### Paso 2: Configurar Variables de Entorno (.env)
1.  Duplica el archivo de configuración de entorno base:
    ```bash
    cp .env.example .env
    ```
2.  *(Opcional)* Si deseas cambiar credenciales, puedes abrir el archivo `.env` recién creado y modificar los valores de conexión de base de datos, aunque la configuración por defecto está lista para acoplarse con Docker.

### Paso 3: Levantar la Infraestructura en Docker
Asegúrate de que Docker Desktop esté corriendo en tu sistema y ejecuta el comando de construcción y levantamiento:
```bash
docker compose up -d --build
```

### Paso 4: Inicializar la Aplicación (Dentro del Contenedor)
Una vez que los contenedores estén levantados e indicados como `Started`, ejecuta las rutinas de inicialización de Laravel **dentro del contenedor `app`** para no requerir dependencias en tu host:
Ejecuta las siguientes rutinas de Laravel dentro del contenedor de la aplicación:

1. **Instalar dependencias y generar Application Key:**
   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   ```
2. **Ejecutar Migraciones de Base de Datos:**
   ```bash
   docker compose exec app php artisan migrate
   ```
3. **Generar Documentación Swagger (OpenAPI 3.0):**
   ```bash
   docker compose exec app php artisan l5-swagger:generate
   ```

---

## 📡 Puntos de Acceso Clave

Una vez levantado el sistema, se exponen los siguientes puertos locales:

*   **API Base URL:** [http://localhost:8080/api](http://localhost:8080/api)
*   **Documentación Swagger UI interactiva:** [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)
*   **Verificación de Salud (Health Check):** [http://localhost:8080/api/health](http://localhost:8080/api/health)

---

## 🛠️ Arquitectura de la API y Recursos

La API REST está diseñada bajo el estándar RESTful, gestionando tres recursos clave de negocio de forma desacoplada y estandarizada:

![](https://www.plantuml.com/plantuml/png/TP5FZzCy4CRl_XIZlj8hE6pJtMubsWFQ94gNbK14spyd93IoPxFM9dQmdI90V20EdBXnxHV3gLisRMWkZZNF_6QUnpVMeN5DLRAI7Xmu3KOMZmw4DCITr8hP9wbgD5X1Zlm92rguBoL1MqPLbDGH95vG6DqeaUfI6_XllliwYfWxRH9j19czv2eTPvDHJt9AojBvQ7xPaQqifMZfc3LzDKgcVL6VbimdywGxr1ZzULySNrzF9nbZs2u2WpLngJH4iCIIbDC3G0lvePfLjI6BuC-IK6Y3ktfsgCUYaafQPv3BxIy5xnebkQontCbYnlxc28FSw8qC88ZpExZF5ii1V6FWlyOIHzkAtY-M464jevgCrGhj1_NYdefsXeR_8IVp8CkNOKkl_LYVzAzEsgEP51xXOs-mb5_3dll-bMy_Dvw2ywFngJQrDkZaHdSse6F4RTOta6n_sqyD2GIk5QHhI2KLUuRNzxQ8b-cR09aPuetdj3QYZNSWq7ZWEln1ffm_1Td0MiTlp_d1zgKYFzwrJzSkTKlMIQqyvKuvCXK96TBfUEDdl0MQ1w2Gdi9tBhFsklqPxa3UAFB7xOzdcvPzPoo7YulNWLx332w7SCZtK5g5ojKGZb-DiUovbuvxGoc46Nh_dJbZyKxeamg0Nf91iBSVK6UJ2jhWpKl7GpYJ0RiX9Pgg_0C0)

### Tabla Resumen de Endpoints

| Método | Endpoint | Descripción | Códigos Esperados |
| :--- | :--- | :--- | :--- |
| **GET** | `/api/health` | Estado operacional y salud del microservicio | `200` |
| **GET** | `/api/personas` | Listar talentos validados en formato **CV Ciego** (con filtros) | `200`, `429` |
| **POST** | `/api/personas` | Registrar un nuevo talento (genera `codigo_talento` y completitud) | `201`, `422` |
| **GET** | `/api/personas/{id}` | Consultar perfil privado completo (nombres, teléfono, etc.) | `200`, `404` |
| **PUT** | `/api/personas/{id}` | Actualizar datos del talento | `200`, `404`, `422` |
| **DELETE**| `/api/personas/{id}` | Desactivación lógica de perfil (Soft Delete `activo = false`) | `200`, `404` |
| **PATCH**| `/api/personas/{id}/validar`| Validar talento para exposición en vitrina pública | `200`, `404` |
| **GET** | `/api/empresas` | Listar empresas registradas y activas | `200` |
| **POST** | `/api/empresas` | Registrar una empresa empleadora (valida RUT chileno único) | `201`, `422` |
| **GET** | `/api/empresas/{id}` | Consultar detalle de empresa corporativa | `200`, `404` |
| **PUT** | `/api/empresas/{id}` | Actualizar datos de empresa | `200`, `404`, `422` |
| **DELETE**| `/api/empresas/{id}` | Desactivación lógica de empresa | `200`, `404` |
| **PATCH**| `/api/empresas/{id}/validar`| Validar empresa para permitir búsquedas | `200`, `404` |
| **GET** | `/api/admin/contactos`| Listar solicitudes de intermediación (con filtros de estado) | `200`, `429` |
| **POST** | `/api/admin/contactos`| Crear una solicitud de contacto (evita colisiones activas) | `201`, `409`, `422` |
| **PATCH**| `/api/admin/contactos/{id}/estado`| Avanzar estado del flujo de selección (fechas auto-registradas) | `200`, `404`, `422` |
| **GET** | `/api/admin/estadisticas`| Resumen estadístico consolidado **(Almacenado en Caché)** | `200`, `429` |

---

## 🚀 Detalles de Rendimiento y Optimización

Para cumplir con el estándar **Sobresaliente** en los indicadores de **Optimización y Depuración de Errores** de la rúbrica, implementamos una serie de defensas técnicas y mejoras arquitectónicas en el core de Laravel 11:

### 🛡️ 1. Limitación de Tasa (Rate Limiting)
*   **Diseño Técnico:** Protegimos la API contra abusos, raspado de datos (*scraping*) y ataques DoS. Definimos un limitador de tasa en `AppServiceProvider` que restringe el tráfico a un máximo de **60 peticiones por minuto** por dirección IP o ID de usuario autenticado.
*   **Respuesta Estandarizada:** Al superarse el umbral, el servidor detiene preventivamente la petición y retorna un código `429 Too Many Requests` con las cabeceras HTTP de control estándar:
    *   `X-RateLimit-Limit`: `60`
    *   `X-RateLimit-Remaining`: `0`
    *   `Retry-After`: Segundos restantes para que expire la penalización.

### ⚡ 2. Estrategia de Caché de Base de Datos Reactiva
*   **La Problemática:** El endpoint `/api/admin/estadisticas` realiza múltiples operaciones de agregación y conteo relacional sobre la base de datos (`count()` sobre tablas de personas, empresas, intermediaciones y agrupaciones de estado). Ejecutar estas consultas pesadas en cada click del panel de control degrada gravemente el rendimiento.
*   **La Solución:** Implementamos caché del lado del servidor en `AdministracionController` mediante la fachada `Cache::remember('admin_estadisticas', 300, ...)` con un tiempo de vida (TTL) de **5 minutos (300 segundos)**.
*   **Coherencia de Datos (Invalidación Reactiva):** Para evitar que el administrador trabaje con información inconsistente u obsoleta, acoplamos eventos del ciclo de vida de Eloquent en los modelos `Persona`, `Empresa` y `ContactoSolicitado`. Al dispararse una inserción (`saved`), edición o desactivación (`deleted`), el modelo vacía la caché proactivamente en segundo plano con `Cache::forget('admin_estadisticas')`, garantizando que la siguiente llamada a estadísticas cargue datos 100% frescos y actualizados de manera automática.

### 🛠️ 3. Manejo Global y Preventivo de Excepciones (Laravel 11 Style)
Aprovechando la nueva estructura de bootstrapeo de Laravel 11, encapsulamos todas las excepciones de la API en el closure `withExceptions` en `bootstrap/app.php` utilizando renderizadores personalizados. Esto garantiza que ningún error imprevisto rompa la consistencia del contrato de respuesta JSON:
*   **404 Not Found:** Intercepta `NotFoundHttpException` y `ModelNotFoundException` (cuando se busca un recurso con un ID inexistente) y devuelve `{"success": false, "message": "El recurso solicitado no fue encontrado."}`.
*   **405 Method Not Allowed:** Intercepta llamadas con verbos HTTP incorrectos y devuelve `{"success": false, "message": "Método HTTP no permitido para esta ruta."}`.
*   **422 Unprocessable Entity:** Convierte excepciones de validación nativas a nuestro formato JSON oficial, anexando el mapa de validación detallado en la clave `"errors"`.
*   **500 Internal Server Error:** Para excepciones de código imprevistas o caídas de infraestructura en entornos de producción (`app.debug = false`), se oculta de forma segura la pila de llamadas (*stack trace*), previniendo la fuga de información sensible del servidor y devolviendo un mensaje genérico. En desarrollo local (`app.debug = true`), se permite la visualización detallada del trace para facilitar la depuración ágil.

---

## 👥 Integrantes y Autoría

*   **Nombre del Equipo:** *Grupo 1*
*   **Integrantes:**
    *   Gina Norambuena Sánchez
    *   Fabian Malinarich Piña

---

### 🤖 Colaboración y Asistencia de IA

Este proyecto backend ha sido desarrollado en una modalidad de **Pair Programming** asistida por **Gem**, un modelo de Inteligencia Artificial de Google DeepMind.

*   **Alcance del Soporte:** La IA actuó como consultor técnico y tutor en la estructuración de la estrategia de ramas en Git, la organización de hitos y tareas, la adopción de buenas prácticas arquitectónicas en Laravel 11, en la revisión de cobertura de la documentación Swagger/OpenAPI 3.0 y documentacion de este README.
*   **Desarrollo del Proyecto:** La lógica de negocio, codificación de controladores, modelado relacional de datos y la intermediación laboral de ProviEmplea fueron diseñadas, implementadas y verificadas íntegramente por los integrantes del **Grupo 1**.


