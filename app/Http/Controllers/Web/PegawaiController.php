<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pegawai');
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%')
                  ->orWhere('unit', 'like', '%' . $request->search . '%');
            });
        }
        $pegawais = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        $departments = \App\Models\Department::orderBy('nama')->get();
        return view('pegawai.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nik' => 'required|string|unique:users',
            'nip' => 'nullable|string|unique:users',
            'no_hp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'pangkat_gol' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:100',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'jabatan' => $request->jabatan,
            'pangkat_gol' => $request->pangkat_gol,
            'unit' => $request->unit,
            'role' => 'pegawai',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(User $pegawai)
    {
        $departments = \App\Models\Department::orderBy('nama')->get();
        return view('pegawai.edit', compact('pegawai', 'departments'));
    }

    public function update(Request $request, User $pegawai)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pegawai->id,
            'nik' => 'required|string|unique:users,nik,' . $pegawai->id,
            'nip' => 'nullable|string|unique:users,nip,' . $pegawai->id,
            'no_hp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'pangkat_gol' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:100',
        ]);

        $data = $request->only(['name', 'email', 'nik', 'nip', 'no_hp', 'jabatan', 'pangkat_gol', 'unit']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $pegawai->update($data);
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diupdate.');
    }

    public function destroy(User $pegawai)
    {
        $pegawai->delete();
        return back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
