<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <style>
            /* ── Reset DataTables default UI ── */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { float: none; }

            /* ── Table base ── */
            #bbb-table_wrapper { width: 100% !important; }
            #bbb-table, #bbb-table.dataTable { width: 100% !important; }

            #bbb-table thead th {
                font-size: 0.6875rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                padding: 0.5rem 0.5rem;
                white-space: nowrap;
            }

            #bbb-table tbody td {
                font-size: 0.8125rem;
                color: #374151;
                padding: 0.5rem 0.5rem;
                border-bottom: 1px solid #f3f4f6;
                vertical-align: middle;
            }

            #bbb-table tbody tr:hover td { background: #f9fafb; }

            .bbb-clamp {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                white-space: normal;
                word-break: break-word;
                line-height: 1.3;
            }

            /* ── Status badges ── */
            .bbb-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.2rem 0.55rem;
                border-radius: 999px;
                font-size: 0.6875rem;
                font-weight: 600;
                white-space: nowrap;
            }

            /* ── Action icons ── */
            .bbb-action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 6px;
                color: #6b7280;
                background: transparent;
                border: none;
                cursor: pointer;
                transition: background 0.12s, color 0.12s;
                flex-shrink: 0;
                text-decoration: none;
            }

            .bbb-action-btn:hover  { background: #f3f4f6; }
            .bbb-action-btn:active { transform: scale(0.92); }
            .bbb-action-btn.blue:hover   { color: #2563eb; background: #eff6ff; }
            .bbb-action-btn.slate        { color: #94a3b8; }
            .bbb-action-btn.slate:hover  { color: #475569; background: #f1f5f9; }

            /* ── Toolbar controls ── */
            .bbb-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                align-items: center;
                margin-bottom: 0.75rem;
            }

            .bbb-toolbar-left  { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; flex: 1; min-width: 0; }
            .bbb-toolbar-right { display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0; }

            .bbb-btn-tool {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                height: 36px;
                padding: 0 0.75rem;
                border-radius: 8px;
                font-size: 0.8125rem;
                font-weight: 500;
                cursor: pointer;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #374151;
                white-space: nowrap;
                transition: background 0.12s;
                touch-action: manipulation;
            }

            .bbb-btn-tool:hover { background: #f9fafb; }
            .bbb-btn-tool.blue  { background: #2563eb; color: #fff; border-color: #2563eb; }
            .bbb-btn-tool.blue:hover { background: #1d4ed8; }

            .bbb-btn-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                cursor: pointer;
                border: none;
                transition: background 0.12s;
                flex-shrink: 0;
                touch-action: manipulation;
            }

            /* ── Filter panels ── */
            .bbb-panel {
                position: absolute;
                z-index: 50;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                padding: 0.875rem;
            }

            /* ── Search input ── */
            #bbb-search-control input {
                height: 36px;
                border-radius: 8px;
                border: 1px solid #d1d5db;
                font-size: 0.8125rem;
                padding: 0 0.75rem;
                outline: none;
                width: 100%;
                min-width: 0;
                transition: border-color 0.15s, box-shadow 0.15s;
            }

            #bbb-search-control input:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            }

            #bbb-length-control select {
                height: 36px;
                border-radius: 8px;
                border: 1px solid #d1d5db;
                font-size: 0.8125rem;
                padding: 0 0.5rem;
                outline: none;
                background: #fff;
            }

            /* ── Pagination ── */
            .dataTables_paginate {
                display: flex;
                align-items: center;
                gap: 4px;
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 0.75rem;
            }

            .dataTables_paginate .paginate_button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 32px;
                height: 32px;
                padding: 0 0.5rem;
                border-radius: 6px;
                font-size: 0.8125rem;
                color: #374151;
                cursor: pointer;
                border: 1px solid transparent;
                transition: background 0.12s;
            }

            .dataTables_paginate .paginate_button:hover   { background: #f3f4f6; }
            .dataTables_paginate .paginate_button.current { background: #2563eb; color: #fff !important; border-color: #2563eb; }
            .dataTables_paginate .paginate_button.disabled { opacity: 0.4; cursor: default; }

            .dataTables_info {
                font-size: 0.75rem;
                color: #9ca3af;
                text-align: center;
                margin-top: 0.5rem;
            }

            /* ── Column toggle ── */
            .bbb-col-toggle {
                display: block;
                width: 100%;
                padding: 0.4rem 0.75rem;
                border-radius: 6px;
                font-size: 0.8125rem;
                text-align: left;
                cursor: pointer;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #6b7280;
                transition: background 0.1s, color 0.1s;
                touch-action: manipulation;
            }

            .bbb-col-toggle.active {
                background: #eff6ff;
                color: #1d4ed8;
                border-color: #bfdbfe;
            }

            /* ═══════════════════════════════════════
               MOBILE CARD VIEW
            ═══════════════════════════════════════ */
            #bbb-cards { display: none; }

            @media (max-width: 767px) {
                #bbb-table-wrap  { display: none; }
                #bbb-cards       { display: block; }

                .bbb-panel {
                    left: 0 !important;
                    right: 0 !important;
                    min-width: 0 !important;
                    width: auto !important;
                    margin: 0 0.5rem;
                    position: fixed;
                    top: auto !important;
                    bottom: 0;
                    border-radius: 16px 16px 0 0;
                    padding: 1.25rem;
                    max-height: 80vh;
                    overflow-y: auto;
                }

                #bbb-search-control {
                    flex: 1;
                    min-width: 0;
                    max-width: none !important;
                }

                .bbb-toolbar-left { flex: none; }

                .dataTables_paginate .paginate_button {
                    min-width: 36px;
                    height: 36px;
                }
            }

            /* ── Card item ── */
            .bbb-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 0.875rem 1rem;
                margin-bottom: 0.625rem;
                position: relative;
                transition: box-shadow 0.12s;
            }

            .bbb-card:active { box-shadow: 0 0 0 2px #bfdbfe; }

            .bbb-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.5rem;
                margin-bottom: 0.625rem;
            }

            .bbb-card-meta { flex: 1; min-width: 0; }

            .bbb-card-supplier {
                font-size: 0.875rem;
                font-weight: 600;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.3;
            }

            .bbb-card-sub {
                font-size: 0.75rem;
                color: #9ca3af;
                margin-top: 2px;
            }

            .bbb-card-body {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.375rem 0.75rem;
                margin-bottom: 0.625rem;
            }

            .bbb-card-field label {
                display: block;
                font-size: 0.625rem;
                font-weight: 600;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 1px;
            }

            .bbb-card-field span {
                font-size: 0.8125rem;
                color: #374151;
                line-height: 1.3;
                word-break: break-word;
            }

            .bbb-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-top: 0.5rem;
                border-top: 1px solid #f3f4f6;
                gap: 0.375rem;
                flex-wrap: wrap;
            }

            .bbb-card-actions { display: flex; gap: 4px; align-items: center; }

            .bbb-empty {
                text-align: center;
                padding: 3rem 1rem;
                color: #9ca3af;
                font-size: 0.875rem;
            }

            #bbb-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.3);
                z-index: 49;
            }

            @media (min-width: 768px) {
                #bbb-table { min-width: 900px; }
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-base text-gray-800 leading-tight">{{ __('Bahan Baku Beras') }}</h2>
    </x-slot>

    <div class="px-3 py-3 sm:px-4 sm:py-4">

        {{-- Backdrop --}}
        <div id="bbb-backdrop"></div>

        {{-- Toolbar --}}
        <div class="bbb-toolbar">
            <div class="bbb-toolbar-left">
                {{-- Date filter --}}
                <div class="relative">
                    <button id="bbb-date-trigger" type="button" class="bbb-btn-tool">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z"/>
                        </svg>
                        <span id="bbb-date-label">Tanggal</span>
                    </button>

                    <div id="bbb-date-panel" class="bbb-panel hidden" style="top:calc(100% + 6px);left:0;min-width:280px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.625rem;margin-bottom:0.75rem;">
                            <div>
                                <label style="display:block;font-size:0.6875rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem;">Dari</label>
                                <input id="bbb-date-from" type="date" style="width:100%;height:36px;border:1px solid #d1d5db;border-radius:8px;padding:0 0.625rem;font-size:0.8125rem;outline:none;box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.6875rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem;">Sampai</label>
                                <input id="bbb-date-to" type="date" style="width:100%;height:36px;border:1px solid #d1d5db;border-radius:8px;padding:0 0.625rem;font-size:0.8125rem;outline:none;box-sizing:border-box;">
                            </div>
                        </div>
                        <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                            <button id="bbb-date-reset" type="button" class="bbb-btn-tool">Reset</button>
                            <button id="bbb-date-apply" type="button" class="bbb-btn-tool blue">Terapkan</button>
                        </div>
                    </div>
                </div>

                {{-- Length control (desktop) --}}
                <div id="bbb-length-control" class="hidden sm:inline-flex items-center"></div>
            </div>

            <div class="bbb-toolbar-right">
                {{-- Search --}}
                <div id="bbb-search-control" class="inline-flex items-center" style="min-width:0;flex:1 1 auto;max-width:220px;"></div>

                {{-- Column toggle (desktop) --}}
                <div class="relative hidden sm:block">
                    <button id="bbb-col-trigger" type="button" class="bbb-btn-icon" style="background:#2563eb;color:#fff;" title="Toggle kolom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                        </svg>
                    </button>
                    <div id="bbb-col-panel" class="bbb-panel hidden" style="top:calc(100% + 6px);right:0;width:180px;">
                        <div style="display:flex;flex-direction:column;gap:0.375rem;">
                            <button type="button" data-col="2"  class="bbb-col-toggle">No Penerimaan</button>
                            <button type="button" data-col="3"  class="bbb-col-toggle active">Tanggal</button>
                            <button type="button" data-col="4"  class="bbb-col-toggle active">Supplier</button>
                            <button type="button" data-col="5"  class="bbb-col-toggle active">Jenis</button>
                            <button type="button" data-col="6"  class="bbb-col-toggle">No Polisi</button>
                            <button type="button" data-col="7"  class="bbb-col-toggle active">Berat</button>
                            <button type="button" data-col="8"  class="bbb-col-toggle">Keputusan</button>
                            <button type="button" data-col="9"  class="bbb-col-toggle active">Harga</button>
                            <button type="button" data-col="10" class="bbb-col-toggle">Harga Rata</button>
                            <button type="button" data-col="11" class="bbb-col-toggle">Keterangan</button>
                            <button type="button" data-col="12" class="bbb-col-toggle active">Status</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ╔═══════════════════════════════════════╗
             ║  DESKTOP: Table                       ║
             ╚═══════════════════════════════════════╝ --}}
        <div id="bbb-table-wrap" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
            <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
                <table id="bbb-table" class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="w-[36px]">No</th>
                            <th class="w-[72px]">ID</th>
                            <th class="w-[100px]">No Penerimaan</th>
                            <th class="w-[80px]">Tanggal</th>
                            <th class="w-[140px]">Supplier</th>
                            <th class="w-[80px]">Jenis</th>
                            <th class="w-[90px]">No Polisi</th>
                            <th class="w-[80px]">Berat</th>
                            <th class="w-[110px]">Keputusan</th>
                            <th class="w-[110px]">Harga</th>
                            <th class="w-[110px]">Harga Rata</th>
                            <th class="w-[150px]">Keterangan</th>
                            <th class="w-[80px]">Status</th>
                            <th class="w-[80px]" style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($beras as $item)
                            @php
                                $tanggal = filled($item->tanggal)
                                    ? \Illuminate\Support\Carbon::parse($item->tanggal) : null;
                                $itemStatus = strtolower((string)($item->status ?? ''));
                                $badgeClass = match($itemStatus) {
                                    'proses'              => 'background:#eff6ff;color:#1d4ed8;',
                                    'menunggu validasi'   => 'background:#fffbeb;color:#92400e;',
                                    'finish'              => 'background:#eef2ff;color:#4338ca;',
                                    'checked'             => 'background:#faf5ff;color:#7c3aed;',
                                    'approved', 'approve' => 'background:#ecfdf5;color:#065f46;',
                                    default               => 'background:#f3f4f6;color:#374151;',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="white-space:nowrap;">{{ $item->idb_beras }}</td>
                                <td><div class="bbb-clamp">{{ $item->no_penerimaan ?? '-' }}</div></td>
                                <td style="white-space:nowrap;" data-date="{{ $tanggal?->format('Y-m-d') }}" data-order="{{ $tanggal?->format('Y-m-d') }}">
                                    {{ $tanggal?->format('d M Y') ?? '-' }}
                                </td>
                                <td><div class="bbb-clamp">{{ $item->supplier ?? '-' }}</div></td>
                                <td><div class="bbb-clamp">{{ $item->jenis ?? '-' }}</div></td>
                                <td><div class="bbb-clamp">{{ $item->nopol ?? '-' }}</div></td>
                                <td style="white-space:nowrap;">{{ is_numeric($item->berat) ? number_format((int)$item->berat, 0, ',', '.') . ' Kg' : '-' }}</td>
                                <td><div class="bbb-clamp">{{ $item->keputusan ?? '-' }}</div></td>
                                <td style="white-space:nowrap;">{{ is_numeric($item->harga) ? 'Rp ' . number_format((int)$item->harga, 0, ',', '.') : '-' }}</td>
                                <td style="white-space:nowrap;">{{ is_numeric($item->harga_rata) ? 'Rp ' . number_format((int)$item->harga_rata, 0, ',', '.') : '-' }}</td>
                                <td><div class="bbb-clamp" style="color:#6b7280;">{{ $item->keterangan ?? '-' }}</div></td>
                                <td>
                                    <span class="bbb-badge" style="{{ $badgeClass }}">{{ $item->status ?? '-' }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:2px;">
                                        {{-- Update Harga --}}
                                        <button type="button" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'bbb-update-harga-modal-{{ $item->idb_beras }}')"
                                            class="bbb-action-btn blue" title="Update Harga">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                        {{-- Print --}}
                                        <a href="{{ route('bahan_baku_beras.print', $item->idb_beras) }}" target="_blank"
                                            class="bbb-action-btn slate" title="Print">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3a1 1 0 011-1h10a1 1 0 011 1v6m-12 0h12M5 9v9a2 2 0 002 2h10a2 2 0 002-2V9M7 15h10"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            {{-- Modal Update Harga --}}
                            <x-modal name="bbb-update-harga-modal-{{ $item->idb_beras }}" focusable>
                                <form method="POST" action="{{ route('bahan_baku_beras.update_harga', $item->idb_beras) }}">
                                    @csrf @method('PUT')

                                    {{-- Header --}}
                                    <div style="padding: 1.25rem 1.5rem 0;">
                                        <h2 style="font-size:1.0625rem;font-weight:600;color:#111827;margin:0;">Update Harga</h2>
                                    </div>

                                    {{-- Body --}}
                                    <div style="padding: 1.25rem 1.5rem; display:flex; flex-direction:column; gap:1rem;">
                                        <div>
                                            <label style="display:block;font-size:0.75rem;font-weight:500;color:#374151;margin-bottom:0.375rem;">No Penerimaan</label>
                                            <input type="text"
                                                value="{{ $item->no_penerimaan }}"
                                                disabled
                                                style="display:block;width:100%;box-sizing:border-box;height:2.375rem;padding:0 0.75rem;border:1px solid #d1d5db;border-radius:8px;font-size:0.875rem;background:#f9fafb;color:#6b7280;outline:none;">
                                        </div>
                                        <div>
                                            <label for="bbb-harga-{{ $item->idb_beras }}" style="display:block;font-size:0.75rem;font-weight:500;color:#374151;margin-bottom:0.375rem;">Harga</label>
                                            <input id="bbb-harga-{{ $item->idb_beras }}"
                                                name="harga"
                                                type="number"
                                                value="{{ $item->harga }}"
                                                required
                                                style="display:block;width:100%;box-sizing:border-box;height:2.375rem;padding:0 0.75rem;border:1px solid #d1d5db;border-radius:8px;font-size:0.875rem;color:#111827;outline:none;transition:border-color 0.15s,box-shadow 0.15s;"
                                                onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.12)'"
                                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div style="padding: 0.875rem 1.5rem 1.25rem; display:flex; justify-content:flex-end; gap:0.5rem; border-top:1px solid #f3f4f6;">
                                        <button type="button" x-on:click="$dispatch('close')"
                                            style="height:36px;padding:0 1rem;border-radius:8px;border:1px solid #d1d5db;background:#fff;font-size:0.875rem;font-weight:500;color:#374151;cursor:pointer;transition:background 0.12s;"
                                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            style="height:36px;padding:0 1.125rem;border-radius:8px;border:none;background:#2563eb;font-size:0.875rem;font-weight:500;color:#fff;cursor:pointer;transition:background 0.12s;"
                                            onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                        @empty
                            <tr>
                                <td colspan="14" style="text-align:center;padding:3rem 1rem;color:#9ca3af;font-size:0.875rem;">
                                    Data belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding:0.75rem 1rem;border-top:1px solid #f3f4f6;">
                <div id="bbb-info" class="dataTables_info"></div>
                <div id="bbb-paginate" class="dataTables_paginate"></div>
            </div>
        </div>

        {{-- ╔═══════════════════════════════════════╗
             ║  MOBILE: Card list                    ║
             ╚═══════════════════════════════════════╝ --}}
        <div id="bbb-cards">
            @forelse ($beras as $item)
                @php
                    $tanggal = filled($item->tanggal)
                        ? \Illuminate\Support\Carbon::parse($item->tanggal) : null;
                    $itemStatus = strtolower((string)($item->status ?? ''));
                    $badgeClass = match($itemStatus) {
                        'proses'              => 'background:#eff6ff;color:#1d4ed8;',
                        'menunggu validasi'   => 'background:#fffbeb;color:#92400e;',
                        'finish'              => 'background:#eef2ff;color:#4338ca;',
                        'checked'             => 'background:#faf5ff;color:#7c3aed;',
                        'approved', 'approve' => 'background:#ecfdf5;color:#065f46;',
                        default               => 'background:#f3f4f6;color:#374151;',
                    };
                @endphp
                <div class="bbb-card"
                     data-search="{{ strtolower(implode(' ', array_filter([
                         $item->supplier,
                         $item->jenis,
                         $item->nopol,
                         $item->no_penerimaan,
                         $item->keputusan,
                         $item->status,
                         $item->keterangan,
                         $tanggal?->format('d M Y'),
                     ]))) }}"
                     data-date="{{ $tanggal?->format('Y-m-d') }}">

                    <div class="bbb-card-header">
                        <div class="bbb-card-meta">
                            <div class="bbb-card-supplier">{{ $item->supplier ?? '-' }}</div>
                            <div class="bbb-card-sub">
                                {{ $tanggal?->format('d M Y') ?? '-' }}
                                @if($item->no_penerimaan) &nbsp;·&nbsp; {{ $item->no_penerimaan }} @endif
                            </div>
                        </div>
                        <span class="bbb-badge" style="{{ $badgeClass }}">{{ $item->status ?? '-' }}</span>
                    </div>

                    <div class="bbb-card-body">
                        <div class="bbb-card-field">
                            <label>Jenis</label>
                            <span>{{ $item->jenis ?? '-' }}</span>
                        </div>
                        <div class="bbb-card-field">
                            <label>Berat</label>
                            <span>{{ is_numeric($item->berat) ? number_format((int)$item->berat, 0, ',', '.') . ' Kg' : '-' }}</span>
                        </div>
                        <div class="bbb-card-field">
                            <label>Harga</label>
                            <span>{{ is_numeric($item->harga) ? 'Rp ' . number_format((int)$item->harga, 0, ',', '.') : '-' }}</span>
                        </div>
                        <div class="bbb-card-field">
                            <label>Harga Rata</label>
                            <span>{{ is_numeric($item->harga_rata) ? 'Rp ' . number_format((int)$item->harga_rata, 0, ',', '.') : '-' }}</span>
                        </div>
                        @if($item->keputusan)
                        <div class="bbb-card-field" style="grid-column:1/-1;">
                            <label>Keputusan</label>
                            <span>{{ $item->keputusan }}</span>
                        </div>
                        @endif
                        @if($item->keterangan)
                        <div class="bbb-card-field" style="grid-column:1/-1;">
                            <label>Keterangan</label>
                            <span style="color:#6b7280;">{{ $item->keterangan }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="bbb-card-footer">
                        <div style="font-size:0.75rem;color:#9ca3af;">
                            {{ $item->nopol ?? '' }}
                        </div>
                        <div class="bbb-card-actions">
                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'bbb-update-harga-modal-{{ $item->idb_beras }}')"
                                class="bbb-action-btn blue" title="Update Harga">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            <a href="{{ route('bahan_baku_beras.print', $item->idb_beras) }}" target="_blank"
                                class="bbb-action-btn slate" title="Print">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3a1 1 0 011-1h10a1 1 0 011 1v6m-12 0h12M5 9v9a2 2 0 002 2h10a2 2 0 002-2V9M7 15h10"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bbb-empty">Data belum tersedia.</div>
            @endforelse

            <div style="padding:0.5rem 0 1rem;">
                <div id="bbb-cards-info" class="dataTables_info"></div>
                <div id="bbb-cards-paginate" class="dataTables_paginate"></div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isMobile = () => window.innerWidth < 768;

            /* ─── DataTable (desktop) ─── */
            const dt = $('#bbb-table').DataTable({
                pageLength: 15,
                lengthMenu: [15, 25, 50, 100],
                autoWidth: false,
                order: [[3, 'desc']],
                columnDefs: [
                    { targets: [1, 2, 6, 8, 10, 11], visible: false },
                    { orderable: false, targets: [0, 13] }
                ],
                dom: 'lrtip',
                language: {
                    search: '', searchPlaceholder: 'Cari...',
                    lengthMenu: 'Tampilkan _MENU_',
                    info: 'Data _START_–_END_ dari _TOTAL_',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' }
                },
                drawCallback: function () {
                    const api = this.api();
                    api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1 + api.page.info().start;
                    });
                    const w = $('#bbb-table_wrapper');
                    $('#bbb-info').empty().append(w.find('.dataTables_info'));
                    $('#bbb-paginate').empty().append(w.find('.dataTables_paginate'));
                }
            });

            const wrapper = $('#bbb-table_wrapper');
            $('#bbb-length-control').empty().append(wrapper.find('.dataTables_length'));
            $('#bbb-search-control').empty().append(wrapper.find('.dataTables_filter'));
            wrapper.find('.dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter').hide();
            wrapper.find('.dataTables_filter label').contents().filter(function(){ return this.nodeType === 3; }).remove();

            /* ─── Mobile cards ─── */
            let cardDateFrom = '', cardDateTo = '', cardSearchQuery = '';
            const PAGE_SIZE = 15;
            let cardPage = 1;
            const allCards = Array.from(document.querySelectorAll('#bbb-cards .bbb-card'));

            function filterCards() {
                const q = cardSearchQuery.toLowerCase().trim();
                return allCards.filter(card => {
                    const txt = card.getAttribute('data-search') || '';
                    if (q && !txt.includes(q)) return false;
                    const dv = card.getAttribute('data-date') || '';
                    if (cardDateFrom || cardDateTo) {
                        if (!dv) return false;
                        const d = new Date(dv + 'T00:00:00');
                        if (isNaN(d.getTime())) return false;
                        if (cardDateFrom && d < new Date(cardDateFrom + 'T00:00:00')) return false;
                        if (cardDateTo   && d > new Date(cardDateTo   + 'T23:59:59')) return false;
                    }
                    return true;
                });
            }

            function renderCards() {
                const visible = filterCards();
                const total = visible.length;
                const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
                if (cardPage > totalPages) cardPage = totalPages;
                const start = (cardPage - 1) * PAGE_SIZE;
                const end = start + PAGE_SIZE;
                allCards.forEach(c => c.style.display = 'none');
                visible.slice(start, end).forEach(c => c.style.display = '');
                const infoEl = document.getElementById('bbb-cards-info');
                infoEl.textContent = total === 0 ? 'Data tidak ditemukan'
                    : `Data ${Math.min(start+1,total)}–${Math.min(end,total)} dari ${total}`;
                const pEl = document.getElementById('bbb-cards-paginate');
                pEl.innerHTML = '';
                if (totalPages <= 1) return;
                const mkBtn = (label, page, disabled, active) => {
                    const btn = document.createElement('button');
                    btn.textContent = label;
                    btn.className = 'paginate_button' + (active ? ' current' : '') + (disabled ? ' disabled' : '');
                    btn.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:6px;font-size:0.8125rem;border:1px solid transparent;background:none;cursor:pointer;';
                    if (active) { btn.style.background='#2563eb'; btn.style.color='#fff'; btn.style.borderColor='#2563eb'; }
                    if (!disabled && !active) btn.addEventListener('click', () => { cardPage = page; renderCards(); window.scrollTo({top:0,behavior:'smooth'}); });
                    return btn;
                };
                pEl.appendChild(mkBtn('‹', cardPage-1, cardPage===1, false));
                let pages = [];
                if (totalPages <= 7) { for(let i=1;i<=totalPages;i++) pages.push(i); }
                else {
                    pages = [1];
                    if (cardPage > 3) pages.push('…');
                    for(let i=Math.max(2,cardPage-1);i<=Math.min(totalPages-1,cardPage+1);i++) pages.push(i);
                    if (cardPage < totalPages-2) pages.push('…');
                    pages.push(totalPages);
                }
                pages.forEach(p => {
                    if (p==='…') { const s=document.createElement('span'); s.textContent='…'; s.style.cssText='display:inline-flex;align-items:center;padding:0 4px;font-size:0.8125rem;color:#9ca3af;'; pEl.appendChild(s); }
                    else pEl.appendChild(mkBtn(p, p, false, p===cardPage));
                });
                pEl.appendChild(mkBtn('›', cardPage+1, cardPage===totalPages, false));
            }

            const searchInput = document.querySelector('#bbb-search-control input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    cardSearchQuery = this.value; cardPage = 1;
                    if (isMobile()) renderCards();
                });
            }

            /* ─── Date filter ─── */
            let dateFrom = '', dateTo = '';
            const initialLabel = document.getElementById('bbb-date-label').textContent;

            $.fn.dataTable.ext.search.push(function(settings, data, idx) {
                if (settings.nTable.id !== 'bbb-table') return true;
                if (!dateFrom && !dateTo) return true;
                const node = dt.row(idx).node();
                const val = node?.querySelector('[data-date]')?.getAttribute('data-date') || '';
                if (!val) return false;
                const d = new Date(val + 'T00:00:00');
                if (isNaN(d.getTime())) return false;
                if (dateFrom && d < new Date(dateFrom + 'T00:00:00')) return false;
                if (dateTo   && d > new Date(dateTo   + 'T23:59:59')) return false;
                return true;
            });

            function fmtDate(s) {
                if (!s) return '';
                const d = new Date(s + 'T00:00:00');
                return isNaN(d.getTime()) ? '' : d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
            }

            function updateLabel() {
                document.getElementById('bbb-date-label').textContent =
                    (dateFrom || dateTo) ? (fmtDate(dateFrom)||'Awal') + ' – ' + (fmtDate(dateTo)||'Akhir') : initialLabel;
            }

            /* ─── Panel helpers ─── */
            const backdrop = document.getElementById('bbb-backdrop');

            function openPanel(id) {
                ['bbb-date-panel','bbb-col-panel'].forEach(p => {
                    document.getElementById(p)?.classList[p===id ? 'toggle' : 'add']('hidden');
                });
                const open = !document.getElementById(id).classList.contains('hidden');
                if (isMobile()) backdrop.style.display = open ? 'block' : 'none';
            }

            function closeAllPanels() {
                ['bbb-date-panel','bbb-col-panel'].forEach(id => document.getElementById(id)?.classList.add('hidden'));
                backdrop.style.display = 'none';
            }

            document.getElementById('bbb-date-trigger').addEventListener('click', e => { e.stopPropagation(); openPanel('bbb-date-panel'); });
            document.getElementById('bbb-col-trigger')?.addEventListener('click', e => { e.stopPropagation(); openPanel('bbb-col-panel'); });
            backdrop.addEventListener('click', closeAllPanels);

            document.getElementById('bbb-date-apply').addEventListener('click', function() {
                dateFrom = cardDateFrom = document.getElementById('bbb-date-from').value;
                dateTo   = cardDateTo   = document.getElementById('bbb-date-to').value;
                updateLabel();
                if (isMobile()) { cardPage=1; renderCards(); } else { dt.draw(); }
                closeAllPanels();
            });

            document.getElementById('bbb-date-reset').addEventListener('click', function() {
                dateFrom = cardDateFrom = dateTo = cardDateTo = '';
                document.getElementById('bbb-date-from').value = '';
                document.getElementById('bbb-date-to').value = '';
                updateLabel();
                if (isMobile()) { cardPage=1; renderCards(); } else { dt.draw(); }
                closeAllPanels();
            });

            /* ─── Column toggles ─── */
            document.querySelectorAll('[data-col]').forEach(btn => {
                const col = dt.column(parseInt(btn.getAttribute('data-col')));
                btn.classList.toggle('active', col.visible());
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const c = dt.column(parseInt(this.getAttribute('data-col')));
                    c.visible(!c.visible());
                    this.classList.toggle('active', c.visible());
                });
            });

            document.addEventListener('click', function(e) {
                ['bbb-date-panel','bbb-col-panel'].forEach(id => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    const trigger = id === 'bbb-date-panel' ? 'bbb-date-trigger' : 'bbb-col-trigger';
                    const tEl = document.getElementById(trigger);
                    if (!el.contains(e.target) && tEl && !tEl.contains(e.target)) el.classList.add('hidden');
                });
                backdrop.style.display = 'none';
            });

            /* ─── Init ─── */
            if (isMobile()) renderCards();
            let wasMobile = isMobile();
            window.addEventListener('resize', () => {
                const nowMobile = isMobile();
                if (nowMobile !== wasMobile) { wasMobile = nowMobile; if (nowMobile) renderCards(); }
            });
        });
        </script>
    @endpush
</x-app-layout>