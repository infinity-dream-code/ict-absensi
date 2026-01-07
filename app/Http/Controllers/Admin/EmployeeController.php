<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return redirect()->route('admin.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $employees = User::where('role', 'user')->latest()->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
        ]);

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->nik . '@absensi.local', // Dummy email untuk kompatibilitas
            'password' => Hash::make($request->nik), // Password default = NIK
            'role' => 'user',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan! Password default: NIK');
    }

    public function edit(User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'nik' => 'required|string|unique:users,nik,' . $employee->id,
            'name' => 'required|string|max:255',
        ]);

        $employee->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->nik . '@absensi.local', // Update email dummy
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus!');
    }
}
