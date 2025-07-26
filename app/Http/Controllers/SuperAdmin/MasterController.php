<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        return view('superadmin.master.index', [
            'title' => 'PUSAT MANAJEMEN MASTER'
        ]);
    }
}