# Documentación de Autenticación - API RIMANAQ

## 📋 Resumen Ejecutivo

La API RIMANAQ utiliza **Laravel Sanctum** para manejar la autenticación mediante tokens. El sistema permite registro, login, logout y acceso a recursos protegidos de forma segura.

## 🔗 Endpoints Disponibles

### Rutas Públicas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/register` | Registro de nuevos usuarios |
| `POST` | `/api/login` | Inicio de sesión |
| `GET` | `/api/login` | Respuesta para usuarios no autenticados (401) |

### Rutas Protegidas (Requieren Token)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/logout` | Cierre de sesión |
| `GET` | `/api/user` | Obtener datos del usuario autenticado |

## 🚀 Guía Rápida de Uso

### 1. Registrar Usuario
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Iniciar Sesión
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "password123"
  }'
```

### 3. Acceder a Recursos (con token)
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer {tu_token_aquí}"
```

### 4. Cerrar Sesión
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {tu_token_aquí}"
```

## ✅ Validaciones

### Registro (`RegisterRequest`)
- **name**: Requerido, texto, máx. 255 caracteres
- **email**: Requerido, formato email, único en la base de datos
- **password**: Requerido, mínimo 8 caracteres, debe coincidir con confirmación

### Login (`LoginRequest`)
- **email**: Requerido, formato email válido
- **password**: Requerido, texto

## 📊 Códigos de Respuesta

| Código | Descripción | Cuándo Ocurre |
|--------|-------------|---------------|
| `200` | OK | Login exitoso, logout, obtener usuario |
| `201` | Creado | Registro exitoso |
| `401` | No autorizado | Token inválido o credenciales incorrectas |
| `422` | Error de validación | Datos inválidos en el formulario |
| `500` | Error del servidor | Error interno de la aplicación |

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Tokens seguros con Laravel Sanctum  
- ✅ Validación robusta de entrada
- ✅ Protección CSRF incluida
- ✅ Revocación de tokens en logout

## 📖 Documentación Completa

Para documentación detallada con ejemplos de implementación, diagramas y casos de uso, consulta:
- `sistema_autenticacion_completo.md` - Documentación técnica completa

## 🛠️ Implementación

### Frontend (JavaScript)
```javascript
// Ejemplo básico de uso con fetch
const login = async (email, password) => {
  const response = await fetch('/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  localStorage.setItem('token', data.token);
  return data;
};
```

### Headers Requeridos
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // Para rutas protegidas
```

---

**💡 Tip:** Guarda el token devuelto en el login para usarlo en todas las peticiones posteriores a rutas protegidas.