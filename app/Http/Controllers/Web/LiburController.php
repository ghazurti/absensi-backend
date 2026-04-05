<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Libur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiburController extends Controller
{
    public function index()
    {
        $liburs = Libur::orderBy('tanggal', 'desc')->paginate(15);
        return view('libur.index', compact('liburs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:liburs,tanggal',
            'nama_libur' => 'required|string|max:100',
        ]);

        Libur::create($request->all());

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(Libur $libur)
    {
        $libur->delete();
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
