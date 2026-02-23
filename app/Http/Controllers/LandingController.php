<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class LandingController extends Controller
{
    public function index()
    {
        $allCompany = Company::where('is_deleted', 0)->get();
        return view('landing', compact('allCompany'));
    }
}

