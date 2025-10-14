# Rimanaq Backend - LMS para Aprendizaje de Lengua de Señas

## Descripción del Proyecto

**Rimanaq Backend** es un sistema de gestión de aprendizaje (LMS) diseñado específicamente para la enseñanza de lenguaje de señas. Este proyecto funciona como el backend y panel administrativo de una aplicación más amplia, proporcionando APIs RESTful para la gestión de cursos, lecciones, gestos y seguimiento del progreso de aprendizaje.

El sistema está construido con **Laravel 12** y está diseñado para trabajar en conjunto con una aplicación frontend que consume estos endpoints para ofrecer una experiencia de aprendizaje interactiva.

## Propósito

El objetivo principal es facilitar el aprendizaje de lenguaje de señas a través de:
- **Gestión estructurada de cursos** organizados por niveles de dificultad
- **Lecciones interactivas** con contenido multimedia
- **Práctica de gestos** utilizando tecnología MediaPipe JS para reconocimiento de gestos
- **Seguimiento del progreso** del estudiante (funcionalidad en desarrollo)

## Arquitectura del Sistema

### Estructura Principal

```
CURSO
└── LECCIÓN
    ├── Contenido (Imágenes/Videos)
    └── Práctica de Gestos (MediaPipe JS)
```

### Modelos Principales

1. **Course (Cursos)**
   - Nombre, descripción, imagen y color temático
   - Agrupa múltiples lecciones relacionadas

2. **Lesson (Lecciones)**
   - Pertenece a un curso específico
   - Contiene contenido educativo (texto, imágenes, videos)
   - Nivel de dificultad y duración estimada
   - Asociada a gestos para práctica

3. **Gesture (Gestos)**
   - Definición de movimientos específicos del lenguaje de señas
   - Datos para reconocimiento con MediaPipe JS
   - Vinculados a lecciones específicas

4. **Progress (Progreso)**
   - Seguimiento del avance del usuario
   - Estado de completitud de lecciones

## Características Principales

### Implementado
- **CRUD completo** para cursos, lecciones y gestos
- **API RESTful** bien documentada con endpoints organizados
- **Sistema de autenticación** con Laravel Sanctum
- **Panel administrativo** simple para gestión de contenido
- **Integración con MediaPipe JS** para reconocimiento de gestos
- **Contenido multimedia** mediante URLs externas
- **Documentación completa** de la API

### En Desarrollo
- **Gestión avanzada de progreso**: Actualmente solo existe seguimiento local
- **CRUD para administración de progreso**: No hay interfaces administrativas para el progreso
- **Endpoints de progreso**: API limitada para consumo del progreso
- **Lógica de gamificación**: Sistema de puntos y logros pendiente

## Tecnologías Utilizadas

- **Backend Framework**: Laravel 12
- **Base de Datos**: MySQL/PostgreSQL (configurable)
- **Autenticación**: Laravel Sanctum
- **Testing**: Pest PHP
- **API**: RESTful con JSON
- **Reconocimiento de Gestos**: MediaPipe JS (frontend)

## Integración con Frontend

Este proyecto está diseñado como un **backend puro** que expone APIs para ser consumidas por una aplicación frontend separada. El frontend se encarga de:
- Renderizar la interfaz de usuario para estudiantes
- Implementar MediaPipe JS para reconocimiento de gestos
- Consumir las APIs para mostrar contenido y gestionar progreso

## Flujo de Aprendizaje

1. **Selección de Curso**: El usuario elige un curso desde el frontend
2. **Acceso a Lecciones**: Ve las lecciones disponibles del curso
3. **Estudio del Contenido**: Revisa material educativo (imágenes/videos)
4. **Práctica de Gestos**: Utiliza MediaPipe JS para practicar movimientos
5. **Validación**: El sistema verifica la correcta ejecución del gesto
6. **Progreso**: Se registra el avance (funcionalidad en desarrollo)

## Instalación y Configuración

### Requisitos Previos
- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js (para assets)

