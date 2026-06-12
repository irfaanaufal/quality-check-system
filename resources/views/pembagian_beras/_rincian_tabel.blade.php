@php
    $isApproved = in_array(strtolower(trim($terimaBb->status ?? '')), ['checked', 'approved', 'approve'], true);
@endphp

{{-- Grouped (sudah sorting) --}}
@foreach($groupedData as $sorting => $groupRows)
    @php
        $gk = collect($groupRows)->sum(fn($r) => (float)$r->jumlah_karung);
        $gt = collect($groupRows)->sum(fn($r) => (float)$r->tonase);
    @endphp

    <table class="data-table">
        <thead>
            <tr class="sorting-header {{ $isApproved ? '' : 'cursor-pointer' }}"
                @if(!$isApproved) data-sorting-number="{{ $sorting }}" @endif>
                <td colspan="8">
                    <span style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                        <span style="display:flex;align-items:center;gap:6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002-2h2a2 2 0 002-2"/>
                            </svg>
                            Sorting Ke-{{ $sorting }}
                            @if(!$isApproved)
                                <span style="font-size:11px;color:#6ee7b7;font-weight:500;">(klik untuk edit)</span>
                            @endif
                        </span>
                        <span style="font-size:11px;font-weight:500;">
                            {{ number_format($gk, 0, ',', '.') }} Karung &bull; {{ number_format($gt, 0, ',', '.') }} Kg
                        </span>
                    </span>
                </td>
            </tr>
            <tr>
                <th>No</th>
                <th>Timbang Ke</th>
                <th>No Penerimaan</th>
                <th>Jml Karung</th>
                <th>Tonase</th>
                <th>Kadar Air</th>
                <th>Kadar Broken</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupRows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->timbang_ke }}</strong></td>
                    <td style="font-size:11px;">{{ $row->no_penerimaan ?: '-' }}</td>
                    <td>{{ $row->jumlah_karung ?: '-' }}</td>
                    <td>
                        <strong>
                            {{ is_numeric($row->tonase)
                                ? number_format((float)$row->tonase, 0, ',', '.') . ' Kg'
                                : '-' }}
                        </strong>
                    </td>
                    <td>
                        {{ ($row->kadar_air !== null && $row->kadar_air !== '')
                            ? $row->kadar_air . '%'
                            : '-' }}
                    </td>
                    <td>
                        {{ ($row->kadar_broken !== null && $row->kadar_broken !== '')
                            ? $row->kadar_broken . '%'
                            : '-' }}
                    </td>
                    <td>
                        <span class="badge-done">Diproses</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

{{-- Unsorted (belum dibagi) --}}
@if(count($unsortedData) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Timbang Ke</th>
                <th>No Penerimaan</th>
                <th>Jml Karung</th>
                <th>Tonase</th>
                <th>Kadar Air</th>
                <th>Kadar Broken</th>
                <th>
                    <label style="display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer;">
                        <input type="checkbox" id="selectAll" class="cb-row" {{ $isApproved ? 'disabled' : '' }}>
                        <span style="font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.04em;">Semua</span>
                    </label>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsortedData as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->timbang_ke }}</strong></td>
                    <td style="font-size:11px;">{{ $row->no_penerimaan ?: '-' }}</td>
                    <td>{{ $row->jumlah_karung ?: '-' }}</td>
                    <td>
                        <strong>
                            {{ is_numeric($row->tonase)
                                ? number_format((float)$row->tonase, 0, ',', '.') . ' Kg'
                                : '-' }}
                        </strong>
                    </td>
                    <td>
                        {{ ($row->kadar_air !== null && $row->kadar_air !== '')
                            ? $row->kadar_air . '%'
                            : '-' }}
                    </td>
                    <td>
                        {{ ($row->kadar_broken !== null && $row->kadar_broken !== '')
                            ? $row->kadar_broken . '%'
                            : '-' }}
                    </td>
                    <td>
                        <input
                            type="checkbox"
                            name="selected_rows[]"
                            value="{{ $row->id }}"
                            class="row-checkbox cb-row"
                            data-tonase="{{ $row->tonase }}"
                            data-kadar-air="{{ $row->kadar_air ?? 0 }}"
                            data-kadar-broken="{{ $row->kadar_broken ?? 0 }}"
                            {{ $isApproved ? 'disabled' : '' }}
                        >
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif