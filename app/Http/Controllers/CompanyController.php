<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $allCompany = Company::where('is_deleted', 0)->get();
        return view('company.index', compact('allCompany'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_company' => 'required|max:255',
            'description_company' => 'required',
            'phone_company' => 'required',
            'email_company' => 'required|email',
            'address_company' => 'required',
            'logo_company' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $company = new Company();
        $company->name_company = $request->name_company;
        $company->description_company = $request->description_company;
        $company->phone_company = $request->phone_company;
        $company->email_company = $request->email_company;
        $company->address_company = $request->address_company;

        if ($request->hasFile('logo_company')) {
            $file = $request->file('logo_company');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('storage/company'), $filename);
            $company->logo_company = 'company/'.$filename;
        }

        $company->save();

        return response()->json([
            'message' => 'Company created successfully!',
            'data' => [
                'id' => $company->id_company,
                'name_company' => $company->name_company,
                'phone_company' => $company->phone_company,
                'email_company' => $company->email_company,
                'logo_company' => $company->logo_company,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'name_company' => 'required|max:255',
            'description_company' => 'required',
            'phone_company' => 'required',
            'email_company' => 'required|email',
            'address_company' => 'required',
            'logo_company' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $company->name_company = $request->name_company;
        $company->description_company = $request->description_company;
        $company->phone_company = $request->phone_company;
        $company->email_company = $request->email_company;
        $company->address_company = $request->address_company;

        if ($request->hasFile('logo_company')) {
            $file = $request->file('logo_company');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('storage/company'), $filename);
            $company->logo_company = 'company/'.$filename;
        }

        $company->save();

        return response()->json([
            'message' => 'Company updated successfully!',
            'data' => [
                'id' => $company->id_company,
                'name_company' => $company->name_company,
                'phone_company' => $company->phone_company,
                'email_company' => $company->email_company,
                'address_company' => $company->address_company,
                'logo_company' => $company->logo_company,
            ]
        ]);    
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->is_deleted = 1; // soft-delete flag
        $company->save();

        return response()->json(['message' => 'Company has been deleted.']);
    }
}

