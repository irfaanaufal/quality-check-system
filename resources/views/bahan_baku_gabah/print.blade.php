<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print - Bahan Baku Gabah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 10px; font-size: 10px; }
        .container { width: auto; display: inline-block; }

        .print-table {
            border-collapse: collapse;
            border: 2px solid #000;
            table-layout: fixed;
            width: 4.5cm; /* 2cm + 0.5cm + 2cm */
        }

        .print-table td {
            border: 1px solid #000;
            padding: 0;
            text-align: left;
            vertical-align: middle;
            font-weight: bold;
            font-size: 8px;
            line-height: 1;
            overflow: hidden;
            word-break: break-word;
            height: 14px;
            max-height: 14px;
        }

        /* Kolom kiri: 2cm */
        .col-left {
            width: 2cm;
        }

        /* Kolom tengah: 0.5cm — vertikal */
        .col-mid {
            width: 0.5cm;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 7px;
            padding: 0;
            white-space: nowrap;
            display: table-cell;
            height: 100%;
        }
        .col-mid-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-weight: bold;
            font-size: 7px;
            white-space: nowrap;
        }

        /* Kolom kanan: 2cm */
        .col-right {
            width: 2cm;
        }

        .red-text {
            color: red;
        }

        .highlight-yellow {
            background-color: yellow;
        }

        .row-total td {
            font-weight: bold;
            background: #f0f0f0;
            white-space: nowrap;
            font-size: 10px;
            text-align: center;
            color: red;
            padding: 0;
        }

        /* Wrapper semua label agar mengalir kiri ke kanan */
        .labels-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        @media print {
            body { margin: 0; padding: 5mm; }
            .no-print { display: none; }
            .print-table { page-break-inside: avoid; }
            .labels-wrapper { gap: 4px; }
        }
    </style>
</head>
<body>
    <div class="labels-wrapper">

        @foreach ($dataSorting as $sorting)
            @php
                $gabah         = $sorting['gabah'];
                $reportTimbang = $sorting['reportTimbang'];
                $selectedKadar = $sorting['selectedKadar'];
                $leftColumn    = $sorting['leftColumn'];
                $rightColumn   = $sorting['rightColumn'];

                // Split kadar kiri & kanan
                $kiri  = [];
                $kanan = [];
                foreach ($selectedKadar as $index => $kadar) {
                    if ($index < ceil(count($selectedKadar) / 2)) {
                        $kiri[] = $kadar;
                    } else {
                        $kanan[] = $kadar;
                    }
                }
                $maxKadarRow      = max(count($kiri), count($kanan), 0);
                $maxTimbanganRows = max($leftColumn->count(), $rightColumn->count(), 0);

                // Rowspan kolom tengah: 3 header + kadar + timbangan (TIDAK termasuk baris TOTAL)
                $totalRows = 3 + $maxKadarRow + $maxTimbanganRows;
            @endphp

            <div class="container">
                <table class="print-table">
                    <colgroup>
                        <col style="width: 2cm;">
                        <col style="width: 0.5cm;">
                        <col style="width: 2cm;">
                    </colgroup>
                    <tbody>

                        {{-- Row 1: H & R --}}
                        <tr>
                            <td class="col-left">
                                H: Rp {{ number_format($gabah->harga, 0, ',', '.') }}
                            </td>
                            <td class="col-mid" rowspan="{{ $totalRows }}">
                                <div class="col-mid-inner">{{ $gabah->catatan_cek }}</div>
                            </td>
                            <td class="col-right {{ $gabah->harga_rata > 0 ? 'highlight-yellow' : '' }}">
                                R: Rp {{ $gabah->harga_rata > 0 ? number_format($gabah->harga_rata, 0, ',', '.') : '' }}
                            </td>
                        </tr>

                        {{-- Row 2: Tanggal & Jenis | Nopol --}}
                        <tr>
                            <td class="col-left">
                                {{ $tanggal }} {{ $gabah->jenis }}
                            </td>
                            <td class="col-right">
                                {{ $gabah->nopol }}
                            </td>
                        </tr>

                        {{-- Row 3: Supplier | Lokasi --}}
                        <tr>
                            <td class="col-left">{{ $gabah->supplier }}</td>
                            <td class="col-right">{{ $gabah->lokasi_penyimpanan }}</td>
                        </tr>

                        {{-- Kadar --}}
                        @for ($i = 0; $i < $maxKadarRow; $i++)
                            <tr>
                                <td class="col-left red-text">
                                    @if (isset($kiri[$i]))
                                        {{ $kiri[$i]->kadar_air }}% - {{ $kiri[$i]->kadar_broken }}%
                                    @endif
                                </td>
                                <td class="col-right red-text">
                                    @if (isset($kanan[$i]))
                                        {{ $kanan[$i]->kadar_air }}% - {{ $kanan[$i]->kadar_broken }}%
                                    @endif
                                </td>
                            </tr>
                        @endfor

                        {{-- Timbangan --}}
                        @for ($i = 0; $i < $maxTimbanganRows; $i++)
                            <tr>
                                <td class="col-left" style="white-space: nowrap;">
                                    @if ($i < $leftColumn->count())
                                        @php $row = $leftColumn[$i]; @endphp
                                        {{ $row->jumlah_karung }}-{{ number_format((float)$row->tonase, 0, ',', '.') }}Kg
                                    @endif
                                </td>
                                <td class="col-right" style="white-space: nowrap;">
                                    @if ($i < $rightColumn->count())
                                        @php $row = $rightColumn[$i]; @endphp
                                        {{ $row->jumlah_karung }}-{{ number_format((float)$row->tonase, 0, ',', '.') }}Kg
                                    @endif
                                </td>
                            </tr>
                        @endfor

                        {{-- Total: colspan=3 penuh karena rowspan kolom tengah sudah selesai --}}
                        <tr class="row-total">
                            <td colspan="3" style="font-size: 10px; font-weight: bold; text-align: center; color: red; background: #f0f0f0; white-space: nowrap; padding: 0; line-height: 1; height: 14px;">
                                TOTAL {{ $reportTimbang->sum('jumlah_karung') }} - {{ number_format((float)$reportTimbang->sum('tonase'), 0, ',', '.') }} Kg
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        @endforeach

    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>
</body>
</html>
