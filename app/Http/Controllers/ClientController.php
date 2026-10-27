<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\TypeCertificate;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    // Pilihan default dropdown Standard (ISO) di form Data Client
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

    public function index()
    {
        // Pilihan dropdown Standard: daftar ISO default + Standard yang sudah ada di menu Standard
        $isoOptions = collect(self::ISO_OPTIONS)
            ->merge(TypeCertificate::where('is_deleted', 0)->pluck('type_certificate'))
            ->unique(fn ($name) => strtolower(trim($name)))
            ->values();

        // Pilihan dropdown Client Name: nama yang pernah diinput + nama di menu Company
        $clientNames = Client::where('is_deleted', 0)->pluck('client_name')
            ->merge(Company::where('is_deleted', 0)->pluck('name_company'))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(fn ($name) => strtolower($name))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $salesOptions = $this->contactOptions('sales_name', 'sales_phone');
        $picOptions = $this->contactOptions('pic_name', 'pic_phone');

        // Ambil semua client tanpa kolom BLOB agar list tetap ringan
        $allClient = Client::select(array_values(array_diff(Schema::getColumnListing('clients'), ['photo', 'document'])))
            ->where('is_deleted', 0)
            ->get();

        return view('client.index', compact('allClient', 'isoOptions', 'clientNames', 'salesOptions', 'picOptions'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), [], $this->attributes());

        $client = new Client();
        $this->fillClient($client, $request);
        if ($oversize = $this->saveWithFiles($client)) {
            return $oversize;
        }

        return response()->json([
            'message' => 'Client created successfully!',
            'data' => $client
        ]);
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate($this->rules(), [], $this->attributes());

        $this->fillClient($client, $request);
        if ($oversize = $this->saveWithFiles($client)) {
            return $oversize;
        }

        return response()->json([
            'message' => 'Client updated successfully!',
            'data' => $client
        ]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->is_deleted = 1; // soft-delete flag
        $client->save();

        return response()->json(['message' => 'Client has been deleted.']);
    }

    // Tampilkan foto (BLOB) client
    public function photo($id)
    {
        $client = Client::select('photo', 'photo_mime')->findOrFail($id);
        abort_if(! $client->photo, 404);

        return response($client->photo)
            ->header('Content-Type', $client->photo_mime)
            ->header('X-Content-Type-Options', 'nosniff');
    }

    // Download dokumen (BLOB) client
    public function document($id)
    {
        $client = Client::select('document', 'document_name', 'document_mime')->findOrFail($id);
        abort_if(! $client->document, 404);

        return response()->streamDownload(function () use ($client) {
            echo $client->document;
        }, $client->document_name, [
            'Content-Type' => $client->document_mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    // Pilihan dropdown Sales / PIC dari data client yang sudah ada, sehingga entri baru otomatis tersedia
    // di input berikutnya. Tiap nama membawa nomor HP terakhirnya, tiap nomor membawa nama pemiliknya.
    private function contactOptions(string $nameColumn, string $phoneColumn): array
    {
        $rows = Client::where('is_deleted', 0)
            ->where(fn ($query) => $query->whereNotNull($nameColumn)->orWhereNotNull($phoneColumn))
            ->orderByDesc('updated_at')
            ->orderByDesc('id_client')
            ->get([$nameColumn.' as name', $phoneColumn.' as phone']);

        // Satu nama tanpa membedakan huruf besar/kecil; ejaan & nomor HP terbaru yang dipakai
        $names = $rows->whereNotNull('name')
            ->groupBy(fn ($row) => strtolower($row->name))
            ->map(fn ($group) => [
                'name' => $group->first()->name,
                'phone' => $group->pluck('phone')->filter()->first(),
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);

        $phones = $rows->whereNotNull('phone')
            ->unique('phone')
            ->map(fn ($row) => [
                'phone' => $row->phone,
                'name' => $row->name ? $names[strtolower($row->name)]['name'] : null,
            ])
            ->sortBy(fn ($item) => strtolower($item['name'] ?? '').' '.$item['phone'], SORT_NATURAL)
            ->values();

        return ['names' => $names->values(), 'phones' => $phones];
    }

    private function rules(): array
    {
        // Angka dengan pemisah spasi / - / . / kurung, boleh diawali +, minimal 6 digit
        $phone = ['nullable', 'string', 'max:30', 'regex:/^\+?(?:[\s().-]*\d){6,}[\s().-]*$/'];

        return [
            'client_name' => 'required|string|max:255',
            'sales_name' => 'nullable|string|max:255',
            'sales_phone' => $phone,
            'scope' => 'nullable|string',
            'legality' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => $phone,
            'iso_standard' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'document' => 'nullable|file|max:5120',
            'ia_date' => 'nullable|date',
            'sa1_date' => 'nullable|date',
            'sa2_date' => 'nullable|date',
            'rc_date' => 'nullable|date',
        ];
    }

    // Nama field di pesan error, sesuai label form
    private function attributes(): array
    {
        return [
            'client_name' => 'client name',
            'sales_phone' => 'no. HP sales',
            'iso_standard' => 'standard',
            'pic_name' => 'PIC name',
            'pic_phone' => 'no. HP PIC',
            'photo' => 'foto',
            'document' => 'dokumen',
            'ia_date' => 'IA',
            'sa1_date' => 'SA1',
            'sa2_date' => 'SA2',
            'rc_date' => 'RC',
        ];
    }

    // Simpan client; jika foto + dokumen melebihi max_allowed_packet MySQL, kembalikan error 422
    private function saveWithFiles(Client $client): ?JsonResponse
    {
        try {
            $client->save();

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

    // Isi field Data Client; foto & dokumen hanya diganti jika ada upload baru
    private function fillClient(Client $client, Request $request): void
    {
        $client->fill($request->only([
            'client_name', 'sales_name', 'scope', 'legality', 'pic_name', 'iso_standard',
            'ia_date', 'sa1_date', 'sa2_date', 'rc_date',
        ]));

        // Nomor HP disimpan tanpa spasi/tanda baca agar nomor yang sama tidak muncul dua kali di dropdown
        foreach (['sales_phone', 'pic_phone'] as $field) {
            $client->$field = $request->filled($field) ? preg_replace('/[^\d+]/', '', $request->input($field)) : null;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $client->photo = $file->get();
            $client->photo_mime = $file->getMimeType();
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $client->document = $file->get();
            $client->document_name = $file->getClientOriginalName();
            $client->document_mime = $file->getMimeType() ?: 'application/octet-stream';
        }
    }
}
