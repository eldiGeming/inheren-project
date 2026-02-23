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
        $companies = Company::where('is_deleted', 0)->get();
        $types = TypeCertificate::where('is_deleted', 0)->get();
        $allCertification = Certification::with(['company', 'type'])->where('is_deleted', 0)->get();

        return view('certification.index', compact('allCertification', 'companies', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_company' => 'required|exists:companies,id_company',
            'id_type_certificate' => 'required|exists:type_certificates,id_type_certificate',
            'issues_date' => 'required|date',
        ]);

        $issuesDate = Carbon::parse($request->issues_date);
        $expiredDate = $issuesDate->copy()->addYear();

        $number = 'CERT-' . date('Y') . '-' . rand(1000, 9999);

        $certification = Certification::create([
            'id_company' => $request->id_company,
            'id_type_certificate' => $request->id_type_certificate,
            'number_certificate' => $number,
            'issues_date' => $issuesDate,
            'expired_date' => $expiredDate,
            'barcode_certificate' => $number, // ✅ hanya simpan kode
        ]);

        $certification->load(['company', 'type']);

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
            'data' => $certification
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

        return view('certification.verify', compact('certification'));
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
        $scope = $certification->scope ?? '';

        $issueDate = Carbon::parse($certification->issues_date)->format('d F Y');
        $expiredDate = Carbon::parse($certification->expired_date)->format('d F Y');

        // ================= CONTENT START =================

        // This is to certify that
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', '', 13);
        $pdf->SetXY(20, 85);
        $pdf->Cell(170, 8, 'This is to certify that', 0, 1, 'R');

        // COMPANY NAME (RED BIG)
        $pdf->SetTextColor(200, 0, 0);
        $pdf->SetFont('helvetica', 'B', 22);
        $pdf->SetXY(20, 95);
        $pdf->MultiCell(170, 10, $companyName, 0, 'R');

        // ADDRESS (RED SMALL)
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(30, 108);
        $pdf->MultiCell(150, 5, $companyAddress, 0, 'R');

        // Compliance text
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', '', 13);
        $pdf->SetXY(20, 125);
        $pdf->Cell(170, 8, 'Which complies with the requirements of', 0, 1, 'R');

        // ISO TYPE (RED BOLD)
        $pdf->SetTextColor(200, 0, 0);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetXY(20, 135);
        $pdf->MultiCell(170, 8, $certificateType, 0, 'R');

        // DESCRIPTION (SMALL BLACK)
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetXY(20, 145);
        $pdf->MultiCell(170, 6, $certificateDescription, 0, 'R');

        // Scope Title
        $pdf->SetFont('helvetica', '', 13);
        $pdf->SetXY(20, 158);
        $pdf->Cell(170, 8, 'For the following activities', 0, 1, 'R');

        // Scope Activities (RED MULTILINE)
        $pdf->SetTextColor(200, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(30, 168);
        $pdf->MultiCell(150, 5, $scope, 0, 'R');

        // ================= FOOTER AREA =================

        // Certificate Number (Left Bottom)
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(20, 245);
        $pdf->Cell(80, 6, 'Certificate No: ' . $certificateNumber, 0, 1, 'L');

        // Dates (Right Bottom)
        $pdf->SetXY(130, 240);
        $pdf->Cell(60, 6, 'Issued: ' . $issueDate, 0, 2, 'R');

        $pdf->SetX(130);
        $pdf->Cell(60, 6, 'Expired: ' . $expiredDate, 0, 1, 'R');
        // ================= QR CODE =================
        $verifyUrl = route('certification.verify', $certificateNumber);

        $pdf->write2DBarcode(
            $verifyUrl,
            'QRCODE,H',
            170,
            255,
            25,
            25,
            [],
            'N'
        );

        return $pdf->Output('certificate-'.$certificateNumber.'.pdf', 'I');
    }
}
