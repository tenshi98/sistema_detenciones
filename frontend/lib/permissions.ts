/**
 * Permissions Configuration
 * 
 * Define qué páginas puede ver cada tipo de usuario
 */

export interface MenuItem {
    name: string;
    path: string;
    allowedUserTypes: number[]; // IDs de tipos de usuario permitidos
}

export const menuItems: MenuItem[] = [
    {
        name: 'Dashboard',
        path: '/dashboard',
        allowedUserTypes: [1, 2, 3, 4], // Todos
    },
    // Solo Administrador
    {
        name: 'Tipos de Usuario',
        path: '/userTypes',
        allowedUserTypes: [1],
    },
    {
        name: 'Usuarios',
        path: '/users',
        allowedUserTypes: [1],
    },
    {
        name: 'Líneas',
        path: '/lineas',
        allowedUserTypes: [1],
    },
    {
        name: 'Turnos',
        path: '/turnos',
        allowedUserTypes: [1],
    },
    {
        name: 'Supervisores',
        path: '/supervisor',
        allowedUserTypes: [1],
    },
    {
        name: 'Tipos de Producción',
        path: '/tipoProduccion',
        allowedUserTypes: [1],
    },
    {
        name: 'Tipos de Tiempos',
        path: '/tiposTiempo',
        allowedUserTypes: [1],
    },
    {
        name: 'Familias de Tiempos',
        path: '/familiasTiempo',
        allowedUserTypes: [1],
    },
    {
        name: 'Tiempos',
        path: '/tiempos',
        allowedUserTypes: [1],
    },
    {
        name: 'Estados de Detenciones',
        path: '/detencionesEstados',
        allowedUserTypes: [1],
    },
    // Admin y Control Tiempo
    {
        name: 'Materiales',
        path: '/material',
        allowedUserTypes: [1, 2],
    },
    {
        name: 'Rutas Material',
        path: '/rutaMaterial',
        allowedUserTypes: [1, 2],
    },
    {
        name: 'Rutas Producción',
        path: '/rutaProduccion',
        allowedUserTypes: [1, 2],
    },
    {
        name: 'Detenciones',
        path: '/detenciones',
        allowedUserTypes: [1, 2],
    },
];

/**
 * Filtra el menú según el tipo de usuario
 */
export function getMenuForUser(userTypeId: number): MenuItem[] {
    return menuItems.filter(item => item.allowedUserTypes.includes(userTypeId));
}

/**
 * Verifica si un usuario puede acceder a una ruta
 */
export function canAccessRoute(userTypeId: number, path: string): boolean {
    const item = menuItems.find(item => item.path === path);
    if (!item) return true; // Si no está en el menú, permitir acceso
    return item.allowedUserTypes.includes(userTypeId);
}
