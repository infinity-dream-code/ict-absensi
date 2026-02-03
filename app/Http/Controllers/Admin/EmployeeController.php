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
        $employees = User::where('role', 'user')->latest()->get();
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
            'nip' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'jenis' => 'nullable|boolean',
        ]);

        // Generate username from first name (lowercase)
        $nameParts = explode(' ', trim($request->name));
        $firstName = strtolower($nameParts[0]);
        $baseUsername = $firstName;
        $username = $baseUsername;
        $counter = 1;

        // Check if username already exists, if yes, append number
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        User::create([
            'nik' => $request->nik,
            'nip' => $request->nip,
            'name' => $request->name,
            'username' => $username,
            'email' => $request->nik . '@absensi.local', // Dummy email untuk kompatibilitas
            'password' => Hash::make('123456'), // Password default = 123456
            'role' => 'user',
            'jenis' => $request->has('jenis') ? $request->boolean('jenis') : true,
        ]);

        return redirect()->route('admin.employees.index')->with('success', "Karyawan berhasil ditambahkan! Username: {$username}, Password default: 123456");
    }

    public function show(User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }
        return redirect()->route('admin.employees.edit', $employee);
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
            'nip' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'jenis' => 'nullable|boolean',
        ]);

        // Generate username from first name (lowercase)
        $nameParts = explode(' ', trim($request->name));
        $firstName = strtolower($nameParts[0]);
        $baseUsername = $firstName;
        $username = $baseUsername;
        $counter = 1;

        // Check if username already exists (excluding current user), if yes, append number
        while (User::where('username', $username)->where('id', '!=', $employee->id)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $employee->update([
            'nik' => $request->nik,
            'nip' => $request->nip,
            'name' => $request->name,
            'username' => $username,
            'email' => $request->nik . '@absensi.local', // Update email dummy
            'jenis' => $request->has('jenis') ? $request->boolean('jenis') : $employee->jenis,
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

    public function resetPassword(User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }

        $employee->update([
            'password' => Hash::make('123456'),
        ]);

        return redirect()->route('admin.employees.index')->with('success', "Password karyawan {$employee->name} berhasil direset menjadi 123456!");
    }

    /**
     * Toggle jenis (dihitung di dashboard). Dipanggil dari checkbox di tabel karyawan.
     */
    public function toggleJenis(Request $request, User $employee)
    {
        if ($employee->role !== 'user') {
            return redirect()->route('admin.employees.index')->with('error', 'Akses ditolak!');
        }

        $employee->update([
            'jenis' => $request->boolean('jenis'),
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Jenis karyawan berhasil diperbarui!');
    }
}
