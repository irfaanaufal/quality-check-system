<?php

namespace App\Http\Controllers;

use App\Models\Gabah;
use App\Models\KriteriaBb;
use App\Models\ReportTimbangGabah;
use App\Models\Pcustomer;
use App\Models\TerimaBg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PembagianGabahController extends Controller
{
    private function buildNoPenerimaan(string $prefix, ?Carbon $tanggalTerima, int $sequenceNumber): string
    {
        $yearMonth = $tanggalTerima?->format('Ym') ?? now()->format('Ym');
        $sequenceSuffix = substr(str_pad((string) $sequenceNumber, 4, '0', STR_PAD_LEFT), -4);

        return sprintf('%s%s%s', $prefix, $yearMonth, $sequenceSuffix);
    }

    private function noPenerimaanPrefix(): string
    {
        return DB::table('config')->where('id', 1)->value('value') ?? 'PG';
    }

    private function currentNoPenerimaanSequence(): int
    {
        $rawSequence = DB::table('config')->where('id', 2)->value('value') ?? '0000';

        return (int) preg_replace('/\D/', '', (string) $rawSequence);
    }

    private function nextNoPenerimaanSequence(): int
    {
        return $this->currentNoPenerimaanSequence() + 1;
    }

    private function syncNoPenerimaanSequence(int $sequenceNumber): void
    {
        $sequenceSuffix = substr(str_pad((string) max($sequenceNumber, 0), 4, '0', STR_PAD_LEFT), -4);

        DB::table('config')->where('id', 2)->update(['value' => $sequenceSuffix]);
    }

    private function nextAvailableNoPenerimaan(?Carbon $tanggalTerima): string
    {
        return $this->buildNoPenerimaan(
            $this->noPenerimaanPrefix(),
            $tanggalTerima,
            $this->nextNoPenerimaanSequence()
        );
    }

    private function nextAvailableSortingNumber(int $terimaBgId): int
    {
        $usedSortings = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBgId)
            ->where('sorting', '>', 0)
            ->distinct()
            ->orderBy('sorting')
            ->pluck('sorting')
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        $nextSorting = 1;

        foreach ($usedSortings as $sorting) {
            if ($sorting === $nextSorting) {
                $nextSorting++;
                continue;
            }

            if ($sorting > $nextSorting) {
                break;
            }
        }

        return $nextSorting;
    }

    public function create(Request $request)
    {
        $terimaBgId = $request->integer('terima_bb_id');

        $terimaBg = $terimaBgId
            ? TerimaBg::query()->findOrFail($terimaBgId)
            : TerimaBg::query()->orderByDesc('tgl_terima')->orderByDesc('id')->firstOrFail();

        // Get all timbang data for this terima_bg
        $rows = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->orderByRaw('CAST(timbang_ke AS UNSIGNED) ASC')
            ->get();

        // Get all existing pembagian for this terima_bg
        $existingPembagian = Gabah::query()
            ->where('id_timbang', $terimaBg->id)
            ->where('status', '!=', 'Cancel')
            ->orderBy('posttime', 'desc')
            ->get();

        $tanggalTerima = filled($terimaBg->tgl_terima) ? Carbon::parse($terimaBg->tgl_terima) : null;
        $noPenerimaan = $this->nextAvailableNoPenerimaan($tanggalTerima);

        $warnaOptions = KriteriaBb::query()
            ->where('varietas', 'Gabah')
            ->where('jenis', 'Warna')
            ->orderBy('nilai')
            ->get();

        $aromaOptions = KriteriaBb::query()
            ->where('varietas', 'Gabah')
            ->where('jenis', 'Aroma')
            ->orderBy('nilai')
            ->get();

        if ($aromaOptions->isEmpty()) {
            $aromaOptions = KriteriaBb::query()
                ->where('jenis', 'Aroma')
                ->orderBy('nilai')
                ->get();
        }

        if ($warnaOptions->isEmpty()) {
            $warnaOptions = KriteriaBb::query()
                ->where('jenis', 'Warna')
                ->orderBy('nilai')
                ->get();
        }

        // Calculate next available sorting number
        $nextSorting = $this->nextAvailableSortingNumber($terimaBg->id);

        return view('pembagian_gabah.create', [
            'terimaBg' => $terimaBg,
            'rows' => $rows,
            'existingPembagian' => $existingPembagian,
            'noPenerimaan' => $noPenerimaan,
            'warnaOptions' => $warnaOptions,
            'aromaOptions' => $aromaOptions,
            'nextSorting' => $nextSorting,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Pembagian Gabah store request received', $request->all());
        
        try {
            $isEditRequest = $request->filled('editing_sorting_number');

            $rules = [
                'terima_bb_id' => ['required', 'integer', 'exists:terima_bg,id'],
                'kondisi_umum' => ['required', 'string', 'max:255'],
                'kondisi_kendaraan' => ['required', 'string', 'max:255'],
                'keputusan_penerimaan' => ['required', 'string', 'max:255'],
                'sorter_gabah' => ['required', 'in:Ya,Tidak'],
                'warna' => ['required', 'integer', 'exists:kriteria_bb,id'],
                'aroma_gabah' => ['required', 'integer', 'exists:kriteria_bb,id'],
                'indikasi_kimia' => ['required', 'string', 'max:255'],
                'catatan_cek' => ['required', 'string', 'max:255'],
                'keterangan_penerimaan' => ['required', 'string', 'max:1000'],
                'pembagian_ke' => ['required', 'integer', 'min:1'],
                'harga' => ['nullable', 'numeric', 'min:0'],
                'editing_sorting_number' => ['nullable', 'integer', 'min:1'],
                'editing_no_penerimaan' => ['nullable', 'string', 'max:255'],
            ];

            if ($isEditRequest) {
                $rules['total_qty_terpilih'] = ['nullable', 'numeric', 'min:0'];
                $rules['selected_rows'] = ['nullable', 'array'];
                $rules['selected_rows.*'] = ['integer', 'exists:report_timbang_gabah,id'];
            } else {
                $rules['total_qty_terpilih'] = ['required', 'numeric', 'gt:0'];
                $rules['selected_rows'] = ['required', 'array', 'min:1'];
                $rules['selected_rows.*'] = ['integer', 'exists:report_timbang_gabah,id'];
            }

            $validated = $request->validate($rules);
            
            Log::info('Validation passed', $validated);

            $terimaBg = TerimaBg::query()->findOrFail($validated['terima_bb_id']);
            
            Log::info('Terima Bg found', ['id' => $terimaBg->id]);

            $isEdit = !empty($validated['editing_sorting_number']);
            $noPenerimaan = '';
            
            if ($isEdit) {
                // Edit mode - use existing no penerimaan
                $noPenerimaan = $validated['editing_no_penerimaan'] ?? '';
                Log::info('Edit mode', ['no_penerimaan' => $noPenerimaan, 'sorting_number' => $validated['editing_sorting_number']]);
            } else {
                // Create mode - generate new no penerimaan
                $tanggalTerima = filled($terimaBg->tgl_terima) ? Carbon::parse($terimaBg->tgl_terima) : null;
                $noPenerimaan = $this->nextAvailableNoPenerimaan($tanggalTerima);
                
                Log::info('No Penerimaan generated', ['no_penerimaan' => $noPenerimaan]);
            }

            $kodePrincipal = Pcustomer::query()
                ->where('kode_cust', $terimaBg->kode_supplier)
                ->value('kode_principal') ?? '';

            $warnaId = $validated['warna'] ?? null;
            $aromaId = $validated['aroma_gabah'] ?? null;

            $userName = auth()->user()?->username ?? auth()->user()?->name ?? 'system';
            
            Log::info('User name', ['user_name' => $userName]);

            $savedPembagian = null;
            $existingNoPenerimaan = $validated['editing_no_penerimaan'] ?? $noPenerimaan;
            $nextNoPenerimaan = $noPenerimaan;
            $updatedAt = Carbon::now();
            $reportLastAction = 'Pembagian Timbangan';
            $selectedRowIds = array_map('intval', $validated['selected_rows'] ?? []);

            if (empty($validated['selected_rows'])) {
                if ($isEdit) {
                    DB::transaction(function () use (
                        $terimaBg,
                        $existingNoPenerimaan,
                        $validated,
                        $userName,
                        $updatedAt
                    ) {
                        $sortingNumber = $validated['editing_sorting_number'];
                        $pembagianToDeleteQuery = Gabah::query()
                            ->where('id_timbang', $terimaBg->id)
                            ->where('status', '!=', 'Cancel');

                        if (filled($existingNoPenerimaan)) {
                            $pembagianToDeleteQuery->where('no_penerimaan', $existingNoPenerimaan);
                        } else {
                            $pembagianToDeleteQuery
                                ->orderBy('posttime', 'asc')
                                ->skip(max($sortingNumber - 1, 0));
                        }

                        $pembagianToDelete = $pembagianToDeleteQuery->first();

                        if ($pembagianToDelete) {
                            $pembagianToDelete->update([
                                'status' => 'Cancel',
                                'last_action' => 'Cancel Pembagian Gabah',
                                'user_updated' => $userName,
                            ]);
                        }

                        $clearQuery = ReportTimbangGabah::query()
                            ->where('id_bahan', (string) $terimaBg->id);

                        if (filled($existingNoPenerimaan)) {
                            $clearQuery->where('no_penerimaan', $existingNoPenerimaan);
                        } else {
                            $clearQuery->where('sorting', $sortingNumber);
                        }

                        $clearQuery->update([
                            'sorting' => 0,
                            'no_penerimaan' => '',
                            'ket_bagian' => '',
                            'user_updated' => $userName,
                            'updated_at' => $updatedAt,
                        ]);
                    });

                    return response()->json([
                        'success' => true,
                        'message' => 'Data pembagian gabah berhasil dihapus.',
                        'data' => [
                            'pembagian' => null,
                            'no_penerimaan' => $noPenerimaan,
                            'next_no_penerimaan' => $this->nextAvailableNoPenerimaan($terimaBg->tgl_terima ? Carbon::parse($terimaBg->tgl_terima) : null),
                            'next_sorting' => $this->nextAvailableSortingNumber($terimaBg->id),
                        ],
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Pilih minimal satu data timbangan sebelum menyimpan.',
                ], 422);
            }

            DB::transaction(function () use (
                &$savedPembagian,
                $terimaBg,
                $validated,
                $noPenerimaan,
                $existingNoPenerimaan,
                $isEdit,
                $kodePrincipal,
                $warnaId,
                $aromaId,
                $userName,
                $updatedAt,
                $reportLastAction,
                $selectedRowIds
            ) {
                Log::info('Starting database transaction', ['is_edit' => $isEdit]);

                if (!$isEdit) {
                    $this->syncNoPenerimaanSequence($this->nextNoPenerimaanSequence());
                }
                
                $dataToSave = [
                    'no_penerimaan' => $noPenerimaan,
                    'tanggal' => $terimaBg->tgl_terima,
                    'supplier' => $terimaBg->nama_supplier,
                    'id_jenis' => (string) $terimaBg->id_jenis,
                    'jenis' => $terimaBg->jenis_bahan,
                    'no_sample' => '',
                    'kode_principal' => $kodePrincipal,
                    'berat' => $validated['total_qty_terpilih'] ?? 0,
                    'stok' => $validated['total_qty_terpilih'] ?? 0,
                    'kemasan' => null, // TerimaBg does not have kemasan_pakai
                    'kondisi' => $validated['kondisi_umum'] ?? '',
                    'kendaraan' => $validated['kondisi_kendaraan'] ?? '',
                    'keputusan' => $validated['keputusan_penerimaan'] ?? '',
                    'nopol' => $terimaBg->nopol,
                    'status' => 'Proses',
                    'user' => $userName,
                    'keterangan' => $validated['catatan_cek'] ?? '',
                    'sorter' => $validated['sorter_gabah'] === 'Ya' ? 1 : 0,
                    'penggunaan_palet' => $terimaBg->penggunaan_palet,
                    'lokasi_penyimpanan' => $terimaBg->tempat_simpan,
                    'nilai' => '',
                    'harga' => $validated['harga'] ?? '',
                    'id_timbang' => $terimaBg->id,
                    'posttime' => now(),
                    'warna' => $warnaId,
                    'aroma' => $aromaId,
                    'indikasi_kimia' => $validated['indikasi_kimia'] ?? '',
                    'catatan_cek' => $validated['keterangan_penerimaan'] ?? '',
                    'user_approve' => '',
                    'harga_rata' => '',
                    'posttime_harga' => '',
                    'poles' => 0,
                    'pecah_kulit' => 0,
                    'user_updated' => $isEdit ? $userName : '',
                    'last_action' => $isEdit ? 'Update Pembagian Gabah' : 'Pembagian Gabah',
                ];
                
                if ($isEdit) {
                    // Edit mode - get pembagian list and update the one at sorting index
                    $pembagianList = Gabah::query()
                        ->where('id_timbang', $terimaBg->id)
                        ->where('status', '!=', 'Cancel')
                        ->orderBy('posttime', 'asc')
                        ->get();
                        
                    $sortingNumber = $validated['editing_sorting_number'];
                    $pembagianToUpdate = Gabah::query()
                        ->where('id_timbang', $terimaBg->id)
                        ->where('no_penerimaan', $noPenerimaan)
                        ->first();

                    if (!$pembagianToUpdate && $sortingNumber >= 1 && $sortingNumber <= $pembagianList->count()) {
                        $pembagianToUpdate = $pembagianList[$sortingNumber - 1];
                    }

                    if ($pembagianToUpdate) {
                        $dataToSave['no_sample'] = $pembagianToUpdate->no_sample ?? $noPenerimaan;
                        $dataToSave['kode_principal'] = $pembagianToUpdate->kode_principal ?? $kodePrincipal;
                        $dataToSave['user_approve'] = $pembagianToUpdate->user_approve ?? '';
                        $dataToSave['harga_rata'] = $pembagianToUpdate->harga_rata ?? '';
                        $dataToSave['posttime_harga'] = $pembagianToUpdate->posttime_harga ?? '';

                        $pembagianToUpdate->update($dataToSave);
                        $savedPembagian = $pembagianToUpdate;
                        Log::info('Gabah record updated', ['id' => $savedPembagian->id]);
                    } else {
                        Log::warning('No existing Gabah record found for edit', [
                            'id_timbang' => $terimaBg->id,
                            'no_penerimaan' => $noPenerimaan,
                            'sorting_number' => $sortingNumber,
                        ]);
                    }
                    
                    // First, clear sorting and no_penerimaan from all report timbang for this sorting number
                    ReportTimbangGabah::query()
                        ->where('id_bahan', (string) $terimaBg->id)
                        ->where('sorting', $sortingNumber)
                        ->update([
                            'sorting' => 0,
                            'no_penerimaan' => '',
                        ]);
                        
                    Log::info('Cleared old sorting from report timbang');
                } else {
                    // Create mode - add user_created
                    $dataToSave['user_created'] = $userName;
                    $dataToSave['user_approve'] = '';
                    $dataToSave['harga_rata'] = '';
                    $dataToSave['posttime_harga'] = '';
                    $savedPembagian = Gabah::create($dataToSave);
                    Log::info('Gabah record created', ['id' => $savedPembagian->id]);
                }

                // Update selected report timbang rows with sorting number
                $sortingNumberToUse = $isEdit ? $validated['editing_sorting_number'] : $this->nextAvailableSortingNumber($terimaBg->id);
                $noPenerimaanToUse = $isEdit ? $existingNoPenerimaan : $noPenerimaan;

                if ($isEdit) {
                    $clearQuery = ReportTimbangGabah::query()
                        ->where('id_bahan', (string) $terimaBg->id);

                    if (filled($existingNoPenerimaan)) {
                        $clearQuery->where('no_penerimaan', $existingNoPenerimaan);
                    } else {
                        $clearQuery->where('sorting', $sortingNumberToUse);
                    }

                    $clearQuery->update([
                        'sorting' => 0,
                        'no_penerimaan' => '',
                        'ket_bagian' => '',
                        'user_updated' => $userName,
                        'updated_at' => $updatedAt,
                    ]);
                }

                if (!empty($validated['selected_rows'])) {
                    Log::info('Updating selected rows', ['rows' => $validated['selected_rows'], 'sorting_number' => $sortingNumberToUse]);

                    ReportTimbangGabah::query()
                        ->whereIn('id', $validated['selected_rows'])
                        ->update([
                            'sorting' => $sortingNumberToUse,
                            'no_penerimaan' => $noPenerimaanToUse,
                            'ket_bagian' => $validated['keterangan_penerimaan'] ?? '',
                            'user_updated' => $userName,
                            'updated_at' => $updatedAt,
                            'last_action' => $reportLastAction,
                        ]);
                        
                    Log::info('Selected rows updated');

                    if ($isEdit) {
                        ReportTimbangGabah::query()
                            ->where('id_bahan', (string) $terimaBg->id)
                            ->where(function ($query) use ($existingNoPenerimaan, $sortingNumberToUse) {
                                if (filled($existingNoPenerimaan)) {
                                    $query->where('no_penerimaan', $existingNoPenerimaan);
                                } else {
                                    $query->where('sorting', $sortingNumberToUse);
                                }
                            })
                            ->whereNotIn('id', $selectedRowIds)
                            ->update([
                                'sorting' => 0,
                                'no_penerimaan' => '',
                                'ket_bagian' => '',
                                'user_updated' => $userName,
                                'updated_at' => $updatedAt,
                            ]);
                    }
                }

                ReportTimbangGabah::query()
                    ->where('id_bahan', (string) $terimaBg->id)
                    ->where('sorting', 0)
                    ->where(function ($query) {
                        $query->whereNull('no_penerimaan')
                            ->orWhere('no_penerimaan', '');
                    })
                    ->whereNotNull('ket_bagian')
                    ->where('ket_bagian', '!=', '')
                    ->update([
                        'ket_bagian' => '',
                        'user_updated' => $userName,
                        'updated_at' => $updatedAt,
                    ]);
            });
            
            Log::info('Transaction completed successfully');

            $nextNoPenerimaan = $this->nextAvailableNoPenerimaan(
                $terimaBg->tgl_terima ? Carbon::parse($terimaBg->tgl_terima) : null
            );

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isEdit ? 'Data pembagian gabah berhasil diperbarui.' : 'Data pembagian gabah berhasil disimpan.',
                    'data' => [
                        'pembagian' => $savedPembagian,
                        'no_penerimaan' => $noPenerimaan,
                        'next_no_penerimaan' => $nextNoPenerimaan,
                        'next_sorting' => $this->nextAvailableSortingNumber($terimaBg->id),
                    ],
                ]);
            }

            return redirect()
                ->route('pembagian_gabah.create', ['terima_bb_id' => $terimaBg->id])
                ->with('success', $isEdit ? 'Data pembagian gabah berhasil diperbarui.' : 'Data pembagian gabah berhasil disimpan.');
                
        } catch (\Exception $e) {
            Log::error('Error in Pembagian Gabah store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getRincian($terimaBgId)
    {
        $terimaBg = TerimaBg::query()->findOrFail($terimaBgId);
        
        $rows = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->orderByRaw('CAST(timbang_ke AS UNSIGNED) ASC')
            ->get();

        $groupedData = [];
        $unsortedData = [];

        foreach ($rows as $row) {
            if (empty($row->sorting)) {
                $unsortedData[] = $row;
            } else {
                $groupedData[$row->sorting][] = $row;
            }
        }

        ksort($groupedData);

        return response()->json([
            'success' => true,
            'data' => [
                'grouped' => $groupedData,
                'unsorted' => $unsortedData,
            ],
        ]);
    }
    
    public function getPembagianDetail($terimaBgId, $sortingNumber)
    {
        $terimaBg = TerimaBg::query()->findOrFail($terimaBgId);
        
        // Get all pembagian for this terima_bg
        $pembagianList = Gabah::query()
            ->where('id_timbang', $terimaBg->id)
            ->where('status', '!=', 'Cancel')
            ->orderBy('posttime', 'asc')
            ->get();
            
        // Get pembagian at the specified index (sorting number - 1)
        if ($sortingNumber < 1 || $sortingNumber > $pembagianList->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembagian tidak ditemukan.',
            ], 404);
        }
        
        $pembagian = $pembagianList[$sortingNumber - 1];
        
        // Get selected report timbang rows for this pembagian
        $selectedRowsQuery = ReportTimbangGabah::query()
            ->where('id_bahan', (string) $terimaBg->id)
            ->where('sorting', $sortingNumber);

        $selectedRows = $selectedRowsQuery->pluck('id')->toArray();
        $reportNoPenerimaan = $selectedRowsQuery->whereNotNull('no_penerimaan')->where('no_penerimaan', '!=', '')->value('no_penerimaan');

        if (!empty($reportNoPenerimaan)) {
            $pembagian->no_penerimaan = $reportNoPenerimaan;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'pembagian' => $pembagian,
                'selected_rows' => $selectedRows,
            ],
        ]);
    }

    public function check($id)
    {
        $record = TerimaBg::query()->findOrFail($id);
        $currentUser = auth()->user()?->username ?: auth()->user()?->name;

        // Verify that all pembagian are filled
        $hasTimbangan = ReportTimbangGabah::where('id_bahan', (string) $record->id)->exists();
        $hasUnsorted = ReportTimbangGabah::where('id_bahan', (string) $record->id)->where('sorting', 0)->exists();

        if (!$hasTimbangan || $hasUnsorted) {
            return redirect()
                ->route('pembagian_gabah.create', ['terima_bb_id' => $record->id])
                ->with('error', 'Pembagian gabah belum selesai diisi.');
        }

        DB::transaction(function () use ($record, $currentUser) {
            $record->update([
                'status' => 'Checked',
                'last_action' => 'Check Pembagian Gabah',
                'user_updated' => $currentUser,
                'updated_at' => now(),
            ]);

            Gabah::where('id_timbang', $record->id)
                ->where('status', '!=', 'Cancel')
                ->update([
                    'status' => 'Finish',
                    'user' => $currentUser,
                ]);
        });

        return redirect()
            ->route('penerimaan_gabah.index')
            ->with('success', 'Data pembagian gabah berhasil di-check.');
    }
}
