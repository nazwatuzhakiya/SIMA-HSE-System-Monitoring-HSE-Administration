<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manpower;
use App\Models\SubcontPersonnel;
use App\Models\SubcontCompany;
use App\Models\LogistikApd;

class LandingController extends Controller
{
    public function index()
    {
        $totalManpower = Manpower::where('status', 'Aktif')->count();
        $totalSubcont = SubcontCompany::where('status', 'Aktif')->count();
        $totalApd = LogistikApd::sum('jumlah');

        return view('landing', compact('totalManpower', 'totalSubcont', 'totalApd'));
    }
}
