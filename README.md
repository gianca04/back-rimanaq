# 🤟 Rimanaq - Sistema de Aprendizaje de Gestos
## Plataforma de enseñanza de Lengua de Señas Peruana (LSP)

![Laravel](https://img.shields.io/badge/Laravel-v11-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql)
![Vue.js](https://img.shields.io/badge/Vue.js-3.0+-4FC08D?style=for-the-badge&logo=vue.js)

---

## 📋 Descripción del Proyecto

**Rimanaq** es una plataforma educativa innovadora diseñada para facilitar el aprendizaje de la Lengua de Señas Peruana (LSP) a través de tecnología de reconocimiento de gestos y gamificación. La plataforma permite a los usuarios aprender gestos de manera interactiva, realizar un seguimiento de su progreso y practicar con contenido estructurado.

### 🎯 Características Principales

- **🎓 Cursos Estructurados**: Sistema de cursos organizados por niveles de dificultad
- **📖 Lecciones Interactivas**: Lecciones progresivas con contenido multimedia
- **✋ Captura de Gestos**: Sistema avanzado de captura y reconocimiento de gestos
- **📊 Seguimiento de Progreso**: Tracking completo del avance del usuario
- **🔐 Autenticación**: Sistema seguro de registro y login
- **📱 API RESTful**: API completa para integración con aplicaciones móviles
- **🎨 Interfaz Moderna**: Frontend responsivo con Vue.js

---

## 🏗️ Arquitectura del Sistema

### Backend (Laravel 11)
- **Framework**: Laravel 11 con PHP 8.2+
- **Base de Datos**: MySQL 8.0+ con migraciones
- **Autenticación**: Laravel Sanctum para API tokens
- **Almacenamiento**: Sistema de archivos local/cloud
- **Validación**: Form Requests personalizados
- **Relaciones**: Eloquent ORM con relaciones complejas

### Frontend (Vue.js 3)
- **Framework**: Vue.js 3 con Composition API
- **Build Tool**: Vite para desarrollo rápido
- **Estilos**: CSS personalizado + framework responsivo
- **Captura de Gestos**: MediaPipe + Canvas API
- **Estado**: Pinia para gestión de estado

### Base de Datos
- **Cursos**: Información de cursos de LSP
- **Lecciones**: Contenido educativo estructurado
- **Gestos**: Datos de landmarks de manos en formato JSON
- **Progreso**: Seguimiento de avance por usuario
- **Usuarios**: Sistema de autenticación y perfiles

---

## 📦 Estructura del Proyecto

```
rimanaq/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores de API
│   │   └── Requests/            # Validaciones de formularios
│   └── Models/                  # Modelos Eloquent
├── database/
│   ├── migrations/              # Migraciones de BD
│   └── factories/               # Factories para testing
├── resources/
│   ├── js/                      # Frontend Vue.js
│   ├── css/                     # Estilos CSS
│   └── views/                   # Plantillas Blade
├── routes/
│   ├── api.php                  # Rutas de API
│   └── web.php                  # Rutas web
├── storage/
│   └── app/public/              # Archivos públicos
├── tests/                       # Tests automatizados
├── API_DOCUMENTATION.md         # Documentación completa de API
├── TESTING_GUIDE.md            # Guía de testing con cURL
├── DATABASE_SCHEMA.md          # Esquema de base de datos
└── INSTALLATION_GUIDE.md       # Guía de instalación
```

---

## 🚀 Instalación Rápida

### 1. Requisitos Previos
```bash
PHP >= 8.2
Composer >= 2.0
Node.js >= 16.0
MySQL >= 8.0
```

### 2. Instalación
```bash
# Clonar repositorio
git clone <repository-url> rimanaq
cd rimanaq

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
DB_DATABASE=rimanaq
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Ejecutar migraciones
php artisan migrate --seed

# Crear enlaces de storage
php artisan storage:link

# Ejecutar servidor
php artisan serve
```

**📖 Para instalación completa, ver:** [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)

---

## 🔌 API Endpoints

### Principales Endpoints Disponibles:

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/courses` | Listar cursos |
| POST | `/api/courses` | Crear curso |
| GET | `/api/lessons` | Listar lecciones |
| GET | `/api/courses/{id}/lessons` | Lecciones de un curso |
| POST | `/api/gestures` | Guardar gesto capturado |
| GET | `/api/lessons/{id}/gestures` | Gestos de una lección |
| POST | `/api/progress/mark-completed` | Marcar lección completada |
| GET | `/api/users/{id}/progress` | Progreso de usuario |

**📖 Para documentación completa, ver:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 🧪 Testing y Validación

### Tests con cURL:
```bash
# Test básico de la API
curl http://localhost:8000/api/courses

# Crear un curso de prueba
curl -X POST http://localhost:8000/api/courses \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Course","description":"Test","color":"#3498db"}'
```

**📖 Para guía completa de testing, ver:** [TESTING_GUIDE.md](TESTING_GUIDE.md)

---

## 📊 Modelos de Datos

### Course (Curso)
```json
{
  "id": 1,
  "name": "LSP Básico",
  "description": "Curso introductorio",
  "image_path": "/images/curso.jpg",
  "color": "#3498db"
}
```

### Lesson (Lección)
```json
{
  "id": 1,
  "course_id": 1,
  "name": "Saludos básicos",
  "level_number": 1,
  "description": "Aprende saludos en LSP",
  "difficulty": "fácil",
  "time_minutes": 15
}
```

### Gesture (Gesto)
```json
{
  "id": 1,
  "lesson_id": 1,
  "gesture_data": {
    "name": "HOLA",
    "frames": [{"landmarks": [...]}],
    "frameCount": 1
  }
}
```

### Progress (Progreso)
```json
{
  "id": 1,
  "user_id": 1,
  "lesson_id": 1,
  "course_id": 1,
  "completed": true,
  "attempts_count": 3
}
```

**📖 Para esquema completo, ver:** [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)

---

## 🔧 Configuración Avanzada

### Entorno de Desarrollo
```bash
# Hot reload para frontend
npm run dev

# Servidor de desarrollo
php artisan serve --host=0.0.0.0 --port=8000

# Queue workers (opcional)
php artisan queue:work
```

### Producción
```bash
# Optimizar para producción
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🤝 Contribución

### Pasos para Contribuir:
1. Fork del repositorio
2. Crear rama de feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### Estándares de Código:
- **PSR-12** para PHP
- **ESLint** para JavaScript
- **Conventional Commits** para mensajes
- **Tests** requeridos para nuevas funcionalidades

---

## 📝 Roadmap

### Fase 1 - Completado ✅
- [x] Sistema de cursos y lecciones
- [x] Captura y almacenamiento de gestos
- [x] Sistema de progreso de usuario
- [x] API RESTful completa
- [x] Autenticación con Sanctum

### Fase 2 - En Desarrollo 🚧
- [ ] Frontend Vue.js interactivo
- [ ] Reconocimiento de gestos en tiempo real
- [ ] Sistema de puntuación y logros
- [ ] Panel de administración

### Fase 3 - Planificado 📋
- [ ] Aplicación móvil (React Native)
- [ ] Inteligencia artificial para evaluación
- [ ] Comunidad y foros de usuarios
- [ ] Contenido multimedia avanzado

---

## 🐛 Reporte de Bugs

Para reportar bugs, crear un issue con:
- **Descripción** clara del problema
- **Pasos** para reproducir
- **Comportamiento esperado** vs actual
- **Screenshots** si aplica
- **Información** del entorno (OS, PHP, navegador)

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

## 👥 Equipo de Desarrollo

- **Backend**: Laravel 11 + MySQL
- **Frontend**: Vue.js 3 + Vite
- **ML/AI**: MediaPipe + TensorFlow.js
- **DevOps**: Docker + CI/CD

---

## 📞 Soporte

- **Documentación**: Ver archivos `.md` en el proyecto
- **Issues**: GitHub Issues
- **Email**: contacto@rimanaq.pe

---

## 🏆 Reconocimientos

- **MediaPipe** por la tecnología de detección de manos
- **Laravel** por el excelente framework PHP
- **Vue.js** por el framework frontend reactivo
- **Comunidad LSP** por el apoyo y contenido educativo

---

**Hecho con ❤️ para la comunidad de Lengua de Señas Peruana**

*Última actualización: 29 de Septiembre, 2025*

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
