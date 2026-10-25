<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Certification;
use App\Models\Company;
use App\Models\TypeCertificate;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use setasign\Fpdi\Tcpdf\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificationController extends Controller
{
    // Pilihan default dropdown Standard (ISO) di form certification
    private const ISO_OPTIONS = [
        'ISO 9001',
        'ISO 14001',
        'ISO 45001',
        'ISO 27001',
        'ISO 22000',
        'HACCP',
        'ISO 37001',
        'ISO 13485',
        'SMK3',
        'SNI',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil company yang tidak dihapus
        $companies = Company::where('is_deleted', 0)->get();

        // Ambil type certificate yang tidak dihapus
        $types = TypeCertificate::where('is_deleted', 0)->get();

        // Pilihan dropdown Standard: daftar ISO default + Standard yang sudah ada di database
        $isoOptions = collect(self::ISO_OPTIONS)
            ->merge($types->pluck('type_certificate'))
            ->unique(fn ($name) => strtolower(trim($name)))
            ->values();

        // Ambil semua certification beserta relasi (tanpa kolom BLOB agar list tetap ringan)
        $allCertification = Certification::with(['company', 'type'])
            ->select(array_values(array_diff(Schema::getColumnListing('certifications'), ['photo', 'document'])))
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
            'types' => $types,
            'isoOptions' => $isoOptions
        ]);
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate($this->rules(), [], $this->attributes());

        $idTypeCertificate = $this->resolveTypeCertificate($request->iso_standard);

        $issuesDate = Carbon::parse($request->issues_date);
        $expiredDate = $issuesDate->copy()->addYear();

        // Generate nomor urut terbaru untuk id_company dan id_type_certificate
        $lastCert = Certification::where('id_company', $request->id_company)
            ->where('id_type_certificate', $idTypeCertificate)
            ->orderBy('id_certification', 'desc')
            ->first();

        $lastNumber = $lastCert ? intval(substr($lastCert->number_certificate, 0, 3)) : 0;
        $nomorUrut = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // 3 digit

        // id_company 2 digit
        $idCompanyPadded = str_pad($request->id_company, 2, '0', STR_PAD_LEFT);

        // id_type_certificate 1 digit
        $idTypePadded = $idTypeCertificate; // as is, 1 digit

        // bulan dan tahun 2 digit
        $month = $issuesDate->format('m');
        $year = $issuesDate->format('y');

        // Gabungkan menjadi nomor 10 digit
        $number = $nomorUrut . $idCompanyPadded . $idTypePadded . $month . $year;

        // Simpan sertifikasi
        $certification = new Certification([
            'id_company' => $request->id_company,
            'id_type_certificate' => $idTypeCertificate,
            'number_certificate' => $number,
            'issues_date' => $issuesDate,
            'expired_date' => $expiredDate,
            'barcode_certificate' => $number,
        ]);
        $this->fillClientData($certification, $request);
        if ($oversize = $this->saveWithFiles($certification)) {
            return $oversize;
        }

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

        $request->validate($this->rules(), [], $this->attributes());

        $issuesDate = Carbon::parse($request->issues_date);
        $expiredDate = $issuesDate->copy()->addYear();

        $certification->id_company = $request->id_company;
        $certification->id_type_certificate = $this->resolveTypeCertificate($request->iso_standard);
        $certification->issues_date = $issuesDate;
        $certification->expired_date = $expiredDate;
        $certification->barcode_certificate = $certification->number_certificate;
        $this->fillClientData($certification, $request);

        if ($oversize = $this->saveWithFiles($certification)) {
            return $oversize;
        }

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

    // Tampilkan foto (BLOB) certification
    public function photo($id)
    {
        $certification = Certification::select('photo', 'photo_mime')->findOrFail($id);
        abort_if(! $certification->photo, 404);

        return response($certification->photo)
            ->header('Content-Type', $certification->photo_mime)
            ->header('X-Content-Type-Options', 'nosniff');
    }

    // Download dokumen (BLOB) certification
    public function document($id)
    {
        $certification = Certification::select('document', 'document_name', 'document_mime')->findOrFail($id);
        abort_if(! $certification->document, 404);

        return response()->streamDownload(function () use ($certification) {
            echo $certification->document;
        }, $certification->document_name, [
            'Content-Type' => $certification->document_mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function rules(): array
    {
        return [
            'id_company' => 'required|exists:companies,id_company',
            'iso_standard' => 'required|string|max:255',
            'issues_date' => 'required|date',
            'sales_name' => 'nullable|string|max:255',
            'scope' => 'nullable|string',
            'legality' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'document' => 'nullable|file|max:5120',
            'sa1_date' => 'nullable|date',
            'sa2_date' => 'nullable|date',
            'rc_date' => 'nullable|date',
        ];
    }

    // Nama field di pesan error, sesuai label form
    private function attributes(): array
    {
        return [
            'id_company' => 'client name',
            'iso_standard' => 'standard',
            'issues_date' => 'IA',
            'pic_name' => 'PIC name',
            'photo' => 'foto',
            'document' => 'dokumen',
            'sa1_date' => 'SA1',
            'sa2_date' => 'SA2',
            'rc_date' => 'RC',
        ];
    }

    // Cari Standard berdasarkan nama (tidak peka huruf besar/kecil), buat baru jika belum ada
    private function resolveTypeCertificate(string $name): int
    {
        $name = trim($name);

        $type = TypeCertificate::where('is_deleted', 0)
            ->whereRaw('LOWER(type_certificate) = ?', [strtolower($name)])
            ->first();

        if (! $type) {
            $type = TypeCertificate::create([
                'type_certificate' => $name,
                'description_certificate' => $name,
                'scope_certificate' => '',
            ]);
        }

        return $type->id_type_certificate;
    }

    // Simpan certification; jika foto + dokumen melebihi max_allowed_packet MySQL, kembalikan error 422
    private function saveWithFiles(Certification $certification): ?JsonResponse
    {
        try {
            $certification->save();

            return null;
        } catch (QueryException $e) {
            // 1153: Got a packet bigger than 'max_allowed_packet' bytes
            if (($e->errorInfo[1] ?? null) != 1153) {
                throw $e;
            }

            $message = 'Ukuran foto + dokumen melebihi batas server database. Gunakan file yang lebih kecil.';

            return response()->json([
                'message' => $message,
                'errors' => ['photo' => [$message], 'document' => [$message]],
            ], 422);
        }
    }

    // Isi field Data Based Client; foto & dokumen hanya diganti jika ada upload baru
    private function fillClientData(Certification $certification, Request $request): void
    {
        $certification->fill($request->only([
            'sales_name', 'scope', 'legality', 'pic_name', 'sa1_date', 'sa2_date', 'rc_date',
        ]));

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $certification->photo = $file->get();
            $certification->photo_mime = $file->getMimeType();
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $certification->document = $file->get();
            $certification->document_name = $file->getClientOriginalName();
            $certification->document_mime = $file->getMimeType() ?: 'application/octet-stream';
        }
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
        $pdf->SetFont('helvetica', 'BI', 14);
        $pdf->SetXY(20, 90);
        $pdf->Cell(165, 8, 'This is to certify that', 0, 1, 'R');

        // COMPANY NAME
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(30, 100);

        $pdf->MultiCell(
            155,
            7,
            $companyName,
            0,
            'R'
        );

        // Ambil posisi Y setelah company name
        $companyNameBottom = $pdf->GetY();

        // ADDRESS
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(30, $companyNameBottom + 2);

        $pdf->MultiCell(
            155,
            5,
            $companyAddress,
            0,
            'R'
        );

        // SCOPE
        $pdf->SetXY(30, 169);
        $pdf->MultiCell(
            155,
            5,
            $scopeCertificate,
            0,
            'R'
        );

        // Compliance text
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 14);
        $pdf->SetXY(20, 133);
        $pdf->Cell(165, 8, 'Which complies with the requirements of', 0, 1, 'R');

        // ISO TYPE (RED BOLD)
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(20, 141);
        $pdf->MultiCell(165, 8, $certificateType, 0, 'R');

        // DESCRIPTION (SMALL BLACK)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(20, 148);
        $pdf->MultiCell(165, 4, $certificateDescription, 0, 'R');

        // Scope Title
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 14);
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
        $qrWebY = 228;
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


        $pdf->SetTextColor(0, 0, 0);

        // PT. Inheren Sertifikasi Indonesia
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(20, 270);
        $pdf->Cell(
            170,
            4,
            'PT. Inheren Sertifikasi Indonesia',
            0,
            1,
            'C'
        );

        // Alamat dan informasi
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(20, $pdf->GetY());
        $pdf->MultiCell(
            170,
            3,
            "PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2\n" .
            "Jakarta Selatan - DKI Jakarta - Indonesia\n" .
            "www.inheren.com | info@inheren.com",
            0,
            'C'
        );
        return $pdf->Output('certificate-'.$certificateNumber.'.pdf', 'I');
    }


    public function generatePdfDraft($id)
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
        // ================= WATERMARK DRAFT =================
        $pdf->SetAlpha(3);

        $pdf->SetFont('helvetica', 'B', 110);
        $pdf->SetTextColor(235, 235, 235);

        $pdf->StartTransform();

        // Putar 45 derajat terhadap titik tengah halaman A4
        $pdf->Rotate(45, 120, 148);

        // Geser agar tepat di tengah
        $pdf->Text(5, 120, 'D R A F T');

        $pdf->StopTransform();

        $pdf->SetAlpha(1);
        $pdf->SetTextColor(0, 0, 0);

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
        $pdf->SetFont('helvetica', 'BI', 14);
        $pdf->SetXY(20, 90);
        $pdf->Cell(165, 8, 'This is to certify that', 0, 1, 'R');

        // COMPANY NAME (RED BIG)
        // COMPANY NAME
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(30, 100);

        $pdf->MultiCell(
            155,
            7,
            $companyName,
            0,
            'R'
        );

        // Ambil posisi Y setelah company name
        $companyNameBottom = $pdf->GetY();

        // ADDRESS
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(30, $companyNameBottom + 2);

        $pdf->MultiCell(
            155,
            5,
            $companyAddress,
            0,
            'R'
        );

        // Compliance text
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 14);
        $pdf->SetXY(20, 133);
        $pdf->Cell(165, 8, 'Which complies with the requirements of', 0, 1, 'R');

        // ISO TYPE (RED BOLD)
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(20, 141);
        $pdf->MultiCell(165, 8, $certificateType, 0, 'R');

        // DESCRIPTION (SMALL BLACK)
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(20, 148);
        $pdf->MultiCell(165, 4, $certificateDescription, 0, 'R');

        // Scope Title
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 14);
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

        $qrX = 40;
        $qrY = 228;
        $qrSize = 25;

        // LOGO DI TENGAH QR
        $logoSize = 8;
        $logoX = $qrX + ($qrSize / 2) - ($logoSize / 2);
        $logoY = $qrY + ($qrSize / 2) - ($logoSize / 2);

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

        $pdf->SetTextColor(0, 0, 0);

        // PT. Inheren Sertifikasi Indonesia
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(20, 270);
        $pdf->Cell(
            170,
            4,
            'PT. Inheren Sertifikasi Indonesia',
            0,
            1,
            'C'
        );

        // Alamat dan informasi
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(20, $pdf->GetY());
        $pdf->MultiCell(
            170,
            3,
            "PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2\n" .
            "Jakarta Selatan - DKI Jakarta - Indonesia\n" .
            "www.inheren.com | info@inheren.com",
            0,
            'C'
        );
        return $pdf->Output('certificate-'.$certificateNumber.'.pdf', 'I');
    }

    public function generatePdfContent($id)
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

        // COMPANY NAME
        $pdf->SetTextColor(139, 0, 0);
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetXY(30, 100);

        $pdf->MultiCell(
            155,
            7,
            $companyName,
            0,
            'R'
        );

        // Ambil posisi Y setelah company name
        $companyNameBottom = $pdf->GetY();

        // ADDRESS
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(30, $companyNameBottom + 2);

        $pdf->MultiCell(
            155,
            5,
            $companyAddress,
            0,
            'R'
        );

        // SCOPE
        $pdf->SetXY(30, 169);
        $pdf->MultiCell(
            155,
            5,
            $scopeCertificate,
            0,
            'R'
        );

        // Compliance text
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', 'BI', 16);
        $pdf->SetXY(20, 133);
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
        $qrWebY = 228;
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


        $pdf->SetTextColor(0, 0, 0);

        // PT. Inheren Sertifikasi Indonesia
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(20, 270);
        $pdf->Cell(
            170,
            4,
            'PT. Inheren Sertifikasi Indonesia',
            0,
            1,
            'C'
        );

        // Alamat dan informasi
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(20, $pdf->GetY());
        $pdf->MultiCell(
            170,
            3,
            "PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2\n" .
            "Jakarta Selatan - DKI Jakarta - Indonesia\n" .
            "www.inheren.com | info@inheren.com",
            0,
            'C'
        );
        return $pdf;
    }

    public function generateCertificateImage($id)
    {
        $pdf = $this->generatePdfContent($id);

        $pdfContent = $pdf->Output(
            'certificate.pdf',
            'S'
        );

        $imagick = new \Imagick();

        $imagick->setResolution(150, 150);
        $imagick->readImageBlob($pdfContent);

        $imagick->setIteratorIndex(0);
        $imagick->setImageFormat('png');

        $image = $imagick->getImageBlob();

        $imagick->clear();
        $imagick->destroy();

        return response($image)
            ->header('Content-Type', 'image/png');
    }
}
