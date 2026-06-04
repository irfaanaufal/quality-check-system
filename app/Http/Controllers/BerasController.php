<?php

namespace App\Http\Controllers;

use App\Models\Beras;
use Illuminate\Http\Request;

class BerasController extends Controller
{
    public function index(Request $request)
    {
        $query = Beras::query();

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function($s) use ($q) {
                $s->where('no_penerimaan', 'like', "%{$q}%")
                  ->orWhere('supplier', 'like', "%{$q}%")
                  ->orWhere('jenis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', 'like', '%'.$request->input('supplier').'%');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        if ($request->filled('date_from')) {
            $query->where('tanggal', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('tanggal', '<=', $request->input('date_to'));
        }

        $beras = $query->orderBy('idb_beras', 'desc')->paginate(25)->withQueryString();

        return view('beras.index', [
            'beras' => $beras,
            'filters' => $request->only(['search','status','supplier','jenis','date_from','date_to']),
        ]);
    }
}
