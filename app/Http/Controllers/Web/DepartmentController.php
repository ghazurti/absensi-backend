<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('nama')->paginate(15);
        return view('departemen.index', compact('departments'));
    }

    public function create()
    {
        return view('departemen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:departments',
            'nama' => 'required|string|max:255|unique:departments',
            'keterangan' => 'nullable|string',
        ]);

        Department::create($request->all());

        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $departemen)
    {
        return view('departemen.edit', compact('departemen'));
    }

    public function update(Request $request, Department $departemen)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:departments,kode,' . $departemen->id,
            'nama' => 'required|string|max:255|unique:departments,nama,' . $departemen->id,
            'keterangan' => 'nullable|string',
        ]);

        $departemen->update($request->all());

        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $departemen)
    {
        try {
            $departemen->delete();
            return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('departemen.index')->with('error', 'Gagal menghapus departemen. Pastikan tidak ada data lain yang terikat dengan departemen ini.');
        }
    }
}
