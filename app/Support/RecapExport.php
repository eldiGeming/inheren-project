<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use TCPDF;

// Download rekap tabel (Certification, Data Client) sebagai Excel (.xlsx) atau PDF
class RecapExport
{
    // Id baris dari tabel di layar ("3,1,2"), urutan dipertahankan sesuai sort & filter yang tampil
    public static function ids(?string $ids): array
    {
        return array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $ids)))));
    }

    /**
     * @param array  $columns judul kolom => lebar kolom di PDF (persen, total 100)
     * @param array  $rows    tiap baris berisi nilai sesuai urutan kolom (teks, angka, tanggal, atau null)
     * @param string $filter  keterangan filter yang aktif di layar
     */
    public static function download(string $format, string $title, ?string $filter, array $columns, array $rows)
    {
        // Waktu cetak dalam WIB (APP_TIMEZONE aplikasi UTC)
        $now = now('Asia/Jakarta');
        $fileName = Str::slug($title).'-'.$now->format('Ymd-His');
        $info = array_values(array_filter([
            $filter ? 'Filter: '.$filter : null,
            'Dicetak: '.$now->format('d-m-Y H:i').' WIB  |  Total: '.count($rows).' data',
        ]));

        if ($format === 'pdf') {
            return response(self::pdf($title, $info, $columns, $rows), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'.pdf"',
            ]);
        }

        return response()->streamDownload(function () use ($title, $info, $columns, $rows) {
            self::xlsx($title, $info, $columns, $rows);
        }, $fileName.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function xlsx(string $title, array $info, array $columns, array $rows): void
    {
        $writer = new Writer();
        $writer->openToFile('php://output');

        // Pengaturan sheet harus sebelum baris pertama ditulis
        $sheet = $writer->getCurrentSheet();
        $sheet->setName(Str::limit($title, 31, ''));
        foreach (array_values($columns) as $index => $percent) {
            $sheet->setColumnWidth(max(8, $percent * 1.8), $index + 1);
        }

        // Baris judul kolom (setelah judul, keterangan, dan satu baris kosong) dibekukan & diberi filter Excel
        $headerRow = count($info) + 3;
        $sheet->setSheetView((new SheetView())->setFreezeRow($headerRow + 1));
        $sheet->setAutoFilter(new AutoFilter(0, $headerRow, count($columns) - 1, $headerRow + max(count($rows), 1)));

        $writer->addRow(Row::fromValues([$title], (new Style())->setFontBold()->setFontSize(14)));
        foreach ($info as $line) {
            $writer->addRow(Row::fromValues([$line], (new Style())->setFontColor('555555')));
        }
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(array_keys($columns), (new Style())->setFontBold()->setBackgroundColor('E7ECF7')));

        $date = (new Style())->setFormat('dd-mm-yyyy')->setCellAlignment('left');
        $text = (new Style())->setShouldWrapText()->setCellVerticalAlignment('top');
        foreach ($rows as $row) {
            $writer->addRow(new Row(array_map(
                fn ($value) => Cell::fromValue($value, $value instanceof DateTimeInterface ? $date : $text),
                $row
            )));
        }

        $writer->close();
    }

    private static function pdf(string $title, array $info, array $columns, array $rows): string
    {
        $e = fn ($value) => nl2br(e($value instanceof DateTimeInterface ? $value->format('d-m-Y') : (string) $value));
        // Tabel dengan banyak kolom (Data Client) memakai huruf lebih kecil agar muat di A4 landscape
        $fontSize = count($columns) > 10 ? 7 : 8.5;

        $html = '<h2 style="font-size:14pt;">'.e($title).'</h2>';
        foreach ($info as $line) {
            $html .= '<div style="font-size:8.5pt;color:#555555;">'.e($line).'</div>';
        }
        $html .= '<br><table border="1" cellpadding="3" style="font-size:'.$fontSize.'pt;">';

        // thead diulang di setiap halaman
        $html .= '<thead><tr style="background-color:#e7ecf7;font-weight:bold;">';
        foreach ($columns as $heading => $percent) {
            $html .= '<th width="'.$percent.'%">'.e($heading).'</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr nobr="true">';
            foreach (array_values($row) as $index => $value) {
                $html .= '<td width="'.array_values($columns)[$index].'%">'.$e($value).'</td>';
            }
            $html .= '</tr>';
        }
        if (! $rows) {
            $html .= '<tr><td width="100%" align="center">Tidak ada data</td></tr>';
        }
        $html .= '</tbody></table>';

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetTitle($title);
        $pdf->setPrintHeader(false);
        $pdf->setFooterFont(['helvetica', '', 8]);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage();
        // Garis tabel tipis abu-abu
        $pdf->SetDrawColor(154, 163, 181);
        $pdf->SetLineWidth(0.1);
        $pdf->writeHTML($html);

        return $pdf->Output($title.'.pdf', 'S');
    }
}
