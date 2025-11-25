<?php
namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator {
    
    public function generarReporteSolicitudesAceptadas($ofertaTitulo, array $candidatos) {
        
        $options = new Options();
        $options->set('defaultFont', 'sans-serif');
        $dompdf = new Dompdf($options);

        $html = $this->generarHtmlReporte($ofertaTitulo, $candidatos);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Reporte_Aceptados_' . time() . '.pdf';
        
        
        $dompdf->stream($filename, ["Attachment" => 1]); 
        exit;
    }
    
    private function generarHtmlReporte($titulo, $candidatos) {
        
        $html = '<html><head><style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    h1 { color: #1d3557; border-bottom: 2px solid #f9b233; padding-bottom: 10px; }
                    h2 { color: #295ba7; font-size: 1.2em; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background-color: #f6f8fa; color: #1d3557; }
                </style></head><body>';
        
        $html .= '<h1>Reporte de Candidatos Aceptados</h1>';
        $html .= '<h2>Oferta: ' . htmlspecialchars($titulo) . '</h2>';
        $html .= '<p>Generado el: ' . date('d/m/Y H:i:s') . '</p>';

        if (empty($candidatos)) {
            $html .= '<p style="color: red;">No hay candidatos aceptados para generar el reporte.</p>';
            return $html . '</body></html>';
        }

        $html .= '<table><thead><tr><th>Nombre Completo</th><th>Correo Electrónico</th></tr></thead><tbody>';

        foreach ($candidatos as $candidato) {
            $nombreCompleto = htmlspecialchars($candidato['nombre'] . ' ' . $candidato['apellido1'] . ' ' . $candidato['apellido2']);
            $correo = htmlspecialchars($candidato['correo']);
            
            $html .= "<tr><td>$nombreCompleto</td><td>$correo</td></tr>";
        }

        $html .= '</tbody></table>';
        $html .= '</body></html>';
        return $html;
    }
}