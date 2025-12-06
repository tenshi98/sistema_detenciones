/**
 * NavBar Component
 * 
 * Barra de navegación con menú dinámico según permisos
 */

'use client';

import React from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { getCurrentUser, logout } from '@/lib/auth';
import { getMenuForUser } from '@/lib/permissions';

export default function NavBar() {
    const router = useRouter();
    const user = getCurrentUser();
    const menuItems = user ? getMenuForUser(user.idTipoUsuario) : [];

    const handleLogout = () => {
        logout();
        router.push('/login');
    };

    if (!user) return null;

    return (
        <nav className="bg-blue-600 text-white shadow-lg">
            <div className="container mx-auto px-4">
                <div className="flex items-center justify-between h-16">
                    {/* Logo */}
                    <div className="flex items-center">
                        <Link href="/dashboard" className="text-xl font-bold">
                            Sistema de Detenciones
                        </Link>
                    </div>

                    {/* Menu Items */}
                    <div className="hidden md:flex items-center space-x-4">
                        {menuItems.map((item) => (
                            <Link
                                key={item.path}
                                href={item.path}
                                className="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition"
                            >
                                {item.name}
                            </Link>
                        ))}
                    </div>

                    {/* User Menu */}
                    <div className="flex items-center space-x-4">
                        <span className="text-sm">
                            {user.Nombre} {user.ApellidoPat}
                        </span>
                        <button
                            onClick={handleLogout}
                            className="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-md text-sm font-medium transition"
                        >
                            Cerrar Sesión
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    );
}
