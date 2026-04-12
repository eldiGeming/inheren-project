<?php

namespace App\Http\Controllers;

use App\Models\TypeCertificate;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;

class TypeCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeCertificate = TypeCertificate::where('is_deleted', 0)->get();
        return view('typeCertificate.index', compact('allTypeCertificate'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(Request $request)
    {
        $request->validate([
            'type_certificate' => 'required|max:255',
            'description_certificate' => 'required',
            'scope_certificate' => 'required',
            'logo_certificate' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $typeCertificate = new TypeCertificate();
        $typeCertificate->type_certificate = $request->type_certificate;
        $typeCertificate->description_certificate = $request->description_certificate;
        $typeCertificate->scope_certificate = $request->scope_certificate;
        
        if ($request->hasFile('logo_certificate')) {
            $file = $request->file('logo_certificate');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('storage/type-certificate'), $filename);
            $typeCertificate->logo_certificate = 'type-certificate/'.$filename;
        }
        $typeCertificate->save();

        return response()->json([
            'message' => 'Type certificate created successfully!',
            'data' => [
                'id' => $typeCertificate->id_type_certificate,
                'type_certificate' => $typeCertificate->type_certificate,
                'description_certificate' => $typeCertificate->description_certificate,
                'scope_certificate' => $typeCertificate->scope_certificate,
                'logo_certificate' => $typeCertificate->logo_certificate,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $typeCertificate = TypeCertificate::findOrFail($id);

        $request->validate([
            'type_certificate' => 'required|max:255',
            'description_certificate' => 'required',
            'scope_certificate' => 'required',
            'logo_certificate' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $typeCertificate->type_certificate = $request->type_certificate;
        $typeCertificate->description_certificate = $request->description_certificate;
        $typeCertificate->scope_certificate = $request->scope_certificate;
        if ($request->hasFile('logo_certificate')) {
            $file = $request->file('logo_certificate');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('storage/type-certificate'), $filename);
            $typeCertificate->logo_certificate = 'type-certificate/'.$filename;
        }

        $typeCertificate->save();

        return response()->json([
            'message' => 'Type certificate updated successfully!',
            'data' => [
                'id' => $typeCertificate->id_type_certificate,
                'type_certificate' => $typeCertificate->type_certificate,
                'description_certificate' => $typeCertificate->description_certificate,
                'scope_certificate' => $typeCertificate->scope_certificate,
                'logo_certificate' => $typeCertificate->logo_certificate,
            ]
        ]);
    }


    public function destroy($id)
    {
        $typeCertificate = TypeCertificate::findOrFail($id);
        $typeCertificate->is_deleted = 1;
        $typeCertificate->save();

        return response()->json([
            'message' => 'Type certificate has been deleted.'
        ]);
    }
}
