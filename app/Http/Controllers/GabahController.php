<?php

namespace App\Http\Controllers;

use App\Models\Gabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GabahController extends Controller
{
    public function index(Request $request)
    {
        $query = Gabah::query()->whereIn('status', ['finish', 'approve', 'approved']);

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

        $gabah = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('bahan_baku_gabah.index', [
            'gabah' => $gabah,
            'filters' => $request->only(['search','status','supplier','jenis','date_from','date_to']),
        ]);
    }

    public function updateHarga(Request $request, $id)
    {
        $request->validate([
            'harga' => 'required|numeric|min:0',
        ]);

        $gabah = Gabah::findOrFail($id);
        $gabah->harga = $request->input('harga');
        $gabah->posttime_harga = now();
        $gabah->save();

        // Hitung harga_rata untuk id_timbang yang sama
        if ($gabah->id_timbang) {
            $relatedRecords = Gabah::where('id_timbang', $gabah->id_timbang)->get();
            $totalHarga = 0;
            $countRecords = 0;
            foreach ($relatedRecords as $record) {
                if (is_numeric($record->harga)) {
                    $totalHarga += (int)$record->harga;
                    $countRecords++;
                }
            }
            $hargaRata = $countRecords > 0 ? round($totalHarga / $countRecords) : 0;

            // Update harga_rata untuk semua record dengan id_timbang tersebut
            Gabah::where('id_timbang', $gabah->id_timbang)->update(['harga_rata' => $hargaRata]);
        }

        return redirect()->back()->with('success', 'Harga berhasil diperbarui');
    }

    public function print($id)
    {
        // Ambil gabah yang diklik
        $gabahKlik = Gabah::findOrFail($id);

        // Ambil semua sorting dalam id_timbang yang sama, urutkan by no_penerimaan
        $semuaSorting = Gabah::where('id_timbang', $gabahKlik->id_timbang)
            ->orderBy('no_penerimaan', 'asc')
            ->get();

        // Ambil data terima_bg (sama untuk semua sorting)
        $terimaBg = \App\Models\TerimaBg::find($gabahKlik->id_timbang);
        $tanggal = $terimaBg
            ? \Illuminate\Support\Carbon::parse($terimaBg->tgl_terima)->format('d/m')
            : '';

        // Siapkan data per sorting
        $dataSorting = [];

        foreach ($semuaSorting as $gabah) {
            // Ambil semua timbangan milik sorting ini (by no_penerimaan)
            $reportTimbang = \App\Models\ReportTimbangGabah::where('no_penerimaan', $gabah->no_penerimaan)
                ->orderByRaw('CAST(timbang_ke AS UNSIGNED) ASC')
                ->get();

            // Pilih kadar: urutan ke 1,4,7,10,... (setiap kelipatan 3 dimulai dari index 0)
            $selectedKadar = collect();
            foreach ($reportTimbang as $index => $row) {
                if ($index % 3 === 0) {
                    $selectedKadar->push($row);
                }
            }

            // Split timbangan kiri & kanan
            $leftColumn  = collect();
            $rightColumn = collect();
            foreach ($reportTimbang as $index => $row) {
                if ($index % 2 === 0) {
                    $leftColumn->push($row);
                } else {
                    $rightColumn->push($row);
                }
            }

            $dataSorting[] = [
                'gabah'         => $gabah,
                'reportTimbang' => $reportTimbang,
                'selectedKadar' => $selectedKadar,
                'leftColumn'    => $leftColumn,
                'rightColumn'   => $rightColumn,
            ];
        }

        return view('bahan_baku_gabah.print', compact(
            'dataSorting',
            'tanggal',
            'terimaBg'
        ));
    }
}
