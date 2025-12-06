/**
 * Tipo Produccion Page (Solo Administrador)
 */

'use client';

import React, { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { isAuthenticated, isAdmin } from '@/lib/auth';
import NavBar from '@/components/layout/NavBar';
import CRUDTable from '@/components/tables/CRUDTable';

export default function TipoProduccionPage() {
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
                    endpoint="/tipoProduccion"
                    title="Tipos de Producción"
                    columns={[
                        { key: 'idTipoProduccion', label: 'ID' },
                        { key: 'Nombre', label: 'Nombre' },
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
                        { name: 'Nombre', label: 'Nombre del Tipo', required: true },
                    ]}
                />
            </div>
        </div>
    );
}
