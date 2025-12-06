# Frontend - Sistema de Registro de Tiempos Muertos

Aplicación web desarrollada con Next.js para la gestión de tiempos muertos en plantas de producción.

## Tecnologías

### Core
- **Next.js 14.2** - Framework React con App Router
- **React 18.3** - Biblioteca de UI
- **TypeScript 5.9** - Tipado estático
- **Tailwind CSS 3.4** - Framework de estilos utility-first

### HTTP & Data
- **Axios 1.13** - Cliente HTTP con interceptores

### Build & Development
- **PostCSS 8.5** - Procesador de CSS
- **Autoprefixer 10.4** - Prefijos CSS automáticos
- **ESLint 8.57** - Linter de código
- **ESLint Config Next 14.2** - Configuración ESLint para Next.js

### TypeScript Types
- **@types/node** - Tipos para Node.js
- **@types/react** - Tipos para React
- **@types/react-dom** - Tipos para React DOM


## Requisitos

- Node.js >= 18.0
- npm o yarn

## Instalación

```bash
# Instalar dependencias
npm install

# Configurar variables de entorno
echo "API_URL=http://localhost:8000" > .env.local

# Iniciar servidor de desarrollo
npm run dev
```

La aplicación estará disponible en `http://localhost:3000`

## Estructura del Proyecto

```
frontend/
├── app/                        # Páginas (App Router)
│   ├── layout.tsx              # Layout principal
│   ├── page.tsx                # Página de inicio
│   ├── login/                  # Página de login
│   ├── dashboard/              # Dashboard principal
│   ├── userTypes/              # Tipos de usuario
│   ├── users/                  # Usuarios
│   ├── lineas/                 # Líneas
│   ├── turnos/                 # Turnos
│   ├── supervisor/             # Supervisores
│   ├── tipoProduccion/         # Tipos de producción
│   ├── material/               # Materiales
│   ├── rutaMaterial/           # Rutas material
│   ├── rutaProduccion/         # Rutas producción
│   ├── tiposTiempo/            # Tipos de tiempos
│   ├── familiasTiempo/         # Familias de tiempos
│   ├── tiempos/                # Tiempos
│   ├── detencionesEstados/     # Estados de detenciones
│   ├── detenciones/            # Detenciones
│   └── detencionesEditar/      # Editar detenciones
├── components/                 # Componentes React
│   ├── ui/                     # Componentes UI base
│   ├── layout/                 # Componentes de layout
│   ├── modals/                 # Modales
│   └── tables/                 # Tablas CRUD
├── lib/                        # Utilidades
│   ├── axios.ts                # Configuración Axios
│   ├── auth.ts                 # Utilidades de autenticación
│   └── permissions.ts          # Control de permisos
└── public/                     # Archivos estáticos
```

## Características

### Autenticación
- Login con JWT
- Refresh automático de tokens
- Protección de rutas
- Logout

### Control de Permisos
El sistema implementa control de acceso basado en roles:

- **Administrador (ID: 1)**: Acceso completo
- **Control Tiempo (ID: 2)**: Gestión de materiales y detenciones
- **Supervisor (ID: 3)**: Visualización
- **Usuario Normal (ID: 4)**: Acceso limitado

### Páginas Implementadas

#### Públicas
- `/login` - Inicio de sesión

#### Protegidas (Todos los usuarios)
- `/dashboard` - Dashboard con detenciones abiertas

#### Solo Administrador
- `/userTypes` - Gestión de tipos de usuario
- `/users` - Gestión de usuarios
- `/lineas` - Gestión de líneas
- `/turnos` - Gestión de turnos
- `/supervisor` - Gestión de supervisores
- `/tipoProduccion` - Gestión de tipos de producción
- `/tiposTiempo` - Gestión de tipos de tiempos
- `/familiasTiempo` - Gestión de familias de tiempos
- `/tiempos` - Gestión de tiempos
- `/detencionesEstados` - Gestión de estados

#### Administrador y Control Tiempo
- `/material` - Gestión de materiales
- `/rutaMaterial` - Gestión de rutas de material
- `/rutaProduccion` - Gestión de rutas de producción
- `/detenciones` - Gestión de detenciones
- `/detencionesEditar/:id` - Edición de detenciones

## Componentes UI

### Componentes Base
- `Button` - Botón reutilizable con variantes
- `Input` - Input con validación
- `Card` - Tarjeta de contenido
- `Modal` - Modal reutilizable
- `Tabs` - Sistema de pestañas
- `Table` - Tabla con paginación

### Componentes de Layout
- `NavBar` - Barra de navegación
- `Sidebar` - Menú lateral
- `Layout` - Layout principal

## Uso de la API

### Ejemplo de llamada autenticada

```typescript
import axios from '@/lib/axios';

// GET request
const response = await axios.get('/detenciones');
const detenciones = response.data.data;

// POST request
const response = await axios.post('/detenciones', {
  idLinea: 1,
  idTurnos: 1,
  idSupervisor: 1,
  idTipoProduccion: 1,
  Fecha: '2024-12-04',
});
```

### Manejo de Autenticación

```typescript
import { login, logout, getCurrentUser, isAdmin } from '@/lib/auth';

// Login
await login('admin', 'admin123');

// Obtener usuario actual
const user = getCurrentUser();

// Verificar permisos
if (isAdmin()) {
  // Código para administradores
}

// Logout
logout();
```

## Scripts Disponibles

```bash
# Desarrollo
npm run dev

# Build para producción
npm run build

# Iniciar en producción
npm start

# Linting
npm run lint
```

## Variables de Entorno

Crear archivo `.env.local`:

```env
API_URL=http://localhost:8000
```

## Desarrollo

### Agregar una Nueva Página

1. Crear directorio en `app/`:
```bash
mkdir app/nueva-pagina
```

2. Crear `page.tsx`:
```typescript
export default function NuevaPaginaPage() {
  return <div>Nueva Página</div>;
}
```

3. Agregar al menú en `lib/permissions.ts`

### Crear un Componente UI

```typescript
// components/ui/MiComponente.tsx
interface MiComponenteProps {
  titulo: string;
}

export default function MiComponente({ titulo }: MiComponenteProps) {
  return <div>{titulo}</div>;
}
```

## Estilos con Tailwind

El proyecto usa Tailwind CSS con una paleta de colores personalizada:

```tsx
<button className="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg">
  Botón
</button>
```

## Protección de Rutas

Las rutas se protegen automáticamente mediante el middleware de Next.js:

```typescript
// middleware.ts
export function middleware(request: NextRequest) {
  const token = request.cookies.get('access_token');

  if (!token) {
    return NextResponse.redirect(new URL('/login', request.url));
  }
}
```

## Manejo de Errores

Los errores de API se manejan automáticamente:

```typescript
try {
  await axios.post('/endpoint', data);
} catch (error: any) {
  const message = error.response?.data?.message || 'Error desconocido';
  // Mostrar mensaje al usuario
}
```

## Testing

```bash
# Ejecutar tests
npm run test

# Tests con coverage
npm run test:coverage
```

## Build y Deployment

```bash
# Build
npm run build

# El build se genera en .next/
# Puede desplegarse en Vercel, Netlify, o cualquier servidor Node.js
```

## Troubleshooting

### Error de conexión con API
- Verificar que `API_URL` en `.env.local` sea correcto
- Verificar que el backend esté corriendo
- Verificar CORS en el backend

### Token expirado
- El sistema renueva automáticamente los tokens
- Si persiste, hacer logout y login nuevamente

### Estilos no se aplican
- Verificar que Tailwind esté configurado correctamente
- Ejecutar `npm run dev` nuevamente


