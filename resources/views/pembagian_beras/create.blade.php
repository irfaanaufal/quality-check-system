<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                    {{ __('Pembagian Beras') }}
                </h2>
            </div>
        </div>
    </x-slot>

    @php
        $tanggalTerima = filled($terimaBb->tgl_terima ?? null)
            ? \Illuminate\Support\Carbon::parse($terimaBb->tgl_terima)
            : null;
        $rows = $rows ?? collect();
        
        // Group data by sorting
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
    @endphp

    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        .row-selected {
            background-color: #d1fae5 !important;
        }
        
        .sorting-group-header {
            background-color: #f0fdf4 !important;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .sorting-group-header:hover {
            background-color: #bbf7d0 !important;
        }
    </style>

    <div id="notification-container" class="px-4 sm:px-5 mb-4"></div>

    <div class="px-3 py-4 sm:px-4 lg:px-5">
        <div class="grid grid-cols-1 gap-6 items-start xl:grid-cols-2">
            <!-- Table Section -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl xl:sticky xl:top-4">
                <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-teal-700 px-5 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                        </svg>
                        Data Rincian Timbangan
                    </h3>
                </div>

                <div class="max-h-[calc(100vh-220px)] overflow-y-auto overflow-x-hidden" id="rincian-tabel-container">
                    @include('pembagian_beras._rincian_tabel', [
                        'groupedData' => $groupedData,
                        'unsortedData' => $unsortedData,
                    ])
                </div>

                <div class="p-4 bg-emerald-50 border-t border-emerald-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-emerald-800">Total Keseluruhan:</span>
                        <div class="flex gap-6">
                            <span class="text-emerald-700"><strong id="total-karung-display">{{ number_format($totalKarung, 0, ',', '.') }}</strong> Karung</span>
                            <span class="text-emerald-700"><strong id="total-tonase-display">{{ number_format($totalTonase, 0, ',', '.') }}</strong> Kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-700 to-slate-800 px-5 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Form Pembagian Beras
                    </h3>
                </div>

                <form id="pembagian-form" class="p-5">
                    @csrf
                    <input type="hidden" name="terima_bb_id" value="{{ $terimaBb->id }}" />
                    <input type="hidden" name="editing_sorting_number" id="editing_sorting_number" value="" />
                    <input type="hidden" name="editing_no_penerimaan" id="editing_no_penerimaan" value="" />

                    <!-- Informasi Dasar -->
                    <div class="mb-8">
                        <h4 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0a9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Dasar
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">No Penerimaan</label>
                                <input type="text" id="no-penerimaan-display" value="{{ $noPenerimaan ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Terima</label>
                                <input type="text" value="{{ $tanggalTerima?->format('Y-m-d') ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">No Polisi</label>
                                <input type="text" value="{{ $terimaBb->nopol ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Supplier</label>
                                <input type="text" value="{{ $terimaBb->nama_supplier ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis Bahan</label>
                                <input type="text" value="{{ $terimaBb->jenis_bahan ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kemasan Pakai</label>
                                <input type="text" value="{{ $terimaBb->kemasan_pakai ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Tempat Simpan</label>
                                <input type="text" value="{{ $terimaBb->tempat_simpan ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Penggunaan Palet</label>
                                <input type="text" value="{{ $terimaBb->penggunaan_palet ?? '-' }}" disabled class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-slate-700" />
                            </div>
                        </div>
                    </div>

                    <!-- Kualitas Beras -->
                    <div class="mb-8">
                        <h4 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 01.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            Kualitas Beras
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Warna</label>
                                <select name="warna" id="warna" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Warna</option>
                                    @foreach ($warnaOptions as $option)
                                        <option value="{{ $option->id }}" {{ old('warna') == $option->id ? 'selected' : '' }}>{{ $option->kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Aroma</label>
                                <select name="aroma_beras" id="aroma_beras" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Aroma</option>
                                    @foreach ($aromaOptions as $option)
                                        <option value="{{ $option->id }}" {{ old('aroma_beras') == $option->id ? 'selected' : '' }}>{{ $option->kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kondisi Umum</label>
                                <select name="kondisi_umum" id="kondisi_umum" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Kondisi Umum</option>
                                    <option value="Kering" {{ old('kondisi_umum') == 'Kering' ? 'selected' : '' }}>Kering</option>
                                    <option value="Basah" {{ old('kondisi_umum') == 'Basah' ? 'selected' : '' }}>Basah</option>
                                    <option value="Kering-Basah" {{ old('kondisi_umum') == 'Kering-Basah' ? 'selected' : '' }}>Kering-Basah</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kondisi Kendaraan</label>
                                <select name="kondisi_kendaraan" id="kondisi_kendaraan" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Kondisi Kendaraan</option>
                                    <option value="Baik" {{ old('kondisi_kendaraan') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup Baik" {{ old('kondisi_kendaraan') == 'Cukup Baik' ? 'selected' : '' }}>Cukup Baik</option>
                                    <option value="Kurang Baik" {{ old('kondisi_kendaraan') == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Keputusan Penerimaan</label>
                                <select name="keputusan_penerimaan" id="keputusan_penerimaan" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Keputusan</option>
                                    <option value="Diterima" {{ old('keputusan_penerimaan') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Ditolak" {{ old('keputusan_penerimaan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Sorter Beras</label>
                                <select name="sorter_beras" id="sorter_beras" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">
                                    <option value="">Pilih Sorter Beras</option>
                                    <option value="Ya" {{ old('sorter_beras') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('sorter_beras') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-8">
                        <h4 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Catatan & Keterangan
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Indikasi Kimia</label>
                                <input name="indikasi_kimia" id="indikasi_kimia" type="text" value="{{ old('indikasi_kimia') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors" placeholder="Tuliskan indikasi kimia" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Catatan Cek</label>
                                <input name="catatan_cek" id="catatan_cek" type="text" value="{{ old('catatan_cek') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors" placeholder="Tuliskan catatan cek" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan Penerimaan</label>
                                <textarea name="keterangan_penerimaan" id="keterangan_penerimaan" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors">{{ old('keterangan_penerimaan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Transaksi -->
                    <div class="mb-8">
                        <h4 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.347 2 3 2 3 .895 3 2-1.347 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0a9 9 0 0118 0z"></path>
                            </svg>
                            Detail Transaksi
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Pembagian Ke</label>
                                <input name="pembagian_ke" id="pembagian_ke" type="text" value="{{ old('pembagian_ke', $nextSorting ?? '') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors" placeholder="Nomor pembagian beras" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Harga</label>
                                <input name="harga" id="harga" type="number" min="0" step="0.01" value="{{ old('harga') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 transition-colors" placeholder="Harga" />
                            </div>
                            <div class="md:col-span-2">
                                <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-lg">
                                    <label class="mb-1.5 block text-sm font-semibold text-emerald-800 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Total Qty Terpilih
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <input type="text" id="total_qty_terpilih_display" disabled class="flex-1 rounded-lg border border-emerald-300 bg-white px-4 py-3 text-2xl font-bold text-emerald-700" value="0 Kg" />
                                    </div>
                                    <input name="total_qty_terpilih" type="hidden" id="total_qty_terpilih_hidden" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                        <button type="submit" id="submit-btn" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-emerald-700 hover:to-teal-800 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan
                        </button>
                        <button type="button" id="reset-btn" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-slate-500 to-slate-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:from-slate-600 hover:to-slate-700 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const terimaBbId = {{ $terimaBb->id }};
        const initialNoPenerimaan = '{{ $noPenerimaan ?? '-' }}';
        const initialNextSorting = '{{ $nextSorting ?? 1 }}';
        let allRowCheckboxes = [];
        let activeSelectedRowIds = new Set();
        let activeEditingSortingNumber = null;
        let availableNoPenerimaan = initialNoPenerimaan;
        let availableNextSorting = initialNextSorting;

        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification-container');
            const bgColor = type === 'success' ? 'bg-white border-green-200 text-green-700' : 'bg-white border-red-200 text-red-700';
            const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
            
            container.innerHTML = `
                <div class="p-4 rounded-lg border shadow-md ${bgColor}">
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${type === 'success' 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                            }
                        </svg>
                        ${message}
                    </div>
                </div>
            `;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        function initializeTableEvents() {
            allRowCheckboxes = document.querySelectorAll('.row-checkbox');
            
            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    allRowCheckboxes.forEach(checkbox => {
                        if (!checkbox.disabled) {
                            checkbox.checked = selectAllCheckbox.checked;
                            const rowId = parseInt(checkbox.value);
                            if (selectAllCheckbox.checked) {
                                activeSelectedRowIds.add(rowId);
                            } else {
                                activeSelectedRowIds.delete(rowId);
                            }
                        }
                    });
                    calculateTotal();
                });
            }
            
            // Individual checkboxes
            allRowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    if (this.checked) {
                        tr.classList.add('row-selected');
                        activeSelectedRowIds.add(parseInt(this.value));
                    } else {
                        tr.classList.remove('row-selected');
                        activeSelectedRowIds.delete(parseInt(this.value));
                    }
                    
                    // Update select all state
                    const selectAll = document.getElementById('selectAll');
                    if (selectAll) {
                        const allEnabledChecked = Array.from(allRowCheckboxes)
                            .filter(cb => !cb.disabled)
                            .every(cb => cb.checked);
                        selectAll.checked = allEnabledChecked;
                    }
                    
                    calculateTotal();
                });
            });
            
            // Sorting group headers click event
            document.querySelectorAll('.sorting-group-header').forEach(header => {
                header.addEventListener('click', function() {
                    const sortingNumber = this.getAttribute('data-sorting-number');
                    if (sortingNumber) {
                        loadPembagianDetail(sortingNumber);
                    }
                });
            });
            
            // Initial total calculation
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            const totalQtyDisplay = document.getElementById('total_qty_terpilih_display');
            const totalQtyHidden = document.getElementById('total_qty_terpilih_hidden');
            
            allRowCheckboxes.forEach(checkbox => {
                if (checkbox.checked && !checkbox.disabled) {
                    const tonase = parseFloat(checkbox.getAttribute('data-tonase')) || 0;
                    total += tonase;
                }
            });
            
            if (totalQtyDisplay) {
                totalQtyDisplay.value = formatNumber(total) + ' Kg';
            }
            if (totalQtyHidden) {
                totalQtyHidden.value = total;
            }
        }
        
        async function refreshTable() {
            try {
                const response = await fetch(`/pembagian-beras/${terimaBbId}/rincian`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Calculate total for all data
                    let totalKarung = 0;
                    let totalTonase = 0;
                    
                    // Sum grouped data
                    Object.keys(data.data.grouped).forEach(sorting => {
                        const rows = data.data.grouped[sorting];
                        totalKarung += rows.reduce((sum, row) => sum + (parseFloat(row.jumlah_karung) || 0), 0);
                        totalTonase += rows.reduce((sum, row) => sum + (parseFloat(row.tonase) || 0), 0);
                    });
                    
                    // Sum unsorted data
                    if (data.data.unsorted) {
                        totalKarung += data.data.unsorted.reduce((sum, row) => sum + (parseFloat(row.jumlah_karung) || 0), 0);
                        totalTonase += data.data.unsorted.reduce((sum, row) => sum + (parseFloat(row.tonase) || 0), 0);
                    }
                    
                    // Update total display
                    document.getElementById('total-karung-display').textContent = formatNumber(totalKarung);
                    document.getElementById('total-tonase-display').textContent = formatNumber(totalTonase);
                    
                    // Re-render the table
                    const container = document.getElementById('rincian-tabel-container');
                    
                    let html = '';
                    
                    // Render grouped data
                    Object.keys(data.data.grouped).sort((a, b) => a - b).forEach(sorting => {
                        const rows = data.data.grouped[sorting];
                        const groupTotalTonase = rows.reduce((sum, row) => sum + (parseFloat(row.tonase) || 0), 0);
                        const groupTotalKarung = rows.reduce((sum, row) => sum + (parseFloat(row.jumlah_karung) || 0), 0);
                        const isEditableGroup = activeEditingSortingNumber && String(activeEditingSortingNumber) === String(sorting);
                        
                        html += `
                            <table class="min-w-full text-sm mb-4 sorting-group-table" data-sorting-number="${sorting}">
                                <thead>
                                    <tr class="sorting-group-header cursor-pointer hover:bg-emerald-100 transition-colors" data-sorting-number="${sorting}">
                                        <th colspan="8" class="border-b border-slate-200 px-4 py-3 text-left text-emerald-800">
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002-2h2a2 2 0 002-2"></path>
                                                    </svg>
                                                    Sorting Ke-${sorting} <span class="text-xs text-emerald-600">(klik untuk edit)</span>
                                                </span>
                                                <span class="text-sm">
                                                    Total: ${formatNumber(groupTotalKarung)} Karung, ${formatNumber(groupTotalTonase)} Kg
                                                </span>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr class="bg-slate-50">
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-12">No</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-20">Timbang Ke</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-36">No Penerimaan</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-20">Jml Karung</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-28">Tonase</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-16">Kadar Air</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-16">Kadar Broken</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-28">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        rows.forEach((row, index) => {
                            const isSelected = activeSelectedRowIds.has(parseInt(row.id));
                            html += `
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${index + 1}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center font-medium text-slate-800">${row.timbang_ke}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600 text-xs">${row.no_penerimaan || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.jumlah_karung || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center font-semibold text-slate-800">${(row.tonase != null && !isNaN(row.tonase)) ? formatNumber(parseFloat(row.tonase)) + ' Kg' : '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.kadar_air || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.kadar_broken || '-'}</td>
                                    <td class="px-2 py-2 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                Sudah Diproses
                                            </span>
                                            ${isEditableGroup ? `
                                                <input type="checkbox" name="selected_rows[]" value="${row.id}" class="row-checkbox w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer" data-tonase="${row.tonase}" ${isSelected ? 'checked' : ''}>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                </tbody>
                            </table>
                        `;
                    });
                    
                    // Render unsorted data
                    if (data.data.unsorted && data.data.unsorted.length > 0) {
                        html += `
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-12">No</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-20">Timbang Ke</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-36">No Penerimaan</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-20">Jml Karung</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-28">Tonase</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-16">Kadar Air</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-16">Kadar Broken</th>
                                        <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700 w-20">
                                            <label class="flex items-center justify-center gap-2 cursor-pointer">
                                                <input type="checkbox" id="selectAll" class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                                <span>Pilih Semua</span>
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        data.data.unsorted.forEach((row, index) => {
                            html += `
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${index + 1}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center font-medium text-slate-800">${row.timbang_ke}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600 text-xs">${row.no_penerimaan || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.jumlah_karung || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center font-semibold text-slate-800">${(row.tonase != null && !isNaN(row.tonase)) ? formatNumber(parseFloat(row.tonase)) + ' Kg' : '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.kadar_air || '-'}</td>
                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">${row.kadar_broken || '-'}</td>
                                    <td class="px-2 py-2 text-center">
                                        <input type="checkbox" name="selected_rows[]" value="${row.id}" class="row-checkbox w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer" data-tonase="${row.tonase}">
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                </tbody>
                            </table>
                        `;
                    }
                    
                    container.innerHTML = html;
                    
                    // Re-initialize events
                    initializeTableEvents();
                }
                
            } catch (error) {
                console.error(error);
                showNotification('Gagal memuat ulang tabel', 'error');
            }
        }
        
        async function loadPembagianDetail(sortingNumber) {
            try {
                const response = await fetch(`/pembagian-beras/${terimaBbId}/detail/${sortingNumber}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reset form first
                    resetForm();
                    
                    // Fill form with data
                    const pembagian = data.data.pembagian;
                    const selectedRows = data.data.selected_rows;
                    activeEditingSortingNumber = sortingNumber;
                    activeSelectedRowIds = new Set((selectedRows || []).map(id => parseInt(id)));

                    document.getElementById('no-penerimaan-display').value = pembagian.no_penerimaan || '-';
                    document.getElementById('editing_sorting_number').value = sortingNumber;
                    document.getElementById('editing_no_penerimaan').value = pembagian.no_penerimaan || '';
                    document.getElementById('pembagian_ke').value = sortingNumber;
                    document.getElementById('harga').value = pembagian.harga || '';
                    document.getElementById('kondisi_umum').value = pembagian.kondisi || '';
                    document.getElementById('kondisi_kendaraan').value = pembagian.kendaraan || '';
                    document.getElementById('keputusan_penerimaan').value = pembagian.keputusan || '';
                    document.getElementById('sorter_beras').value = pembagian.sorter == 1 ? 'Ya' : 'Tidak';
                    document.getElementById('warna').value = pembagian.warna || '';
                    document.getElementById('aroma_beras').value = pembagian.aroma || '';
                    document.getElementById('indikasi_kimia').value = pembagian.indikasi_kimia || '';
                    document.getElementById('catatan_cek').value = pembagian.keterangan || '';
                    document.getElementById('keterangan_penerimaan').value = pembagian.catatan_cek || '';
                    
                    // Fill warna and aroma - we need to find the id based on criteria
                    // For now, just leave them blank or add logic later
                    
                    // Refresh table first to make sure we have the latest data
                    await refreshTable();
                    
                    // Check selected rows
                    allRowCheckboxes.forEach(checkbox => {
                        if (activeSelectedRowIds.has(parseInt(checkbox.value))) {
                            checkbox.checked = true;
                            checkbox.closest('tr').classList.add('row-selected');
                        }
                    });
                    
                    // Recalculate total
                    calculateTotal();
                    
                    showNotification(`Data Sorting Ke-${sortingNumber} berhasil dimuat`, 'success');
                } else {
                    showNotification(data.message || 'Gagal memuat data', 'error');
                }
                
            } catch (error) {
                console.error(error);
                showNotification('Terjadi kesalahan saat memuat data', 'error');
            }
        }
        
        function resetForm() {
            // Reset form fields
            document.getElementById('pembagian-form').reset();
            
            // Reset hidden fields
            document.getElementById('editing_sorting_number').value = '';
            document.getElementById('editing_no_penerimaan').value = '';
            activeEditingSortingNumber = null;
            activeSelectedRowIds = new Set();
            
            // Reset displays
            document.getElementById('no-penerimaan-display').value = availableNoPenerimaan || initialNoPenerimaan;
            document.getElementById('pembagian_ke').value = availableNextSorting || initialNextSorting;
            document.getElementById('total_qty_terpilih_display').value = '0 Kg';
            document.getElementById('total_qty_terpilih_hidden').value = '0';
            
            // Clear selected checkboxes
            allRowCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('tr').classList.remove('row-selected');
            });
            
            // Reset select all
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('pembagian-form');
            const submitBtn = document.getElementById('submit-btn');
            const resetBtn = document.getElementById('reset-btn');
            
            // Initialize table events
            initializeTableEvents();
            
            // Form submission
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                
                // Disable button and show loading
                submitBtn.disabled = true;
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8.001 8.001 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                `;
                
                try {
                    const formData = new FormData(form);
                    
                    // Add selected rows
                    const selectedRows = [];
                    allRowCheckboxes.forEach(checkbox => {
                        if (checkbox.checked && !checkbox.disabled) {
                            selectedRows.push(checkbox.value);
                        }
                    });
                    
                    selectedRows.forEach(id => {
                        formData.append('selected_rows[]', id);
                    });
                    
                    const response = await fetch('{{ route('pembagian_beras.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        
                        // Update no penerimaan display
                        if (data.data) {
                            if (data.data.next_sorting) {
                                availableNextSorting = data.data.next_sorting;
                            }
                            if (data.data.next_no_penerimaan) {
                                availableNoPenerimaan = data.data.next_no_penerimaan;
                            }
                            document.getElementById('no-penerimaan-display').value = availableNoPenerimaan || initialNoPenerimaan;
                        }
                        
                        // Reset form after save
                        resetForm();
                        
                        // Refresh table
                        await refreshTable();
                        
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                    
                } catch (error) {
                    console.error(error);
                    showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            });
            
            // Reset button click
            resetBtn.addEventListener('click', resetForm);
        });
    </script>
</x-app-layout>
