<?php

namespace App\Http\Controllers;

use App\Models\Pcustomer;
use App\Models\ReportTimbangGabah;
use App\Models\TerimaBg;
use App\Models\VarietasBeras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PenerimaanGabahController extends Controller
{
    /**
    * Display the penerimaan gabah index page.
     */
    public function index(Request $request)
    {
        $query = TerimaBg::query()
            ->leftJoin('pcustomer', 'pcustomer.kode_cust', '=', 'terima_bg.kode_supplier')
            ->leftJoin('varietas_beras', 'varietas_beras.id', '=', 'terima_bg.id_jenis')
            ->select([
                'terima_bg.id',
                'terima_bg.tgl_terima as tanggal_terima',
                'terima_bg.kode_supplier as supplier_code',
                'terima_bg.nama_supplier',
                'pcustomer.nama_cust as supplier_name',
                'terima_bg.id_jenis',
                'varietas_beras.alias as varietas_alias',
                'terima_bg.penggunaan_palet',
                'terima_bg.jam_awal',
                'terima_bg.tonase',
                'terima_bg.nopol',
                'terima_bg.tempat_simpan',
                'terima_bg.status',
                'terima_bg.user_created',
                'terima_bg.user_finish',
                'terima_bg.user_updated',
                'terima_bg.keterangan',
                'terima_bg.last_action',
            ]);

        if ($request->filled('search')) {
            $keyword = $request->input('search');

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('terima_bg.id', 'like', "%{$keyword}%")
                    ->orWhere('terima_bg.kode_supplier', 'like', "%{$keyword}%")
                    ->orWhere('terima_bg.nama_supplier', 'like', "%{$keyword}%")
                    ->orWhere('pcustomer.nama_cust', 'like', "%{$keyword}%")
                    ->orWhere('varietas_beras.alias', 'like', "%{$keyword}%")
                    ->orWhere('terima_bg.nopol', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('terima_bg.status', 'like', '%'.$request->input('status').'%');
        }

        if ($request->filled('supplier')) {
            $query->where('pcustomer.nama_cust', 'like', '%'.$request->input('supplier').'%');
        }

        if ($request->filled('jenis')) {
            $query->where('varietas_beras.alias', 'like', '%'.$request->input('jenis').'%');
        }

        if ($request->filled('date_from')) {
            $query->where('terima_bg.tgl_terima', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('terima_bg.tgl_terima', '<=', $request->input('date_to'));
        }

        $items = $query
            ->orderByDesc('terima_bg.tgl_terima')
            ->orderByDesc('terima_bg.id')
            ->limit(50)
            ->get();
        $suppliers = Pcustomer::query()
            ->where('kode_cust', 'not like', '%88')
            ->orderBy('nama_cust')
            ->get(['kode_cust', 'nama_cust']);

        $varietasGabah = VarietasBeras::query()
            ->where('jenis', 'gabah')
            ->orderBy('alias')
            ->get(['id', 'alias']);

        $dateLabel = 'Jan 1 - Mei 1, 2026';

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->filled('date_from') ? Carbon::parse($request->input('date_from'))->translatedFormat('j M Y') : 'Awal';
            $to = $request->filled('date_to') ? Carbon::parse($request->input('date_to'))->translatedFormat('j M Y') : 'Akhir';
            $dateLabel = $from.' - '.$to;
        }

        return view('penerimaan_gabah.index', [
            'items' => $items,
            'suppliers' => $suppliers,
            'varietasGabah' => $varietasGabah,
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
            'penggunaan_palet' => ['nullable', 'in:Ya,Tidak'],
        ]);

        $supplier = Pcustomer::query()
            ->where('kode_cust', $validated['kode_supplier'])
            ->firstOrFail();

        $varietas = VarietasBeras::query()
            ->findOrFail($validated['id_jenis']);

        $userName = Auth::user()?->username ?: Auth::user()?->name;
        $now = Carbon::now();

        $record = new TerimaBg();
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
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penerimaan gabah berhasil disimpan.');
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
            'penggunaan_palet' => ['nullable', 'in:Ya,Tidak'],
        ]);

        $record = TerimaBg::query()->findOrFail($id);
        
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
            'penggunaan_palet' => $validated['penggunaan_palet'] ?? '',
            'user_updated' => $userName,
            'updated_at' => $now,
            'last_action' => 'Finish Penerimaan Gabah',
        ]);

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penerimaan gabah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $record = TerimaBg::query()->findOrFail($id);

        ReportTimbangGabah::query()
            ->where('id_bahan', (string) $record->id)
            ->delete();

        $record->delete();

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penerimaan gabah berhasil dihapus.');
    }

    public function approve($id)
    {
        $record = TerimaBg::query()->findOrFail($id);
        $currentUser = Auth::user()?->username ?: Auth::user()?->name;

        // Verify that all pembagian are filled
        $hasTimbangan = ReportTimbangGabah::where('id_bahan', (string) $record->id)->exists();
        $hasUnsorted = ReportTimbangGabah::where('id_bahan', (string) $record->id)->where('sorting', 0)->exists();

        if (!$hasTimbangan || $hasUnsorted) {
            return redirect()
                ->route('penerimaan_gabah.index')
                ->with('error', 'Pembagian gabah belum selesai diisi.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $currentUser) {
            $record->update([
                'status' => 'Approved',
                'user_approved' => $currentUser,
                'last_action' => 'Approve Penerimaan Gabah',
            ]);

            \App\Models\Gabah::where('id_timbang', $record->id)
                ->where('status', '!=', 'Cancel')
                ->update([
                    'status' => 'Approved',
                    'user_approve' => $currentUser,
                ]);
        });

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penerimaan gabah berhasil di-approve.');
    }

    public function unapprove($id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'super admin'])) {
            return redirect()
                ->route('penerimaan_gabah.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk unapprove.');
        }

        $record = TerimaBg::query()->findOrFail($id);
        $currentUser = $user->username ?: $user->name;

        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $currentUser) {
            $record->update([
                'status' => 'Checked',
                'user_approved' => '',
                'last_action' => 'Unapprove Penerimaan Gabah',
                'user_updated' => $currentUser,
                'updated_at' => now(),
            ]);

            \App\Models\Gabah::where('id_timbang', $record->id)
                ->where('status', '!=', 'Cancel')
                ->update([
                    'status' => 'Finish',
                    'user_approve' => '',
                ]);
        });

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Approval data penerimaan gabah berhasil dibatalkan.');
    }

    public function uncheck($id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'super admin'])) {
            return redirect()
                ->route('penerimaan_gabah.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk uncheck.');
        }

        $record = TerimaBg::query()->findOrFail($id);
        $currentUser = $user->username ?: $user->name;

        $record->update([
            'status' => 'Finish',
            'last_action' => 'Uncheck Penerimaan Gabah',
            'user_updated' => $currentUser,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Status checked berhasil dibatalkan.');
    }
}
