<?php

namespace App\Http\Controllers;

use App\Models\Pcustomer;
use App\Models\ReportTimbangBeras;
use App\Models\TerimaBb;
use App\Models\VarietasBeras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PenerimaanBerasController extends Controller
{
    /**
    * Display the penerimaan beras index page.
     */
    public function index(Request $request)
    {
        $query = TerimaBb::query()
            ->leftJoin('pcustomer', 'pcustomer.kode_cust', '=', 'terima_bb.kode_supplier')
            ->leftJoin('varietas_beras', 'varietas_beras.id', '=', 'terima_bb.id_jenis')
            ->select([
                'terima_bb.id',
                'terima_bb.tgl_terima as tanggal_terima',
                'terima_bb.kode_supplier as supplier_code',
                'terima_bb.nama_supplier',
                'pcustomer.nama_cust as supplier_name',
                'terima_bb.id_jenis',
                'varietas_beras.alias as varietas_alias',
                'terima_bb.kemasan_pakai',
                'terima_bb.penggunaan_palet',
                'terima_bb.jam_awal',
                'terima_bb.tonase',
                'terima_bb.nopol',
                'terima_bb.tempat_simpan',
                'terima_bb.status',
                'terima_bb.user_created',
                'terima_bb.user_finish',
                'terima_bb.user_updated',
                'terima_bb.keterangan',
                'terima_bb.last_action',
            ]);

        if ($request->filled('search')) {
            $keyword = $request->input('search');

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('terima_bb.id', 'like', "%{$keyword}%")
                    ->orWhere('terima_bb.kode_supplier', 'like', "%{$keyword}%")
                    ->orWhere('terima_bb.nama_supplier', 'like', "%{$keyword}%")
                    ->orWhere('pcustomer.nama_cust', 'like', "%{$keyword}%")
                    ->orWhere('varietas_beras.alias', 'like', "%{$keyword}%")
                    ->orWhere('terima_bb.nopol', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('terima_bb.status', 'like', '%'.$request->input('status').'%');
        }

        if ($request->filled('supplier')) {
            $query->where('pcustomer.nama_cust', 'like', '%'.$request->input('supplier').'%');
        }

        if ($request->filled('jenis')) {
            $query->where('varietas_beras.alias', 'like', '%'.$request->input('jenis').'%');
        }

        if ($request->filled('date_from')) {
            $query->where('terima_bb.tgl_terima', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('terima_bb.tgl_terima', '<=', $request->input('date_to'));
        }

        $items = $query
            ->orderByDesc('terima_bb.tgl_terima')
            ->orderByDesc('terima_bb.id')
            ->limit(50)
            ->get();
        $suppliers = Pcustomer::query()
            ->where('kode_cust', 'not like', '%88')
            ->orderBy('nama_cust')
            ->get(['kode_cust', 'nama_cust']);

        $varietasBeras = VarietasBeras::query()
            ->where('jenis', 'beras')
            ->orderBy('alias')
            ->get(['id', 'alias']);

        $dateLabel = 'Jan 1 - Mei 1, 2026';

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->filled('date_from') ? Carbon::parse($request->input('date_from'))->translatedFormat('j M Y') : 'Awal';
            $to = $request->filled('date_to') ? Carbon::parse($request->input('date_to'))->translatedFormat('j M Y') : 'Akhir';
            $dateLabel = $from.' - '.$to;
        }

        return view('penerimaan_beras.index', [
            'items' => $items,
            'suppliers' => $suppliers,
            'varietasBeras' => $varietasBeras,
            'filters' => $request->only(['search', 'status', 'supplier', 'jenis', 'date_from', 'date_to']),
            'dateLabel' => $dateLabel,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_terima' => ['required', 'date'],
            'kode_supplier' => ['required', 'exists:pcustomer,kode_cust'],
            'id_jenis' => ['required', 'exists:varietas_beras,id'],
            'nopol' => ['nullable', 'string', 'max:255'],
            'jam_awal' => ['required', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'tempat_simpan' => ['nullable', 'string', 'max:255'],
            'kemasan_pakai' => ['nullable', 'string', 'max:255'],
            'penggunaan_palet' => ['nullable', 'in:Ya,Tidak'],
        ]);

        $supplier = Pcustomer::query()
            ->where('kode_cust', $validated['kode_supplier'])
            ->firstOrFail();

        $varietas = VarietasBeras::query()
            ->findOrFail($validated['id_jenis']);

        $userName = Auth::user()?->username ?: Auth::user()?->name;
        $now = Carbon::now();

        $record = new TerimaBb();
        $record->tgl_terima = $validated['tgl_terima'];
        $record->kode_supplier = $supplier->kode_cust;
        $record->nama_supplier = $supplier->nama_cust;
        $record->id_jenis = $varietas->id;
        $record->jenis_bahan = $varietas->alias;
        $record->tonase = '0';
        $record->nopol = $validated['nopol'] ?? null;
        $record->jam_awal = $validated['jam_awal'];
        $record->jam_akhir = null;
        $record->tempat_simpan = $validated['tempat_simpan'] ?? null;
        $record->kemasan_pakai = $validated['kemasan_pakai'] ?? null;
        $record->penggunaan_palet = $validated['penggunaan_palet'] ?? '';
        $record->status = 'Proses';
        $record->user_created = $userName;
        $record->user_updated = null;
        $record->posttime = $now;
        $record->updated_at = null;
        $record->keterangan = '';
        $record->last_action = 'Input Penerimaan';
        $record->user_finish = '';
        $record->user_approved = '';
        $record->save();

        return redirect()
            ->route('penerimaan_beras.index')
            ->with('success', 'Data penerimaan beras berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl_terima' => ['required', 'date'],
            'kode_supplier' => ['required', 'exists:pcustomer,kode_cust'],
            'id_jenis' => ['required', 'exists:varietas_beras,id'],
            'nopol' => ['nullable', 'string', 'max:255'],
            'jam_awal' => ['required', 'string', 'max:255'],
            'tempat_simpan' => ['nullable', 'string', 'max:255'],
            'kemasan_pakai' => ['nullable', 'string', 'max:255'],
            'penggunaan_palet' => ['nullable', 'in:Ya,Tidak'],
        ]);

        $record = TerimaBb::query()->findOrFail($id);
        
        $supplier = Pcustomer::query()
            ->where('kode_cust', $validated['kode_supplier'])
            ->firstOrFail();

        $varietas = VarietasBeras::query()
            ->findOrFail($validated['id_jenis']);

        $userName = Auth::user()?->username ?: Auth::user()?->name;
        $now = Carbon::now();

        $record->update([
            'tgl_terima' => $validated['tgl_terima'],
            'kode_supplier' => $supplier->kode_cust,
            'nama_supplier' => $supplier->nama_cust,
            'id_jenis' => $varietas->id,
            'jenis_bahan' => $varietas->alias,
            'nopol' => $validated['nopol'] ?? null,
            'jam_awal' => $validated['jam_awal'],
            'tempat_simpan' => $validated['tempat_simpan'] ?? null,
            'kemasan_pakai' => $validated['kemasan_pakai'] ?? null,
            'penggunaan_palet' => $validated['penggunaan_palet'] ?? '',
            'user_updated' => $userName,
            'updated_at' => $now,
            'last_action' => 'Finish Penerimaan Beras',
        ]);

        return redirect()
            ->route('penerimaan_beras.index')
            ->with('success', 'Data penerimaan beras berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $record = TerimaBb::query()->findOrFail($id);

        ReportTimbangBeras::query()
            ->where('id_bahan', (string) $record->id)
            ->delete();

        $record->delete();

        return redirect()
            ->route('penerimaan_beras.index')
            ->with('success', 'Data penerimaan beras berhasil dihapus.');
    }
}