<x-modal name="timbangan-gabah-edit-modal-{{ $timbangan->id }}" maxWidth="2xl" focusable>
    <div class="bg-gray-100 px-5 py-5 sm:px-6">
        <div class="rounded-md border border-gray-300 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 py-3 text-lg font-normal text-gray-700">Form Edit Penimbangan Ke-{{ $timbangan->timbang_ke }}</div>

            <form method="POST" action="{{ route('timbangan-gabah.update', $timbangan->id) }}" class="px-4 py-4 sm:px-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-800">Jumlah Karung</label>
                        <div class="flex items-stretch overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                            <input name="jumlah_karung" type="number" min="0" step="1" value="{{ old('jumlah_karung', $timbangan->jumlah_karung) }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">Krg</span>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-800">Tonase</label>
                        <div class="flex items-stretch overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                            <input name="tonase" type="number" min="0" step="0.001" value="{{ old('tonase', $timbangan->tonase) }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">Kg</span>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-semibold text-slate-800">Kadar Air</label>
                        <div class="flex items-stretch overflow-hidden rounded-sm border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                            <input name="kadar_air" type="number" min="0" step="0.01" value="{{ old('kadar_air', $timbangan->kadar_air) }}" class="w-full border-0 px-4 py-2.5 text-slate-700 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <span class="inline-flex items-center border-l border-slate-300 bg-slate-50 px-3 text-sm italic text-slate-500">%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-200 pt-5">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-blue-700 sm:w-auto">Update Data</button>
                        <button type="button" x-on:click.prevent="$dispatch('close-modal', 'timbangan-gabah-edit-modal-{{ $timbangan->id }}')" class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-red-700 sm:w-auto">Tutup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-modal>
