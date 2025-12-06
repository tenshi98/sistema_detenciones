/**
 * Dashboard Page
 *
 * Dashboard principal con detenciones abiertas
 */

'use client';

import React, { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import axios from '@/lib/axios';
import { isAuthenticated } from '@/lib/auth';
import NavBar from '@/components/layout/NavBar';
import Card from '@/components/ui/Card';
import Button from '@/components/ui/Button';

export default function DashboardPage() {
    const router = useRouter();
    const [detenciones, setDetenciones] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!isAuthenticated()) {
            router.push('/login');
            return;
        }
        fetchDetenciones();
    }, []);

    const fetchDetenciones = async () => {
        try {
            const response = await axios.get('/detenciones/abiertas');
            setDetenciones(response.data.data || []);
        } catch (err) {
            console.error('Error fetching detenciones:', err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50">
            <NavBar />

            <div className="container mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold mb-6">Dashboard - Detenciones Abiertas</h1>

                {loading ? (
                    <div className="text-center py-8">Cargando...</div>
                ) : detenciones.length === 0 ? (
                    <Card>
                        <p className="text-center text-gray-500">No hay detenciones abiertas</p>
                    </Card>
                ) : (
                    <div className="grid gap-4">
                        {detenciones.map((det) => (
                            <Card key={det.idDetencion}>
                                <div className="flex justify-between items-start">
                                    <div className="space-y-2">
                                        <h3 className="text-lg font-semibold">
                                            {det.LineaNombre} - Turno {det.TurnoNombre}
                                        </h3>
                                        <div className="text-sm text-gray-600 space-y-1">
                                            <p><strong>Fecha:</strong> {new Date(det.Fecha).toLocaleDateString()}</p>
                                            <p><strong>Supervisor:</strong> {det.SupervisorNombre}</p>
                                            <p><strong>Tipo Producción:</strong> {det.TipoProduccionNombre}</p>
                                            <p><strong>Usuario:</strong> {det.UsuarioNombre}</p>
                                            {det.Observaciones && <p><strong>Observaciones:</strong> {det.Observaciones}</p>}
                                        </div>
                                    </div>
                                    <div className="flex space-x-2">
                                        <Button
                                            size="sm"
                                            variant="secondary"
                                            onClick={() => router.push(`/detencionesEditar/${det.idDetencion}`)}
                                        >
                                            Ver/Editar
                                        </Button>
                                    </div>
                                </div>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
