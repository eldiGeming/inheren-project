<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Certification;
use App\Models\Company;
use App\Models\TypeCertificate;
use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil company yang tidak dihapus
        $companies = Company::where('is_deleted', 0)->get();

        // Ambil type certificate yang tidak dihapus
        $types = TypeCertificate::where('is_deleted', 0)->get();
        // Ambil semua certification beserta relasi
        $allCertification = Certification::with(['company', 'type'])
            ->where('is_deleted', 0)
            ->get()
            ->map(function ($cert) {
                // Format tanggal
                $cert->issues_date_formatted = Carbon::parse($cert->issues_date)->format('d-F-Y');
                $cert->expired_date_formatted = Carbon::parse($cert->expired_date)->format('d-F-Y');
                $verifyUrl = route('certification.verify', $cert->barcode_certificate);
                $cert->qrcode = base64_encode(
                QrCode::format('png')
                    ->size(250)
                    ->errorCorrection('H')
                    ->merge(public_path('assets/images/logo-abbr.png'), 0.25, true)
                    ->generate($verifyUrl)
            );
                return $cert;
            });

        return view('certification.index', [
            'allCertification' => $allCertification,
            'companies' => $companies,
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'id_company' => 'required|exists:companies,id_company',
            'id_type_certificate' => 'required|exists:type_certificates,id_type_certificate',
            'issues_date' => 'required|date',
        ]);

        $issuesDate = Carbon::parse($request->issues_date);
        $expiredDate = $issuesDate->copy()->addYear();

        // Generate nomor urut terbaru untuk id_company dan id_type_certificate
        $lastCert = Certification::where('id_company', $request->id_company)
            ->where('id_type_certificate', $request->id_type_certificate)
            ->orderBy('id_certification', 'desc')
            ->first();

        $lastNumber = $lastCert ? intval(substr($lastCert->number_certificate, 0, 3)) : 0;
        $nomorUrut = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // 3 digit

        // id_company 2 digit
        $idCompanyPadded = str_pad($request->id_company, 2, '0', STR_PAD_LEFT);

        // id_type_certificate 1 digit
        $idTypePadded = $request->id_type_certificate; // as is, 1 digit

        // bulan dan tahun 2 digit
        $month = $issuesDate->format('m');
        $year = $issuesDate->format('y');

        // Gabungkan menjadi nomor 10 digit
        $number = $nomorUrut . $idCompanyPadded . $idTypePadded . $month . $year;

        // Simpan sertifikasi
        $certification = Certification::create([
            'id_company' => $request->id_company,
            'id_type_certificate' => $request->id_type_certificate,
            'number_certificate' => $number,
            'issues_date' => $issuesDate,
            'expired_date' => $expiredDate,
            'barcode_certificate' => $number, 
        ]);

        $certification->load(['company', 'type']);
        $certification->issues_date_formatted = $issuesDate->format('d-F-Y');
        $certification->expired_date_formatted = $expiredDate->format('d-F-Y');

        return response()->json([
            'message' => 'Certification created successfully!',
            'data' => $certification
        ]);
    }


    public function update(Request $request, $id)
    {
        $certification = Certification::findOrFail($id);

        $request->validate([
            'id_company' => 'required|exists:companies,id_company',
            'id_type_certificate' => 'required|exists:type_certificates,id_type_certificate',
            'issues_date' => 'required|date',
        ]);

        $issuesDate = Carbon::parse($request->issues_date);
        $expiredDate = $issuesDate->copy()->addYear();

        $certification->id_company = $request->id_company;
        $certification->id_type_certificate = $request->id_type_certificate;
        $certification->issues_date = $issuesDate;
        $certification->expired_date = $expiredDate;
        $certification->barcode_certificate = $certification->number_certificate;

        $certification->save();

        $certification->load(['company', 'type']);

        return response()->json([
            'message' => 'Certification updated successfully!',
            'data' => $certification->load(['company','type'])
        ]);
    }


    public function destroy($id)
    {
        $company = Certification::findOrFail($id);
        $company->is_deleted = 1;
        $company->save();

        return response()->json(['message' => 'Certification has been deleted.']);
    }

    public function show($id)
    {
        $certification = Certification::with(['company', 'type'])
            ->where('id_certificate', $id)
            ->where('is_deleted', 0)
            ->firstOrFail();

        return view('certification.detail-scan', compact('certification'));
    }

    public function verify($code)
    {
        $certification = Certification::with(['company', 'type'])
            ->where('barcode_certificate', $code)
            ->where('is_deleted', 0)
            ->first();

        if (!$certification) {
            return view('certification.verify-not-found');
        }

        $verifyUrl = route('certification.verify', $code);

        // generate QR dengan logo di tengah
        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->errorCorrection('H')
                ->merge(public_path('assets/images/logo-abbr.png'), 0.25, true)
                ->generate($verifyUrl)
        );

        return view('certification.verify', compact('certification', 'qrCode'));
    }

    public function generatePdf($id)
    {
        $certification = Certification::with(['company', 'type'])
            ->findOrFail($id);

        $pdf = new Fpdi();

        // ================= PAGE SETTING =================
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage('P', 'A4');

        // ================= LOAD TEMPLATE =================
        $pdf->setSourceFile(public_path('templates/certificate-template.pdf'));
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx, 0, 0, 210, 297);

        // ================= DATA =================
        $companyName   = strtoupper($certification->company->name_company);
        $companyAddress = $certification->company->address_company ?? '';
        $certificateType = strtoupper($certification->type->type_certificate);
        $certificateDescription = $certification->type->description_certificate ?? '';
        $certificateNumber = $certification->number_certificate;
        $scopeCertificate = $certification->type->scope_certificate ?? '';
        $logoCertificate = $certification->type->logo_certificate ?? '';

        $issueDate = Carbon::parse($certification->issues_date)->format('d F Y');
        $expiredDate = Carbon::parse($certification->expired_date)->format('d F Y');

        // ================= CONTENT START =================

        // This is to certify that
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 16);
        $pdf->SetXY(20, 90);
        $pdf->Cell(165, 8, 'This is to certify that', 0, 1, 'R');

        // COMPANY NAME (RED BIG)
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetXY(20, 100);
        $pdf->MultiCell(165, 10, $companyName, 0, 'R');

        // ADDRESS (RED SMALL)
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(30, 108);
        $pdf->MultiCell(155, 5, $companyAddress, 0, 'R');

        // Compliance text
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 16);
        $pdf->SetXY(20, 130);
        $pdf->Cell(165, 8, 'Which complies with the requirements of', 0, 1, 'R');

        // ISO TYPE (RED BOLD)
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetXY(20, 141);
        $pdf->MultiCell(165, 8, $certificateType, 0, 'R');

        // DESCRIPTION (SMALL BLACK)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(20, 148);
        $pdf->MultiCell(165, 4, $certificateDescription, 0, 'R');

        // Scope Title
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 16);
        $pdf->SetXY(20, 160);
        $pdf->Cell(165, 4, 'For the following activities', 0, 1, 'R');

        // Scope Activities (RED MULTILINE)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(30, 169);
        $pdf->MultiCell(155, 5, $scopeCertificate, 0, 'R');

        // ================= FOOTER AREA =================

        $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0,0,0);

    // ===============================
    // QR VERIFY DI KIRI BAWAH
    // ===============================
    $verifyUrl = route('certification.verify', $certificateNumber);

    $qrX = 40;
    $qrY = 228;
    $qrSize = 25;

    $pdf->write2DBarcode(
        $verifyUrl,
        'QRCODE,H',
        $qrX,
        $qrY,
        $qrSize,
        $qrSize,
        [],
        'N'
    );

    // LOGO DI TENGAH QR
    $logoSize = 8;
    $logoX = $qrX + ($qrSize / 2) - ($logoSize / 2);
    $logoY = $qrY + ($qrSize / 2) - ($logoSize / 2);

    $pdf->Image(
        public_path('assets/images/logo-abbr.png'),
        $logoX,
        $logoY,
        $logoSize,
        $logoSize,
        'PNG'
    );

    // ===============================
    // CERTIFICATE NO DI BAWAH QR
    // ===============================
    $pdf->SetXY($qrX - 1, $qrY + $qrSize - 2);

    // ===============================
    // LOGO CERTIFICATE DI SAMPING QR
    // ===============================

    $certLogoWidth  = 20; // panjang horizontal
    $certLogoHeight = 25; // tinggi

    // posisi di kanan QR
    $certLogoX = $qrX + $qrSize + 5; // jarak 5 dari QR
    $certLogoY = $qrY + ($qrSize / 2) - ($certLogoHeight / 2); // center sejajar QR

    // tampilkan logo kalau ada
    if (!empty($logoCertificate)) {
        $pdf->Image(
            public_path('storage/' . $logoCertificate),
            $certLogoX,
            $certLogoY,
            $certLogoWidth,
            $certLogoHeight,
            'PNG'
        );
    }

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(25,10, $certificateNumber,0,2,'C');

    // ===============================
    // DATES (KANAN BAWAH)
    // ===============================
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','',9);

    $pdf->SetXY(130, 195);
    $pdf->Cell(55, 6, 'Issued: ' . $issueDate, 0, 2, 'R');

    $pdf->SetX(130);
    $pdf->Cell(55, 6, 'Expires: ' . $expiredDate, 0, 1, 'R');

    $pdf->SetX(130);
    $pdf->Cell(55, 6, 'Originally Certified: ' . $issueDate, 0, 1, 'R');


    $qrWebX = 158;
    $qrWebY = 230;
    $qrWebSize = 25;
    $signatureUrl = asset('storage/sign/sign_director.png');

    $pdf->write2DBarcode(
        $signatureUrl,
        'QRCODE,H',
        $qrWebX,
        $qrWebY,
        $qrWebSize,
        $qrWebSize,
        [],
        'N'
    );

    // GARIS DAN DIRECTOR
    $pdf->SetFont('helvetica','',9);
    $pdf->SetX(120);
    $pdf->Cell(58,5,'Director',0,1,'R');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0,0,0);

$text = "PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2
Jakarta Selatan - DKI Jakarta - Indonesia
www.inheren.com | info@inheren.com";

// posisi bawah halaman
$pdf->SetXY(20, 275);

$pdf->MultiCell(
    170,   // lebar hampir full page
    3,
    $text,
    0,
    'C'    // center supaya seperti footer sertifikat
);
        return $pdf->Output('certificate-'.$certificateNumber.'.pdf', 'I');
    }
}
