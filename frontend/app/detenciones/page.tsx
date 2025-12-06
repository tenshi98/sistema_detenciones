/**
 * Detenciones List Page (Admin y Control Tiempo)
 */

'use client';

import React, { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import axios from '@/lib/axios';
import { isAuthenticated, hasPermission, getCurrentUser } from '@/lib/auth';
import NavBar from '@/components/layout/NavBar';
import Card from '@/components/ui/Card';
import Button from '@/components/ui/Button';
import Modal from '@/components/ui/Modal';
import Input from '@/components/ui/Input';

export default function DetencionesPage() {
    const router = useRouter();
    const [detenciones, setDetenciones] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [formData, setFormData] = useState<any>({});
    const [lineas, setLineas] = useState<any[]>([]);
    const [turnos, setTurnos] = useState<any[]>([]);
    const [supervisores, setSupervisores] = useState<any[]>([]);
    const [tiposProduccion, setTiposProduccion] = useState<any[]>([]);
    const [error, setError] = useState('');

    useEffect(() => {
        if (!isAuthenticated()) {
            router.push('/login');
            return;
        }
        if (!hasPermission()) {
            router.push('/dashboard');
            return;
        }
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const [detRes, linRes, turRes, supRes, tipRes] = await Promise.all([
                axios.get('/detenciones'),
                axios.get('/lineas'),
                axios.get('/turnos'),
                axios.get('/supervisor'),
                axios.get('/tipoProduccion'),
            ]);
            setDetenciones(detRes.data.data || []);
            setLineas(linRes.data.data || []);
            setTurnos(turRes.data.data || []);
            setSupervisores(supRes.data.data || []);
            setTiposProduccion(tipRes.data.data || []);
        } catch (err) {
            console.error('Error fetching data:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleCreate = () => {
        setFormData({
            Fecha: new Date().toISOString().split('T')[0],
        });
        setError('');
        setIsModalOpen(true);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        try {
            const response = await axios.post('/detenciones', formData);
            const newId = response.data.data.id;
            setIsModalOpen(false);
            router.push(`/detencionesEditar/${newId}`);
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al crear detención');
        }
    };

    if (loading) {
        return <div className="min-h-screen bg-gray-50"><NavBar /><div className="text-center py-8">Cargando...</div></div>;
    }

    return (
        <div className="min-h-screen bg-gray-50">
            <NavBar />
            <div className="container mx-auto px-4 py-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold">Detenciones</h1>
                    <Button onClick={handleCreate}>Crear Nueva Detención</Button>
                </div>

                <div className="grid gap-4">
                    {detenciones.map((det) => (
                        <Card key={det.idDetencion}>
                            <div className="flex justify-between items-start">
                                <div className="space-y-2">
                                    <h3 className="text-lg font-semibold">
                                        Detención #{det.idDetencion}
                                    </h3>
                                    <div className="text-sm text-gray-600 space-y-1">
                                        <p><strong>Fecha:</strong> {new Date(det.Fecha).toLocaleDateString()}</p>
                                        <p><strong>Estado:</strong> <span className={`px-2 py-1 rounded text-xs ${det.idEstado === 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                                            {det.idEstado === 1 ? 'Abierta' : 'Cerrada'}
                                        </span></p>
                                    </div>
                                </div>
                                <Button
                                    size="sm"
                                    onClick={() => router.push(`/detencionesEditar/${det.idDetencion}`)}
                                >
                                    Ver/Editar
                                </Button>
                            </div>
                        </Card>
                    ))}
                </div>

                <Modal
                    isOpen={isModalOpen}
                    onClose={() => setIsModalOpen(false)}
                    title="Crear Nueva Detención"
                    size="lg"
                >
                    <form onSubmit={handleSubmit} className="space-y-4">
                        {error && (
                            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                {error}
                            </div>
                        )}

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Línea <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={formData.idLinea || ''}
                                    onChange={(e) => setFormData({ ...formData, idLinea: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {lineas.map((l) => (
                                        <option key={l.idLinea} value={l.idLinea}>{l.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Turno <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={formData.idTurnos || ''}
                                    onChange={(e) => setFormData({ ...formData, idTurnos: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {turnos.map((t) => (
                                        <option key={t.idTurnos} value={t.idTurnos}>{t.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Supervisor <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={formData.idSupervisor || ''}
                                    onChange={(e) => setFormData({ ...formData, idSupervisor: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {supervisores.map((s) => (
                                        <option key={s.idSupervisor} value={s.idSupervisor}>{s.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Producción <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={formData.idTipoProduccion || ''}
                                    onChange={(e) => setFormData({ ...formData, idTipoProduccion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {tiposProduccion.map((tp) => (
                                        <option key={tp.idTipoProduccion} value={tp.idTipoProduccion}>{tp.Nombre}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <Input
                            label="Fecha"
                            type="date"
                            value={formData.Fecha || ''}
                            onChange={(e) => setFormData({ ...formData, Fecha: e.target.value })}
                            required
                        />

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones
                            </label>
                            <textarea
                                value={formData.Observaciones || ''}
                                onChange={(e) => setFormData({ ...formData, Observaciones: e.target.value })}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                rows={3}
                            />
                        </div>

                        <div className="flex justify-end space-x-2 pt-4">
                            <Button type="button" variant="secondary" onClick={() => setIsModalOpen(false)}>
                                Cancelar
                            </Button>
                            <Button type="submit">Crear y Editar</Button>
                        </div>
                    </form>
                </Modal>
            </div>
        </div>
    );
}