### Pasos de Instalación

```bash
# Clonar el repositorio
git clone [repository-url]
cd back-rimanaq

# Instalar dependencias PHP
composer install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=rimanaq
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed

# Iniciar servidor de desarrollo
php artisan serve
```

## Documentación de la API

### Base URL
```
http://localhost:8000/api
```

### Endpoints Disponibles

#### Autenticación (Rutas Públicas)
- `POST /register` - Registro de nuevos usuarios
- `POST /login` - Inicio de sesión
- `GET /login` - Endpoint para APIs (devuelve 401 si no autenticado)

#### Autenticación (Rutas Protegidas)
- `POST /logout` - Cerrar sesión
- `GET /user` - Obtener datos del usuario autenticado

#### Usuarios
- `GET /users` - Listar todos los usuarios
- `GET /users/{user}` - Obtener usuario específico

#### Cursos (CRUD Completo)
- `GET /courses` - Listar todos los cursos
- `POST /courses` - Crear nuevo curso
- `GET /courses/{course}` - Obtener curso específico
- `PUT/PATCH /courses/{course}` - Actualizar curso
- `DELETE /courses/{course}` - Eliminar curso

#### Lecciones (CRUD Completo)
- `GET /lessons` - Listar todas las lecciones
- `POST /lessons` - Crear nueva lección
- `GET /lessons/{lesson}` - Obtener lección específica
- `PUT/PATCH /lessons/{lesson}` - Actualizar lección
- `DELETE /lessons/{lesson}` - Eliminar lección

#### Gestos (CRUD Completo)
- `GET /gestures` - Listar todos los gestos
- `POST /gestures` - Crear nuevo gesto
- `GET /gestures/{gesture}` - Obtener gesto específico
- `PUT/PATCH /gestures/{gesture}` - Actualizar gesto
- `DELETE /gestures/{gesture}` - Eliminar gesto

#### Progreso (CRUD Completo)
- `GET /progress` - Listar todo el progreso
- `POST /progress` - Crear registro de progreso
- `GET /progress/{progress}` - Obtener progreso específico
- `PUT/PATCH /progress/{progress}` - Actualizar progreso
- `DELETE /progress/{progress}` - Eliminar progreso

#### Relaciones y Consultas Específicas
- `GET /courses/{course}/lessons` - Lecciones de un curso específico
- `GET /lessons/{lesson}/gestures` - Gestos de una lección específica

#### Progreso por Recurso
- `GET /users/{user}/progress` - Progreso de un usuario específico
- `GET /courses/{course}/progress` - Progreso de un curso específico
- `GET /lessons/{lesson}/progress` - Progreso de una lección específica

#### Acciones Específicas de Progreso
- `POST /progress/mark-completed` - Marcar lección como completada
- `POST /progress/increment-attempts` - Incrementar intentos de una lección

### Documentación Completa
Para más detalles sobre parámetros, respuestas y ejemplos, consulta `API_DOCUMENTATION.md`.

## Roadmap de Mejoras

### Prioridad Alta
- [ ] **Sistema de Progreso Completo**
  - CRUD administrativo para progreso
  - APIs robustas para seguimiento
  - Lógica de completitud y puntajes

### Prioridad Media  
- [ ] **Gestión de Archivos Local**
  - Subida y almacenamiento de imágenes
  - Gestión de videos locales
  - Optimización de archivos multimedia

### Prioridad Baja
- [ ] **Gamificación**
  - Sistema de puntos y badges
  - Leaderboards y competencias
  - Streak de días consecutivos

## Contribución

Este proyecto está en desarrollo activo. Las contribuciones son bienvenidas, especialmente en:
- Mejoras al sistema de progreso
- Optimización de APIs
- Documentación adicional
- Testing y QA

## Licencia

---

**Nota**: Este es el componente backend/administrativo del sistema Rimanaq. Para la experiencia completa, debe ser usado en conjunto con la aplicación frontend correspondiente que consume estas APIs.
https://rimanaq.netlify.app/