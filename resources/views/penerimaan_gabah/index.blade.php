<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <style>
            @media (max-width: 640px) {
                #penerimaan-gabah-table_wrapper {
                    overflow-x: auto;
                }

                #penerimaan-gabah-table_wrapper .dataTables_length,
                #penerimaan-gabah-table_wrapper .dataTables_filter {
                    width: 100%;
                    float: none;
                    text-align: left;
                }

                #penerimaan-gabah-table_wrapper .dataTables_length label,
                #penerimaan-gabah-table_wrapper .dataTables_filter label {
                    width: 100%;
                }

                #penerimaan-gabah-table_wrapper .dataTables_length select,
                #penerimaan-gabah-table_wrapper .dataTables_filter input {
                    width: 100% !important;
                    margin-left: 0 !important;
                }

                #penerimaan-gabah-table_wrapper .dataTables_filter {
                    margin-top: 0.5rem;
                }

                #penerimaan-gabah-table_wrapper .dataTables_info,
                #penerimaan-gabah-table_wrapper .dataTables_paginate {
                    float: none;
                    text-align: center;
                }

                #penerimaan-gabah-table_wrapper .dataTables_paginate {
                    margin-top: 0.5rem;
                }

                #penerimaan-gabah-table,
                #penerimaan-gabah-table.dataTable {
                    min-width: 980px;
                }
            }

            @media (min-width: 641px) {
                #penerimaan-gabah-table_wrapper .dataTables_scrollHead,
                #penerimaan-gabah-table_wrapper .dataTables_scrollBody {
                    overflow: visible !important;
                }

                #penerimaan-gabah-table,
                #penerimaan-gabah-table.dataTable {
                    min-width: 0 !important;
                }
            }

            .penerimaan-gabah-cell-content {
                min-width: 0;
            }

            .penerimaan-gabah-clamp-2 {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                white-space: normal;
                word-break: break-word;
                line-height: 1.25rem;
                max-height: 2.5rem;
            }

            #penerimaan-gabah-table_wrapper {
                width: 100% !important;
            }

            #penerimaan-gabah-table,
            #penerimaan-gabah-table.dataTable {
                width: 100% !important;
            }

            #penerimaan-gabah-table thead th,
            #penerimaan-gabah-table tbody td {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #penerimaan-gabah-table thead th {
                font-size: 0.7rem;
            }

            #penerimaan-gabah-table tbody td {
                font-size: 0.8rem;
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-md text-gray-800 leading-tight">
                    {{ __('Penerimaan Gabah') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="">
        <div class="px-2 py-3 sm:px-4 sm:py-4">
            <div class="flex flex-col gap-3 pb-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex w-full flex-wrap items-center gap-2 lg:w-auto">
                    <button id="penerimaan-gabah-date-label-trigger" type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 shadow-sm transition hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z" />
                        </svg>
                        <span id="penerimaan-gabah-date-label">{{ $dateLabel }}</span>
                    </button>

                    <div id="penerimaan-gabah-date-filter-panel" class="hidden absolute z-40 mt-14 rounded-md border border-gray-300 bg-white p-3 shadow-lg">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div>
                                <label for="penerimaan-gabah-date-from" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Dari</label>
                                <input id="penerimaan-gabah-date-from" type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="penerimaan-gabah-date-to" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Sampai</label>
                                <input id="penerimaan-gabah-date-to" type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-end gap-2">
                            <button id="penerimaan-gabah-date-reset" type="button" class="inline-flex h-9 items-center rounded-md border border-gray-300 bg-white px-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Reset</button>
                            <button id="penerimaan-gabah-date-apply" type="button" class="inline-flex h-9 items-center rounded-md bg-blue-600 px-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Terapkan</button>
                        </div>
                    </div>

                    <div id="penerimaan-gabah-length-control" class="inline-flex items-center"></div>
                </div>

                <div class="flex w-full items-center gap-2 flex-nowrap lg:w-auto lg:justify-end">
                    <div id="penerimaan-gabah-search-control" class="inline-flex min-w-0 flex-1 items-center"></div>

                    <div class="relative shrink-0">
                        <button id="penerimaan-gabah-column-filter-trigger" type="button"
                                class="inline-flex h-10 w-auto items-center justify-center gap-2 rounded-md bg-blue-600 px-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
                                aria-label="Filter data kolom">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1.755 0-3.375.328-4.5.889-1.125.56-1.875 1.34-1.875 2.236 0 .895.75 1.675 1.875 2.236C8.625 8.922 10.245 9.25 12 9.25s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236 0-.895-.75-1.675-1.875-2.236C15.375 3.328 13.755 3 12 3zM6.375 8.25v3c0 .895.75 1.675 1.875 2.236 1.125.56 2.745.889 4.5.889s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236v-3M6.375 13.5v3c0 .895.75 1.675 1.875 2.236 1.125.56 2.745.889 4.5.889s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236v-3" />
                            </svg>
                        </button>

                        <div id="penerimaan-gabah-column-filter-panel" class="hidden absolute right-0 top-full z-40 mt-2 w-[210px] rounded-md border border-gray-500 bg-white p-2 shadow-lg">
                            <div class="space-y-1.5">
                                <button type="button" data-column-toggle="1" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Tanggal</button>
                                <button type="button" data-column-toggle="2" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Supplier</button>
                                <button type="button" data-column-toggle="3" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Lokasi</button>
                                <button type="button" data-column-toggle="4" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Jenis</button>
                                <button type="button" data-column-toggle="5" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Tonase</button>
                                <button type="button" data-column-toggle="6" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">No Polisi</button>
                                <button type="button" data-column-toggle="7" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Status</button>
                                <button type="button" data-column-toggle="8" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">User Input</button>
                                <button type="button" data-column-toggle="9" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">User Validasi</button>
                                <button type="button" data-column-toggle="10" class="penerimaan-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Keterangan</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'penerimaan-gabah-create-modal' }))"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-emerald-600 text-white shadow-sm transition hover:bg-emerald-700"
                            aria-label="Tambah data">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-md border border-gray-300 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                <div class="min-h-[560px] overflow-x-hidden px-2 py-2 sm:px-4 sm:py-4">
                    <div class="w-full">
                        <table id="penerimaan-gabah-table" class="w-full table-fixed divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="w-[36px] px-1 py-3">No</th>
                                    <th class="w-[72px] px-1 py-3">Tanggal</th>
                                    <th class="w-[140px] px-1 py-3">Supplier</th>
                                    <th class="w-[120px] px-1 py-3">Lokasi</th>
                                    <th class="w-[72px] px-1 py-3">Jenis</th>
                                    <th class="w-[78px] px-1 py-3">Tonase</th>
                                    <th class="w-[92px] px-1 py-3">No Polisi</th>
                                    <th class="w-[76px] px-1 py-3">Status</th>
                                    <th class="w-[108px] px-1 py-3">User Input</th>
                                    <th class="w-[108px] px-1 py-3">User Validasi</th>
                                    <th class="w-[150px] px-1 py-3">Keterangan</th>
                                    <th class="w-[120px] px-1 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @php
                                    $currentUser = auth()->user()?->username ?: auth()->user()?->name;
                                @endphp
                                @forelse ($items as $item)
                                    <tr class="align-top text-gray-700 hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-1 py-2">{{ $loop->iteration }}</td>
                                        @php
                                            $tanggalTerima = filled($item->tanggal_terima)
                                                ? \Illuminate\Support\Carbon::parse($item->tanggal_terima)
                                                : null;
                                            $itemStatus = strtolower((string) ($item->status ?? ''));
                                            $isCreator = $currentUser === $item->user_created;
                                        @endphp
                                        <td class="whitespace-nowrap px-1 py-2" data-date="{{ $tanggalTerima?->format('Y-m-d') }}" data-order="{{ $tanggalTerima?->format('Y-m-d') }}">{{ $tanggalTerima?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->supplier_name ?? $item->nama_supplier ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->tempat_simpan ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->varietas_alias ?? '-' }}</div></td>
                                        <td class="whitespace-nowrap px-1 py-2">{{ is_numeric($item->tonase) ? number_format((int) round((float) $item->tonase), 0, ',', '.') . ' Kg' : '-' }}</td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->nopol ?? '-' }}</div></td>
                                        <td class="whitespace-nowrap px-1 py-2">
                                            @php
                                                $statusClass = match (strtolower((string)($item->status ?? ''))) {
                                                    'proses' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10',
                                                    'menunggu validasi' => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20',
                                                    'finish' => 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10',
                                                    'checked' => 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20',
                                                    'approved', 'approve' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                                                    default => 'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                                {{ $item->status ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->user_created ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->user_finish ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top text-gray-600"><div class="penerimaan-gabah-cell-content penerimaan-gabah-clamp-2">{{ $item->keterangan ?? '-' }}</div></td>
                                        <td class="whitespace-nowrap px-1 py-2">
                                            @php
                                                $isSuperAdmin = auth()->user()->hasRole('super admin');
                                                $isAdmin = auth()->user()->hasRole('admin');
                                                $isAdminOrSuper = $isSuperAdmin || $isAdmin;

                                                // Inisialisasi status visibilitas ikon aksi sesuai matriks hak akses
                                                $showTimbang = false;
                                                $showTimbangReadOnly = false;
                                                $showValidasi = false;
                                                $showPembagian = false;
                                                $showPembagianReadOnly = false;
                                                $showUnapprove = false;
                                                $showApprove = false;
                                                $showUncheck = false;
                                                $showEdit = false;
                                                $showHapus = false;

                                                if ($itemStatus === 'proses') {
                                                    // Timbang: pembuat data, admin, super admin
                                                    $showTimbang = $isCreator || $isAdminOrSuper;
                                                    // Edit: semua role
                                                    $showEdit = true;
                                                    // Hapus: tidak ada
                                                    $showHapus = false;
                                                } elseif ($itemStatus === 'menunggu validasi') {
                                                    // Validasi: selain pembuat data (User Lain, Admin, Super Admin)
                                                    $showValidasi = !$isCreator || $isAdminOrSuper;
                                                    // Timbangan (Read Only): semua role
                                                    $showTimbangReadOnly = true;
                                                } elseif ($itemStatus === 'finish') {
                                                    // Pembagian: selain pembuat data (User Lain, Admin, Super Admin)
                                                    $showPembagian = !$isCreator || $isAdminOrSuper;
                                                    // Timbangan (Read Only): semua role
                                                    $showTimbangReadOnly = true;
                                                } elseif ($itemStatus === 'checked') {
                                                    // Pembagian (Read Only): semua role
                                                    $showPembagianReadOnly = true;
                                                    // Timbangan (Read Only): semua role
                                                    $showTimbangReadOnly = true;
                                                    // Uncheck & Approve: Admin dan Super Admin
                                                    $showUncheck = $isAdminOrSuper;
                                                    $showApprove = $isAdminOrSuper;
                                                } elseif (in_array($itemStatus, ['approved', 'approve'])) {
                                                    // Pembagian (Read Only): semua role
                                                    $showPembagianReadOnly = true;
                                                    // Timbangan (Read Only): semua role
                                                    $showTimbangReadOnly = true;
                                                    // Unapprove: Admin dan Super Admin
                                                    $showUnapprove = $isAdminOrSuper;
                                                }
                                            @endphp
                                            <div class="flex items-center justify-end gap-2 text-gray-500">
                                                {{-- TIMBANG (ACTIVE) --}}
                                                @if ($showTimbang)
                                                    <a href="{{ route('timbangan-gabah.create', ['terima_bb_id' => $item->id]) }}" class="inline-flex items-center justify-center transition hover:text-blue-700" title="Timbang">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- TIMBANGAN (READ ONLY) --}}
                                                @if ($showTimbangReadOnly)
                                                    <a href="{{ route('timbangan-gabah.create', ['terima_bb_id' => $item->id]) }}" class="inline-flex items-center justify-center text-slate-400 hover:text-slate-600 transition" title="Timbangan (Read Only)">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M8.25 7.5h7.5M6 7.5l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zm12 0l-3 6a1.5 1.5 0 001.5 2.25h3a1.5 1.5 0 001.5-2.25l-3-6zM5.25 20.25h13.5" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- VALIDASI (ACTIVE) --}}
                                                @if ($showValidasi)
                                                    <form method="POST" action="{{ route('timbangan-gabah.validate', $item->id) }}" class="inline-flex m-0 p-0" onsubmit="return confirm('Yakin ingin memvalidasi data ini?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center transition hover:text-green-600" title="Validasi">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0a9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- PEMBAGIAN (ACTIVE) --}}
                                                @if ($showPembagian)
                                                    <a href="{{ route('pembagian_gabah.create', ['terima_bb_id' => $item->id]) }}" class="inline-flex items-center justify-center transition hover:text-yellow-600" title="Lanjut Pembagian Gabah">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- PEMBAGIAN (READ ONLY) --}}
                                                @if ($showPembagianReadOnly)
                                                    <a href="{{ route('pembagian_gabah.create', ['terima_bb_id' => $item->id]) }}" class="inline-flex items-center justify-center text-slate-400 hover:text-slate-600 transition" title="Pembagian (Read Only)">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12M6 20h12M8 4v2.5c0 2.485 1.955 4.5 4.356 4.5 2.4 0 4.344-2.015 4.344-4.5V4M8 20v-2.5c0-2.485 1.955-4.5 4.356-4.5 2.4 0 4.344 2.015 4.344 4.5V20" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- UNAPPROVE (ACTIVE) --}}
                                                @if ($showUnapprove)
                                                    <form method="POST" action="{{ route('penerimaan_gabah.unapprove', $item->id) }}" class="inline-flex m-0 p-0" onsubmit="return confirm('Yakin ingin membatalkan approval data ini?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center transition hover:text-orange-600" title="Unapprove">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- APPROVE (ACTIVE) --}}
                                                @if ($showApprove)
                                                    <form method="POST" action="{{ route('penerimaan_gabah.approve', $item->id) }}" class="inline-flex m-0 p-0" onsubmit="return confirm('Yakin ingin menyetujui (approve) data ini?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center transition hover:text-emerald-600" title="Approve">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- UNCHECK (ACTIVE) --}}
                                                @if ($showUncheck)
                                                    <form method="POST" action="{{ route('penerimaan_gabah.uncheck', $item->id) }}" class="inline-flex m-0 p-0" onsubmit="return confirm('Yakin ingin membatalkan check data ini?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center transition hover:text-amber-600" title="Batalkan Check">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- EDIT (ACTIVE) --}}
                                                @if ($showEdit)
                                                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'penerimaan-gabah-edit-modal-{{ $item->id }}')" class="inline-flex items-center justify-center transition hover:text-amber-600" title="Edit">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487z" />
                                                        </svg>
                                                    </button>
                                                    @include('penerimaan_gabah.edit', ['item' => $item])
                                                @endif

                                                {{-- HAPUS (ACTIVE) --}}
                                                @if ($showHapus)
                                                    <form method="POST" action="{{ route('penerimaan_gabah.destroy', $item->id) }}" class="inline-flex m-0 p-0" onsubmit="return confirm('Yakin hapus penerimaan gabah ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center justify-center transition hover:text-red-600" title="Hapus">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-10.5 0V6.375A1.125 1.125 0 018.625 5.25h6.75a1.125 1.125 0 011.125 1.125V7.5m-9 0 1.05 11.025A1.5 1.5 0 009.665 20.25h4.67a1.5 1.5 0 001.495-1.725L16.5 7.5M10.5 11.25v4.5m3-4.5v4.5" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="12" class="px-3 py-16 text-center text-sm text-gray-500">
                                        Data belum tersedia.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('penerimaan_gabah.create')

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#penerimaan-gabah-table');

                if (!table.length) {
                    return;
                }

                const dataTable = table.DataTable({
                    pageLength: 15,
                    lengthMenu: [15, 25, 50, 100],
                    autoWidth: false,
                    order: [[1, 'desc']],
                    columnDefs: [
                        { targets: [3, 5, 6, 10], visible: false },
                        { orderable: false, targets: [0, 11] }
                    ],
                    drawCallback: function () {
                        const api = this.api();

                        api.column(0, { page: 'current' }).nodes().each(function (cell, index) {
                            cell.innerHTML = index + 1 + api.page.info().start;
                        });
                    },
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Data tidak tersedia',
                        zeroRecords: 'Data tidak ditemukan',
                        paginate: {
                            first: 'Awal',
                            last: 'Akhir',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        }
                    }
                });

                const wrapper = table.closest('.dataTables_wrapper');
                const lengthControl = wrapper.find('.dataTables_length');
                const searchControl = wrapper.find('.dataTables_filter');
                const dateLabelTrigger = document.getElementById('penerimaan-gabah-date-label-trigger');
                const dateFilterPanel = document.getElementById('penerimaan-gabah-date-filter-panel');
                const dateLabelText = document.getElementById('penerimaan-gabah-date-label');
                const dateFromInput = document.getElementById('penerimaan-gabah-date-from');
                const dateToInput = document.getElementById('penerimaan-gabah-date-to');
                const dateApplyButton = document.getElementById('penerimaan-gabah-date-apply');
                const dateResetButton = document.getElementById('penerimaan-gabah-date-reset');
                const columnFilterTrigger = document.getElementById('penerimaan-gabah-column-filter-trigger');
                const columnFilterPanel = document.getElementById('penerimaan-gabah-column-filter-panel');
                const columnToggleButtons = Array.from(document.querySelectorAll('.penerimaan-gabah-column-option'));

                let activeDateFrom = '';
                let activeDateTo = '';
                const initialDateLabel = dateLabelText.textContent;

                const formatDateLabel = function (dateString) {
                    if (!dateString) {
                        return '';
                    }

                    const date = new Date(dateString + 'T00:00:00');

                    if (Number.isNaN(date.getTime())) {
                        return '';
                    }

                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                };

                const updateDateLabel = function () {
                    if (!activeDateFrom && !activeDateTo) {
                        dateLabelText.textContent = initialDateLabel;
                        return;
                    }

                    const fromLabel = activeDateFrom ? formatDateLabel(activeDateFrom) : 'Awal';
                    const toLabel = activeDateTo ? formatDateLabel(activeDateTo) : 'Akhir';

                    dateLabelText.textContent = fromLabel + ' - ' + toLabel;
                };

                const syncColumnToggleState = function (button) {
                    const columnIndex = Number(button.getAttribute('data-column-toggle'));
                    const isVisible = dataTable.column(columnIndex).visible();

                    button.classList.toggle('bg-gray-200', isVisible);
                    button.classList.toggle('border-gray-500', isVisible);
                    button.classList.toggle('bg-white', !isVisible);
                    button.classList.toggle('border-gray-300', !isVisible);
                    button.classList.toggle('text-gray-800', isVisible);
                    button.classList.toggle('text-gray-500', !isVisible);
                };

                $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                    if (settings.nTable.id !== 'penerimaan-gabah-table') {
                        return true;
                    }

                    if (!activeDateFrom && !activeDateTo) {
                        return true;
                    }

                    const rowNode = dataTable.row(dataIndex).node();
                    const rowDateValue = rowNode?.querySelector('[data-date]')?.getAttribute('data-date') || '';

                    if (!rowDateValue) {
                        return false;
                    }

                    const rowDate = new Date(rowDateValue + 'T00:00:00');

                    if (Number.isNaN(rowDate.getTime())) {
                        return false;
                    }

                    if (activeDateFrom) {
                        const fromDate = new Date(activeDateFrom + 'T00:00:00');
                        if (rowDate < fromDate) {
                            return false;
                        }
                    }

                    if (activeDateTo) {
                        const toDate = new Date(activeDateTo + 'T23:59:59');
                        if (rowDate > toDate) {
                            return false;
                        }
                    }

                    return true;
                });

                $('#penerimaan-gabah-length-control').empty().append(lengthControl);
                $('#penerimaan-gabah-search-control').empty().append(searchControl);

                lengthControl.addClass('m-0 text-sm text-gray-700');
                lengthControl.find('label').addClass('inline-flex items-center gap-2');
                lengthControl.find('select').addClass('rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500');

                searchControl.addClass('m-0 text-sm text-gray-700');
                searchControl.find('label').addClass('inline-flex items-center gap-2');
                searchControl.find('input').addClass('h-10 w-full min-w-0 rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500');

                updateDateLabel();

                dateLabelTrigger.addEventListener('click', function (event) {
                    event.stopPropagation();
                    columnFilterPanel.classList.add('hidden');
                    dateFilterPanel.classList.toggle('hidden');
                });

                columnFilterTrigger.addEventListener('click', function (event) {
                    event.stopPropagation();
                    dateFilterPanel.classList.add('hidden');
                    columnFilterPanel.classList.toggle('hidden');
                });

                columnToggleButtons.forEach(function (button) {
                    syncColumnToggleState(button);

                    button.addEventListener('click', function (event) {
                        event.stopPropagation();

                        const columnIndex = Number(button.getAttribute('data-column-toggle'));
                        const column = dataTable.column(columnIndex);

                        column.visible(!column.visible());
                        syncColumnToggleState(button);
                    });
                });

                dateApplyButton.addEventListener('click', function () {
                    activeDateFrom = dateFromInput.value;
                    activeDateTo = dateToInput.value;
                    updateDateLabel();
                    dataTable.draw();
                    dateFilterPanel.classList.add('hidden');
                });

                dateResetButton.addEventListener('click', function () {
                    activeDateFrom = '';
                    activeDateTo = '';
                    dateFromInput.value = '';
                    dateToInput.value = '';
                    updateDateLabel();
                    dataTable.draw();
                    dateFilterPanel.classList.add('hidden');
                });

                document.addEventListener('click', function (event) {
                    if (!dateFilterPanel.contains(event.target) && !dateLabelTrigger.contains(event.target)) {
                        dateFilterPanel.classList.add('hidden');
                    }

                    if (!columnFilterPanel.contains(event.target) && !columnFilterTrigger.contains(event.target)) {
                        columnFilterPanel.classList.add('hidden');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
