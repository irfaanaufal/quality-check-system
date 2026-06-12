<?php

namespace App\Http\Controllers;

use App\Models\ReportTimbangGabah;
use App\Models\TerimaBg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TimbanganGabahController extends Controller
{
    public function create(Request $request): View
    {
        $terimaBgId = $request->integer('terima_bb_id');

        $terimaBg = $terimaBgId
            ? TerimaBg::query()->findOrFail($terimaBgId)
            : TerimaBg::query()
                ->orderByDesc('tgl_terima')
                ->orderByDesc('id')
                ->firstOrFail();

        $rows = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->orderByRaw('CAST(timbang_ke AS UNSIGNED) ASC')
            ->get();

        $nextTimbangKe = $rows->count() + 1;

        return view('timbangan_gabah.create', [
            'terimaBg' => $terimaBg,
            'rows' => $rows,
            'nextTimbangKe' => $nextTimbangKe,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'terima_bb_id' => ['required', 'integer'],
            'jumlah_karung' => ['required', 'numeric', 'min:0'],
            'tonase' => ['required', 'numeric', 'min:0'],
            'kadar_air' => ['nullable', 'numeric', 'min:0'],
        ]);

        $terimaBg = TerimaBg::query()->findOrFail($validated['terima_bb_id']);
        $existingCount = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->count();

        $now = Carbon::now();

        ReportTimbangGabah::create([
            'id_bahan' => (string) $terimaBg->id,
            'no_penerimaan' => '',
            'tanggal_terima' => $terimaBg->tgl_terima,
            'supplier' => $terimaBg->nama_supplier,
            'id_jenis' => (string) $terimaBg->id_jenis,
            'jumlah_karung' => $validated['jumlah_karung'],
            'timbang_ke' => $existingCount + 1,
            'tonase' => $validated['tonase'],
            'kadar_air' => $validated['kadar_air'] ?? 0,
            'kadar_broken' => 0,
            'keterangan' => '',
            'ket_bagian' => '',
            'sorting' => 0,
            'user_created' => auth()->user()?->username ?: auth()->user()?->name,
            'posttime' => $now,
            'user_updated' => '',
            'updated_at' => null,
            'last_action' => 'Input Timbangan',
        ]);

        $totalTonase = (float) ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->sum('tonase');
        
        $terimaBg->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-gabah.create', ['terima_bb_id' => $terimaBg->id])
            ->with('success', 'Data timbangan berhasil disimpan.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_karung' => ['required', 'numeric', 'min:0'],
            'tonase' => ['required', 'numeric', 'min:0'],
            'kadar_air' => ['nullable', 'numeric', 'min:0'],
        ]);

        $timbangan = ReportTimbangGabah::query()->findOrFail($id);
        
        $timbangan->update([
            'jumlah_karung' => $validated['jumlah_karung'],
            'tonase' => $validated['tonase'],
            'kadar_air' => $validated['kadar_air'] ?? 0,
            'user_updated' => auth()->user()?->username ?: auth()->user()?->name,
            'updated_at' => Carbon::now(),
        ]);

        $terimaBg = TerimaBg::query()->findOrFail($timbangan->id_bahan);
        $totalTonase = (float) ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->sum('tonase');
        
        $terimaBg->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-gabah.create', ['terima_bb_id' => $terimaBg->id])
            ->with('success', 'Data timbangan berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $timbangan = ReportTimbangGabah::query()->findOrFail($id);
        $terimaBgId = $timbangan->id_bahan;
        
        $timbangan->delete();

        $terimaBg = TerimaBg::query()->findOrFail($terimaBgId);
        $totalTonase = (float) ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->sum('tonase');
        
        $terimaBg->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-gabah.create', ['terima_bb_id' => $terimaBg->id])
            ->with('success', 'Data timbangan berhasil dihapus.');
    }

    public function selesaiTimbang(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $terimaBg = TerimaBg::query()->findOrFail($id);
        $currentUser = auth()->user()?->username ?: auth()->user()?->name;
        $now = Carbon::now(config('app.timezone'));

        $terimaBg->update([
            'jam_akhir' => $now->format('H:i'),
            'user_updated' => $currentUser,
            'updated_at' => $now,
            'status' => 'Menunggu Validasi',
            'last_action' => 'Menunggu Validasi',
            'keterangan' => $validated['keterangan'] ?? $terimaBg->keterangan,
        ]);

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penimbangan berhasil diselesaikan.');
    }

    public function validateApproval($id): RedirectResponse
    {
        $terimaBg = TerimaBg::query()->findOrFail($id);
        $currentUser = auth()->user()?->username ?: auth()->user()?->name;

        if ($currentUser === $terimaBg->user_created && !auth()->user()->hasRole('super admin')) {
            return redirect()
                ->route('penerimaan_gabah.index')
                ->with('error', 'Anda tidak bisa memvalidasi data yang Anda input sendiri.');
        }

        $terimaBg->update([
            'user_finish' => $currentUser,
            'status' => 'Finish',
            'last_action' => 'Finish Penerimaan Gabah',
        ]);

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data penimbangan berhasil selesai.');
    }
}
