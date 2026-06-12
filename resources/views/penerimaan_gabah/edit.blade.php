<x-modal name="penerimaan-gabah-edit-modal-{{ $item->id }}" maxWidth="4xl" focusable>
	<div
		x-data="{
			searchableSelect(options, placeholder, initialValue, initialLabel) {
				return {
					options,
					placeholder,
					open: false,
					query: initialLabel || '',
					value: initialValue || '',
					get filteredOptions() {
						const keyword = this.query.toLowerCase().trim();
						if (!keyword) return this.options;
						return this.options.filter((option) => `${option.label} ${option.value}`.toLowerCase().includes(keyword));
					},
					choose(option) {
						this.value = option.value;
						this.query = option.label;
						this.open = false;
					},
						resolveTypedValue() {
							const keyword = this.query.toLowerCase().trim();
							if (!keyword) {
								this.value = '';
								this.query = '';
								return;
							}

							const exactMatch = this.options.find((option) => option.label.toLowerCase() === keyword);
							if (exactMatch) {
								this.choose(exactMatch);
								return;
							}

							if (this.filteredOptions.length === 1) {
								this.choose(this.filteredOptions[0]);
								return;
							}

							if (!this.value) {
								this.query = '';
							}
						},
					resetIfNeeded() {
						if (!this.value) this.query = '';
					}
				};
			}
		}"
		class="bg-gray-100"
	>
		<div class="bg-gray-100 px-5 py-5 sm:px-6">
			<div class="rounded-md border border-gray-300 bg-white shadow-sm">
				<div class="border-b border-gray-200 px-4 py-3 text-lg font-normal text-gray-700">Form Edit Penerimaan Gabah</div>

				<form method="POST" action="{{ route('penerimaan_gabah.update', $item->id) }}" class="px-4 py-4 sm:px-5">
					@csrf
                    @method('PUT')
					<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
						<div class="space-y-5">
							<div>
								<label for="tanggal_{{ $item->id }}" class="mb-1 block text-base font-semibold text-gray-800">Tanggal</label>
								<div class="flex items-stretch overflow-hidden rounded-sm border border-gray-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
									<span class="inline-flex w-12 items-center justify-center border-r border-gray-300 bg-white text-gray-500">
										<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z" />
										</svg>
									</span>
									<input id="tanggal_{{ $item->id }}" type="date" disabled value="{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('Y-m-d') }}" class="w-full border-0 bg-gray-100 px-4 py-3 text-lg text-gray-800 opacity-80 focus:outline-none focus:ring-0">
									<input type="hidden" name="tgl_terima" value="{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('Y-m-d') }}">
								</div>
							</div>

							<div>
								<label class="mb-1 block text-base font-semibold text-gray-800">No Polisi</label>
								<input name="nopol" type="text" value="{{ old('nopol', $item->nopol) }}" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
							</div>

							<div>
								<label class="mb-1 block text-base font-semibold text-gray-800">Jam Awal Bongkaran</label>
								<div class="flex items-stretch overflow-hidden rounded-sm border border-gray-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
									<span class="inline-flex w-12 items-center justify-center border-r border-gray-300 bg-white text-gray-500">
										<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 100 18 9 9 0 000-18z" />
										</svg>
									</span>
									<input type="time" name="jam_awal"  disabled value="{{ old('jam_awal', $item->jam_awal) }}" class="w-full border-0 bg-gray-100 px-4 py-3 text-lg text-gray-800 opacity-80 focus:outline-none focus:ring-0">
								</div>
							</div>

							<div>
								<label class="mb-1 block text-base font-semibold text-gray-800">Tempat Simpan</label>
								<input name="tempat_simpan" type="text" value="{{ old('tempat_simpan', $item->tempat_simpan) }}" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
							</div>
						</div>

						<div class="space-y-5">
							<div x-data="searchableSelect(@js(collect($suppliers ?? [])->map(fn ($supplier) => ['value' => $supplier->kode_cust, 'label' => $supplier->nama_cust])->values()), 'Pilih Supplier', '{{ $item->supplier_code }}', '{{ $item->supplier_name }}')" class="relative">
								<label class="mb-1 block text-base font-semibold text-gray-800">Nama Supplier</label>
								<input type="text" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 pr-10 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500" :placeholder="placeholder" :class="open ? 'border-blue-500 ring-1 ring-blue-500' : ''" x-model="query" x-on:focus="open = true" x-on:click="open = true" x-on:input="open = true; value = ''" x-on:keydown.enter.prevent="resolveTypedValue()" x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
								<input type="hidden" name="kode_supplier" :value="value">
								<button type="button" class="absolute inset-y-0 right-0 mt-7 flex items-center px-3 text-gray-500" x-on:click="open = !open">
									<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
								</button>
								<div x-cloak x-show="open" x-transition class="absolute z-30 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg">
									<template x-for="option in filteredOptions" :key="option.value">
										<button type="button" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-700" x-on:mousedown.prevent="choose(option)" x-text="option.label"></button>
									</template>
									<div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500">Data tidak ditemukan.</div>
								</div>
							</div>

							<div x-data="searchableSelect(@js(collect($varietasGabah ?? [])->map(fn ($varietas) => ['value' => $varietas->id, 'label' => $varietas->alias])->values()), 'Pilih Jenis Gabah', '{{ $item->id_jenis }}', '{{ $item->varietas_alias }}')" class="relative">
								<label class="mb-1 block text-base font-semibold text-gray-800">Jenis Gabah</label>
								<input type="text" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 pr-10 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500" :placeholder="placeholder" :class="open ? 'border-blue-500 ring-1 ring-blue-500' : ''" x-model="query" x-on:focus="open = true" x-on:click="open = true" x-on:input="open = true; value = ''" x-on:keydown.enter.prevent="resolveTypedValue()" x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
								<input type="hidden" name="id_jenis" :value="value">
								<button type="button" class="absolute inset-y-0 right-0 mt-7 flex items-center px-3 text-gray-500" x-on:click="open = !open">
									<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
								</button>
								<div x-cloak x-show="open" x-transition class="absolute z-30 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg">
									<template x-for="option in filteredOptions" :key="option.value">
										<button type="button" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 transition hover:bg-blue-50 hover:text-blue-700" x-on:mousedown.prevent="choose(option)" x-text="option.label"></button>
									</template>
									<div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500">Data tidak ditemukan.</div>
								</div>
							</div>

							<div>
								<label for="penggunaan_palet" class="mb-1 block text-base font-semibold text-gray-800">Penggunaan Palet</label>
								<select id="penggunaan_palet" name="penggunaan_palet" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
									<option value="">Pilih Penggunaan Palet</option>
									<option value="Ya" {{ old('penggunaan_palet', $item->penggunaan_palet) == 'Ya' ? 'selected' : '' }}>Ya</option>
									<option value="Tidak" {{ old('penggunaan_palet', $item->penggunaan_palet) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
								</select>
							</div>
						</div>
					</div>

					<div class="mt-6 border-t border-gray-200 pt-5">
						<div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
							<button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-blue-700 sm:w-auto">Update</button>
							<button type="button" x-on:click.prevent="$dispatch('close-modal', 'penerimaan-gabah-edit-modal-{{ $item->id }}')" class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-red-700 sm:w-auto">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-modal>
