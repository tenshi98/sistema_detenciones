# Sistema de Registro de Tiempos Muertos

Sistema completo para el registro y seguimiento de tiempos muertos en plantas de producción, desarrollado con PHP backend (MVC + JWT) y Next.js frontend.

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características Principales](#-características-principales)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Usuario por Defecto](#-usuario-por-defecto)
- [Tipos de Usuario y Permisos](#-tipos-de-usuario-y-permisos)
- [Entidades del Sistema](#-entidades-del-sistema)
- [Flujo de Trabajo](#-flujo-de-trabajo)
- [Endpoints API Principales](#-endpoints-api-principales)
- [Migración a Otras Bases de Datos](#-migración-a-otras-bases-de-datos)
- [Testing](#-testing)
- [Ejemplos de Uso](#-ejemplos-de-uso)
- [Solución de Problemas](#-solución-de-problemas)
- [Documentación Adicional](#-documentación-adicional)

## 📋 Descripción

Este sistema permite gestionar y analizar los tiempos muertos en líneas de producción, incluyendo:
- Registro de detenciones por línea, turno y tipo de producción
- Gestión de órdenes de fabricación asociadas
- Seguimiento detallado de tiempos por tipo y familia
- Análisis de porcentajes y resúmenes
- Control de acceso basado en roles

## 🚀 Características Principales

### Backend (PHP)
- ✅ Arquitectura MVC modular
- ✅ Autenticación JWT con refresh token
- ✅ 16 entidades CRUD completas
- ✅ Middleware de protección de rutas
- ✅ Rate limiting por IP/usuario
- ✅ Sistema de logging estructurado
- ✅ Validaciones complejas (unicidad compuesta)
- ✅ Soporte multi-base de datos (MySQL, PostgreSQL, SQL Server)
- ✅ Soft deletes
- ✅ CORS configurado

### Frontend (Next.js)
- ✅ Next.js 14 con App Router
- ✅ Tailwind CSS para estilos
- ✅ TypeScript
- ✅ Axios con interceptores JWT
- ✅ Control de permisos por rol
- ✅ Componentes UI reutilizables
- ✅ Protección de rutas
- ✅ Refresh automático de tokens

## 📁 Estructura del Proyecto

```
sistema_detenciones/
├── backend/                    # API REST en PHP
│   ├── config/                 # Configuración y rutas
│   ├── database/               # Scripts SQL
│   ├── public/                 # Punto de entrada
│   ├── src/
│   │   ├── Controllers/        # Controladores CRUD
│   │   ├── Middleware/         # JWT, CORS, Rate Limit
│   │   ├── Models/             # Modelos de datos
│   │   ├── Services/           # Servicios (JWT)
│   │   └── Utils/              # Utilidades
│   ├── logs/                   # Archivos de log
│   ├── .env                    # Variables de entorno
│   └── composer.json
│
└── frontend/                   # Aplicación Next.js
    ├── app/                    # Páginas (App Router)
    ├── components/             # Componentes React
    ├── lib/                    # Utilidades
    ├── public/                 # Archivos estáticos
    └── package.json
```

## 🛠️ Requisitos del Sistema

### Backend
- PHP >= 7.0
- MySQL >= 5.7 (o MariaDB 10.2+, PostgreSQL, SQL Server)
- Composer
- Extensiones PHP:
   - `PDO`
   - `pdo_mysql`
   - `json`
   - `mbstring`

### Frontend
- Node.js >= 18.0
- npm o yarn

## 📦 Instalación

### 1. Backend

```bash
git clone https://github.com/tenshi98/sistema_detenciones.git
cd sistema_detenciones/backend

# Instalar dependencias
composer install

# Configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales

# Crear base de datos
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql

# Iniciar servidor de desarrollo
cd public
php -S localhost:8000
```

### 2. Frontend

```bash
cd frontend

# Instalar dependencias
npm install

# Configurar variables de entorno
echo "API_URL=http://localhost:8000" > .env.local

# Iniciar servidor de desarrollo
npm run dev
```

La aplicación estará disponible en:
- **Backend API**: http://localhost:8000
- **Frontend**: http://localhost:3000

## 👤 Usuario por Defecto

- **Usuario**: admin
- **Contraseña**: admin123
- **Tipo**: Administrador

## 🔐 Tipos de Usuario y Permisos

| Tipo de Usuario | ID | Permisos |
|----------------|----|-----------|
| Administrador | 1 | Acceso completo a todas las funcionalidades |
| Control Tiempo | 2 | Gestión de materiales, rutas y detenciones |
| Supervisor | 3 | Visualización de detenciones |
| Usuario Normal | 4 | Acceso limitado |

## 📊 Entidades del Sistema

### Catálogos Base
- **Tipos de Usuario**: Roles del sistema
- **Usuarios**: Usuarios del sistema
- **Líneas**: Líneas de producción
- **Turnos**: Turnos de trabajo (A, B, C, AB, BC)
- **Supervisores**: Supervisores de planta
- **Tipos de Producción**: Normal, Paralela

### Materiales y Rutas
- **Materiales**: Productos fabricados
- **Rutas Material**: Rutas de material (Directo, Embotellado, Etiquetado)
- **Rutas Producción**: Rutas con velocidad nominal

### Tiempos
- **Tipos de Tiempos**: Producción, Preparación, Muerto
- **Familias de Tiempos**: 10 familias predefinidas
- **Tiempos**: Combinación de tipo + familia

### Detenciones
- **Detenciones**: Registro principal
- **Detenciones OF**: Órdenes de fabricación
- **Detenciones OF Detalle**: Detalles de tiempo
- **Estados de Detención**: Abierta, Cerrada

## 🔄 Flujo de Trabajo

1. **Crear Detención**: Se registra una detención para una línea, turno y fecha específica
2. **Agregar OF**: Se agregan órdenes de fabricación a la detención
3. **Registrar Detalles**: Se registran los tiempos muertos con fecha, hora y minutos dentro de la órden de fabricación
4. **Ver Resumen**: El sistema calcula automáticamente:
   - Suma de minutos por OF, tipo y familia
   - Porcentajes por tipo y familia
5. **Cerrar Detención**: Se cambia el estado a "Cerrada"

## 📡 Endpoints API Principales

### Autenticación
```
POST /auth/login
POST /auth/register
POST /auth/refresh
```

### Detenciones
```
GET  /detenciones                    # Listar todas
GET  /detenciones/abiertas           # Solo abiertas
GET  /detenciones/{id}               # Ver una
POST /detenciones                    # Crear
PUT  /detenciones/{id}               # Actualizar
```

### Órdenes de Fabricación
```
GET  /detencionesOF/detencion/{id}   # Por detención
POST /detencionesOF                  # Crear
PUT  /detencionesOF/{id}             # Actualizar
```

### Detalles
```
GET  /detencionesOFDetalle/of/{id}          # Por OF
GET  /detencionesOFDetalle/resumen/{id}     # Resumen
GET  /detencionesOFDetalle/porcentajes/{id} # Porcentajes
POST /detencionesOFDetalle                  # Crear
PUT  /detencionesOFDetalle/{id}             # Actualizar
DELETE /detencionesOFDetalle/{id}           # Eliminar
```

Ver documentación completa en `backend/README.md`

## 🔧 Migración a Otras Bases de Datos

### PostgreSQL
1. Cambiar en `.env`:
   ```env
   DB_DRIVER=pgsql
   DB_PORT=5432
   ```

2. Adaptar `database/schema.sql`:
   - `AUTO_INCREMENT` → `SERIAL`
   - `TINYINT(1)` → `BOOLEAN`
   - `TIMESTAMP DEFAULT '0000-00-00'` → `TIMESTAMP DEFAULT '1970-01-01 00:00:01'`

### SQL Server
1. Cambiar en `.env`:
   ```env
   DB_DRIVER=sqlsrv
   DB_PORT=1433
   ```

2. Adaptar `database/schema.sql`:
   - `AUTO_INCREMENT` → `IDENTITY(1,1)`
   - `TINYINT(1)` → `BIT`
   - `VARCHAR` → `NVARCHAR`

## 🧪 Testing

### Backend
```bash
cd backend
composer test
```

### Frontend
```bash
cd frontend
npm run test
```

## 📝 Ejemplos de Uso

### Login
```bash
curl -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"UserName":"admin","Password":"admin123"}'
```

### Crear Detención
```bash
curl -X POST http://localhost:8000/detenciones \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "idLinea": 1,
    "idTurnos": 1,
    "idSupervisor": 1,
    "idTipoProduccion": 1,
    "Fecha": "2024-12-04",
    "Observaciones": "Detención de prueba"
  }'
```

## 🐛 Solución de Problemas

### Error de conexión a BD
- Verificar credenciales en `.env`
- Verificar que MySQL esté corriendo
- Verificar que la base de datos exista

### Error 401 en API
- Verificar que el token JWT sea válido
- Verificar que no haya expirado
- Intentar hacer refresh del token

### Frontend no conecta con Backend
- Verificar que `API_URL` en `.env.local` sea correcto
- Verificar que el backend esté corriendo
- Verificar CORS en backend `.env`

## 📚 Documentación Adicional

- [Backend README](backend/README.md) - Documentación detallada del API
- [Swagger/OpenAPI](backend/docs/swagger.yaml) - Documentación de endpoints
- [Database Schema](backend/database/schema.sql) - Esquema de base de datos

