/**
 * Users Page (Solo Administrador)
 */

'use client';

import React, { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { isAuthenticated, isAdmin } from '@/lib/auth';
import NavBar from '@/components/layout/NavBar';
import CRUDTable from '@/components/tables/CRUDTable';

export default function UsersPage() {
    const router = useRouter();

    useEffect(() => {
        if (!isAuthenticated()) {
            router.push('/login');
        } else if (!isAdmin()) {
            router.push('/dashboard');
        }
    }, []);

    return (
        <div className="min-h-screen bg-gray-50">
            <NavBar />
            <div className="container mx-auto px-4 py-8">
                <CRUDTable
                    endpoint="/users"
                    title="Usuarios"
                    columns={[
                        { key: 'idUsuario', label: 'ID' },
                        { key: 'UserName', label: 'Usuario' },
                        { key: 'Nombre', label: 'Nombre' },
                        { key: 'ApellidoPat', label: 'Apellido Paterno' },
                        {
                            key: 'Activo',
                            label: 'Estado',
                            render: (value) => (
                                <span className={`px-2 py-1 rounded text-xs ${value === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                    {value === 1 ? 'Activo' : 'Inactivo'}
                                </span>
                            ),
                        },
                    ]}
                    fields={[
                        { name: 'UserName', label: 'Nombre de Usuario', required: true },
                        { name: 'Password', label: 'Contraseña', type: 'password', required: true },
                        { name: 'Nombre', label: 'Nombre', required: true },
                        { name: 'ApellidoPat', label: 'Apellido Paterno', required: true },
                        { name: 'ApellidoMat', label: 'Apellido Materno' },
                    ]}
                />
            </div>
        </div>
    );
}
