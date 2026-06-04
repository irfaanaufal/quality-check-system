<x-modal name="penerimaan-beras-create-modal" maxWidth="4xl" focusable>
	<div
		x-data="{
			setDefaults() {
				const now = new Date();
				const year = now.getFullYear();
				const month = String(now.getMonth() + 1).padStart(2, '0');
				const day = String(now.getDate()).padStart(2, '0');
				const hours24 = now.getHours();
				const minutes = String(now.getMinutes()).padStart(2, '0');

				const dateValue = `${year}-${month}-${day}`;
				const jamValue = `${String(hours24).padStart(2, '0')}:${minutes}`;

				this.$refs.tanggalDisplay.value = dateValue;
				this.$refs.tglTerima.value = dateValue;
				this.$refs.jamAwalDisplay.value = jamValue;
				this.$refs.jamAwal.value = jamValue;
				this.$refs.jamAkhir.value = jamValue;
			},
			searchableSelect(options, placeholder) {
				return {
					options,
					placeholder,
					open: false,
					query: '',
					value: '',
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
		x-init="setDefaults()"
		x-on:open-modal.window="if ($event.detail === 'penerimaan-beras-create-modal') setDefaults()"
		class="bg-gray-100"
	>
		<div class="bg-gray-100 px-5 py-5 sm:px-6">
			<div class="rounded-md border border-gray-300 bg-white shadow-sm">
				<div class="border-b border-gray-200 px-4 py-3 text-lg font-normal text-gray-700">Form Penerimaan Beras</div>

				<form method="POST" action="{{ route('penerimaan_beras.store') }}" class="px-4 py-4 sm:px-5">
					@csrf
					<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
						<div class="space-y-5">
							<div>
								<label for="tanggal" class="mb-1 block text-base font-semibold text-gray-800">Tanggal</label>
								<div class="flex items-stretch overflow-hidden rounded-sm border border-gray-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
									<span class="inline-flex w-12 items-center justify-center border-r border-gray-300 bg-white text-gray-500">
										<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z" />
										</svg>
									</span>
									<input x-ref="tanggalDisplay" id="tanggal" type="date" disabled class="w-full border-0 bg-gray-100 px-4 py-3 text-lg text-gray-800 opacity-80 focus:outline-none focus:ring-0">
									<input x-ref="tglTerima" type="hidden" name="tgl_terima">
								</div>
							</div>

							<div>
								<label for="nopol" class="mb-1 block text-base font-semibold text-gray-800">No Polisi</label>
								<input id="nopol" name="nopol" type="text" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
							</div>

							<div>
								<label for="jam_awal" class="mb-1 block text-base font-semibold text-gray-800">Jam Awal Bongkaran</label>
								<div class="flex items-stretch overflow-hidden rounded-sm border border-gray-300 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
									<span class="inline-flex w-12 items-center justify-center border-r border-gray-300 bg-white text-gray-500">
										<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 100 18 9 9 0 000-18z" />
										</svg>
									</span>
									<input x-ref="jamAwalDisplay" id="jam_awal_display" type="text" disabled class="w-full border-0 bg-gray-100 px-4 py-3 text-lg text-gray-800 opacity-80 focus:outline-none focus:ring-0">
									<input x-ref="jamAwal" type="hidden" name="jam_awal">
											<input x-ref="jamAkhir" type="hidden" name="jam_akhir">
								</div>
							</div>

							<div>
								<label for="tempat_simpan" class="mb-1 block text-base font-semibold text-gray-800">Tempat Simpan</label>
								<input id="tempat_simpan" name="tempat_simpan" type="text" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
							</div>
						</div>

						<div class="space-y-5">
							<div x-data="searchableSelect(@js(collect($suppliers ?? [])->map(fn ($supplier) => ['value' => $supplier->kode_cust, 'label' => $supplier->nama_cust])->values()), 'Pilih Supplier')" class="relative">
								<label for="nama_supplier" class="mb-1 block text-base font-semibold text-gray-800">Nama Supplier</label>
								<input type="text" id="nama_supplier" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 pr-10 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500" :placeholder="placeholder" :class="open ? 'border-blue-500 ring-1 ring-blue-500' : ''" x-model="query" x-on:focus="open = true" x-on:click="open = true" x-on:input="open = true; value = ''" x-on:keydown.enter.prevent="resolveTypedValue()" x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
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
								@error('kode_supplier')
									<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
								@enderror
							</div>

							<div x-data="searchableSelect(@js(collect($varietasBeras ?? [])->map(fn ($varietas) => ['value' => $varietas->id, 'label' => $varietas->alias])->values()), 'Pilih Jenis Beras')" class="relative">
								<label for="jenis_beras" class="mb-1 block text-base font-semibold text-gray-800">Jenis Beras</label>
								<input type="text" id="jenis_beras" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 pr-10 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500" :placeholder="placeholder" :class="open ? 'border-blue-500 ring-1 ring-blue-500' : ''" x-model="query" x-on:focus="open = true" x-on:click="open = true" x-on:input="open = true; value = ''" x-on:keydown.enter.prevent="resolveTypedValue()" x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
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
								@error('id_jenis')
									<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="kemasan_pakai" class="mb-1 block text-base font-semibold text-gray-800">Kemasan Pakai</label>
								<input id="kemasan_pakai" name="kemasan_pakai" type="text" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
							</div>

							<div>
								<label for="penggunaan_palet" class="mb-1 block text-base font-semibold text-gray-800">Penggunaan Palet</label>
								<select id="penggunaan_palet" name="penggunaan_palet" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 text-lg text-gray-800 focus:border-blue-500 focus:ring-blue-500">
									<option value="">Pilih Penggunaan Palet</option>
									<option value="Ya">Ya</option>
									<option value="Tidak">Tidak</option>
								</select>
							</div>
						</div>
					</div>

					<div class="mt-6 border-t border-gray-200 pt-5">
						<div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
							<button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-blue-700 sm:w-auto">Submit</button>
							<button type="button" x-on:click.prevent="$dispatch('close-modal', 'penerimaan-beras-create-modal')" class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-5 py-2.5 text-base font-medium text-white shadow-sm transition hover:bg-red-700 sm:w-auto">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	</x-modal>
