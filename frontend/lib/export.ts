/**
 * Export Utilities
 * 
 * Utilidades para exportar datos a Excel y PDF
 */

/**
 * Exporta datos a Excel
 */
export function exportToExcel(data: any[], filename: string) {
    // Convertir datos a CSV
    if (data.length === 0) {
        alert('No hay datos para exportar');
        return;
    }

    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row =>
            headers.map(header => {
                const value = row[header];
                // Escapar valores con comas o comillas
                if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value;
            }).join(',')
        )
    ].join('\n');

    // Crear blob y descargar
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', `${filename}.csv`);
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Exporta datos a PDF
 */
export function exportToPDF(title: string, data: any[], filename: string) {
    if (data.length === 0) {
        alert('No hay datos para exportar');
        return;
    }

    // Crear contenido HTML para el PDF
    const headers = Object.keys(data[0]);

    const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>${title}</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
        }
        h1 {
          color: #333;
          border-bottom: 2px solid #0ea5e9;
          padding-bottom: 10px;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
        }
        th {
          background-color: #0ea5e9;
          color: white;
          padding: 10px;
          text-align: left;
          font-weight: bold;
        }
        td {
          padding: 8px;
          border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
          background-color: #f9f9f9;
        }
        .footer {
          margin-top: 30px;
          text-align: center;
          color: #666;
          font-size: 12px;
        }
      </style>
    </head>
    <body>
      <h1>${title}</h1>
      <p>Fecha de generación: ${new Date().toLocaleString('es-CL')}</p>
      <table>
        <thead>
          <tr>
            ${headers.map(h => `<th>${h}</th>`).join('')}
          </tr>
        </thead>
        <tbody>
          ${data.map(row => `
            <tr>
              ${headers.map(h => `<td>${row[h] || ''}</td>`).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
      <div class="footer">
        <p>Sistema de Registro de Tiempos Muertos</p>
      </div>
    </body>
    </html>
  `;

    // Abrir ventana de impresión
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        printWindow.focus();

        // Esperar a que cargue y luego imprimir
        setTimeout(() => {
            printWindow.print();
        }, 250);
    }
}

/**
 * Exporta resumen de detención a PDF
 */
export function exportDetencionToPDF(detencion: any, resumen: any[], porcentajes: any[]) {
    const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Detención #${detencion.idDetencion}</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
        }
        h1 {
          color: #333;
          border-bottom: 3px solid #0ea5e9;
          padding-bottom: 10px;
        }
        h2 {
          color: #0ea5e9;
          margin-top: 30px;
          border-bottom: 1px solid #ddd;
          padding-bottom: 5px;
        }
        .info-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 15px;
          margin: 20px 0;
        }
        .info-item {
          padding: 10px;
          background-color: #f9f9f9;
          border-left: 3px solid #0ea5e9;
        }
        .info-label {
          font-weight: bold;
          color: #666;
          font-size: 12px;
        }
        .info-value {
          font-size: 16px;
          color: #333;
          margin-top: 5px;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 15px;
        }
        th {
          background-color: #0ea5e9;
          color: white;
          padding: 10px;
          text-align: left;
          font-weight: bold;
        }
        td {
          padding: 8px;
          border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
          background-color: #f9f9f9;
        }
        .total-row {
          font-weight: bold;
          background-color: #e0f2fe !important;
        }
        .footer {
          margin-top: 40px;
          text-align: center;
          color: #666;
          font-size: 12px;
          border-top: 1px solid #ddd;
          padding-top: 20px;
        }
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <h1>Reporte de Detención #${detencion.idDetencion}</h1>
      
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Fecha</div>
          <div class="info-value">${new Date(detencion.Fecha).toLocaleDateString('es-CL')}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Línea</div>
          <div class="info-value">${detencion.LineaNombre || 'N/A'}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Turno</div>
          <div class="info-value">${detencion.TurnoNombre || 'N/A'}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Supervisor</div>
          <div class="info-value">${detencion.SupervisorNombre || 'N/A'}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Tipo de Producción</div>
          <div class="info-value">${detencion.TipoProduccionNombre || 'N/A'}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Estado</div>
          <div class="info-value">${detencion.EstadoNombre || 'N/A'}</div>
        </div>
      </div>

      ${detencion.Observaciones ? `
        <div class="info-item" style="margin: 20px 0;">
          <div class="info-label">Observaciones</div>
          <div class="info-value">${detencion.Observaciones}</div>
        </div>
      ` : ''}

      <h2>Resumen por OF, Tipo y Familia</h2>
      <table>
        <thead>
          <tr>
            <th>Lote</th>
            <th>Tipo Tiempo</th>
            <th>Familia Tiempo</th>
            <th style="text-align: right;">Total Minutos</th>
          </tr>
        </thead>
        <tbody>
          ${resumen.map(r => `
            <tr>
              <td>${r.Lote}</td>
              <td>${r.TipoTiempo}</td>
              <td>${r.FamiliaTiempo}</td>
              <td style="text-align: right; font-weight: bold;">${r.TotalMinutos}</td>
            </tr>
          `).join('')}
          ${resumen.length > 0 ? `
            <tr class="total-row">
              <td colspan="3" style="text-align: right;">TOTAL:</td>
              <td style="text-align: right;">${resumen.reduce((sum, r) => sum + parseInt(r.TotalMinutos), 0)} min</td>
            </tr>
          ` : ''}
        </tbody>
      </table>

      <h2>Porcentajes por Tipo y Familia</h2>
      <table>
        <thead>
          <tr>
            <th>Tipo Tiempo</th>
            <th>Familia Tiempo</th>
            <th style="text-align: right;">Total Minutos</th>
            <th style="text-align: right;">Porcentaje</th>
          </tr>
        </thead>
        <tbody>
          ${porcentajes.map(p => `
            <tr>
              <td>${p.TipoTiempo}</td>
              <td>${p.FamiliaTiempo}</td>
              <td style="text-align: right; font-weight: bold;">${p.TotalMinutos}</td>
              <td style="text-align: right; font-weight: bold; color: #0ea5e9;">${p.Porcentaje}%</td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div class="footer">
        <p>Sistema de Registro de Tiempos Muertos</p>
        <p>Generado el ${new Date().toLocaleString('es-CL')}</p>
      </div>
    </body>
    </html>
  `;

    // Abrir ventana de impresión
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
        }, 250);
    }
}
