# 📚 API Documentation - Rimanaq
## Sistema de Aprendizaje de Gestos en Lengua de Señas

### 🔗 Base URL
```
http://localhost:8000/api
```

### 📋 Índice
1. [Cursos (Courses)](#cursos-courses)
2. [Lecciones (Lessons)](#lecciones-lessons)
3. [Gestos (Gestures)](#gestos-gestures)
4. [Progreso (Progress)](#progreso-progress)
5. [Autenticación](#autenticación)
6. [Códigos de Respuesta](#códigos-de-respuesta)

---

## 🎓 Cursos (Courses)

### Modelo Course
```json
{
  "id": 1,
  "name": "Curso Básico de LSP",
  "description": "Introducción a la Lengua de Señas Peruana",
  "image_path": "/images/curso-basico.jpg",
  "color": "#3498db",
  "created_at": "2025-09-29T22:00:00Z",
  "updated_at": "2025-09-29T22:00:00Z"
}
```

### Endpoints

#### 📝 Listar todos los cursos
```http
GET /api/courses
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "name": "Curso Básico de LSP",
    "description": "Introducción a la Lengua de Señas Peruana",
    "image_path": "/images/curso-basico.jpg",
    "color": "#3498db",
    "created_at": "2025-09-29T22:00:00Z",
    "updated_at": "2025-09-29T22:00:00Z"
  }
]
```

#### 📝 Crear un nuevo curso
```http
POST /api/courses
```

**Parámetros:**
```json
{
  "name": "Curso Intermedio de LSP",
  "description": "Nivel intermedio de Lengua de Señas Peruana",
  "image_path": "/images/curso-intermedio.jpg",
  "color": "#e74c3c"
}
```

**Respuesta:**
```json
{
  "course": {
    "name": "Curso Intermedio de LSP",
    "description": "Nivel intermedio de Lengua de Señas Peruana", 
    "image_path": "/images/curso-intermedio.jpg",
    "color": "#e74c3c"
  },
  "message": "Course created successfully!"
}
```

#### 📝 Obtener un curso específico
```http
GET /api/courses/{id}
```

#### 📝 Actualizar un curso
```http
PUT /api/courses/{id}
```

**Parámetros:**
```json
{
  "name": "Curso Básico Actualizado",
  "description": "Nueva descripción del curso"
}
```

#### 📝 Eliminar un curso
```http
DELETE /api/courses/{id}
```

**Respuesta:**
```json
{
  "message": "Course deleted successfully!"
}
```

---g

## 📖 Lecciones (Lessons)

### Modelo Lesson
```json
{
  "id": 1,
  "course_id": 1,
  "name": "Leccion de Prueba",
  "level_number": 1,
  "description": "Esta es una leccion de prueba para probar endpoints",
  "difficulty": "fácil",
  "time_minutes": 15,
  "created_at": "2025-10-05T21:11:14.000000Z",
  "updated_at": "2025-10-05T21:11:14.000000Z",
  "content": null,
  "difficulty_label": "Fácil",
  "formatted_duration": "15 min",
  "progress_count": 0,
  "course": {
    "id": 1,
    "name": "Curso de Prueba",
    "description": "Curso para probar endpoints",
    "image_path": "https://i.ytimg.com/vi/HjOOGujV-LU/maxresdefault.jpg",
    "color": "#3498db",
    "created_at": "2025-10-05T21:09:50.000000Z",
    "updated_at": "2025-10-06T00:05:17.000000Z"
  },
  "gestures": [
    {
      "id": 6,
      "lesson_id": 1,
      "gesture_data": {
        "name": "pruebaaa",
        "frames": [...],
        "frameCount": 3,
        "isSequential": true
      },
      "created_at": "2025-10-06T07:05:13.000000Z",
      "updated_at": "2025-10-06T07:05:13.000000Z"
    }
  ]
}
```

**Campos adicionales explicados:**
- `content`: Contenido estructurado de la lección (puede ser null o un array de objetos)
- `difficulty_label`: Versión formateada de la dificultad
- `formatted_duration`: Duración en formato legible
- `progress_count`: Número de progresos registrados para esta lección
- `gestures`: Array de gestos asociados a la lección (solo en endpoint show)

### Endpoints

#### 📝 Listar todas las lecciones
```http
GET /api/lessons
```

**Headers requeridos:**
```http
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "name": "Leccion de Prueba",
      "level_number": 1,
      "description": "Esta es una leccion de prueba para probar endpoints",
      "difficulty": "fácil",
      "time_minutes": 15,
      "created_at": "2025-10-05T21:11:14.000000Z",
      "updated_at": "2025-10-05T21:11:14.000000Z",
      "content": null,
      "difficulty_label": "Fácil",
      "formatted_duration": "15 min",
      "progress_count": 0,
      "course": {
        "id": 1,
        "name": "Curso de Prueba",
        "description": "Curso para probar endpoints",
        "image_path": "https://i.ytimg.com/vi/HjOOGujV-LU/maxresdefault.jpg",
        "color": "#3498db",
        "created_at": "2025-10-05T21:09:50.000000Z",
        "updated_at": "2025-10-06T00:05:17.000000Z"
      }
    },
    {
      "id": 2,
      "course_id": 1,
      "name": "Leccion 02 de pruebas",
      "level_number": 2,
      "description": "DESCRIPCIÓN DE LA SEGUNDA LECCIÓN DE PRUEBAS.",
      "difficulty": "fácil",
      "time_minutes": 5,
      "created_at": "2025-10-06T08:49:55.000000Z",
      "updated_at": "2025-10-06T08:49:55.000000Z",
      "content": [
        {
          "index": 0,
          "titulo": "ASD",
          "descripcion": "ASDAS",
          "contenido": "ASDASDASD",
          "media": {
            "tipo": "image",
            "url": "https://i.pinimg.com/474x/59/5e/ef/595eef2e2109829e64648b4438802849.jpg"
          }
        }
      ],
      "difficulty_label": "Fácil",
      "formatted_duration": "5 min",
      "progress_count": 0,
      "course": {
        "id": 1,
        "name": "Curso de Prueba",
        "description": "Curso para probar endpoints",
        "image_path": "https://i.ytimg.com/vi/HjOOGujV-LU/maxresdefault.jpg",
        "color": "#3498db",
        "created_at": "2025-10-05T21:09:50.000000Z",
        "updated_at": "2025-10-06T00:05:17.000000Z"
      }
    }
  ],
  "meta": {
    "total": 2,
    "message": "Lecciones obtenidas exitosamente"
  }
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

#### 📝 Crear una nueva lección
```http
POST /api/lessons
```

**Parámetros:**
```json
{
  "course_id": 1,
  "name": "Números del 1 al 10",
  "level_number": 2,
  "description": "Aprende a hacer los números en LSP",
  "difficulty": "fácil",
  "time_minutes": 20
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Lección creada exitosamente",
  "data": {
    "id": 2,
    "course_id": 1,
    "name": "Números del 1 al 10",
    "level_number": 2,
    "description": "Aprende a hacer los números en LSP",
    "difficulty": "fácil",
    "time_minutes": 20,
    "created_at": "2025-09-29T22:30:00Z",
    "updated_at": "2025-09-29T22:30:00Z",
    "course": {
      "id": 1,
      "name": "Curso Básico de LSP"
    }
  }
}
```

#### 📝 Obtener lecciones de un curso específico
```http
GET /api/courses/{course_id}/lessons
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "name": "Saludos básicos",
      "level_number": 1,
      "difficulty": "fácil",
      "time_minutes": 15
    }
  ]
}
```

#### 📝 Obtener una lección específica
```http
GET /api/lessons/{id}
```

**Headers requeridos:**
```http
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "course_id": 1,
    "name": "Leccion de Prueba",
    "level_number": 1,
    "description": "Esta es una leccion de prueba para probar endpoints",
    "difficulty": "fácil",
    "time_minutes": 15,
    "created_at": "2025-10-05T21:11:14.000000Z",
    "updated_at": "2025-10-05T21:11:14.000000Z",
    "content": null,
    "difficulty_label": "Fácil",
    "formatted_duration": "15 min",
    "progress_count": 0,
    "course": {
      "id": 1,
      "name": "Curso de Prueba",
      "description": "Curso para probar endpoints",
      "image_path": "https://i.ytimg.com/vi/HjOOGujV-LU/maxresdefault.jpg",
      "color": "#3498db",
      "created_at": "2025-10-05T21:09:50.000000Z",
      "updated_at": "2025-10-06T00:05:17.000000Z"
    },
    "gestures": [
      {
        "id": 6,
        "lesson_id": 1,
        "gesture_data": {
          "name": "pruebaaa",
          "frames": [
            {
              "id": 1759734292423,
              "timestamp": "2025-10-06T07:04:52.423Z",
              "landmarks": [...],
              "landmarksNormalizados": [...],
              "handedness": [...],
              "gestureName": "pruebaaa",
              "frameIndex": 0,
              "sequenceMetadata": {...}
            }
          ],
          "frameCount": 3,
          "isSequential": true
        },
        "created_at": "2025-10-06T07:05:13.000000Z",
        "updated_at": "2025-10-06T07:05:13.000000Z"
      }
    ]
  },
  "meta": {
    "gestures_count": 1,
    "message": "Lección obtenida exitosamente"
  }
}
```

**Error - Lección no encontrada (404):**
```json
{
  "message": "No query results for model [App\\Models\\Lesson] 999"
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

#### 📝 Actualizar una lección
```http
PUT /api/lessons/{id}
```

#### 📝 Eliminar una lección
```http
DELETE /api/lessons/{id}
```

---

## 🔄 Códigos de Respuesta para Lecciones

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Lección creada exitosamente |
| 401 | Unauthorized - Token de autenticación requerido |
| 404 | Not Found - Lección no encontrada |
| 422 | Unprocessable Entity - Error de validación en los datos |
| 500 | Internal Server Error - Error interno del servidor |

---

## 📝 Notas Importantes sobre Lecciones

1. **Autenticación requerida:** Todos los endpoints de lecciones requieren un token Bearer válido.
2. **Relación con cursos:** Las lecciones deben estar asociadas a cursos existentes.
3. **Contenido estructurado:** El campo `content` puede contener un array de objetos con la estructura del contenido de la lección.
4. **Campos calculados:** `difficulty_label`, `formatted_duration` y `progress_count` son campos calculados automáticamente.
5. **Relaciones cargadas:** El endpoint `show` incluye los gestos asociados, mientras que `index` no.
6. **Ordenamiento:** Las lecciones se ordenan por `course_id` y luego por `level_number`.

---

## 🧪 Ejemplos de Uso - Lecciones (Probados)

### Autenticación
Primero debes autenticarte para obtener el token:

```powershell
$loginResult = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method Post -ContentType "application/json" -Body '{"email":"admin@gmail.com","password":"admin1234"}'
$token = $loginResult.token
$headers = @{"Authorization" = "Bearer $token"}
```

### Obtener todas las lecciones
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/lessons" -Method Get -Headers $headers
```

### Obtener una lección específica
```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/lessons/1" -Method Get -Headers $headers
```

### Usando cURL (alternativo)
```bash
# Login
curl -X POST "http://127.0.0.1:8000/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gmail.com","password":"admin1234"}'

# Listar lecciones (reemplaza YOUR_TOKEN con el token obtenido)
curl -X GET "http://127.0.0.1:8000/api/lessons" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Obtener lección específica
curl -X GET "http://127.0.0.1:8000/api/lessons/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## ✋ Gestos (Gestures)

### Modelo Gesture
```json
{
  "id": 1,
  "lesson_id": 1,
  "gesture_data": {
    ...
  },
  "created_at": "2025-09-29T22:00:00Z",
  "updated_at": "2025-09-29T22:00:00Z",
  "lesson": {
    "id": 1,
    "name": "Saludos básicos"
  }
}
```

---

## 📋 Endpoints del GestureController

### � GET /api/gestures
**Descripción:** Obtiene una lista de todos los gestos almacenados en el sistema.

**Sintaxis:**
```http
GET /api/gestures
Authorization: Bearer {token}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "lesson_id": 1,
      "gesture_data": {
        "id": 1728154221000,
        "name": "HOLA",
        "frames": [
          {
            "id": 1728154221001,
            "timestamp": "2025-10-05T21:12:00.000Z",
            "landmarks": [[{"x": 0.7179, "y": 0.7625, "z": 0.0000}]],
            "gestureName": "HOLA",
            "frameIndex": 0
          }
        ],
        "frameCount": 1,
        "isSequential": true
      },
      "created_at": "2025-10-05T21:11:47.000000Z",
      "updated_at": "2025-10-05T21:12:17.000000Z",
      "lesson": {
        "id": 1,
        "name": "Leccion de Prueba"
      }
    }
  ]
}
```

**Respuesta sin gestos (200):**
```json
{
  "success": true,
  "data": []
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### ➕ POST /api/gestures
**Descripción:** Crea un nuevo gesto en el sistema asociado a una lección específica.

**Sintaxis:**
```http
POST /api/gestures
Authorization: Bearer {token}
Content-Type: application/json
```

**Parámetros requeridos:**
```json
{
  "lesson_id": 1,
  "gesture_data": {
    "id": 1728154221000,
    "name": "HOLA",
    "frames": [
      {
        "id": 1728154221001,
        "timestamp": "2025-10-05T21:12:00.000Z",
        "landmarks": [
          [
            {"x": 0.7179, "y": 0.7625, "z": 0.0000},
            {"x": 0.6374, "y": 0.7263, "z": -0.0311}
          ]
        ],
        "gestureName": "HOLA",
        "frameIndex": 0
      }
    ],
    "frameCount": 1,
    "isSequential": true
  }
}
```

**Respuesta Exitosa (201):**
```json
{
  "success": true,
  "message": "Gesto creado exitosamente",
  "data": {
    "lesson_id": 1,
    "gesture_data": {
      "id": 1728154221000,
      "name": "HOLA",
      "frames": [...],
      "frameCount": 1,
      "isSequential": true
    },
    "updated_at": "2025-10-05T21:12:17.000000Z",
    "created_at": "2025-10-05T21:11:47.000000Z",
    "id": 1,
    "lesson": {
      "id": 1,
      "name": "Leccion de Prueba"
    }
  }
}
```

**Error de validación (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "lesson_id": ["The lesson id field is required."],
    "gesture_data": ["The gesture data field is required."]
  }
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### � GET /api/gestures/{id}
**Descripción:** Obtiene un gesto específico por su ID.

**Sintaxis:**
```http
GET /api/gestures/{id}
Authorization: Bearer {token}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "lesson_id": 1,
    "gesture_data": {
      "id": 1728154221000,
      "name": "ADIOS",
      "frames": [
        {
          "id": 1728154221001,
          "timestamp": "2025-10-05T21:12:00.000Z",
          "landmarks": [[{"x": 0.7179, "y": 0.7625, "z": 0.0000}]],
          "gestureName": "ADIOS",
          "frameIndex": 0
        }
      ],
      "frameCount": 1,
      "isSequential": true
    },
    "created_at": "2025-10-05T21:11:47.000000Z",
    "updated_at": "2025-10-05T21:12:47.000000Z",
    "lesson": {
      "id": 1,
      "name": "Leccion de Prueba"
    }
  }
}
```

**Error - Gesto no encontrado (404):**
```json
{
  "message": "No query results for model [App\\Models\\Gesture] 999"
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### ✏️ PUT /api/gestures/{id}
**Descripción:** Actualiza un gesto existente en el sistema.

**Sintaxis:**
```http
PUT /api/gestures/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

**Parámetros:**
```json
{
  "lesson_id": 1,
  "gesture_data": {
    "id": 1728154221000,
    "name": "ADIOS",
    "frames": [
      {
        "id": 1728154221001,
        "timestamp": "2025-10-05T21:12:00.000Z",
        "landmarks": [[{"x": 0.7179, "y": 0.7625, "z": 0.0000}]],
        "gestureName": "ADIOS",
        "frameIndex": 0
      }
    ],
    "frameCount": 1,
    "isSequential": true
  }
}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "Gesto actualizado exitosamente",
  "data": {
    "id": 1,
    "lesson_id": 1,
    "gesture_data": {
      "id": 1728154221000,
      "name": "ADIOS",
      "frames": [...],
      "frameCount": 1,
      "isSequential": true
    },
    "created_at": "2025-10-05T21:11:47.000000Z",
    "updated_at": "2025-10-05T21:12:47.000000Z",
    "lesson": {
      "id": 1,
      "name": "Leccion de Prueba"
    }
  }
}
```

**Error - Gesto no encontrado (404):**
```json
{
  "message": "No query results for model [App\\Models\\Gesture] 999"
}
```

**Error de validación (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "lesson_id": ["The lesson id field must be an integer."]
  }
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### �️ DELETE /api/gestures/{id}
**Descripción:** Elimina un gesto específico del sistema de forma permanente.

**Sintaxis:**
```http
DELETE /api/gestures/{id}
Authorization: Bearer {token}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "Gesto eliminado exitosamente"
}
```

**Error - Gesto no encontrado (404):**
```json
{
  "message": "No query results for model [App\\Models\\Gesture] 999"
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### 📚 GET /api/lessons/{lesson_id}/gestures
**Descripción:** Obtiene todos los gestos asociados a una lección específica.

**Sintaxis:**
```http
GET /api/lessons/{lesson_id}/gestures
Authorization: Bearer {token}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "lesson_id": 1,
      "gesture_data": {
        "id": 1728154221000,
        "name": "ADIOS",
        "frames": [
          {
            "id": 1728154221001,
            "timestamp": "2025-10-05T21:12:00.000Z",
            "landmarks": [[{"x": 0.7179, "y": 0.7625, "z": 0.0000}]],
            "gestureName": "ADIOS",
            "frameIndex": 0
          }
        ],
        "frameCount": 1,
        "isSequential": true
      },
      "created_at": "2025-10-05T21:11:47.000000Z",
      "updated_at": "2025-10-05T21:12:47.000000Z"
    }
  ]
}
```

**Respuesta sin gestos (200):**
```json
{
  "success": true,
  "data": []
}
```

**Error de autenticación (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

## 🔄 Códigos de Respuesta para Gestos

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Gesto creado exitosamente |
| 401 | Unauthorized - Token de autenticación requerido |
| 404 | Not Found - Gesto o lección no encontrada |
| 422 | Unprocessable Entity - Error de validación en los datos |
| 500 | Internal Server Error - Error interno del servidor |

---

## 📝 Notas Importantes

1. **Autenticación requerida:** Todos los endpoints requieren un token Bearer válido.
2. **Validación de datos:** El campo `gesture_data` debe contener la estructura completa del gesto con landmarks de MediaPipe.
3. **Relación con lecciones:** Los gestos deben estar asociados a lecciones existentes.
4. **Formato JSON:** Todos los datos de entrada y salida utilizan formato JSON.
5. **Timestamps:** Se generan automáticamente en formato ISO 8601.

---

## 📊 Progreso (Progress)

### Modelo Progress
```json
{
  "id": 1,
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1,
  "completed": true,
  "attempts_count": 3,
  "created_at": "2025-09-29T22:00:00Z",
  "updated_at": "2025-09-29T22:30:00Z",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "lesson": {
    "id": 1,
    "name": "Saludos básicos"
  },
  "course": {
    "id": 1,
    "name": "Curso Básico de LSP"
  }
}
```

### Endpoints

#### 📝 Listar todo el progreso
```http
GET /api/progress
```

#### 📝 Crear/Actualizar progreso
```http
POST /api/progress
```

**Parámetros:**
```json
{
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1,
  "completed": false,
  "attempts_count": 1
}
```

#### 📝 Obtener progreso de un usuario
```http
GET /api/users/{user_id}/progress
```

#### 📝 Obtener progreso de un curso
```http
GET /api/courses/{course_id}/progress
```

#### 📝 Obtener progreso de una lección
```http
GET /api/lessons/{lesson_id}/progress
```

#### 📝 Marcar lección como completada
```http
POST /api/progress/mark-completed
```

**Parámetros:**
```json
{
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Lección marcada como completada",
  "data": {
    "id": 1,
    "user_id": 1,
    "lesson_id": 1,
    "course_id": 1,
    "completed": true,
    "attempts_count": 4
  }
}
```

#### 📝 Incrementar intentos
```http
POST /api/progress/increment-attempts
```

**Parámetros:**
```json
{
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1
}
```

---

## 🔐 Autenticación

### Registro
```http
POST /api/register
```

**Parámetros:**
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```http
POST /api/login
```

**Parámetros:**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

### Logout
```http
POST /api/logout
```
*Requiere autenticación*

---

## 📋 Códigos de Respuesta

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado exitosamente |
| 400 | Bad Request - Error en los parámetros |
| 401 | Unauthorized - No autenticado |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

---

## 🔄 Formatos de Respuesta

### Respuesta Exitosa
```json
{
  "success": true,
  "data": {...},
  "message": "Operación exitosa"
}
```

### Respuesta de Error
```json
{
  "success": false,
  "message": "Error en la operación",
  "errors": {
    "field": ["Error específico del campo"]
  }
}
```

---

## 📈 Ejemplos de Flujo de Uso

### 1. Crear un curso completo
```bash
# 1. Crear curso
POST /api/courses
{
  "name": "LSP Básico",
  "description": "Curso básico",
  "color": "#3498db"
}

# 2. Crear lección
POST /api/lessons
{
  "course_id": 1,
  "name": "Saludos",
  "level_number": 1,
  "difficulty": "fácil",
  "time_minutes": 15
}

# 3. Agregar gesto
POST /api/gestures
{
  "lesson_id": 1,
  "gesture_data": {...}
}
```

### 2. Trackear progreso de usuario
```bash
# 1. Incrementar intento
POST /api/progress/increment-attempts
{
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1
}

# 2. Marcar como completado
POST /api/progress/mark-completed
{
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1
}

# 3. Ver progreso del usuario
GET /api/users/1/progress
```

---

*Documentación generada el 29 de Septiembre, 2025*