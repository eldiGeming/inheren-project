<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        // Ambil semua client tanpa kolom BLOB agar list tetap ringan
        $allClient = Client::select(array_values(array_diff(Schema::getColumnListing('clients'), ['photo', 'document'])))
            ->where('is_deleted', 0)
            ->get();

        return view('client.index', compact('allClient', 'isoOptions'));
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

    private function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'sales_name' => 'nullable|string|max:255',
            'scope' => 'nullable|string',
            'legality' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
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
            'iso_standard' => 'standard',
            'pic_name' => 'PIC name',
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
