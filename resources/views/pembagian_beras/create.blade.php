<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-base text-gray-800 leading-tight truncate">
                {{ __('Pembagian Beras') }}
            </h2>
        </div>
    </x-slot>

    @php
        $tanggalTerima = filled($terimaBb->tgl_terima ?? null)
            ? \Illuminate\Support\Carbon::parse($terimaBb->tgl_terima)
            : null;
        $rows = $rows ?? collect();
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
        $totalTonase = $rows->sum(fn ($row) => (float) $row->tonase);
        $totalKarung = $rows->sum(fn ($row) => (float) $row->jumlah_karung);
        $warnaOptions = $warnaOptions ?? collect();
        $aromaOptions = $aromaOptions ?? collect();
        $isApproved = in_array(strtolower(trim($terimaBb->status ?? '')), ['checked', 'approved', 'approve'], true);
    @endphp

    <style>
        /* Spinner animation */
        @keyframes spin { to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 0.8s linear infinite; }

        /* Hide number spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }

        /* Table + form shared */
        .pb-section-title {
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6b7280;
            margin: 0 0 0.75rem;
        }

        .pb-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .pb-card-header {
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .pb-card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pb-card-header h3 {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
            line-height: 1.3;
        }

        /* Info fields */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-grid .col-span-2 { grid-column: span 1; }
        }

        .info-item label {
            display: block;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-item .info-val {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
        }

        /* Form inputs */
        .pb-field label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
        }

        .pb-field input[type="text"],
        .pb-field input[type="number"],
        .pb-field select,
        .pb-field textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.625rem 0.75rem;
            font-size: 0.9375rem;
            color: #111827;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            -webkit-appearance: none;
            appearance: none;
            outline: none;
        }

        .pb-field input:focus,
        .pb-field select:focus,
        .pb-field textarea:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
        }

        .pb-field input:disabled,
        .pb-field select:disabled {
            background: #f9fafb;
            color: #6b7280;
        }

        .pb-field textarea { resize: vertical; min-height: 72px; }

        /* QC cards */
        .qc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.625rem;
            margin-bottom: 0.75rem;
        }

        .qc-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem;
            text-align: center;
        }

        .qc-card .qc-label {
            font-size: 0.6875rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .qc-card .qc-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #111827;
        }

        .qc-card .qc-score {
            font-size: 0.6875rem;
            color: #10b981;
            font-weight: 600;
            margin-top: 0.125rem;
        }

        .qc-total {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qc-total .qc-total-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #065f46;
        }

        .qc-total .qc-total-sub {
            font-size: 0.75rem;
            color: #6ee7b7;
            margin-top: 0.125rem;
        }

        #qc-final-nilai {
            font-size: 1.75rem;
            font-weight: 700;
            color: #059669;
        }

        /* Total bar */
        .total-bar {
            padding: 0.75rem 1rem;
            background: #ecfdf5;
            border-top: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .total-bar .total-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #065f46;
        }

        .total-bar .total-vals {
            display: flex;
            gap: 1rem;
            font-size: 0.8125rem;
            color: #047857;
        }

        .total-bar .total-vals strong { font-weight: 700; color: #065f46; }

        /* Total qty selected */
        .qty-box {
            background: #ecfdf5;
            border: 1.5px solid #6ee7b7;
            border-radius: 10px;
            padding: 0.875rem 1rem;
        }

        .qty-box-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        #total_qty_terpilih_display {
            font-size: 1.5rem;
            font-weight: 700;
            color: #059669;
            border: none;
            background: transparent;
            padding: 0;
            width: 100%;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.6875rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            border: none;
            outline: none;
        }

        .btn:active { transform: scale(0.97); }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        .btn-primary {
            background: #059669;
            color: #fff;
        }

        .btn-primary:hover:not(:disabled) { background: #047857; }

        .btn-info {
            background: #2563eb;
            color: #fff;
        }

        .btn-info:hover:not(:disabled) { background: #1d4ed8; }

        .btn-ghost {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-ghost:hover:not(:disabled) { background: #e5e7eb; }

        .btn-full { width: 100%; justify-content: center; }

        /* Approved banner */
        .approved-banner {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.875rem 1rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            color: #065f46;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* Section divider */
        .form-section {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .form-section:last-child { border-bottom: none; }

        /* Notification */
        #notification-area {
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .notif {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .notif-success { background: #ecfdf5; color: #065f46; border-bottom: 1px solid #a7f3d0; }
        .notif-error { background: #fef2f2; color: #991b1b; border-bottom: 1px solid #fecaca; }

        /* Row selected highlight */
        .row-selected td { background: #f0fdf4 !important; }

        /* Mobile layout */
        @media (max-width: 1023px) {
            .page-layout { flex-direction: column; }
        }

        @media (min-width: 1024px) {
            .page-layout { flex-direction: row; align-items: flex-start; }
            .table-col { flex: 1.1; position: sticky; top: 1rem; max-height: calc(100vh - 5rem); overflow: hidden; display: flex; flex-direction: column; }
            .table-col .table-scroll { flex: 1; overflow-y: auto; overflow-x: auto; }
            .form-col { flex: 1; overflow-y: auto; max-height: calc(100vh - 5rem); }
        }

        /* Table styles */
        .data-table { width: 100%; font-size: 0.8125rem; border-collapse: collapse; }
        .data-table th {
            padding: 0.5rem 0.625rem;
            text-align: center;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .data-table td {
            padding: 0.5rem 0.625rem;
            text-align: center;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            white-space: nowrap;
        }

        .data-table tbody tr:hover td { background: #f9fafb; }

        .sorting-header {
            background: #f0fdf4;
            cursor: pointer;
        }

        .sorting-header:hover { background: #dcfce7; }

        .sorting-header td {
            padding: 0.625rem 0.75rem;
            font-weight: 600;
            color: #065f46;
            font-size: 0.8125rem;
            border-bottom: 1px solid #a7f3d0;
        }

        .badge-done {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            background: #ecfdf5;
            color: #065f46;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .cb-row {
            width: 18px;
            height: 18px;
            accent-color: #059669;
            cursor: pointer;
        }

        /* Inline form grid */
        .form-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        @media (max-width: 480px) {
            .form-2col { grid-template-columns: 1fr; }
            .form-2col .span2 { grid-column: span 1; }
        }

        /* Scrollable table wrapper on mobile */
        .table-scroll-x {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>

    {{-- Notification area --}}
    <div id="notification-area"></div>

    <div class="page-layout flex gap-4 p-3 sm:p-4">

        {{-- ============ TABLE COLUMN ============ --}}
        <div class="table-col">
            <div class="pb-card">
                <div class="pb-card-header">
                    <div class="pb-card-header-icon" style="background:#ecfdf5;">
                        <svg width="18" height="18" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </div>
                    <h3>Data Rincian Timbangan</h3>
                </div>

                <div class="table-scroll table-scroll-x" id="rincian-tabel-container">
                    @include('pembagian_beras._rincian_tabel', [
                        'groupedData' => $groupedData,
                        'unsortedData' => $unsortedData,
                    ])
                </div>

                <div class="total-bar">
                    <span class="total-label">Total Keseluruhan</span>
                    <div class="total-vals">
                        <span><strong id="total-karung-display">{{ number_format($totalKarung, 0, ',', '.') }}</strong> Karung</span>
                        <span><strong id="total-tonase-display">{{ number_format($totalTonase, 0, ',', '.') }}</strong> Kg</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ FORM COLUMN ============ --}}
        <div class="form-col">
            <div class="pb-card">
                <div class="pb-card-header">
                    <div class="pb-card-header-icon" style="background:#f3f4f6;">
                        <svg width="18" height="18" fill="none" stroke="#374151" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3>Form Pembagian Beras</h3>
                </div>

                <form id="pembagian-form">
                    @csrf
                    <input type="hidden" name="terima_bb_id" value="{{ $terimaBb->id }}" />
                    <input type="hidden" name="editing_sorting_number" id="editing_sorting_number" value="" />
                    <input type="hidden" name="editing_no_penerimaan" id="editing_no_penerimaan" value="" />

                    <fieldset {{ $isApproved ? 'disabled' : '' }} style="border:none;padding:0;margin:0;">

                    {{-- INFO DASAR --}}
                    <div class="form-section">
                        <p class="pb-section-title">Informasi Dasar</p>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>No Penerimaan</label>
                                <div class="info-val" id="no-penerimaan-display">{{ $noPenerimaan ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Tanggal Terima</label>
                                <div class="info-val">{{ $tanggalTerima?->format('d/m/Y') ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>No Polisi</label>
                                <div class="info-val">{{ $terimaBb->nopol ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Nama Supplier</label>
                                <div class="info-val">{{ $terimaBb->nama_supplier ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Jenis Bahan</label>
                                <div class="info-val">{{ $terimaBb->jenis_bahan ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Kemasan Pakai</label>
                                <div class="info-val">{{ $terimaBb->kemasan_pakai ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <label>Tempat Simpan</label>
                                <div class="info-val">{{ $terimaBb->tempat_simpan ?? '-' }}</div>
                            </div>
                            <div class="info-item col-span-2">
                                <label>Penggunaan Palet</label>
                                <div class="info-val">{{ $terimaBb->penggunaan_palet ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- KUALITAS BERAS --}}
                    <div class="form-section">
                        <p class="pb-section-title">Kualitas Beras</p>
                        <div class="form-2col">
                            <div class="pb-field">
                                <label>Warna</label>
                                <select name="warna" id="warna" required>
                                    <option value="">Pilih Warna</option>
                                    @foreach ($warnaOptions as $option)
                                        <option value="{{ $option->id }}" data-nilai="{{ $option->nilai }}" {{ old('warna') == $option->id ? 'selected' : '' }}>{{ $option->kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pb-field">
                                <label>Aroma</label>
                                <select name="aroma_beras" id="aroma_beras" required>
                                    <option value="">Pilih Aroma</option>
                                    @foreach ($aromaOptions as $option)
                                        <option value="{{ $option->id }}" data-nilai="{{ $option->nilai }}" {{ old('aroma_beras') == $option->id ? 'selected' : '' }}>{{ $option->kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pb-field">
                                <label>Kondisi Umum</label>
                                <select name="kondisi_umum" id="kondisi_umum" required>
                                    <option value="">Pilih Kondisi</option>
                                    <option value="Kering" {{ old('kondisi_umum') == 'Kering' ? 'selected' : '' }}>Kering</option>
                                    <option value="Basah" {{ old('kondisi_umum') == 'Basah' ? 'selected' : '' }}>Basah</option>
                                    <option value="Kering-Basah" {{ old('kondisi_umum') == 'Kering-Basah' ? 'selected' : '' }}>Kering-Basah</option>
                                </select>
                            </div>
                            <div class="pb-field">
                                <label>Kondisi Kendaraan</label>
                                <select name="kondisi_kendaraan" id="kondisi_kendaraan" required>
                                    <option value="">Pilih Kondisi</option>
                                    <option value="Baik" {{ old('kondisi_kendaraan') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup Baik" {{ old('kondisi_kendaraan') == 'Cukup Baik' ? 'selected' : '' }}>Cukup Baik</option>
                                    <option value="Kurang Baik" {{ old('kondisi_kendaraan') == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik</option>
                                </select>
                            </div>
                            <div class="pb-field">
                                <label>Keputusan Penerimaan</label>
                                <select name="keputusan_penerimaan" id="keputusan_penerimaan" required>
                                    <option value="">Pilih Keputusan</option>
                                    <option value="Diterima" {{ old('keputusan_penerimaan') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Ditolak" {{ old('keputusan_penerimaan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="pb-field">
                                <label>Sorter Beras</label>
                                <select name="sorter_beras" id="sorter_beras" required>
                                    <option value="">Pilih</option>
                                    <option value="Ya" {{ old('sorter_beras') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('sorter_beras') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="form-section">
                        <p class="pb-section-title">Catatan & Keterangan</p>
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            <div class="pb-field">
                                <label>Indikasi Kimia <span style="color:#ef4444;">*</span></label>
                                <input name="indikasi_kimia" id="indikasi_kimia" type="text" value="{{ old('indikasi_kimia') }}" required placeholder="Tuliskan indikasi kimia" />
                            </div>
                            <div class="pb-field">
                                <label>Catatan Cek</label>
                                <input name="catatan_cek" id="catatan_cek" type="text" value="{{ old('catatan_cek') }}" placeholder="Tuliskan catatan cek" />
                            </div>
                            <div class="pb-field">
                                <label>Keterangan Penerimaan <span style="color:#ef4444;">*</span></label>
                                <textarea name="keterangan_penerimaan" id="keterangan_penerimaan" rows="3" required>{{ old('keterangan_penerimaan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL TRANSAKSI --}}
                    <div class="form-section">
                        <p class="pb-section-title">Detail Transaksi</p>
                        <div class="form-2col" style="margin-bottom:0.75rem;">
                            <div class="pb-field">
                                <label>Pembagian Ke</label>
                                <input name="pembagian_ke" id="pembagian_ke" type="text" value="{{ old('pembagian_ke', $nextSorting ?? '') }}" required placeholder="No. pembagian" />
                            </div>
                            <div class="pb-field">
                                <label>Harga</label>
                                <input name="harga" id="harga" type="number" min="0" step="0.01" value="{{ old('harga') }}" placeholder="0" />
                            </div>
                        </div>
                        <div class="qty-box">
                            <div class="qty-box-label">Total Qty Terpilih</div>
                            <input type="text" id="total_qty_terpilih_display" disabled value="0 Kg" />
                            <input name="total_qty_terpilih" type="hidden" id="total_qty_terpilih_hidden" value="0">
                        </div>
                    </div>

                    {{-- HASIL QC --}}
                    <div class="form-section">
                        <p class="pb-section-title">Hasil Penilaian QC</p>
                        <div class="qc-grid">
                            <div class="qc-card">
                                <div class="qc-label">Rata Kadar Air</div>
                                <div class="qc-value" id="qc-air-val">-</div>
                                <div class="qc-score" id="qc-air-score">Skor: -</div>
                            </div>
                            <div class="qc-card">
                                <div class="qc-label">Rata Broken</div>
                                <div class="qc-value" id="qc-broken-val">-</div>
                                <div class="qc-score" id="qc-broken-score">Skor: -</div>
                            </div>
                            <div class="qc-card">
                                <div class="qc-label">Nilai Warna</div>
                                <div class="qc-value" id="qc-warna-val">-</div>
                                <div class="qc-score" id="qc-warna-score">Skor: -</div>
                            </div>
                            <div class="qc-card">
                                <div class="qc-label">Nilai Aroma</div>
                                <div class="qc-value" id="qc-aroma-val">-</div>
                                <div class="qc-score" id="qc-aroma-score">Skor: -</div>
                            </div>
                        </div>
                        <div class="qc-total">
                            <div>
                                <div class="qc-total-label">Nilai Kualitas Akhir</div>
                                <div class="qc-total-sub">Rata-rata 4 kriteria</div>
                            </div>
                            <span id="qc-final-nilai">-</span>
                        </div>
                    </div>

                    </fieldset>

                    {{-- ACTION BUTTONS --}}
                    <div class="form-section" style="display:flex;flex-wrap:wrap;gap:0.625rem;">
                        @if ($isApproved)
                            <div class="approved-banner" style="width:100%;">
                                <svg width="18" height="18" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @if (strtolower(trim($terimaBb->status ?? '')) === 'checked')
                                    Sudah Di-check — Menunggu Approval
                                @else
                                    Pembagian Beras Sudah Disetujui
                                @endif
                            </div>
                        @else
                            <button type="submit" id="submit-btn" class="btn btn-primary" style="flex:1;justify-content:center;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Simpan
                            </button>

                            @php
                                $hasTimbanganCheck = \App\Models\ReportTimbangBeras::where('id_bahan', (string) $terimaBb->id)->exists();
                                $hasUnsortedCheck = \App\Models\ReportTimbangBeras::where('id_bahan', (string) $terimaBb->id)->where('sorting', 0)->exists();
                                $allPembagianFilledCheck = $hasTimbanganCheck && !$hasUnsortedCheck;
                            @endphp

                            @if ($allPembagianFilledCheck && strtolower(trim($terimaBb->status ?? '')) === 'finish')
                                <button type="submit" form="check-form" id="check-btn" class="btn btn-info" style="flex:1;justify-content:center;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Checked
                                </button>
                            @endif
                        @endif
                    </div>
                </form>

                @if (!$isApproved && strtolower(trim($terimaBb->status ?? '')) === 'finish')
                    <form id="check-form" method="POST" action="{{ route('pembagian_beras.check', $terimaBb->id) }}" class="hidden">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        const terimaBbId = {{ $terimaBb->id }};
        const initialNoPenerimaan = '{{ $noPenerimaan ?? '-' }}';
        const initialNextSorting = '{{ $nextSorting ?? 1 }}';
        const isApproved = {{ $isApproved ? 'true' : 'false' }};
        let allRowCheckboxes = [];
        let activeSelectedRowIds = new Set();
        let activeEditingSortingNumber = null;
        let availableNoPenerimaan = initialNoPenerimaan;
        let availableNextSorting = initialNextSorting;

        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function showNotification(message, type = 'success') {
            const area = document.getElementById('notification-area');
            const cls = type === 'success' ? 'notif-success' : 'notif-error';
            const icon = type === 'success'
                ? '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                : '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
            area.innerHTML = `<div class="notif ${cls}">${icon} ${message}</div>`;
            setTimeout(() => { area.innerHTML = ''; }, 4000);
        }

        function initializeTableEvents() {
            allRowCheckboxes = document.querySelectorAll('.row-checkbox');

            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    allRowCheckboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = selectAll.checked;
                            const id = parseInt(cb.value);
                            selectAll.checked ? activeSelectedRowIds.add(id) : activeSelectedRowIds.delete(id);
                            cb.closest('tr').classList.toggle('row-selected', cb.checked);
                        }
                    });
                    calculateTotal();
                });
            }

            allRowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    if (this.checked) {
                        tr.classList.add('row-selected');
                        activeSelectedRowIds.add(parseInt(this.value));
                    } else {
                        tr.classList.remove('row-selected');
                        activeSelectedRowIds.delete(parseInt(this.value));
                    }
                    const sa = document.getElementById('selectAll');
                    if (sa) {
                        sa.checked = Array.from(allRowCheckboxes).filter(c => !c.disabled).every(c => c.checked);
                    }
                    calculateTotal();
                });
            });

            document.querySelectorAll('.sorting-header').forEach(header => {
                header.addEventListener('click', function () {
                    if (isApproved) return;
                    const sn = this.getAttribute('data-sorting-number');
                    if (sn) loadPembagianDetail(sn);
                });
            });

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            allRowCheckboxes.forEach(cb => {
                if (cb.checked && !cb.disabled) {
                    total += parseFloat(cb.getAttribute('data-tonase')) || 0;
                }
            });
            document.getElementById('total_qty_terpilih_display').value = formatNumber(total) + ' Kg';
            document.getElementById('total_qty_terpilih_hidden').value = total;
            updateQCSummary();
        }

        function updateQCSummary() {
            let totalAir = 0, totalBroken = 0, countAir = 0, countBroken = 0;
            allRowCheckboxes.forEach(cb => {
                if (cb.checked && !cb.disabled) {
                    const air = parseFloat(cb.getAttribute('data-kadar-air'));
                    const broken = parseFloat(cb.getAttribute('data-kadar-broken'));
                    if (!isNaN(air) && air > 0) { totalAir += air; countAir++; }
                    if (!isNaN(broken) && broken > 0) { totalBroken += broken; countBroken++; }
                }
            });

            const avgAir = countAir > 0 ? totalAir / countAir : 0;
            const avgBroken = countBroken > 0 ? totalBroken / countBroken : 0;

            let airScore = 0;
            if (countAir > 0) {
                if (avgAir <= 13.0) airScore = 4;
                else if (avgAir <= 14.0) airScore = 3;
                else if (avgAir <= 14.5) airScore = 2;
                else airScore = 1;
            }

            let brokenScore = 0;
            if (countBroken > 0) {
                if (avgBroken <= 10.0) brokenScore = 4;
                else if (avgBroken <= 20.0) brokenScore = 3;
                else if (avgBroken <= 30.0) brokenScore = 2;
                else brokenScore = 1;
            }

            const warnaSelect = document.getElementById('warna');
            const warnaOpt = warnaSelect ? warnaSelect.options[warnaSelect.selectedIndex] : null;
            const warnaBase = (warnaOpt && warnaOpt.value) ? parseFloat(warnaOpt.getAttribute('data-nilai')) || 0 : 0;
            const catatan = (document.getElementById('catatan_cek')?.value || '').trim();
            const hasDefect = catatan !== '' && catatan !== '-';
            const warnaScore = warnaBase > 0 ? (hasDefect ? Math.max(1, warnaBase - 1) : warnaBase) : 0;

            const aromaSelect = document.getElementById('aroma_beras');
            const aromaOpt = aromaSelect ? aromaSelect.options[aromaSelect.selectedIndex] : null;
            const aromaScore = (aromaOpt && aromaOpt.value) ? parseFloat(aromaOpt.getAttribute('data-nilai')) || 0 : 0;

            document.getElementById('qc-air-val').textContent = countAir > 0 ? avgAir.toFixed(2) + '%' : '-';
            document.getElementById('qc-air-score').textContent = countAir > 0 ? 'Skor: ' + airScore : 'Skor: -';
            document.getElementById('qc-broken-val').textContent = countBroken > 0 ? avgBroken.toFixed(2) + '%' : '-';
            document.getElementById('qc-broken-score').textContent = countBroken > 0 ? 'Skor: ' + brokenScore : 'Skor: -';
            document.getElementById('qc-warna-val').textContent = warnaBase > 0 ? (warnaOpt.text + (hasDefect ? ' (−1)' : '')) : '-';
            document.getElementById('qc-warna-score').textContent = warnaBase > 0 ? 'Skor: ' + warnaScore : 'Skor: -';
            document.getElementById('qc-aroma-val').textContent = aromaScore > 0 ? aromaOpt.text : '-';
            document.getElementById('qc-aroma-score').textContent = aromaScore > 0 ? 'Skor: ' + aromaScore : 'Skor: -';

            const finalEl = document.getElementById('qc-final-nilai');
            if (countAir > 0 && countBroken > 0 && warnaBase > 0 && aromaScore > 0) {
                finalEl.textContent = ((airScore + brokenScore + warnaScore + aromaScore) / 4).toFixed(2);
            } else {
                finalEl.textContent = '-';
            }
        }

        async function refreshTable() {
            try {
                const res = await fetch(`/pembagian-beras/${terimaBbId}/rincian`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (!data.success) return;

                let totalKarung = 0, totalTonase = 0;
                Object.keys(data.data.grouped).forEach(s => {
                    data.data.grouped[s].forEach(r => {
                        totalKarung += parseFloat(r.jumlah_karung) || 0;
                        totalTonase += parseFloat(r.tonase) || 0;
                    });
                });
                if (data.data.unsorted) {
                    data.data.unsorted.forEach(r => {
                        totalKarung += parseFloat(r.jumlah_karung) || 0;
                        totalTonase += parseFloat(r.tonase) || 0;
                    });
                }

                document.getElementById('total-karung-display').textContent = formatNumber(totalKarung);
                document.getElementById('total-tonase-display').textContent = formatNumber(totalTonase);

                const container = document.getElementById('rincian-tabel-container');
                let html = '';

                Object.keys(data.data.grouped).sort((a, b) => a - b).forEach(sorting => {
                    const rows = data.data.grouped[sorting];
                    const gt = rows.reduce((s, r) => s + (parseFloat(r.tonase) || 0), 0);
                    const gk = rows.reduce((s, r) => s + (parseFloat(r.jumlah_karung) || 0), 0);
                    const isEditing = !isApproved && activeEditingSortingNumber && String(activeEditingSortingNumber) === String(sorting);

                    html += `<table class="data-table"><thead>
                        <tr class="sorting-header" ${!isApproved ? `data-sorting-number="${sorting}"` : ''}>
                            <td colspan="8">
                                <span style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                                    <span style="display:flex;align-items:center;gap:6px;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002-2h2a2 2 0 002-2"/></svg>
                                        Sorting Ke-${sorting}
                                        ${!isApproved ? '<span style="font-size:11px;color:#6ee7b7;font-weight:500;">(klik untuk edit)</span>' : ''}
                                    </span>
                                    <span style="font-size:11px;font-weight:500;">${formatNumber(gk)} Karung &bull; ${formatNumber(gt)} Kg</span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>No</th><th>Timbang Ke</th><th>No Penerimaan</th><th>Jml Karung</th><th>Tonase</th><th>Kadar Air</th><th>Kadar Broken</th><th>Status</th>
                        </tr></thead><tbody>`;

                    rows.forEach((row, i) => {
                        const sel = activeSelectedRowIds.has(parseInt(row.id));
                        html += `<tr class="${sel ? 'row-selected' : ''}">
                            <td>${i+1}</td><td><strong>${row.timbang_ke}</strong></td>
                            <td style="font-size:11px;">${row.no_penerimaan || '-'}</td>
                            <td>${row.jumlah_karung || '-'}</td>
                            <td><strong>${row.tonase != null && !isNaN(row.tonase) ? formatNumber(parseFloat(row.tonase))+' Kg' : '-'}</strong></td>
                            <td>${row.kadar_air ? row.kadar_air+'%' : '-'}</td>
                            <td>${row.kadar_broken ? row.kadar_broken+'%' : '-'}</td>
                            <td>${isEditing
                                ? `<input type="checkbox" name="selected_rows[]" value="${row.id}" class="row-checkbox cb-row" data-tonase="${row.tonase}" data-kadar-air="${row.kadar_air||0}" data-kadar-broken="${row.kadar_broken||0}" ${sel?'checked':''}>`
                                : `<span class="badge-done">Diproses</span>`
                            }</td>
                        </tr>`;
                    });

                    html += `</tbody></table>`;
                });

                if (data.data.unsorted && data.data.unsorted.length > 0) {
                    html += `<table class="data-table"><thead><tr>
                        <th>No</th><th>Timbang Ke</th><th>No Penerimaan</th><th>Jml Karung</th><th>Tonase</th><th>Kadar Air</th><th>Kadar Broken</th>
                        <th><label style="display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer;">
                            <input type="checkbox" id="selectAll" class="cb-row" ${isApproved?'disabled':''}>
                            <span style="font-size:10px;font-weight:600;color:#6b7280;">Semua</span>
                        </label></th>
                    </tr></thead><tbody>`;

                    data.data.unsorted.forEach((row, i) => {
                        html += `<tr>
                            <td>${i+1}</td><td><strong>${row.timbang_ke}</strong></td>
                            <td style="font-size:11px;">${row.no_penerimaan||'-'}</td>
                            <td>${row.jumlah_karung||'-'}</td>
                            <td><strong>${row.tonase!=null&&!isNaN(row.tonase)?formatNumber(parseFloat(row.tonase))+' Kg':'-'}</strong></td>
                            <td>${row.kadar_air?row.kadar_air+'%':'-'}</td>
                            <td>${row.kadar_broken?row.kadar_broken+'%':'-'}</td>
                            <td><input type="checkbox" name="selected_rows[]" value="${row.id}" class="row-checkbox cb-row" data-tonase="${row.tonase}" data-kadar-air="${row.kadar_air||0}" data-kadar-broken="${row.kadar_broken||0}" ${isApproved?'disabled':''}></td>
                        </tr>`;
                    });

                    html += `</tbody></table>`;
                }

                container.innerHTML = html;
                initializeTableEvents();

            } catch (err) {
                console.error(err);
                showNotification('Gagal memuat ulang tabel', 'error');
            }
        }

        async function loadPembagianDetail(sortingNumber) {
            try {
                const res = await fetch(`/pembagian-beras/${terimaBbId}/detail/${sortingNumber}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.success) {
                    resetForm();
                    const p = data.data.pembagian;
                    const selRows = data.data.selected_rows || [];

                    activeEditingSortingNumber = sortingNumber;
                    activeSelectedRowIds = new Set(selRows.map(id => parseInt(id)));

                    const noPenEl = document.getElementById('no-penerimaan-display');
                    if (noPenEl) noPenEl.textContent = p.no_penerimaan || '-';

                    document.getElementById('editing_sorting_number').value = sortingNumber;
                    document.getElementById('editing_no_penerimaan').value = p.no_penerimaan || '';
                    document.getElementById('pembagian_ke').value = sortingNumber;
                    document.getElementById('harga').value = p.harga || '';
                    document.getElementById('kondisi_umum').value = p.kondisi || '';
                    document.getElementById('kondisi_kendaraan').value = p.kendaraan || '';
                    document.getElementById('keputusan_penerimaan').value = p.keputusan || '';
                    document.getElementById('sorter_beras').value = p.sorter == 1 ? 'Ya' : 'Tidak';
                    document.getElementById('warna').value = p.warna || '';
                    document.getElementById('aroma_beras').value = p.aroma || '';
                    document.getElementById('indikasi_kimia').value = p.indikasi_kimia || '';
                    document.getElementById('catatan_cek').value = p.catatan_cek || '';
                    document.getElementById('keterangan_penerimaan').value = p.keterangan || '';

                    await refreshTable();

                    allRowCheckboxes.forEach(cb => {
                        if (activeSelectedRowIds.has(parseInt(cb.value))) {
                            cb.checked = true;
                            cb.closest('tr').classList.add('row-selected');
                        }
                    });

                    calculateTotal();
                    showNotification(`Data Sorting Ke-${sortingNumber} berhasil dimuat`, 'success');

                    // Scroll ke form di mobile
                    const formCol = document.querySelector('.form-col');
                    if (formCol && window.innerWidth < 1024) {
                        formCol.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                } else {
                    showNotification(data.message || 'Gagal memuat data', 'error');
                }
            } catch (err) {
                console.error(err);
                showNotification('Terjadi kesalahan saat memuat data', 'error');
            }
        }

        function resetForm() {
            document.getElementById('pembagian-form').reset();
            document.getElementById('editing_sorting_number').value = '';
            document.getElementById('editing_no_penerimaan').value = '';
            activeEditingSortingNumber = null;
            activeSelectedRowIds = new Set();

            const noPenEl = document.getElementById('no-penerimaan-display');
            if (noPenEl) noPenEl.textContent = availableNoPenerimaan || initialNoPenerimaan;
            document.getElementById('pembagian_ke').value = availableNextSorting || initialNextSorting;
            document.getElementById('total_qty_terpilih_display').value = '0 Kg';
            document.getElementById('total_qty_terpilih_hidden').value = '0';

            allRowCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.closest('tr')?.classList.remove('row-selected');
            });

            const sa = document.getElementById('selectAll');
            if (sa) sa.checked = false;

            updateQCSummary();
        }

        document.addEventListener('DOMContentLoaded', function () {
            initializeTableEvents();

            document.getElementById('warna')?.addEventListener('change', updateQCSummary);
            document.getElementById('aroma_beras')?.addEventListener('change', updateQCSummary);
            document.getElementById('catatan_cek')?.addEventListener('input', updateQCSummary);

            const form = document.getElementById('pembagian-form');
            const submitBtn = document.getElementById('submit-btn');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (!submitBtn) return;

                submitBtn.disabled = true;
                const orig = submitBtn.innerHTML;
                submitBtn.innerHTML = `<svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.75"/></svg> Menyimpan...`;

                try {
                    const formData = new FormData(form);
                    allRowCheckboxes.forEach(cb => {
                        if (cb.checked && !cb.disabled) formData.append('selected_rows[]', cb.value);
                    });

                    const res = await fetch('{{ route('pembagian_beras.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (data.success) {
                        showNotification(data.message, 'success');
                        if (data.data?.next_sorting) availableNextSorting = data.data.next_sorting;
                        if (data.data?.next_no_penerimaan) availableNoPenerimaan = data.data.next_no_penerimaan;

                        const noPenEl = document.getElementById('no-penerimaan-display');
                        if (noPenEl) noPenEl.textContent = availableNoPenerimaan || initialNoPenerimaan;

                        resetForm();
                        await refreshTable();

                        if (document.querySelectorAll('input[name="selected_rows[]"]').length === 0) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = orig;
                }
            });
        });
    </script>
</x-app-layout>