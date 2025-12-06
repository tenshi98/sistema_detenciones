# Backend - Sistema de Registro de Tiempos Muertos

API REST desarrollada en PHP para el registro y seguimiento de tiempos muertos en plantas de producción.

## Requisitos

- PHP >= 7.0
- MySQL >= 5.7
- Composer
- Extensiones PHP: PDO, pdo_mysql, json, mbstring

## Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/tenshi98/sistema_detenciones.git
cd sistema_detenciones/backend
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
```

Editar `.env` con tus credenciales de base de datos:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_detenciones
DB_USER=root
DB_PASS=tu_password

JWT_SECRET=tu-clave-secreta-segura
```

4. **Crear base de datos**
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql
```

## Ejecución

### Servidor de desarrollo PHP
```bash
cd public
php -S localhost:8000
```

La API estará disponible en `http://localhost:8000`

### Usuario por defecto
- **Usuario**: admin
- **Password**: admin123

## Endpoints Principales

### Autenticación
- `POST /auth/login` - Iniciar sesión
- `POST /auth/register` - Registrar usuario
- `POST /auth/refresh` - Renovar token

### Entidades CRUD
Todas las entidades siguen el patrón REST:
- `GET /{endpoint}` - Listar todos
- `GET /{endpoint}/{id}` - Obtener uno
- `POST /{endpoint}` - Crear
- `PUT /{endpoint}/{id}` - Actualizar
- `DELETE /{endpoint}/{id}` - Eliminar (desactivar)
- `POST /{endpoint}/{id}/activate` - Activar

Endpoints disponibles:
- `/userTypes` - Tipos de usuario
- `/users` - Usuarios
- `/lineas` - Líneas de producción
- `/turnos` - Turnos
- `/supervisor` - Supervisores
- `/tipoProduccion` - Tipos de producción
- `/material` - Materiales
- `/rutaMaterial` - Rutas de material
- `/rutaProduccion` - Rutas de producción
- `/tiposTiempo` - Tipos de tiempo
- `/familiasTiempo` - Familias de tiempo
- `/tiempos` - Tiempos
- `/detencionesEstados` - Estados de detención
- `/detenciones` - Detenciones
- `/detencionesOF` - Órdenes de fabricación
- `/detencionesOFDetalle` - Detalles de OF

## Estructura del Proyecto

```
backend/
├── config/
│   ├── config.php          # Configuración general
│   ├── database.php        # Gestión de BD
│   ├── Router.php          # Sistema de rutas
│   └── routes.php          # Definición de rutas
├── database/
│   ├── schema.sql          # Esquema de BD
│   └── seeds.sql           # Datos iniciales
├── public/
│   └── index.php           # Punto de entrada
├── src/
│   ├── Controllers/        # Controladores
│   ├── Middleware/         # Middlewares (JWT, CORS, Rate Limit)
│   ├── Models/             # Modelos de datos
│   ├── Services/           # Servicios (JWT)
│   └── Utils/              # Utilidades (Logger, Validator, Response)
├── logs/                   # Archivos de log
├── .env                    # Variables de entorno
└── composer.json           # Dependencias
```

## Migración a Otras Bases de Datos

El sistema está diseñado para soportar múltiples motores de base de datos.

### PostgreSQL
1. Cambiar en `.env`:
```env
DB_DRIVER=pgsql
DB_PORT=5432
```

2. Adaptar el schema.sql para PostgreSQL:
- Cambiar `AUTO_INCREMENT` por `SERIAL`
- Cambiar `TINYINT(1)` por `BOOLEAN`
- Ajustar tipos de datos según PostgreSQL

### SQL Server
1. Cambiar en `.env`:
```env
DB_DRIVER=sqlsrv
DB_PORT=1433
```

2. Adaptar el schema.sql para SQL Server:
- Cambiar `AUTO_INCREMENT` por `IDENTITY(1,1)`
- Cambiar `TINYINT(1)` por `BIT`
- Usar `NVARCHAR` en lugar de `VARCHAR`

## Características

- ✅ Arquitectura MVC modular
- ✅ Autenticación JWT con refresh token
- ✅ Middleware de protección de rutas
- ✅ Rate limiting por IP/usuario
- ✅ CORS configurado
- ✅ Logging estructurado (JSON)
- ✅ Validación y sanitización de datos
- ✅ Manejo robusto de errores
- ✅ Soporte multi-base de datos
- ✅ Soft deletes
- ✅ Validaciones de unicidad compuestas

## Seguridad

- Passwords hasheados con bcrypt
- Tokens JWT con expiración
- Sanitización de inputs
- Prepared statements (PDO)
- Rate limiting
- CORS configurado

## Logs

Los logs se guardan en `logs/` con formato JSON:
- Rotación automática (30 días)
- Niveles: INFO, WARNING, ERROR
- Incluye contexto de request


