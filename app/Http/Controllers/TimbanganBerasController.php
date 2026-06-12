<?php

namespace App\Http\Controllers;

use App\Models\ReportTimbangBeras;
use App\Models\TerimaBb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TimbanganBerasController extends Controller
{
    public function create(Request $request): View
    {
        $terimaBbId = $request->integer('terima_bb_id');

        $terimaBb = $terimaBbId
            ? TerimaBb::query()->findOrFail($terimaBbId)
            : TerimaBb::query()
                ->orderByDesc('tgl_terima')
                ->orderByDesc('id')
                ->firstOrFail();

        $rows = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->orderByRaw('CAST(timbang_ke AS UNSIGNED) ASC')
            ->get();

        $nextTimbangKe = $rows->count() + 1;

        return view('timbangan_beras.create', [
            'terimaBb' => $terimaBb,
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
            'kadar_broken' => ['nullable', 'numeric', 'min:0'],
        ]);

        $terimaBb = TerimaBb::query()->findOrFail($validated['terima_bb_id']);
        $existingCount = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->count();

        $now = Carbon::now();

        ReportTimbangBeras::create([
            'id_bahan' => (string) $terimaBb->id,
            'no_penerimaan' => '',
            'tanggal_terima' => $terimaBb->tgl_terima,
            'supplier' => $terimaBb->nama_supplier,
            'id_jenis' => (string) $terimaBb->id_jenis,
            'jumlah_karung' => $validated['jumlah_karung'],
            'timbang_ke' => $existingCount + 1,
            'tonase' => $validated['tonase'],
            'kadar_air' => $validated['kadar_air'] ?? 0,
            'kadar_broken' => $validated['kadar_broken'] ?? 0,
            'keterangan' => '',
            'ket_bagian' => '',
            'sorting' => 0,
            'user_created' => auth()->user()?->username ?: auth()->user()?->name,
            'posttime' => $now,
            'user_updated' => '',
            'updated_at' => null,
            'last_action' => 'Input Timbangan',
        ]);

        $totalTonase = (float) ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->sum('tonase');
        
        $terimaBb->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-beras.create', ['terima_bb_id' => $terimaBb->id])
            ->with('success', 'Data timbangan berhasil disimpan.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_karung' => ['required', 'numeric', 'min:0'],
            'tonase' => ['required', 'numeric', 'min:0'],
            'kadar_air' => ['nullable', 'numeric', 'min:0'],
            'kadar_broken' => ['nullable', 'numeric', 'min:0'],
        ]);

        $timbangan = ReportTimbangBeras::query()->findOrFail($id);
        
        $timbangan->update([
            'jumlah_karung' => $validated['jumlah_karung'],
            'tonase' => $validated['tonase'],
            'kadar_air' => $validated['kadar_air'] ?? 0,
            'kadar_broken' => $validated['kadar_broken'] ?? 0,
            'user_updated' => auth()->user()?->username ?: auth()->user()?->name,
            'updated_at' => Carbon::now(),
        ]);

        $terimaBb = TerimaBb::query()->findOrFail($timbangan->id_bahan);
        $totalTonase = (float) ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->sum('tonase');
        
        $terimaBb->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-beras.create', ['terima_bb_id' => $terimaBb->id])
            ->with('success', 'Data timbangan berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $timbangan = ReportTimbangBeras::query()->findOrFail($id);
        $terimaBbId = $timbangan->id_bahan;
        
        $timbangan->delete();

        $terimaBb = TerimaBb::query()->findOrFail($terimaBbId);
        $totalTonase = (float) ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->sum('tonase');
        
        $terimaBb->update(['tonase' => (string) $totalTonase]);

        return redirect()
            ->route('timbangan-beras.create', ['terima_bb_id' => $terimaBb->id])
            ->with('success', 'Data timbangan berhasil dihapus.');
    }

    public function selesaiTimbang(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $terimaBb = TerimaBb::query()->findOrFail($id);
        $currentUser = auth()->user()?->username ?: auth()->user()?->name;
        $now = Carbon::now(config('app.timezone'));

        $terimaBb->update([
            'jam_akhir' => $now->format('H:i'),
            'user_updated' => $currentUser,
            'updated_at' => $now,
            'status' => 'Menunggu Validasi',
            'last_action' => 'Menunggu Validasi',
            'keterangan' => $validated['keterangan'] ?? $terimaBb->keterangan,
        ]);

        return redirect()
            ->route('penerimaan_beras.index')
            ->with('success', 'Data penimbangan berhasil diselesaikan.');
    }

    public function validateApproval($id): RedirectResponse
    {
        $terimaBb = TerimaBb::query()->findOrFail($id);
        $currentUser = auth()->user()?->username ?: auth()->user()?->name;

        if ($currentUser === $terimaBb->user_created && !auth()->user()->hasRole('super admin')) {
            return redirect()
                ->route('penerimaan_beras.index')
                ->with('error', 'Anda tidak bisa memvalidasi data yang Anda input sendiri.');
        }

        $terimaBb->update([
            'user_finish' => $currentUser,
            'status' => 'Finish',
            'last_action' => 'Finish Penerimaan Beras',
        ]);

        return redirect()
            ->route('penerimaan_beras.index')
            ->with('success', 'Data penimbangan berhasil selesai.');
    }
}
