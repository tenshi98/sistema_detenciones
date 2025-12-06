/**
 * CRUDTable Component
 * 
 * Tabla CRUD reutilizable con paginación y acciones
 */

'use client';

import React, { useState, useEffect } from 'react';
import axios from '@/lib/axios';
import Button from '../ui/Button';
import Modal from '../ui/Modal';
import Input from '../ui/Input';

interface Column {
    key: string;
    label: string;
    render?: (value: any, row: any) => React.ReactNode;
}

interface CRUDTableProps {
    endpoint: string;
    columns: Column[];
    title: string;
    fields: Array<{
        name: string;
        label: string;
        type?: string;
        required?: boolean;
    }>;
    onValidate?: (data: any) => string | null;
}

export default function CRUDTable({
    endpoint,
    columns,
    title,
    fields,
    onValidate,
}: CRUDTableProps) {
    const [data, setData] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState<any>(null);
    const [formData, setFormData] = useState<any>({});
    const [error, setError] = useState('');

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            setLoading(true);
            const response = await axios.get(endpoint);
            setData(response.data.data || []);
        } catch (err: any) {
            console.error('Error fetching data:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleCreate = () => {
        setEditingItem(null);
        setFormData({});
        setError('');
        setIsModalOpen(true);
    };

    const handleEdit = (item: any) => {
        setEditingItem(item);
        setFormData(item);
        setError('');
        setIsModalOpen(true);
    };

    const handleDelete = async (id: number) => {
        if (!confirm('¿Está seguro de eliminar este registro?')) return;

        try {
            await axios.delete(`${endpoint}/${id}`);
            fetchData();
        } catch (err: any) {
            alert(err.response?.data?.message || 'Error al eliminar');
        }
    };

    const handleActivate = async (id: number) => {
        try {
            await axios.post(`${endpoint}/${id}/activate`);
            fetchData();
        } catch (err: any) {
            alert(err.response?.data?.message || 'Error al activar');
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        // Validación personalizada
        if (onValidate) {
            const validationError = onValidate(formData);
            if (validationError) {
                setError(validationError);
                return;
            }
        }

        try {
            if (editingItem) {
                await axios.put(`${endpoint}/${editingItem[Object.keys(editingItem)[0]]}`, formData);
            } else {
                await axios.post(endpoint, formData);
            }
            setIsModalOpen(false);
            fetchData();
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al guardar');
        }
    };

    if (loading) {
        return <div className="text-center py-8">Cargando...</div>;
    }

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold">{title}</h1>
                <Button onClick={handleCreate}>Crear Nuevo</Button>
            </div>

            <div className="bg-white rounded-lg shadow overflow-hidden">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map((col) => (
                                <th
                                    key={col.key}
                                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    {col.label}
                                </th>
                            ))}
                            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {data.map((row, idx) => (
                            <tr key={idx}>
                                {columns.map((col) => (
                                    <td key={col.key} className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {col.render ? col.render(row[col.key], row) : row[col.key]}
                                    </td>
                                ))}
                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button
                                        onClick={() => handleEdit(row)}
                                        className="text-blue-600 hover:text-blue-900"
                                    >
                                        Editar
                                    </button>
                                    {row.Activo === 1 ? (
                                        <button
                                            onClick={() => handleDelete(row[Object.keys(row)[0]])}
                                            className="text-red-600 hover:text-red-900"
                                        >
                                            Desactivar
                                        </button>
                                    ) : (
                                        <button
                                            onClick={() => handleActivate(row[Object.keys(row)[0]])}
                                            className="text-green-600 hover:text-green-900"
                                        >
                                            Activar
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <Modal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                title={editingItem ? 'Editar Registro' : 'Crear Registro'}
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    {error && (
                        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            {error}
                        </div>
                    )}

                    {fields.map((field) => (
                        <Input
                            key={field.name}
                            label={field.label}
                            type={field.type || 'text'}
                            value={formData[field.name] || ''}
                            onChange={(e) => setFormData({ ...formData, [field.name]: e.target.value })}
                            required={field.required}
                        />
                    ))}

                    <div className="flex justify-end space-x-2 pt-4">
                        <Button type="button" variant="secondary" onClick={() => setIsModalOpen(false)}>
                            Cancelar
                        </Button>
                        <Button type="submit">Guardar</Button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}
