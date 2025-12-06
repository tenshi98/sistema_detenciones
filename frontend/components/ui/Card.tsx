/**
 * Card Component
 * 
 * Card para contenedores de contenido
 */

import React from 'react';

interface CardProps {
    children: React.ReactNode;
    title?: string;
    className?: string;
}

export default function Card({ children, title, className = '' }: CardProps) {
    return (
        <div className={`bg-white rounded-lg shadow-md ${className}`}>
            {title && (
                <div className="px-6 py-4 border-b">
                    <h2 className="text-xl font-semibold text-gray-900">{title}</h2>
                </div>
            )}
            <div className="p-6">
                {children}
            </div>
        </div>
    );
}
