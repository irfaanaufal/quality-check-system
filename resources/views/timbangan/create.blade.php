<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-md text-gray-800 leading-tight">
                    {{ __('Timbangan') }}
                </h2>
            </div>
        </div>
    </x-slot>

    @php
        $tanggalTerima = filled($terimaBb->tgl_terima ?? null)
            ? \Illuminate\Support\Carbon::parse($terimaBb->tgl_terima)
            : null;
        $rows = $rows ?? collect();
        $totalTonase = $rows->sum(function ($row) {
            return (float) $row->tonase;
        });
        $totalKarung = $rows->sum(function ($row) {
            return (float) $row->jumlah_karung;
        });
        $currentUsername = strtolower(trim(auth()->user()?->username ?? ''));
        $currentName = strtolower(trim(auth()->user()?->name ?? ''));
        $userCreated = strtolower(trim($terimaBb->user_created ?? ''));
        $isCreator = in_array($userCreated, array_filter([$currentUsername, $currentName]), true);
        $isApproved = filled($terimaBb->jam_akhir);
        $isProcessing = strtolower(trim($terimaBb->status ?? '')) === 'proses' || !$isApproved;
        $isFinished = !$isProcessing;
    @endphp

    <style>
        /* Menghilangkan panah naik/turun pada input number */
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    @if (session('success') || session('error'))
        <div class="mb-4 px-4 py-3 rounded-md border border-slate-200 bg-white shadow-sm">
            @if (session('success'))
                <div class="text-sm font-medium text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="text-sm font-medium text-red-700">{{ session('error') }}</div>
            @endif
        </div>
    @endif

    <div class="px-3 py-4 sm:px-4 lg:px-5">
        <div class="grid grid-cols-1 gap-4 items-start xl:grid-cols-[minmax(0,520px)_minmax(0,1fr)]">
            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm max-w-full">
                <div class="border-b border-slate-200 px-4 py-3 text-base font-medium text-slate-700">
                    Form Proses Penerimaan Beras
                </div>

                <form method="POST" action="{{ route('timbangan.store') }}" class="px-4 py-4">
                    @csrf
                    <input type="hidden" name="terima_bb_id" value="{{ $terimaBb->id }}">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Tanggal</label>
                            <input type="text" value="{{ $tanggalTerima?->format('Y-m-d') ?? '-' }}" disabled class="w-full rounded-sm border border-slate-300 bg-slate-100 px-4 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Nama Supplier</label>
                            <input type="text" value="{{ $terimaBb->nama_supplier ?? '-' }}" disabled class="w-full rounded-sm border border-slate-300 bg-slate-100 px-4 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Jenis Beras</label>
                            <input type="text" value="{{ $terimaBb->jenis_bahan ?? '-' }}" disabled class="w-full rounded-sm border border-slate-300 bg-slate-100 px-4 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Penimbangan Ke</label>
                            <input type="text" value="{{ $nextTimbangKe ?? 1 }}" disabled class="w-full rounded-sm border border-slate-300 bg-slate-100 px-4 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Jumlah Karung</label>
                            <div class="flex overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <input name="jumlah_karung" type="number" min="0" step="1" value="{{ old('jumlah_karung') }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0" placeholder="">
                                <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">Krg</span>
                            </div>
                            @error('jumlah_karung')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Tonase</label>
                            <div class="flex overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <input name="tonase" type="number" min="0" step="0.001" value="{{ old('tonase') }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0" placeholder="">
                                <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">Kg</span>
                            </div>
                            @error('tonase')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Kadar Air</label>
                            <div class="flex overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <input name="kadar_air" type="number" min="0" step="0.01" value="{{ old('kadar_air') }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0" placeholder="">
                                <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">%</span>
                            </div>
                            @error('kadar_air')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-800">Kadar Broken</label>
                            <div class="flex overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                                <input name="kadar_broken" type="number" min="0" step="0.01" value="{{ old('kadar_broken') }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0" placeholder="">
                                <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">%</span>
                            </div>
                            @error('kadar_broken')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3 border-t border-slate-200 pt-4">
                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                            Simpan
                        </button>
                        <button type="button" onclick="window.location='{{ route('penerimaan_beras.index') }}'" class="inline-flex items-center rounded-md bg-red-500 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-red-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-base font-medium text-slate-700">
                    Data Penimbangan
                </div>

                <div class="overflow-x-auto px-4 py-4">
                    <table class="min-w-full border border-slate-200 text-sm">
                        <thead>
                            <tr class="bg-slate-600 text-white">
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">No</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Timbang Ke</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Jml Karung</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Tonase</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Kadar Air</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Kadar Broken</th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ $row->timbang_ke }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ $row->jumlah_karung ?: '-' }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ is_numeric($row->tonase) ? number_format((float) $row->tonase, 0, ',', '.') . ' Kg' : '-' }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ $row->kadar_air ?: '-' }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">{{ $row->kadar_broken ?: '-' }}</td>
                                    <td class="border border-slate-200 px-3 py-2 text-center">
                                        @if ($isProcessing)
                                            <!-- Belum selesai: Tampilkan Edit & Hapus -->
                                            <div class="flex items-center justify-center gap-2 text-gray-500">
                                                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'timbangan-edit-modal-{{ $row->id }}')" class="inline-flex items-center justify-center transition hover:text-amber-600" title="Edit">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487z" />
                                                    </svg>
                                                </button>
                                                <form method="POST" action="{{ route('timbangan.destroy', $row->id) }}" onsubmit="return confirm('Yakin hapus penimbangan ke-{{ $row->timbang_ke }}?');" class="inline-flex m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center transition hover:text-red-600" title="Hapus">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-10.5 0V6.375A1.125 1.125 0 018.625 5.25h6.75a1.125 1.125 0 011.125 1.125V7.5m-9 0 1.05 11.025A1.5 1.5 0 009.665 20.25h4.67a1.5 1.5 0 001.495-1.725L16.5 7.5M10.5 11.25v4.5m3-4.5v4.5" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @include('timbangan.edit', ['timbangan' => $row])
                                        @elseif ($isFinished)
                                            <div class="flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="border border-slate-200 px-3 py-8 text-center text-slate-500">
                                        Belum ada data penimbangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($rows->isNotEmpty())
                        <tfoot>
                            <tr class="bg-slate-100">
                                <td colspan="2" class="border border-slate-200 px-3 py-3 text-center font-semibold text-slate-700">Total Keseluruhan</td>
                                <td class="border border-slate-200 px-3 py-3 text-center font-semibold text-slate-700">{{ number_format($totalKarung, 0, ',', '.') }} Krg</td>
                                <td class="border border-slate-200 px-3 py-3 text-center font-semibold text-slate-700">{{ number_format($totalTonase, 0, ',', '.') }} Kg</td>
                                <td colspan="3" class="border border-slate-200 px-3 py-3 text-center font-semibold text-slate-700"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                    </table>
                    @if ($isProcessing)
                    <form method="POST" action="{{ route('timbangan.selesai_timbang', $terimaBb->id) }}">
                        @csrf
                        <div class="mt-4">
                            <label class="mb-2 block text-sm font-semibold text-slate-800">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full rounded-sm border border-slate-300 bg-white px-4 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan', $terimaBb->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button
                                type="submit"
                                onclick="return confirm('Yakin ingin menyelesaikan data penimbangan ini?')"
                                class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                                Selesai Timbang
                            </button>
                        </div>
                    </form>
                    @elseif ($isCreator && $isFinished)
                    <div class="mt-4 flex justify-end">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700">
                            ✓ Menunggu validasi
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
