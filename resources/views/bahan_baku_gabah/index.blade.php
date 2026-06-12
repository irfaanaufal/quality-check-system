<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <style>
            @media (max-width: 640px) {
                #bahan-baku-gabah-table_wrapper {
                    overflow-x: auto;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_length,
                #bahan-baku-gabah-table_wrapper .dataTables_filter {
                    width: 100%;
                    float: none;
                    text-align: left;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_length label,
                #bahan-baku-gabah-table_wrapper .dataTables_filter label {
                    width: 100%;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_length select,
                #bahan-baku-gabah-table_wrapper .dataTables_filter input {
                    width: 100% !important;
                    margin-left: 0 !important;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_filter {
                    margin-top: 0.5rem;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_info,
                #bahan-baku-gabah-table_wrapper .dataTables_paginate {
                    float: none;
                    text-align: center;
                }

                #bahan-baku-gabah-table_wrapper .dataTables_paginate {
                    margin-top: 0.5rem;
                }

                #bahan-baku-gabah-table,
                #bahan-baku-gabah-table.dataTable {
                    min-width: 980px;
                }
            }

            @media (min-width: 641px) {
                #bahan-baku-gabah-table_wrapper .dataTables_scrollHead,
                #bahan-baku-gabah-table_wrapper .dataTables_scrollBody {
                    overflow: visible !important;
                }

                #bahan-baku-gabah-table,
                #bahan-baku-gabah-table.dataTable {
                    min-width: 0 !important;
                }
            }

            .bahan-baku-gabah-cell-content {
                min-width: 0;
            }

            .bahan-baku-gabah-clamp-2 {
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                white-space: normal;
                word-break: break-word;
                line-height: 1.25rem;
                max-height: 2.5rem;
            }

            #bahan-baku-gabah-table_wrapper {
                width: 100% !important;
            }

            #bahan-baku-gabah-table,
            #bahan-baku-gabah-table.dataTable {
                width: 100% !important;
            }

            #bahan-baku-gabah-table thead th,
            #bahan-baku-gabah-table tbody td {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #bahan-baku-gabah-table thead th {
                font-size: 0.7rem;
            }

            #bahan-baku-gabah-table tbody td {
                font-size: 0.8rem;
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-md text-gray-800 leading-tight">
                    {{ __('Bahan Baku Gabah') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="">
        <div class="px-2 py-3 sm:px-4 sm:py-4">
            <div class="flex flex-col gap-3 pb-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex w-full flex-wrap items-center gap-2 lg:w-auto">
                    <button id="bahan-baku-gabah-date-label-trigger" type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 shadow-sm transition hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linecap="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z" />
                        </svg>
                        <span id="bahan-baku-gabah-date-label">Tanggal</span>
                    </button>

                    <div id="bahan-baku-gabah-date-filter-panel" class="hidden absolute z-40 mt-14 rounded-md border border-gray-300 bg-white p-3 shadow-lg">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div>
                                <label for="bahan-baku-gabah-date-from" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Dari</label>
                                <input id="bahan-baku-gabah-date-from" type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="bahan-baku-gabah-date-to" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Sampai</label>
                                <input id="bahan-baku-gabah-date-to" type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-end gap-2">
                            <button id="bahan-baku-gabah-date-reset" type="button" class="inline-flex h-9 items-center rounded-md border border-gray-300 bg-white px-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Reset</button>
                            <button id="bahan-baku-gabah-date-apply" type="button" class="inline-flex h-9 items-center rounded-md bg-blue-600 px-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Terapkan</button>
                        </div>
                    </div>

                    <div id="bahan-baku-gabah-length-control" class="inline-flex items-center"></div>
                </div>

                <div class="flex w-full items-center gap-2 flex-nowrap lg:w-auto lg:justify-end">
                    <div id="bahan-baku-gabah-search-control" class="inline-flex min-w-0 flex-1 items-center"></div>

                    <div class="relative shrink-0">
                        <button id="bahan-baku-gabah-column-filter-trigger" type="button"
                                class="inline-flex h-10 w-auto items-center justify-center gap-2 rounded-md bg-blue-600 px-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
                                aria-label="Filter data kolom">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linecap="round" d="M12 3c-1.755 0-3.375.328-4.5.889-1.125.56-1.875 1.34-1.875 2.236 0 .895.75 1.675 1.875 2.236C8.625 8.922 10.245 9.25 12 9.25s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236 0-.895-.75-1.675-1.875-2.236C15.375 3.328 13.755 3 12 3zM6.375 8.25v3c0 .895.75 1.675 1.875 2.236 1.125.56 2.745.889 4.5.889s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236v-3M6.375 13.5v3c0 .895.75 1.675 1.875 2.236 1.125.56 2.745.889 4.5.889s3.375-.328 4.5-.889c1.125-.56 1.875-1.34 1.875-2.236v-3" />
                            </svg>
                        </button>

                        <div id="bahan-baku-gabah-column-filter-panel" class="hidden absolute right-0 top-full z-40 mt-2 w-[210px] rounded-md border border-gray-500 bg-white p-2 shadow-lg">
                            <div class="space-y-1.5">
                                <button type="button" data-column-toggle="2" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">No Penerimaan</button>
                                <button type="button" data-column-toggle="3" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Tanggal</button>
                                <button type="button" data-column-toggle="4" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Supplier</button>
                                <button type="button" data-column-toggle="5" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Jenis</button>
                                <button type="button" data-column-toggle="6" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">No Polisi</button>
                                <button type="button" data-column-toggle="7" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Berat</button>
                                <button type="button" data-column-toggle="8" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Keputusan</button>
                                <button type="button" data-column-toggle="9" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Harga</button>
                                <button type="button" data-column-toggle="10" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Harga Rata</button>
                                <button type="button" data-column-toggle="11" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Keterangan</button>
                                <button type="button" data-column-toggle="12" class="bahan-baku-gabah-column-option w-full rounded-md border border-gray-500 bg-gray-200 px-3 py-1.5 text-left text-sm text-gray-800">Status</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-md border border-gray-300 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
                <div class="min-h-[560px] overflow-x-hidden px-2 py-2 sm:px-4 sm:py-4">
                    <div class="w-full">
                        <table id="bahan-baku-gabah-table" class="w-full table-fixed divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="w-[36px] px-1 py-3">No</th>
                                    <th class="w-[72px] px-1 py-3">ID</th>
                                    <th class="w-[100px] px-1 py-3">No Penerimaan</th>
                                    <th class="w-[80px] px-1 py-3">Tanggal</th>
                                    <th class="w-[120px] px-1 py-3">Supplier</th>
                                    <th class="w-[72px] px-1 py-3">Jenis</th>
                                    <th class="w-[92px] px-1 py-3">No Polisi</th>
                                    <th class="w-[78px] px-1 py-3">Berat</th>
                                    <th class="w-[90px] px-1 py-3">Keputusan</th>
                                    <th class="w-[100px] px-1 py-3">Harga</th>
                                    <th class="w-[100px] px-1 py-3">Harga Rata</th>
                                    <th class="w-[150px] px-1 py-3">Keterangan</th>
                                    <th class="w-[76px] px-1 py-3">Status</th>
                                    <th class="w-[120px] px-1 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($gabah as $item)
                                    <tr class="align-top text-gray-700 hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-1 py-2">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap px-1 py-2">{{ $item->idb_gabah }}</td>
                                        @php
                                            $tanggal = filled($item->tanggal)
                                                ? \Illuminate\Support\Carbon::parse($item->tanggal)
                                                : null;
                                            $itemStatus = strtolower((string) ($item->status ?? ''));
                                        @endphp
                                        <td class="px-1 py-2 align-top"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->no_penerimaan ?? '-' }}</div></td>
                                        <td class="whitespace-nowrap px-1 py-2" data-date="{{ $tanggal?->format('Y-m-d') }}" data-order="{{ $tanggal?->format('Y-m-d') }}">{{ $tanggal?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-1 py-2 align-top"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->supplier ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->jenis ?? '-' }}</div></td>
                                        <td class="px-1 py-2 align-top"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->nopol ?? '-' }}</div></td>
                                        <td class="whitespace-nowrap px-1 py-2">{{ is_numeric($item->berat) ? number_format((int) $item->berat, 0, ',', '.') . ' Kg' : '-' }}</td>
                                        <td class="px-1 py-2 align-top"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->keputusan ?? '-' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-1 py-2">{{ is_numeric($item->harga) ? 'Rp ' . number_format((int) $item->harga, 0, ',', '.') : '-' }}</td>
                                        <td class="whitespace-nowrap px-1 py-2">{{ is_numeric($item->harga_rata) ? 'Rp ' . number_format((int) $item->harga_rata, 0, ',', '.') : '-' }}</td>
                                        <td class="px-1 py-2 align-top text-gray-600"><div class="bahan-baku-gabah-cell-content bahan-baku-gabah-clamp-2">{{ $item->keterangan ?? '-' }}</div></td>
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
                                        <td class="whitespace-nowrap px-1 py-2">
                                            <div class="flex items-center justify-end gap-2 text-gray-500">
                                                {{-- Update Harga --}}
                                                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'bahan-baku-gabah-update-harga-modal-{{ $item->idb_gabah }}')" class="inline-flex items-center justify-center transition hover:text-blue-700" title="Update Harga">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linecap="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                                {{-- Print --}}
                                                <a href="{{ route('bahan_baku_gabah.print', $item->idb_gabah) }}" target="_blank" class="inline-flex items-center justify-center transition hover:text-gray-700" title="Print">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linecap="round" d="M6 9V3a1 1 0 011-1h10a1 1 0 011 1v6m-12 0h12M5 9v9a2 2 0 002 2h10a2 2 0 002-2V9M7 15h10" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Update Harga Modal --}}
                                    <x-modal name="bahan-baku-gabah-update-harga-modal-{{ $item->idb_gabah }}" focusable>
                                        <form method="POST" action="{{ route('bahan_baku_gabah.update_harga', $item->idb_gabah) }}" class="p-6">
                                            @csrf
                                            @method('PUT')
                                            <h2 class="text-lg font-medium text-gray-900 mb-4">Update Harga</h2>
                                            <div class="mt-4">
                                                <x-input-label for="no-penerimaan-{{ $item->idb_gabah }}" :value="'No Penerimaan'" />
                                                <x-text-input id="no-penerimaan-{{ $item->idb_gabah }}" type="text" class="mt-1 block w-full bg-gray-100" value="{{ $item->no_penerimaan }}" disabled />
                                            </div>
                                            <div class="mt-4">
                                                <x-input-label for="harga-{{ $item->idb_gabah }}" :value="'Harga'" />
                                                <x-text-input id="harga-{{ $item->idb_gabah }}" name="harga" type="number" class="mt-1 block w-full" value="{{ $item->harga }}" required />
                                            </div>
                                            <div class="mt-6 flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">
                                                    Batal
                                                </x-secondary-button>
                                                <x-primary-button class="ml-3">
                                                    Simpan
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @empty
                                    <tr>
                                        <td colspan="14" class="px-3 py-16 text-center text-sm text-gray-500">
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

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#bahan-baku-gabah-table');

                if (!table.length) {
                    return;
                }

                const dataTable = table.DataTable({
                    pageLength: 15,
                    lengthMenu: [15, 25, 50, 100],
                    autoWidth: false,
                    order: [[1, 'desc']],
                    columnDefs: [
                        { targets: [1], visible: false },
                        { orderable: false, targets: [0, 13] }
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
                const dateLabelTrigger = document.getElementById('bahan-baku-gabah-date-label-trigger');
                const dateFilterPanel = document.getElementById('bahan-baku-gabah-date-filter-panel');
                const dateLabelText = document.getElementById('bahan-baku-gabah-date-label');
                const dateFromInput = document.getElementById('bahan-baku-gabah-date-from');
                const dateToInput = document.getElementById('bahan-baku-gabah-date-to');
                const dateApplyButton = document.getElementById('bahan-baku-gabah-date-apply');
                const dateResetButton = document.getElementById('bahan-baku-gabah-date-reset');
                const columnFilterTrigger = document.getElementById('bahan-baku-gabah-column-filter-trigger');
                const columnFilterPanel = document.getElementById('bahan-baku-gabah-column-filter-panel');
                const columnToggleButtons = Array.from(document.querySelectorAll('.bahan-baku-gabah-column-option'));

                let activeDateFrom = '';
                let activeDateTo = '';
                const initialDateLabel = dateLabelText.textContent;

                const formatDateLabel = (dateString) => {
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

                const updateDateLabel = () => {
                    if (!activeDateFrom && !activeDateTo) {
                        dateLabelText.textContent = initialDateLabel;
                        return;
                    }

                    const fromLabel = activeDateFrom ? formatDateLabel(activeDateFrom) : 'Awal';
                    const toLabel = activeDateTo ? formatDateLabel(activeDateTo) : 'Akhir';

                    dateLabelText.textContent = fromLabel + ' - ' + toLabel;
                };

                const syncColumnToggleState = (button) => {
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
                    if (settings.nTable.id !== 'bahan-baku-gabah-table') {
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

                $('#bahan-baku-gabah-length-control').empty().append(lengthControl);
                $('#bahan-baku-gabah-search-control').empty().append(searchControl);

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
