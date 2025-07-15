<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    // Method untuk menampilkan halaman hub
    public function index()
    {
        return view('superadmin.master.index', [
            'title' => 'Pusat Manajemen Master'
        ]);
    }
}