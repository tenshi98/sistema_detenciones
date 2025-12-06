/**
 * Detenciones Edit Page (Admin y Control Tiempo)
 * 
 * Página compleja con 3 tabs:
 * - Identificación: Editar datos de la detención
 * - Resumen: Ver resúmenes y porcentajes
 * - Detalle: Gestionar OFs y detalles
 */

'use client';

import React, { useState, useEffect } from 'react';
import { useRouter, useParams } from 'next/navigation';
import axios from '@/lib/axios';
import { isAuthenticated, hasPermission } from '@/lib/auth';
import { exportDetencionToPDF, exportToExcel } from '@/lib/export';
import NavBar from '@/components/layout/NavBar';
import Card from '@/components/ui/Card';
import Button from '@/components/ui/Button';
import Input from '@/components/ui/Input';
import Tabs from '@/components/ui/Tabs';
import Modal from '@/components/ui/Modal';

export default function DetencionesEditarPage() {
    const router = useRouter();
    const params = useParams();
    const id = params?.id;

    const [detencion, setDetencion] = useState<any>(null);
    const [ofs, setOfs] = useState<any[]>([]);
    const [resumen, setResumen] = useState<any[]>([]);
    const [porcentajes, setPorcentajes] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    // Catálogos
    const [lineas, setLineas] = useState<any[]>([]);
    const [turnos, setTurnos] = useState<any[]>([]);
    const [supervisores, setSupervisores] = useState<any[]>([]);
    const [tiposProduccion, setTiposProduccion] = useState<any[]>([]);
    const [estados, setEstados] = useState<any[]>([]);
    const [materiales, setMateriales] = useState<any[]>([]);
    const [rutasMaterial, setRutasMaterial] = useState<any[]>([]);
    const [rutasProduccion, setRutasProduccion] = useState<any[]>([]);
    const [tiempos, setTiempos] = useState<any[]>([]);

    // Modales
    const [isOFModalOpen, setIsOFModalOpen] = useState(false);
    const [isDetalleModalOpen, setIsDetalleModalOpen] = useState(false);
    const [editingOF, setEditingOF] = useState<any>(null);
    const [editingDetalle, setEditingDetalle] = useState<any>(null);
    const [ofFormData, setOfFormData] = useState<any>({});
    const [detalleFormData, setDetalleFormData] = useState<any>({});
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
    }, [id]);

    const fetchData = async () => {
        try {
            const [
                detRes,
                ofRes,
                resRes,
                porRes,
                linRes,
                turRes,
                supRes,
                tipRes,
                estRes,
                matRes,
                rutMRes,
                rutPRes,
                tieRes,
            ] = await Promise.all([
                axios.get(`/detenciones/${id}`),
                axios.get(`/detencionesOF/detencion/${id}`),
                axios.get(`/detencionesOFDetalle/resumen/${id}`),
                axios.get(`/detencionesOFDetalle/porcentajes/${id}`),
                axios.get('/lineas'),
                axios.get('/turnos'),
                axios.get('/supervisor'),
                axios.get('/tipoProduccion'),
                axios.get('/detencionesEstados'),
                axios.get('/material'),
                axios.get('/rutaMaterial'),
                axios.get('/rutaProduccion'),
                axios.get('/tiempos'),
            ]);

            setDetencion(detRes.data.data);
            setOfs(ofRes.data.data || []);
            setResumen(resRes.data.data || []);
            setPorcentajes(porRes.data.data || []);
            setLineas(linRes.data.data || []);
            setTurnos(turRes.data.data || []);
            setSupervisores(supRes.data.data || []);
            setTiposProduccion(tipRes.data.data || []);
            setEstados(estRes.data.data || []);
            setMateriales(matRes.data.data || []);
            setRutasMaterial(rutMRes.data.data || []);
            setRutasProduccion(rutPRes.data.data || []);
            setTiempos(tieRes.data.data || []);

            // Fetch detalles for each OF
            const ofsWithDetalles = await Promise.all(
                (ofRes.data.data || []).map(async (of: any) => {
                    const detRes = await axios.get(`/detencionesOFDetalle/of/${of.idDetencionOF}`);
                    return { ...of, detalles: detRes.data.data || [] };
                })
            );
            setOfs(ofsWithDetalles);
        } catch (err) {
            console.error('Error fetching data:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleUpdateDetencion = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            await axios.put(`/detenciones/${id}`, detencion);
            alert('Detención actualizada exitosamente');
        } catch (err: any) {
            alert(err.response?.data?.message || 'Error al actualizar');
        }
    };

    const handleCreateOF = () => {
        setEditingOF(null);
        setOfFormData({ idDetencion: id });
        setError('');
        setIsOFModalOpen(true);
    };

    const handleEditOF = (of: any) => {
        setEditingOF(of);
        setOfFormData(of);
        setError('');
        setIsOFModalOpen(true);
    };

    const handleSubmitOF = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        try {
            if (editingOF) {
                await axios.put(`/detencionesOF/${editingOF.idDetencionOF}`, ofFormData);
            } else {
                await axios.post('/detencionesOF', ofFormData);
            }
            setIsOFModalOpen(false);
            fetchData();
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al guardar');
        }
    };

    const handleCreateDetalle = (ofId: number) => {
        setEditingDetalle(null);
        setDetalleFormData({
            idDetencionOF: ofId,
            idDetencion: id,
            Fecha: new Date().toISOString().split('T')[0],
        });
        setError('');
        setIsDetalleModalOpen(true);
    };

    const handleEditDetalle = (detalle: any) => {
        setEditingDetalle(detalle);
        setDetalleFormData(detalle);
        setError('');
        setIsDetalleModalOpen(true);
    };

    const handleSubmitDetalle = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        try {
            if (editingDetalle) {
                await axios.put(`/detencionesOFDetalle/${editingDetalle.idDetalles}`, detalleFormData);
            } else {
                await axios.post('/detencionesOFDetalle', detalleFormData);
            }
            setIsDetalleModalOpen(false);
            fetchData();
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al guardar');
        }
    };

    const handleDeleteDetalle = async (detalleId: number) => {
        if (!confirm('¿Está seguro de eliminar este detalle?')) return;

        try {
            await axios.delete(`/detencionesOFDetalle/${detalleId}`);
            fetchData();
        } catch (err: any) {
            alert(err.response?.data?.message || 'Error al eliminar');
        }
    };

    if (loading) {
        return <div className="min-h-screen bg-gray-50"><NavBar /><div className="text-center py-8">Cargando...</div></div>;
    }

    if (!detencion) {
        return <div className="min-h-screen bg-gray-50"><NavBar /><div className="text-center py-8">Detención no encontrada</div></div>;
    }

    const tabs = [
        {
            id: 'identificacion',
            label: 'Identificación',
            content: (
                <Card>
                    <form onSubmit={handleUpdateDetencion} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Línea</label>
                                <select
                                    value={detencion.idLinea || ''}
                                    onChange={(e) => setDetencion({ ...detencion, idLinea: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    {lineas.map((l) => (
                                        <option key={l.idLinea} value={l.idLinea}>{l.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Turno</label>
                                <select
                                    value={detencion.idTurnos || ''}
                                    onChange={(e) => setDetencion({ ...detencion, idTurnos: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    {turnos.map((t) => (
                                        <option key={t.idTurnos} value={t.idTurnos}>{t.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Supervisor</label>
                                <select
                                    value={detencion.idSupervisor || ''}
                                    onChange={(e) => setDetencion({ ...detencion, idSupervisor: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    {supervisores.map((s) => (
                                        <option key={s.idSupervisor} value={s.idSupervisor}>{s.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Tipo de Producción</label>
                                <select
                                    value={detencion.idTipoProduccion || ''}
                                    onChange={(e) => setDetencion({ ...detencion, idTipoProduccion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    {tiposProduccion.map((tp) => (
                                        <option key={tp.idTipoProduccion} value={tp.idTipoProduccion}>{tp.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select
                                    value={detencion.idEstado || ''}
                                    onChange={(e) => setDetencion({ ...detencion, idEstado: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    {estados.map((e) => (
                                        <option key={e.idEstado} value={e.idEstado}>{e.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <Input
                                label="Fecha"
                                type="date"
                                value={detencion.Fecha?.split('T')[0] || ''}
                                onChange={(e) => setDetencion({ ...detencion, Fecha: e.target.value })}
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea
                                value={detencion.Observaciones || ''}
                                onChange={(e) => setDetencion({ ...detencion, Observaciones: e.target.value })}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                rows={3}
                            />
                        </div>

                        <div className="flex justify-end">
                            <Button type="submit">Guardar Cambios</Button>
                        </div>
                    </form>
                </Card>
            ),
        },
        {
            id: 'resumen',
            label: 'Resumen',
            content: (
                <div className="space-y-6">
                    <div className="flex justify-end space-x-2 mb-4">
                        <Button
                            variant="secondary"
                            onClick={() => exportToExcel(resumen, `detencion_${id}_resumen`)}
                        >
                            📊 Exportar a Excel
                        </Button>
                        <Button
                            variant="primary"
                            onClick={() => exportDetencionToPDF(detencion, resumen, porcentajes)}
                        >
                            📄 Exportar a PDF
                        </Button>
                    </div>

                    <Card title="Resumen por OF, Tipo y Familia">
                        <table className="min-w-full">
                            <thead>
                                <tr className="border-b">
                                    <th className="text-left py-2">Lote</th>
                                    <th className="text-left py-2">Tipo Tiempo</th>
                                    <th className="text-left py-2">Familia Tiempo</th>
                                    <th className="text-right py-2">Total Minutos</th>
                                </tr>
                            </thead>
                            <tbody>
                                {resumen.map((r, idx) => (
                                    <tr key={idx} className="border-b">
                                        <td className="py-2">{r.Lote}</td>
                                        <td className="py-2">{r.TipoTiempo}</td>
                                        <td className="py-2">{r.FamiliaTiempo}</td>
                                        <td className="py-2 text-right font-semibold">{r.TotalMinutos}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Card>

                    <Card title="Porcentajes por Tipo y Familia">
                        <table className="min-w-full">
                            <thead>
                                <tr className="border-b">
                                    <th className="text-left py-2">Tipo Tiempo</th>
                                    <th className="text-left py-2">Familia Tiempo</th>
                                    <th className="text-right py-2">Total Minutos</th>
                                    <th className="text-right py-2">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                {porcentajes.map((p, idx) => (
                                    <tr key={idx} className="border-b">
                                        <td className="py-2">{p.TipoTiempo}</td>
                                        <td className="py-2">{p.FamiliaTiempo}</td>
                                        <td className="py-2 text-right font-semibold">{p.TotalMinutos}</td>
                                        <td className="py-2 text-right font-semibold text-blue-600">{p.Porcentaje}%</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Card>
                </div>
            ),
        },
        {
            id: 'detalle',
            label: 'Detalle',
            content: (
                <div className="space-y-4">
                    <div className="flex justify-between items-center">
                        <h3 className="text-lg font-semibold">Órdenes de Fabricación y Detalles</h3>
                        <Button onClick={handleCreateOF}>Nueva Orden de Fabricación</Button>
                    </div>

                    {ofs.map((of) => (
                        <Card key={of.idDetencionOF}>
                            <div className="space-y-4">
                                {/* OF Header */}
                                <div className="flex justify-between items-start border-b pb-4">
                                    <div>
                                        <h4 className="font-semibold text-lg">OF #{of.idDetencionOF} - Lote: {of.Lote}</h4>
                                        <div className="text-sm text-gray-600 mt-2 space-y-1">
                                            <p><strong>Material:</strong> {of.MaterialNombre}</p>
                                            <p><strong>Ruta Material:</strong> {of.RutaMaterialNombre}</p>
                                            <p><strong>Ruta Producción:</strong> {of.RutaProduccionNombre}</p>
                                            <p><strong>Cantidad:</strong> {of.CantidadProd}</p>
                                            <p><strong>Vel. Nominal:</strong> {of.VelNominal}</p>
                                        </div>
                                    </div>
                                    <div className="flex space-x-2">
                                        <Button size="sm" variant="secondary" onClick={() => handleEditOF(of)}>
                                            Editar OF
                                        </Button>
                                        <Button size="sm" onClick={() => handleCreateDetalle(of.idDetencionOF)}>
                                            Nuevo Detalle
                                        </Button>
                                    </div>
                                </div>

                                {/* Detalles Table */}
                                {of.detalles && of.detalles.length > 0 && (
                                    <table className="min-w-full text-sm">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-4 py-2 text-left">Tiempo</th>
                                                <th className="px-4 py-2 text-left">Fecha</th>
                                                <th className="px-4 py-2 text-left">Hora Inicio</th>
                                                <th className="px-4 py-2 text-left">Hora Término</th>
                                                <th className="px-4 py-2 text-right">Minutos</th>
                                                <th className="px-4 py-2 text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {of.detalles.map((det: any) => (
                                                <tr key={det.idDetalles} className="border-b">
                                                    <td className="px-4 py-2">{det.TiempoNombre}</td>
                                                    <td className="px-4 py-2">{new Date(det.Fecha).toLocaleDateString()}</td>
                                                    <td className="px-4 py-2">{det.HoraInicio}</td>
                                                    <td className="px-4 py-2">{det.HoraTermino}</td>
                                                    <td className="px-4 py-2 text-right font-semibold">{det.Minutos}</td>
                                                    <td className="px-4 py-2 text-right space-x-2">
                                                        <button
                                                            onClick={() => handleEditDetalle(det)}
                                                            className="text-blue-600 hover:text-blue-900"
                                                        >
                                                            Editar
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteDetalle(det.idDetalles)}
                                                            className="text-red-600 hover:text-red-900"
                                                        >
                                                            Borrar
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                )}
                            </div>
                        </Card>
                    ))}
                </div>
            ),
        },
    ];

    return (
        <div className="min-h-screen bg-gray-50">
            <NavBar />
            <div className="container mx-auto px-4 py-8">
                <div className="mb-6">
                    <Button variant="secondary" onClick={() => router.push('/detenciones')}>
                        ← Volver a Detenciones
                    </Button>
                </div>

                <h1 className="text-2xl font-bold mb-6">Editar Detención #{id}</h1>

                <Tabs tabs={tabs} defaultTab="identificacion" />

                {/* Modal OF */}
                <Modal
                    isOpen={isOFModalOpen}
                    onClose={() => setIsOFModalOpen(false)}
                    title={editingOF ? 'Editar Orden de Fabricación' : 'Nueva Orden de Fabricación'}
                    size="lg"
                >
                    <form onSubmit={handleSubmitOF} className="space-y-4">
                        {error && (
                            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                {error}
                            </div>
                        )}

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Material *</label>
                                <select
                                    value={ofFormData.idMaterial || ''}
                                    onChange={(e) => setOfFormData({ ...ofFormData, idMaterial: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {materiales.map((m) => (
                                        <option key={m.idMaterial} value={m.idMaterial}>{m.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Ruta Material *</label>
                                <select
                                    value={ofFormData.idMaterialRuta || ''}
                                    onChange={(e) => setOfFormData({ ...ofFormData, idMaterialRuta: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {rutasMaterial.map((rm) => (
                                        <option key={rm.idMaterialRuta} value={rm.idMaterialRuta}>{rm.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Ruta Producción *</label>
                                <select
                                    value={ofFormData.idRutaProduccion || ''}
                                    onChange={(e) => setOfFormData({ ...ofFormData, idRutaProduccion: e.target.value })}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {rutasProduccion.map((rp) => (
                                        <option key={rp.idRutaProduccion} value={rp.idRutaProduccion}>{rp.Nombre}</option>
                                    ))}
                                </select>
                            </div>

                            <Input
                                label="Cantidad Producida"
                                type="number"
                                value={ofFormData.CantidadProd || ''}
                                onChange={(e) => setOfFormData({ ...ofFormData, CantidadProd: e.target.value })}
                                required
                            />

                            <Input
                                label="Lote"
                                value={ofFormData.Lote || ''}
                                onChange={(e) => setOfFormData({ ...ofFormData, Lote: e.target.value })}
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea
                                value={ofFormData.Observaciones || ''}
                                onChange={(e) => setOfFormData({ ...ofFormData, Observaciones: e.target.value })}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                rows={3}
                            />
                        </div>

                        <div className="flex justify-end space-x-2 pt-4">
                            <Button type="button" variant="secondary" onClick={() => setIsOFModalOpen(false)}>
                                Cancelar
                            </Button>
                            <Button type="submit">Guardar</Button>
                        </div>
                    </form>
                </Modal>

                {/* Modal Detalle */}
                <Modal
                    isOpen={isDetalleModalOpen}
                    onClose={() => setIsDetalleModalOpen(false)}
                    title={editingDetalle ? 'Editar Detalle' : 'Nuevo Detalle'}
                >
                    <form onSubmit={handleSubmitDetalle} className="space-y-4">
                        {error && (
                            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                {error}
                            </div>
                        )}

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Tiempo *</label>
                            <select
                                value={detalleFormData.idTiempos || ''}
                                onChange={(e) => setDetalleFormData({ ...detalleFormData, idTiempos: e.target.value })}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            >
                                <option value="">Seleccione...</option>
                                {tiempos.map((t) => (
                                    <option key={t.idTiempos} value={t.idTiempos}>{t.Nombre}</option>
                                ))}
                            </select>
                        </div>

                        <Input
                            label="Fecha"
                            type="date"
                            value={detalleFormData.Fecha?.split('T')[0] || ''}
                            onChange={(e) => setDetalleFormData({ ...detalleFormData, Fecha: e.target.value })}
                            required
                        />

                        <div className="grid grid-cols-2 gap-4">
                            <Input
                                label="Hora Inicio"
                                type="time"
                                value={detalleFormData.HoraInicio || ''}
                                onChange={(e) => setDetalleFormData({ ...detalleFormData, HoraInicio: e.target.value })}
                                required
                            />

                            <Input
                                label="Hora Término"
                                type="time"
                                value={detalleFormData.HoraTermino || ''}
                                onChange={(e) => setDetalleFormData({ ...detalleFormData, HoraTermino: e.target.value })}
                                required
                            />
                        </div>

                        <Input
                            label="Minutos"
                            type="number"
                            value={detalleFormData.Minutos || ''}
                            onChange={(e) => setDetalleFormData({ ...detalleFormData, Minutos: e.target.value })}
                            required
                        />

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea
                                value={detalleFormData.Observaciones || ''}
                                onChange={(e) => setDetalleFormData({ ...detalleFormData, Observaciones: e.target.value })}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                rows={3}
                            />
                        </div>

                        <div className="flex justify-end space-x-2 pt-4">
                            <Button type="button" variant="secondary" onClick={() => setIsDetalleModalOpen(false)}>
                                Cancelar
                            </Button>
                            <Button type="submit">Guardar</Button>
                        </div>
                    </form>
                </Modal>
            </div>
        </div>
    );
}
