<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <style>
            /* ── Reset DataTables ── */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { float: none; }

            /* ── Table base ── */
            #pnb-table_wrapper { width: 100% !important; }
            #pnb-table, #pnb-table.dataTable { width: 100% !important; }

            #pnb-table thead th {
                font-size: 0.6875rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                padding: 0.5rem 0.625rem;
                white-space: nowrap;
            }

            #pnb-table tbody td {
                font-size: 0.8125rem;
                color: #374151;
                padding: 0.5rem 0.625rem;
                border-bottom: 1px solid #f3f4f6;
                vertical-align: middle;
            }

            #pnb-table tbody tr:hover td { background: #f9fafb; }

            .pnb-clamp {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                white-space: normal;
                word-break: break-word;
                line-height: 1.3;
            }

            /* ── Status badges ── */
            .pnb-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.2rem 0.55rem;
                border-radius: 999px;
                font-size: 0.6875rem;
                font-weight: 600;
                white-space: nowrap;
            }

            /* ── Action icons ── */
            .pnb-action-btn {
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
            }

            .pnb-action-btn:hover  { background: #f3f4f6; }
            .pnb-action-btn:active { transform: scale(0.92); }
            .pnb-action-btn.blue:hover   { color: #2563eb; background: #eff6ff; }
            .pnb-action-btn.blue.active-state  { color: #2563eb; background: #eff6ff; }
            .pnb-action-btn.green:hover  { color: #059669; background: #ecfdf5; }
            .pnb-action-btn.amber:hover  { color: #d97706; background: #fffbeb; }
            .pnb-action-btn.orange:hover { color: #ea580c; background: #fff7ed; }
            .pnb-action-btn.red:hover    { color: #dc2626; background: #fef2f2; }
            .pnb-action-btn.slate        { color: #94a3b8; }
            .pnb-action-btn.slate:hover  { color: #475569; background: #f1f5f9; }

            /* ── Toolbar ── */
            .pnb-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                align-items: center;
                margin-bottom: 0.75rem;
            }

            .pnb-toolbar-left  { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; flex: 1; min-width: 0; }
            .pnb-toolbar-right { display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0; }

            .pnb-btn-tool {
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

            .pnb-btn-tool:hover { background: #f9fafb; }
            .pnb-btn-tool.blue  { background: #2563eb; color: #fff; border-color: #2563eb; }
            .pnb-btn-tool.blue:hover { background: #1d4ed8; }
            .pnb-btn-tool.green { background: #059669; color: #fff; border-color: #059669; }
            .pnb-btn-tool.green:hover { background: #047857; }

            .pnb-btn-icon {
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
            .pnb-panel {
                position: absolute;
                z-index: 50;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                padding: 0.875rem;
            }

            /* ── Search input ── */
            #pnb-search-control input {
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

            #pnb-search-control input:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            }

            #pnb-length-control select {
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
            .pnb-col-toggle {
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

            .pnb-col-toggle.active {
                background: #eff6ff;
                color: #1d4ed8;
                border-color: #bfdbfe;
            }

            /* ═══════════════════════════════════════
               MOBILE CARD VIEW
            ═══════════════════════════════════════ */

            #pnb-cards { display: none; }

            @media (max-width: 767px) {
                #pnb-table-wrap  { display: none; }
                #pnb-cards       { display: block; }

                .pnb-panel {
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

                #pnb-date-panel .panel-grid {
                    grid-template-columns: 1fr 1fr;
                }

                #pnb-search-control {
                    flex: 1;
                    min-width: 0;
                    max-width: none !important;
                }

                .pnb-toolbar-left { flex: none; }

                .dataTables_paginate .paginate_button {
                    min-width: 36px;
                    height: 36px;
                }
            }

            /* ── Card item ── */
            .pnb-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 0.875rem 1rem;
                margin-bottom: 0.625rem;
                position: relative;
                transition: box-shadow 0.12s;
            }

            .pnb-card:active { box-shadow: 0 0 0 2px #bfdbfe; }

            .pnb-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.5rem;
                margin-bottom: 0.625rem;
            }

            .pnb-card-meta {
                flex: 1;
                min-width: 0;
            }

            .pnb-card-supplier {
                font-size: 0.875rem;
                font-weight: 600;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.3;
            }

            .pnb-card-date {
                font-size: 0.75rem;
                color: #9ca3af;
                margin-top: 2px;
            }

            .pnb-card-body {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.375rem 0.75rem;
                margin-bottom: 0.625rem;
            }

            .pnb-card-field label {
                display: block;
                font-size: 0.625rem;
                font-weight: 600;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 1px;
            }

            .pnb-card-field span {
                font-size: 0.8125rem;
                color: #374151;
                line-height: 1.3;
                word-break: break-word;
            }

            .pnb-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-top: 0.5rem;
                border-top: 1px solid #f3f4f6;
                gap: 0.375rem;
                flex-wrap: wrap;
            }

            .pnb-card-actions {
                display: flex;
                gap: 4px;
                align-items: center;
            }

            .pnb-empty {
                text-align: center;
                padding: 3rem 1rem;
                color: #9ca3af;
                font-size: 0.875rem;
            }

            #pnb-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.3);
                z-index: 49;
            }

            @media (min-width: 768px) {
                #pnb-table { min-width: 800px; }
                .pnb-toolbar { gap: 0.375rem; }
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-base text-gray-800 leading-tight">{{ __('Penerimaan Beras') }}</h2>
    </x-slot>

    <div class="px-3 py-3 sm:px-4 sm:py-4">

        {{-- Backdrop --}}
        <div id="pnb-backdrop"></div>

        {{-- Toolbar --}}
        <div class="pnb-toolbar">
            <div class="pnb-toolbar-left">
                {{-- Date filter --}}
                <div class="relative">
                    <button id="pnb-date-trigger" type="button" class="pnb-btn-tool">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z"/>
                        </svg>
                        <span id="pnb-date-label">{{ $dateLabel }}</span>
                    </button>

                    <div id="pnb-date-panel" class="pnb-panel hidden" style="top:calc(100% + 6px);left:0;min-width:280px;">
                        <div class="panel-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:0.625rem;margin-bottom:0.75rem;">
                            <div>
                                <label style="display:block;font-size:0.6875rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem;">Dari</label>
                                <input id="pnb-date-from" type="date" style="width:100%;height:36px;border:1px solid #d1d5db;border-radius:8px;padding:0 0.625rem;font-size:0.8125rem;outline:none;box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.6875rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem;">Sampai</label>
                                <input id="pnb-date-to" type="date" style="width:100%;height:36px;border:1px solid #d1d5db;border-radius:8px;padding:0 0.625rem;font-size:0.8125rem;outline:none;box-sizing:border-box;">
                            </div>
                        </div>
                        <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                            <button id="pnb-date-reset" type="button" class="pnb-btn-tool">Reset</button>
                            <button id="pnb-date-apply" type="button" class="pnb-btn-tool blue">Terapkan</button>
                        </div>
                    </div>
                </div>

                {{-- Length (desktop only) --}}
                <div id="pnb-length-control" class="hidden sm:inline-flex items-center"></div>
            </div>

            <div class="pnb-toolbar-right">
                {{-- Search --}}
                <div id="pnb-search-control" class="inline-flex items-center" style="min-width:0;flex:1 1 auto;max-width:220px;"></div>

                {{-- Column toggle (desktop only) --}}
                <div class="relative hidden sm:block">
                    <button id="pnb-col-trigger" type="button" class="pnb-btn-icon" style="background:#2563eb;color:#fff;" title="Toggle kolom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                        </svg>
                    </button>
                    <div id="pnb-col-panel" class="pnb-panel hidden" style="top:calc(100% + 6px);right:0;width:180px;">
                        <div style="display:flex;flex-direction:column;gap:0.375rem;">
                            <button type="button" data-col="1"  class="pnb-col-toggle active">Tanggal</button>
                            <button type="button" data-col="2"  class="pnb-col-toggle active">Supplier</button>
                            <button type="button" data-col="3"  class="pnb-col-toggle active">Lokasi</button>
                            <button type="button" data-col="4"  class="pnb-col-toggle active">Jenis</button>
                            <button type="button" data-col="5"  class="pnb-col-toggle">Kemasan</button>
                            <button type="button" data-col="6"  class="pnb-col-toggle active">Tonase</button>
                            <button type="button" data-col="7"  class="pnb-col-toggle active">No Polisi</button>
                            <button type="button" data-col="8"  class="pnb-col-toggle active">Status</button>
                            <button type="button" data-col="9"  class="pnb-col-toggle">User Input</button>
                            <button type="button" data-col="10" class="pnb-col-toggle">User Validasi</button>
                            <button type="button" data-col="11" class="pnb-col-toggle active">Keterangan</button>
                        </div>
                    </div>
                </div>

                {{-- Add button --}}
                <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'penerimaan-beras-create-modal' }))"
                    class="pnb-btn-icon" style="background:#059669;color:#fff;" title="Tambah data">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ╔═══════════════════════════════════════╗
             ║  DESKTOP: Table card                  ║
             ╚═══════════════════════════════════════╝ --}}
        <div id="pnb-table-wrap" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
            <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
                <table id="pnb-table" class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="w-[36px]">No</th>
                            <th class="w-[80px]">Tanggal</th>
                            <th class="w-[140px]">Supplier</th>
                            <th class="w-[110px]">Lokasi</th>
                            <th class="w-[80px]">Jenis</th>
                            <th class="w-[90px]">Kemasan</th>
                            <th class="w-[80px]">Tonase</th>
                            <th class="w-[86px]">No Polisi</th>
                            <th class="w-[84px]">Status</th>
                            <th class="w-[100px]">User Input</th>
                            <th class="w-[100px]">User Validasi</th>
                            <th class="w-[140px]">Keterangan</th>
                            <th class="w-[96px]" style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentUser = auth()->user()?->username ?: auth()->user()?->name; @endphp
                        @forelse ($items as $item)
                            @php
                                $tanggalTerima = filled($item->tanggal_terima)
                                    ? \Illuminate\Support\Carbon::parse($item->tanggal_terima) : null;
                                $itemStatus = strtolower((string)($item->status ?? ''));
                                $isCreator = $currentUser === $item->user_created;
                                $isSuperAdmin = auth()->user()->hasRole('super admin');
                                $isAdmin = auth()->user()->hasRole('admin');
                                $isAdminOrSuper = $isSuperAdmin || $isAdmin;

                                $showTimbang = $showTimbangRO = $showValidasi = $showPembagian =
                                $showPembagianRO = $showUnapprove = $showApprove = $showUncheck =
                                $showEdit = $showHapus = false;

                                if ($itemStatus === 'proses') {
                                    $showTimbang = $isCreator || $isAdminOrSuper;
                                    $showEdit = true;
                                } elseif ($itemStatus === 'menunggu validasi') {
                                    $showValidasi = !$isCreator || $isAdminOrSuper;
                                    $showTimbangRO = true;
                                } elseif ($itemStatus === 'finish') {
                                    $showPembagian = !$isCreator || $isAdminOrSuper;
                                    $showTimbangRO = true;
                                } elseif ($itemStatus === 'checked') {
                                    $showPembagianRO = $showTimbangRO = true;
                                    $showUncheck = $showApprove = $isAdminOrSuper;
                                } elseif (in_array($itemStatus, ['approved', 'approve'])) {
                                    $showPembagianRO = $showTimbangRO = true;
                                    $showUnapprove = $isAdminOrSuper;
                                }

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
                                <td data-date="{{ $tanggalTerima?->format('Y-m-d') }}" data-order="{{ $tanggalTerima?->format('Y-m-d') }}">
                                    {{ $tanggalTerima?->format('d M Y') ?? '-' }}
                                </td>
                                <td><div class="pnb-clamp">{{ $item->supplier_name ?? $item->nama_supplier ?? '-' }}</div></td>
                                <td><div class="pnb-clamp">{{ $item->tempat_simpan ?? '-' }}</div></td>
                                <td><div class="pnb-clamp">{{ $item->varietas_alias ?? '-' }}</div></td>
                                <td><div class="pnb-clamp">{{ $item->kemasan_pakai ?? '-' }}</div></td>
                                <td style="white-space:nowrap;">{{ is_numeric($item->tonase) ? number_format((int)round((float)$item->tonase), 0, ',', '.') . ' Kg' : '-' }}</td>
                                <td><div class="pnb-clamp">{{ $item->nopol ?? '-' }}</div></td>
                                <td>
                                    <span class="pnb-badge" style="{{ $badgeClass }}">{{ $item->status ?? '-' }}</span>
                                </td>
                                <td><div class="pnb-clamp">{{ $item->user_created ?? '-' }}</div></td>
                                <td><div class="pnb-clamp">{{ $item->user_finish ?? '-' }}</div></td>
                                <td><div class="pnb-clamp" style="color:#6b7280;">{{ $item->keterangan ?? '-' }}</div></td>
                                <td>
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:2px;">
                                        @if($showTimbang)
                                            <a href="{{ route('timbangan-beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn blue" title="Timbang">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5"/></svg>
                                            </a>
                                        @endif
                                        @if($showTimbangRO)
                                            <a href="{{ route('timbangan-beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn slate" title="Timbangan (Read Only)">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5"/></svg>
                                            </a>
                                        @endif
                                        @if($showValidasi)
                                            <form method="POST" action="{{ route('timbangan-beras.validate', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin memvalidasi data ini?');">
                                                @csrf
                                                <button type="submit" class="pnb-action-btn green" title="Validasi">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($showPembagian)
                                            <a href="{{ route('pembagian_beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn amber" title="Pembagian Beras">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20"/></svg>
                                            </a>
                                        @endif
                                        @if($showPembagianRO)
                                            <a href="{{ route('pembagian_beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn slate" title="Pembagian (Read Only)">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20"/></svg>
                                            </a>
                                        @endif
                                        @if($showUnapprove)
                                            <form method="POST" action="{{ route('penerimaan_beras.unapprove', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin membatalkan approval?');">
                                                @csrf
                                                <button type="submit" class="pnb-action-btn orange" title="Unapprove">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($showApprove)
                                            <form method="POST" action="{{ route('penerimaan_beras.approve', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin approve pembagian beras ini?');">
                                                @csrf
                                                <button type="submit" class="pnb-action-btn green" title="Approve">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($showUncheck)
                                            <form method="POST" action="{{ route('penerimaan_beras.uncheck', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin membatalkan check?');">
                                                @csrf
                                                <button type="submit" class="pnb-action-btn amber" title="Batalkan Check">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($showEdit)
                                            <button type="button" x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'penerimaan-beras-edit-modal-{{ $item->id }}')"
                                                class="pnb-action-btn amber" title="Edit">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487z"/></svg>
                                            </button>
                                            @include('penerimaan_beras.edit', ['item' => $item])
                                        @endif
                                        @if($showHapus)
                                            <form method="POST" action="{{ route('penerimaan_beras.destroy', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin hapus data ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="pnb-action-btn red" title="Hapus">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-10.5 0V6.375A1.125 1.125 0 018.625 5.25h6.75a1.125 1.125 0 011.125 1.125V7.5m-9 0 1.05 11.025A1.5 1.5 0 009.665 20.25h4.67a1.5 1.5 0 001.495-1.725L16.5 7.5M10.5 11.25v4.5m3-4.5v4.5"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" style="text-align:center;padding:3rem 1rem;color:#9ca3af;font-size:0.875rem;">
                                    Data belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding:0.75rem 1rem;border-top:1px solid #f3f4f6;">
                <div id="pnb-info" class="dataTables_info"></div>
                <div id="pnb-paginate" class="dataTables_paginate"></div>
            </div>
        </div>

        {{-- ╔═══════════════════════════════════════╗
             ║  MOBILE: Card list                    ║
             ╚═══════════════════════════════════════╝ --}}
        <div id="pnb-cards">
            @forelse ($items as $item)
                @php
                    $tanggalTerima = filled($item->tanggal_terima)
                        ? \Illuminate\Support\Carbon::parse($item->tanggal_terima) : null;
                    $itemStatus = strtolower((string)($item->status ?? ''));
                    $currentUser = auth()->user()?->username ?: auth()->user()?->name;
                    $isCreator = $currentUser === $item->user_created;
                    $isSuperAdmin = auth()->user()->hasRole('super admin');
                    $isAdmin = auth()->user()->hasRole('admin');
                    $isAdminOrSuper = $isSuperAdmin || $isAdmin;

                    $showTimbang = $showTimbangRO = $showValidasi = $showPembagian =
                    $showPembagianRO = $showUnapprove = $showApprove = $showUncheck =
                    $showEdit = $showHapus = false;

                    if ($itemStatus === 'proses') {
                        $showTimbang = $isCreator || $isAdminOrSuper;
                        $showEdit = true;
                    } elseif ($itemStatus === 'menunggu validasi') {
                        $showValidasi = !$isCreator || $isAdminOrSuper;
                        $showTimbangRO = true;
                    } elseif ($itemStatus === 'finish') {
                        $showPembagian = !$isCreator || $isAdminOrSuper;
                        $showTimbangRO = true;
                    } elseif ($itemStatus === 'checked') {
                        $showPembagianRO = $showTimbangRO = true;
                        $showUncheck = $showApprove = $isAdminOrSuper;
                    } elseif (in_array($itemStatus, ['approved', 'approve'])) {
                        $showPembagianRO = $showTimbangRO = true;
                        $showUnapprove = $isAdminOrSuper;
                    }

                    $badgeClass = match($itemStatus) {
                        'proses'              => 'background:#eff6ff;color:#1d4ed8;',
                        'menunggu validasi'   => 'background:#fffbeb;color:#92400e;',
                        'finish'              => 'background:#eef2ff;color:#4338ca;',
                        'checked'             => 'background:#faf5ff;color:#7c3aed;',
                        'approved', 'approve' => 'background:#ecfdf5;color:#065f46;',
                        default               => 'background:#f3f4f6;color:#374151;',
                    };
                @endphp

                <div class="pnb-card"
                     data-search="{{ strtolower(implode(' ', array_filter([
                         $item->supplier_name ?? $item->nama_supplier,
                         $item->tempat_simpan,
                         $item->varietas_alias,
                         $item->kemasan_pakai,
                         $item->nopol,
                         $item->status,
                         $item->user_created,
                         $item->keterangan,
                         $tanggalTerima?->format('d M Y'),
                     ]))) }}"
                     data-date="{{ $tanggalTerima?->format('Y-m-d') }}">

                    <div class="pnb-card-header">
                        <div class="pnb-card-meta">
                            <div class="pnb-card-supplier">{{ $item->supplier_name ?? $item->nama_supplier ?? '-' }}</div>
                            <div class="pnb-card-date">
                                {{ $tanggalTerima?->format('d M Y') ?? '-' }}
                                @if($item->nopol) &nbsp;·&nbsp; {{ $item->nopol }} @endif
                            </div>
                        </div>
                        <span class="pnb-badge" style="{{ $badgeClass }}">{{ $item->status ?? '-' }}</span>
                    </div>

                    <div class="pnb-card-body">
                        <div class="pnb-card-field">
                            <label>Jenis</label>
                            <span>{{ $item->varietas_alias ?? '-' }}</span>
                        </div>
                        <div class="pnb-card-field">
                            <label>Tonase</label>
                            <span>{{ is_numeric($item->tonase) ? number_format((int)round((float)$item->tonase), 0, ',', '.') . ' Kg' : '-' }}</span>
                        </div>
                        <div class="pnb-card-field">
                            <label>Kemasan</label>
                            <span>{{ $item->kemasan_pakai ?? '-' }}</span>
                        </div>
                        <div class="pnb-card-field">
                            <label>Lokasi</label>
                            <span>{{ $item->tempat_simpan ?? '-' }}</span>
                        </div>
                        @if($item->keterangan)
                        <div class="pnb-card-field" style="grid-column:1/-1;">
                            <label>Keterangan</label>
                            <span style="color:#6b7280;">{{ $item->keterangan }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="pnb-card-footer">
                        <div style="font-size:0.75rem;color:#9ca3af;">
                            {{ $item->user_created ?? '' }}
                            @if($item->user_finish) <span style="margin:0 2px;">→</span> {{ $item->user_finish }} @endif
                        </div>
                        <div class="pnb-card-actions">
                            @if($showTimbang)
                                <a href="{{ route('timbangan-beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn blue" title="Timbang">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5"/></svg>
                                </a>
                            @endif
                            @if($showTimbangRO)
                                <a href="{{ route('timbangan-beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn slate" title="Timbangan (Read Only)">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5"/></svg>
                                </a>
                            @endif
                            @if($showValidasi)
                                <form method="POST" action="{{ route('timbangan-beras.validate', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin memvalidasi data ini?');">
                                    @csrf
                                    <button type="submit" class="pnb-action-btn green" title="Validasi">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($showPembagian)
                                <a href="{{ route('pembagian_beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn amber" title="Pembagian Beras">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20"/></svg>
                                </a>
                            @endif
                            @if($showPembagianRO)
                                <a href="{{ route('pembagian_beras.create', ['terima_bb_id' => $item->id]) }}" class="pnb-action-btn slate" title="Pembagian (Read Only)">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20"/></svg>
                                </a>
                            @endif
                            @if($showUnapprove)
                                <form method="POST" action="{{ route('penerimaan_beras.unapprove', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin membatalkan approval?');">
                                    @csrf
                                    <button type="submit" class="pnb-action-btn orange" title="Unapprove">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($showApprove)
                                <form method="POST" action="{{ route('penerimaan_beras.approve', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin approve pembagian beras ini?');">
                                    @csrf
                                    <button type="submit" class="pnb-action-btn green" title="Approve">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($showUncheck)
                                <form method="POST" action="{{ route('penerimaan_beras.uncheck', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin ingin membatalkan check?');">
                                    @csrf
                                    <button type="submit" class="pnb-action-btn amber" title="Batalkan Check">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($showEdit)
                                <button type="button" x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'penerimaan-beras-edit-modal-{{ $item->id }}')"
                                    class="pnb-action-btn amber" title="Edit">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487z"/></svg>
                                </button>
                            @endif
                            @if($showHapus)
                                <form method="POST" action="{{ route('penerimaan_beras.destroy', $item->id) }}" style="display:contents;" onsubmit="return confirm('Yakin hapus data ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pnb-action-btn red" title="Hapus">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-10.5 0V6.375A1.125 1.125 0 018.625 5.25h6.75a1.125 1.125 0 011.125 1.125V7.5m-9 0 1.05 11.025A1.5 1.5 0 009.665 20.25h4.67a1.5 1.5 0 001.495-1.725L16.5 7.5M10.5 11.25v4.5m3-4.5v4.5"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="pnb-empty">Data belum tersedia.</div>
            @endforelse

            {{-- Mobile pagination & info --}}
            <div style="padding:0.5rem 0 1rem;">
                <div id="pnb-cards-info" class="dataTables_info"></div>
                <div id="pnb-cards-paginate" class="dataTables_paginate"></div>
            </div>
        </div>

    </div>

    @include('penerimaan_beras.create')

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* ─────────────────────────────────────────
               Helper
            ───────────────────────────────────────── */
            const isMobile = () => window.innerWidth < 768;

            /* ─────────────────────────────────────────
               DataTable (desktop)
            ───────────────────────────────────────── */
            const dt = $('#pnb-table').DataTable({
                pageLength: 15,
                lengthMenu: [15, 25, 50, 100],
                autoWidth: false,
                order: [[1, 'desc']],
                columnDefs: [
                    { targets: [5, 9, 10], visible: false },
                    { orderable: false, targets: [0, 12] }
                ],
                dom: 'lrtfip',
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

                    // Renumber rows
                    api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1 + api.page.info().start;
                    });

                    // Hanya jalankan untuk desktop
                    if (isMobile()) return;

                    const w = $('#pnb-table_wrapper');

                    // Pindahkan info & pagination ke kontainer kustom hanya sekali saat render pertama
                    if ($('#pnb-paginate').children().length === 0) {
                        $('#pnb-info').append(w.find('.dataTables_info'));
                        $('#pnb-paginate').append(w.find('.dataTables_paginate'));
                    }
                }
            });

            // Pindahkan length + search ke toolbar (move biasa, tidak perlu clone)
            const wrapper = $('#pnb-table_wrapper');
            $('#pnb-length-control').empty().append(wrapper.find('.dataTables_length'));
            $('#pnb-search-control').empty().append(wrapper.find('.dataTables_filter'));

            // Sembunyikan sisa kontrol di dalam wrapper
            wrapper.find('.dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter').hide();

            // Hapus teks label "Search:" bawaan DataTables
            $('#pnb-search-control').find('.dataTables_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();

            /* ─────────────────────────────────────────
               Mobile card search + filter + pagination
            ───────────────────────────────────────── */
            let cardDateFrom = '', cardDateTo = '';
            let cardSearchQuery = '';
            const PAGE_SIZE = 15;
            let cardPage = 1;

            const allCards = Array.from(document.querySelectorAll('#pnb-cards .pnb-card'));

            function filterCards() {
                const q = cardSearchQuery.toLowerCase().trim();
                return allCards.filter(card => {
                    const searchText = card.getAttribute('data-search') || '';
                    if (q && !searchText.includes(q)) return false;
                    const dateVal = card.getAttribute('data-date') || '';
                    if (cardDateFrom || cardDateTo) {
                        if (!dateVal) return false;
                        const d = new Date(dateVal + 'T00:00:00');
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
                const end   = start + PAGE_SIZE;

                allCards.forEach(c => c.style.display = 'none');
                visible.slice(start, end).forEach(c => c.style.display = '');

                // Info
                const infoEl = document.getElementById('pnb-cards-info');
                if (total === 0) {
                    infoEl.textContent = 'Data tidak ditemukan';
                } else {
                    const s = Math.min(start + 1, total), e = Math.min(end, total);
                    infoEl.textContent = `Data ${s}–${e} dari ${total}`;
                }

                // Pagination
                const paginateEl = document.getElementById('pnb-cards-paginate');
                paginateEl.innerHTML = '';
                if (totalPages <= 1) return;

                const mkBtn = (label, page, disabled, active) => {
                    const btn = document.createElement('button');
                    btn.textContent = label;
                    btn.className = 'paginate_button' + (active ? ' current' : '') + (disabled ? ' disabled' : '');
                    btn.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:6px;font-size:0.8125rem;border:1px solid transparent;background:none;cursor:pointer;';
                    if (active)   { btn.style.background = '#2563eb'; btn.style.color = '#fff'; btn.style.borderColor = '#2563eb'; }
                    if (!disabled && !active) {
                        btn.addEventListener('click', () => {
                            cardPage = page;
                            renderCards();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                    return btn;
                };

                paginateEl.appendChild(mkBtn('‹', cardPage - 1, cardPage === 1, false));

                let pages = [];
                if (totalPages <= 7) {
                    for (let i = 1; i <= totalPages; i++) pages.push(i);
                } else {
                    pages = [1];
                    if (cardPage > 3) pages.push('…');
                    for (let i = Math.max(2, cardPage - 1); i <= Math.min(totalPages - 1, cardPage + 1); i++) pages.push(i);
                    if (cardPage < totalPages - 2) pages.push('…');
                    pages.push(totalPages);
                }

                pages.forEach(p => {
                    if (p === '…') {
                        const s = document.createElement('span');
                        s.textContent = '…';
                        s.style.cssText = 'display:inline-flex;align-items:center;padding:0 4px;font-size:0.8125rem;color:#9ca3af;';
                        paginateEl.appendChild(s);
                    } else {
                        paginateEl.appendChild(mkBtn(p, p, false, p === cardPage));
                    }
                });

                paginateEl.appendChild(mkBtn('›', cardPage + 1, cardPage === totalPages, false));
            }

            // Sync input search ke mobile cards
            const searchInput = document.querySelector('#pnb-search-control input');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    cardSearchQuery = this.value;
                    cardPage = 1;
                    if (isMobile()) renderCards();
                });
            }

            /* ─────────────────────────────────────────
               Date filter (shared desktop + mobile)
            ───────────────────────────────────────── */
            const initialLabel = document.getElementById('pnb-date-label').textContent;
            let dateFrom = '', dateTo = '';

            $.fn.dataTable.ext.search.push(function (settings, data, idx) {
                if (settings.nTable.id !== 'pnb-table') return true;
                if (!dateFrom && !dateTo) return true;
                const node = dt.row(idx).node();
                const val  = node?.querySelector('[data-date]')?.getAttribute('data-date') || '';
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
                return isNaN(d.getTime()) ? '' : d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }

            function updateLabel() {
                const el = document.getElementById('pnb-date-label');
                el.textContent = (dateFrom || dateTo)
                    ? (fmtDate(dateFrom) || 'Awal') + ' – ' + (fmtDate(dateTo) || 'Akhir')
                    : initialLabel;
            }

            /* ─────────────────────────────────────────
               Panel toggle helpers
            ───────────────────────────────────────── */
            const backdrop = document.getElementById('pnb-backdrop');

            function openPanel(id) {
                ['pnb-date-panel', 'pnb-col-panel'].forEach(p => {
                    const el = document.getElementById(p);
                    if (p === id) el.classList.toggle('hidden');
                    else el.classList.add('hidden');
                });
                const anyOpen = !document.getElementById(id).classList.contains('hidden');
                if (isMobile()) backdrop.style.display = anyOpen ? 'block' : 'none';
            }

            function closeAllPanels() {
                ['pnb-date-panel', 'pnb-col-panel'].forEach(id => {
                    document.getElementById(id).classList.add('hidden');
                });
                backdrop.style.display = 'none';
            }

            document.getElementById('pnb-date-trigger').addEventListener('click', e => { e.stopPropagation(); openPanel('pnb-date-panel'); });
            document.getElementById('pnb-col-trigger')?.addEventListener('click', e => { e.stopPropagation(); openPanel('pnb-col-panel'); });
            backdrop.addEventListener('click', closeAllPanels);

            document.getElementById('pnb-date-apply').addEventListener('click', function () {
                dateFrom = cardDateFrom = document.getElementById('pnb-date-from').value;
                dateTo   = cardDateTo   = document.getElementById('pnb-date-to').value;
                updateLabel();
                if (isMobile()) { cardPage = 1; renderCards(); }
                else { dt.draw(); }
                closeAllPanels();
            });

            document.getElementById('pnb-date-reset').addEventListener('click', function () {
                dateFrom = cardDateFrom = dateTo = cardDateTo = '';
                document.getElementById('pnb-date-from').value = '';
                document.getElementById('pnb-date-to').value   = '';
                updateLabel();
                if (isMobile()) { cardPage = 1; renderCards(); }
                else { dt.draw(); }
                closeAllPanels();
            });

            /* ─────────────────────────────────────────
               Column toggles (desktop)
            ───────────────────────────────────────── */
            document.querySelectorAll('[data-col]').forEach(btn => {
                const col = dt.column(parseInt(btn.getAttribute('data-col')));
                btn.classList.toggle('active', col.visible());
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const c = dt.column(parseInt(this.getAttribute('data-col')));
                    c.visible(!c.visible());
                    this.classList.toggle('active', c.visible());
                });
            });

            // Tutup panel saat klik di luar
            document.addEventListener('click', function (e) {
                ['pnb-date-panel', 'pnb-col-panel'].forEach(id => {
                    const el = document.getElementById(id);
                    const triggerId = id === 'pnb-date-panel' ? 'pnb-date-trigger' : 'pnb-col-trigger';
                    const triggerEl = document.getElementById(triggerId);
                    if (!el.contains(e.target) && triggerEl && !triggerEl.contains(e.target)) {
                        el.classList.add('hidden');
                    }
                });
                const dateOpen = !document.getElementById('pnb-date-panel').classList.contains('hidden');
                const colOpen  = !document.getElementById('pnb-col-panel').classList.contains('hidden');
                if (!dateOpen && !colOpen) backdrop.style.display = 'none';
            });

            /* ─────────────────────────────────────────
               Initial render + resize handler
            ───────────────────────────────────────── */
            if (isMobile()) renderCards();

            let wasMobile = isMobile();
            window.addEventListener('resize', () => {
                const nowMobile = isMobile();
                if (nowMobile !== wasMobile) {
                    wasMobile = nowMobile;
                    if (nowMobile) renderCards();
                }
            });
        });
        </script>
    @endpush
</x-app-layout>