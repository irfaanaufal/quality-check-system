<?php

namespace App\Http\Controllers;

use App\Models\Beras;
use App\Models\KriteriaBb;
use App\Models\ReportTimbangBeras;
use App\Models\Pcustomer;
use App\Models\TerimaBb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PembagianBerasController extends Controller
{
    private function buildNoPenerimaan(string $prefix, ?Carbon $tanggalTerima, int $sequenceNumber): string
    {
        $yearMonth = $tanggalTerima?->format('Ym') ?? now()->format('Ym');
        $sequenceSuffix = substr(str_pad((string) $sequenceNumber, 4, '0', STR_PAD_LEFT), -4);

        return sprintf('%s%s%s', $prefix, $yearMonth, $sequenceSuffix);
    }

    private function nextAvailableSortingNumber(int $terimaBbId): int
    {
        $usedSortings = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBbId)
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
        $terimaBbId = $request->integer('terima_bb_id');

        $terimaBb = $terimaBbId
            ? TerimaBb::query()->findOrFail($terimaBbId)
            : TerimaBb::query()->orderByDesc('tgl_terima')->orderByDesc('id')->firstOrFail();

        // Get all timbang data for this terima_bb
        $rows = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->orderBy('timbang_ke')
            ->get();

        // Get all existing pembagian for this terima_bb
        $existingPembagian = Beras::query()
            ->where('id_timbang', $terimaBb->id)
            ->orderBy('posttime', 'desc')
            ->get();

        $noPenerimaanPrefix = DB::table('config')->where('id', 7)->value('value') ?? 'PB';
        $rawSequence = DB::table('config')->where('id', 8)->value('value') ?? '0000';
        $sequenceNumber = ((int) preg_replace('/\D/', '', (string) $rawSequence)) + 1;
        $tanggalTerima = filled($terimaBb->tgl_terima) ? Carbon::parse($terimaBb->tgl_terima) : null;
        $noPenerimaan = $this->buildNoPenerimaan($noPenerimaanPrefix, $tanggalTerima, $sequenceNumber);

        $jenisBahan = strtolower(trim($terimaBb->jenis_bahan ?? ''));
        $varietas = 'Beras Putih';

        if ($jenisBahan === 'beras merah') {
            $varietas = 'Beras Merah';
        } elseif ($jenisBahan === 'beras ketan') {
            $varietas = 'Beras Ketan';
        } elseif (in_array($jenisBahan, ['w1', 'w2', 'w3', 'ir', 'jpn', 'w3/pw gempel', 'mapan-ir', 'ir wangi'], true)) {
            $varietas = 'Beras Putih';
        }

        $warnaOptions = KriteriaBb::query()
            ->where('varietas', $varietas)
            ->where('jenis', 'Warna')
            ->orderBy('nilai')
            ->get();

        $aromaOptions = KriteriaBb::query()
            ->where('varietas', $varietas)
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
        $nextSorting = $this->nextAvailableSortingNumber($terimaBb->id);

        return view('pembagian_beras.create', [
            'terimaBb' => $terimaBb,
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
        Log::info('Pembagian Beras store request received', $request->all());
        
        try {
            $validated = $request->validate([
                'terima_bb_id' => ['required', 'integer', 'exists:terima_bb,id'],
                'kondisi_umum' => ['nullable', 'string', 'max:255'],
                'kondisi_kendaraan' => ['nullable', 'string', 'max:255'],
                'keputusan_penerimaan' => ['nullable', 'string', 'max:255'],
                'sorter_beras' => ['nullable', 'in:Ya,Tidak'],
                'warna' => ['nullable', 'integer', 'exists:kriteria_bb,id'],
                'aroma_beras' => ['nullable', 'integer', 'exists:kriteria_bb,id'],
                'indikasi_kimia' => ['nullable', 'string', 'max:255'],
                'catatan_cek' => ['nullable', 'string', 'max:255'],
                'keterangan_penerimaan' => ['nullable', 'string', 'max:1000'],
                'pembagian_ke' => ['nullable', 'string', 'max:255'],
                'harga' => ['nullable', 'numeric', 'min:0'],
                'total_qty_terpilih' => ['nullable', 'numeric', 'min:0'],
                'selected_rows' => ['nullable', 'array'],
                'selected_rows.*' => ['integer', 'exists:report_timbang_beras,id'],
                'editing_sorting_number' => ['nullable', 'integer', 'min:1'],
                'editing_no_penerimaan' => ['nullable', 'string', 'max:255'],
            ]);
            
            Log::info('Validation passed', $validated);

            $terimaBb = TerimaBb::query()->findOrFail($validated['terima_bb_id']);
            
            Log::info('Terima Bb found', ['id' => $terimaBb->id]);

            $isEdit = !empty($validated['editing_sorting_number']);
            $noPenerimaan = '';
            
            if ($isEdit) {
                // Edit mode - use existing no penerimaan
                $noPenerimaan = $validated['editing_no_penerimaan'] ?? '';
                Log::info('Edit mode', ['no_penerimaan' => $noPenerimaan, 'sorting_number' => $validated['editing_sorting_number']]);
            } else {
                // Create mode - generate new no penerimaan
                $noPenerimaanPrefix = DB::table('config')->where('id', 7)->value('value') ?? 'PB';
                $rawSequence = DB::table('config')->where('id', 8)->value('value') ?? '0000';
                $sequenceNumber = ((int) preg_replace('/\D/', '', (string) $rawSequence)) + 1;
                $tanggalTerima = filled($terimaBb->tgl_terima) ? Carbon::parse($terimaBb->tgl_terima) : null;
                $noPenerimaan = $this->buildNoPenerimaan($noPenerimaanPrefix, $tanggalTerima, $sequenceNumber);
                
                Log::info('No Penerimaan generated', ['no_penerimaan' => $noPenerimaan, 'sequence' => $sequenceNumber]);
            }

            $jenisBahan = strtolower(trim($terimaBb->jenis_bahan ?? ''));
            $varietas = match (true) {
                $jenisBahan === 'beras merah' => 'Beras Merah',
                $jenisBahan === 'beras ketan' => 'Beras Ketan',
                in_array($jenisBahan, ['w1', 'w2', 'w3', 'ir', 'jpn', 'w3/pw gempel', 'mapan-ir', 'ir wangi'], true) => 'Beras Putih',
                default => 'Beras Putih',
            };

            $kodePrincipal = Pcustomer::query()
                ->where('kode_cust', $terimaBb->kode_supplier)
                ->value('kode_principal') ?? '';

            $warnaId = $validated['warna'] ?? null;
            $aromaId = $validated['aroma_beras'] ?? null;

            $userName = auth()->user()?->username ?? auth()->user()?->name ?? 'system';
            
            Log::info('User name', ['user_name' => $userName]);

            $savedPembagian = null;
            $existingNoPenerimaan = $validated['editing_no_penerimaan'] ?? $noPenerimaan;
            $nextNoPenerimaan = $noPenerimaan;

            if (empty($validated['selected_rows'])) {
                if ($isEdit) {
                    DB::transaction(function () use (
                        $terimaBb,
                        $existingNoPenerimaan,
                        $validated
                    ) {
                        $sortingNumber = $validated['editing_sorting_number'];
                        $pembagianToDeleteQuery = Beras::query()
                            ->where('id_timbang', $terimaBb->id);

                        if (filled($existingNoPenerimaan)) {
                            $pembagianToDeleteQuery->where('no_penerimaan', $existingNoPenerimaan);
                        } else {
                            $pembagianToDeleteQuery
                                ->orderBy('posttime', 'asc')
                                ->skip(max($sortingNumber - 1, 0));
                        }

                        $pembagianToDelete = $pembagianToDeleteQuery->first();

                        if ($pembagianToDelete) {
                            $pembagianToDelete->delete();
                        }

                        $clearQuery = ReportTimbangBeras::query()
                            ->where('id_bahan', (string) $terimaBb->id);

                        if (filled($existingNoPenerimaan)) {
                            $clearQuery->where('no_penerimaan', $existingNoPenerimaan);
                        } else {
                            $clearQuery->where('sorting', $sortingNumber);
                        }

                        $clearQuery->update([
                            'sorting' => 0,
                            'no_penerimaan' => '',
                            'ket_bagian' => '',
                        ]);
                    });

                    return response()->json([
                        'success' => true,
                        'message' => 'Data pembagian beras berhasil dihapus.',
                        'data' => [
                            'pembagian' => null,
                            'no_penerimaan' => $noPenerimaan,
                            'next_no_penerimaan' => $nextNoPenerimaan,
                            'next_sorting' => $this->nextAvailableSortingNumber($terimaBb->id),
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
                $terimaBb,
                $validated,
                $noPenerimaan,
                $existingNoPenerimaan,
                $isEdit,
                $kodePrincipal,
                $warnaId,
                $aromaId,
                $userName
            ) {
                Log::info('Starting database transaction', ['is_edit' => $isEdit]);
                
                if (!$isEdit) {
                    // Create mode - increment sequence
                    $rawSequence = DB::table('config')->where('id', 8)->value('value') ?? '0000';
                    $sequenceNumber = ((int) preg_replace('/\D/', '', (string) $rawSequence)) + 1;
                    $sequenceSuffix = substr(str_pad((string) $sequenceNumber, 4, '0', STR_PAD_LEFT), -4);
                    DB::table('config')->where('id', 8)->update(['value' => $sequenceSuffix]);
                    Log::info('Config updated for create');
                }

                $dataToSave = [
                    'no_penerimaan' => $noPenerimaan,
                    'tanggal' => $terimaBb->tgl_terima,
                    'supplier' => $terimaBb->nama_supplier,
                    'id_jenis' => (string) $terimaBb->id_jenis,
                    'jenis' => $terimaBb->jenis_bahan,
                    'no_sample' => '',
                    'kode_principal' => $kodePrincipal,
                    'berat' => $validated['total_qty_terpilih'] ?? 0,
                    'stok' => $validated['total_qty_terpilih'] ?? 0,
                    'kemasan' => $terimaBb->kemasan_pakai,
                    'kondisi' => $validated['kondisi_umum'] ?? '',
                    'kendaraan' => $validated['kondisi_kendaraan'] ?? '',
                    'keputusan' => $validated['keputusan_penerimaan'] ?? '',
                    'nopol' => $terimaBb->nopol,
                    'status' => 'Proses',
                    'user' => $userName,
                    'keterangan' => $validated['catatan_cek'] ?? '',
                    'sorter' => $validated['sorter_beras'] === 'Ya' ? 1 : 0,
                    'penggunaan_palet' => $terimaBb->penggunaan_palet,
                    'lokasi_penyimpanan' => $terimaBb->tempat_simpan,
                    'nilai' => '',
                    'harga' => $validated['harga'] ?? '',
                    'id_timbang' => $terimaBb->id,
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
                    'last_action' => $isEdit ? 'Update Pembagian Beras' : 'Pembagian Beras',
                ];
                
                if ($isEdit) {
                    // Edit mode - get pembagian list and update the one at sorting index
                    $pembagianList = Beras::query()
                        ->where('id_timbang', $terimaBb->id)
                        ->orderBy('posttime', 'asc')
                        ->get();
                        
                    $sortingNumber = $validated['editing_sorting_number'];
                    $pembagianToUpdate = Beras::query()
                        ->where('id_timbang', $terimaBb->id)
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
                        Log::info('Beras record updated', ['id' => $savedPembagian->idb_beras]);
                    } else {
                        Log::warning('No existing Beras record found for edit', [
                            'id_timbang' => $terimaBb->id,
                            'no_penerimaan' => $noPenerimaan,
                            'sorting_number' => $sortingNumber,
                        ]);
                    }
                    
                    // First, clear sorting and no_penerimaan from all report timbang for this sorting number
                    ReportTimbangBeras::query()
                        ->where('id_bahan', (string) $terimaBb->id)
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
                    $savedPembagian = Beras::create($dataToSave);
                    Log::info('Beras record created', ['id' => $savedPembagian->idb_beras]);
                }

                // Update selected report timbang rows with sorting number
                $sortingNumberToUse = $isEdit ? $validated['editing_sorting_number'] : $this->nextAvailableSortingNumber($terimaBb->id);
                $noPenerimaanToUse = $isEdit ? $existingNoPenerimaan : $noPenerimaan;

                if ($isEdit) {
                    $clearQuery = ReportTimbangBeras::query()
                        ->where('id_bahan', (string) $terimaBb->id);

                    if (filled($existingNoPenerimaan)) {
                        $clearQuery->where('no_penerimaan', $existingNoPenerimaan);
                    } else {
                        $clearQuery->where('sorting', $sortingNumberToUse);
                    }

                    $clearQuery->update([
                        'sorting' => 0,
                        'no_penerimaan' => '',
                        'ket_bagian' => '',
                    ]);
                }

                if (!empty($validated['selected_rows'])) {
                    Log::info('Updating selected rows', ['rows' => $validated['selected_rows'], 'sorting_number' => $sortingNumberToUse]);

                    ReportTimbangBeras::query()
                        ->whereIn('id', $validated['selected_rows'])
                        ->update([
                            'sorting' => $sortingNumberToUse,
                            'no_penerimaan' => $noPenerimaanToUse,
                            'ket_bagian' => $validated['keterangan_penerimaan'] ?? '',
                        ]);
                        
                    Log::info('Selected rows updated');
                }
            });
            
            Log::info('Transaction completed successfully');

            $currentSequence = (int) preg_replace(
                '/\D/',
                '',
                (string) DB::table('config')->where('id', 8)->value('value')
            );
            $nextNoPenerimaan = $this->buildNoPenerimaan(
                DB::table('config')->where('id', 7)->value('value') ?? 'PB',
                $terimaBb->tgl_terima ? Carbon::parse($terimaBb->tgl_terima) : null,
                $currentSequence + 1
            );

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isEdit ? 'Data pembagian beras berhasil diperbarui.' : 'Data pembagian beras berhasil disimpan.',
                    'data' => [
                        'pembagian' => $savedPembagian,
                        'no_penerimaan' => $noPenerimaan,
                        'next_no_penerimaan' => $nextNoPenerimaan,
                        'next_sorting' => $this->nextAvailableSortingNumber($terimaBb->id),
                    ],
                ]);
            }

            return redirect()
                ->route('pembagian_beras.create', ['terima_bb_id' => $terimaBb->id])
                ->with('success', $isEdit ? 'Data pembagian beras berhasil diperbarui.' : 'Data pembagian beras berhasil disimpan.');
                
        } catch (\Exception $e) {
            Log::error('Error in Pembagian Beras store', [
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

    public function getRincian($terimaBbId)
    {
        $terimaBb = TerimaBb::query()->findOrFail($terimaBbId);
        
        $rows = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
            ->orderBy('timbang_ke')
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
    
    public function getPembagianDetail($terimaBbId, $sortingNumber)
    {
        $terimaBb = TerimaBb::query()->findOrFail($terimaBbId);
        
        // Get all pembagian for this terima_bb
        $pembagianList = Beras::query()
            ->where('id_timbang', $terimaBb->id)
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
        $selectedRowsQuery = ReportTimbangBeras::query()
            ->where('id_bahan', (string) $terimaBb->id)
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
}
