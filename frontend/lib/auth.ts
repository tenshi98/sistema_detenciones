/**
 * Auth Utilities
 * 
 * Funciones de utilidad para autenticación
 */

import axios from './axios';

export interface User {
    idUsuario: number;
    idTipoUsuario: number;
    UserName: string;
    Nombre: string;
    ApellidoPat?: string;
    ApellidoMat?: string;
}

export interface LoginResponse {
    access_token: string;
    refresh_token: string;
    user: User;
}

/**
 * Login de usuario
 */
export async function login(username: string, password: string): Promise<LoginResponse> {
    const response = await axios.post('/auth/login', {
        UserName: username,
        Password: password,
    });

    const data = response.data.data;

    // Guardar tokens y usuario en localStorage
    localStorage.setItem('access_token', data.access_token);
    localStorage.setItem('refresh_token', data.refresh_token);
    localStorage.setItem('user', JSON.stringify(data.user));

    return data;
}

/**
 * Logout de usuario
 */
export function logout(): void {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
}

/**
 * Obtiene el usuario actual
 */
export function getCurrentUser(): User | null {
    const userStr = localStorage.getItem('user');
    if (!userStr) return null;

    try {
        return JSON.parse(userStr);
    } catch {
        return null;
    }
}

/**
 * Verifica si el usuario está autenticado
 */
export function isAuthenticated(): boolean {
    return !!localStorage.getItem('access_token');
}

/**
 * Verifica si el usuario es administrador
 */
export function isAdmin(): boolean {
    const user = getCurrentUser();
    return user?.idTipoUsuario === 1;
}

/**
 * Verifica si el usuario es Control Tiempo
 */
export function isControlTiempo(): boolean {
    const user = getCurrentUser();
    return user?.idTipoUsuario === 2;
}

/**
 * Verifica si el usuario tiene permiso (Admin o Control Tiempo)
 */
export function hasPermission(): boolean {
    return isAdmin() || isControlTiempo();
}
