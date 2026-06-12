@php
    $isApproved = in_array(strtolower(trim($terimaBg->status ?? '')), ['checked', 'approved', 'approve'], true);

    // Calculate total for unsorted data
    $unsortedTotalKarung = 0;
    $unsortedTotalTonase = 0;
    foreach ($unsortedData as $row) {
        $unsortedTotalKarung += (float) $row->jumlah_karung;
        $unsortedTotalTonase += (float) $row->tonase;
    }
@endphp

{{-- Render grouped data --}}
@foreach($groupedData as $sorting => $groupRows)
    @php
        $groupTotalKarung = 0;
        $groupTotalTonase = 0;
        foreach ($groupRows as $row) {
            $groupTotalKarung += (float) $row->jumlah_karung;
            $groupTotalTonase += (float) $row->tonase;
        }
    @endphp
    <div class="overflow-x-auto mb-4">
        <table class="w-full text-sm sorting-group-table" data-sorting-number="{{ $sorting }}">
            <thead>
                <tr class="sorting-group-header {{ $isApproved ? 'cursor-default' : 'cursor-pointer hover:bg-emerald-100 transition-colors' }}" @if(!$isApproved) data-sorting-number="{{ $sorting }}" @endif>
                    <th colspan="7" class="border-b border-slate-200 px-4 py-3 text-left text-emerald-800">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <span class="flex flex-wrap items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                                </svg>
                                Sorting Ke-{{ $sorting }} @if(!$isApproved) <span class="text-xs text-emerald-600">(klik untuk edit)</span> @endif
                            </span>
                            <span class="text-xs sm:text-sm">
                                Total: {{ number_format($groupTotalKarung, 0, ',', '.') }} Karung, {{ number_format($groupTotalTonase, 0, ',', '.') }} Kg
                            </span>
                        </div>
                    </th>
                </tr>
                <tr class="bg-slate-50">
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">No</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Timbang Ke</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">No Penerimaan</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Jml Karung</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Tonase</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Kadar Air</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupRows as $index => $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $index + 1 }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center font-medium text-slate-800">{{ $row->timbang_ke }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600 text-xs">{{ $row->no_penerimaan ?: '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $row->jumlah_karung ?: '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center font-semibold text-slate-800">{{ is_numeric($row->tonase) ? number_format((float) $row->tonase, 0, ',', '.') . ' Kg' : '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $row->kadar_air !== null && $row->kadar_air !== '' ? $row->kadar_air . '%' : '-' }}</td>
                        <td class="px-2 py-2 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                Sudah Diproses
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach

{{-- Render unsorted data --}}
@if(count($unsortedData) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50">
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">No</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Timbang Ke</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">No Penerimaan</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Jml Karung</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Tonase</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">Kadar Air</th>
                    <th class="border-b border-slate-200 px-2 py-3 text-center font-semibold text-slate-700">
                        <label class="flex flex-col items-center justify-center gap-1 cursor-pointer sm:flex-row sm:gap-2">
                            <input type="checkbox" id="selectAll" class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2" {{ $isApproved ? 'disabled' : '' }}>
                            <span class="text-xs sm:text-sm">Pilih Semua</span>
                        </label>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($unsortedData as $index => $row)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $index + 1 }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center font-medium text-slate-800">{{ $row->timbang_ke }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600 text-xs">{{ $row->no_penerimaan ?: '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $row->jumlah_karung ?: '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center font-semibold text-slate-800">{{ is_numeric($row->tonase) ? number_format((float) $row->tonase, 0, ',', '.') . ' Kg' : '-' }}</td>
                        <td class="border-r border-slate-100 px-2 py-2 text-center text-slate-600">{{ $row->kadar_air !== null && $row->kadar_air !== '' ? $row->kadar_air . '%' : '-' }}</td>
                        <td class="px-2 py-2 text-center">
                            <input type="checkbox" name="selected_rows[]" value="{{ $row->id }}" class="row-checkbox w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 cursor-pointer" data-tonase="{{ $row->tonase }}" {{ $isApproved ? 'disabled' : '' }}>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
