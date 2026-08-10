<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function export(Request $request)
    {
        $type = $request->query('type', 'pdf');
        $startDate = $request->query('start', date('Y-m-01'));
        $endDate = $request->query('end', date('Y-m-d'));

        // Ambil data barang dari database
        $dataBarang = Barang::with(['kategori', 'satuan'])->get();

        // 1. Ekspor Excel / CSV (Tanpa library tambahan, langsung download .csv/.xls)
        if ($type === 'excel') {
            $filename = "Laporan_Stok_{$startDate}_s_d_{$endDate}.csv";
            
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename={$filename}",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($dataBarang) {
                $file = fopen('php://output', 'w');
                // Header Kolom Excel
                fputcsv($file, ['ID', 'Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok Minimum', 'Stok Saat Ini']);

                foreach ($dataBarang as $b) {
                    fputcsv($file, [
                        $b->id,
                        $b->kode_barang,
                        $b->nama_barang,
                        is_object($b->kategori) ? ($b->kategori->nama ?? $b->kategori->nama_kategori) : $b->kategori,
                        is_object($b->satuan) ? ($b->satuan->nama ?? $b->satuan->nama_satuan) : $b->satuan,
                        $b->stok_minimum,
                        $b->stok ?? 0,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // 2. Ekspor PDF (Cetak Halaman HTML/Dokumen Siap Print)
        $htmlContent = "
        <html>
        <head>
            <title>Laporan Stok Reorder</title>
            <style>
                body { font-family: sans-serif; padding: 20px; }
                h2 { margin-bottom: 5px; }
                p { color: #555; font-size: 14px; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h2>SPPG Munggur - Laporan Stok & Reorder</h2>
            <p>Periode: {$startDate} s/d {$endDate}</p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Stok Minimum</th>
                        <th>Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($dataBarang as $index => $b) {
            $no = $index + 1;
            $htmlContent .= "
                <tr>
                    <td>{$no}</td>
                    <td>{$b->kode_barang}</td>
                    <td>{$b->nama_barang}</td>
                    <td>{$b->stok_minimum}</td>
                    <td>" . ($b->stok ?? 0) . "</td>
                </tr>";
        }

        $htmlContent .= "
                </tbody>
            </table>
            <script>window.print();</script>
        </body>
        </html>";

        return response($htmlContent, 200)
            ->header('Content-Type', 'text/html');
    }
}