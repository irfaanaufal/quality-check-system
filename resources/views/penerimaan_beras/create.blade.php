<x-modal name="penerimaan-beras-create-modal" maxWidth="2xl" focusable>
    <div
        x-data="{
            setDefaults() {
                const now = new Date();
                const y = now.getFullYear();
                const m = String(now.getMonth() + 1).padStart(2, '0');
                const d = String(now.getDate()).padStart(2, '0');
                const h = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');
                const dateVal = `${y}-${m}-${d}`;
                const jamVal = `${h}:${min}`;
                this.$refs.tanggalDisplay.value = dateVal;
                this.$refs.tglTerima.value = dateVal;
                this.$refs.jamAwalDisplay.value = jamVal;
                this.$refs.jamAwal.value = jamVal;
                this.$refs.jamAkhir.value = jamVal;
            },
            searchableSelect(options, placeholder) {
                return {
                    options, placeholder,
                    open: false, query: '', value: '',
                    get filteredOptions() {
                        const kw = this.query.toLowerCase().trim();
                        if (!kw) return this.options;
                        return this.options.filter(o => `${o.label} ${o.value}`.toLowerCase().includes(kw));
                    },
                    choose(option) { this.value = option.value; this.query = option.label; this.open = false; },
                    resolveTypedValue() {
                        const kw = this.query.toLowerCase().trim();
                        if (!kw) { this.value = ''; this.query = ''; return; }
                        const exact = this.options.find(o => o.label.toLowerCase() === kw);
                        if (exact) { this.choose(exact); return; }
                        if (this.filteredOptions.length === 1) { this.choose(this.filteredOptions[0]); return; }
                        if (!this.value) this.query = '';
                    }
                };
            }
        }"
        x-init="setDefaults()"
        x-on:open-modal.window="if ($event.detail === 'penerimaan-beras-create-modal') setDefaults()"
    >
        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:10px;padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb;">
            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 style="font-size:0.9375rem;font-weight:600;color:#111827;margin:0;">Form Penerimaan Beras</h3>
        </div>

        <form method="POST" action="{{ route('penerimaan_beras.store') }}" style="padding:1.25rem;">
            @csrf

            <div class="pnb-grid">

                {{-- Tanggal --}}
                <div class="pnb-field">
                    <label>Tanggal</label>
                    <div class="pnb-input-icon">
                        <span class="pnb-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v3.75m10.5-3.75v3.75M3 10.5h18m-16.5-6h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6A1.5 1.5 0 014.5 4.5z"/>
                            </svg>
                        </span>
                        <input x-ref="tanggalDisplay" type="date" disabled class="pnb-input pnb-input-disabled">
                        <input x-ref="tglTerima" type="hidden" name="tgl_terima">
                    </div>
                </div>

                {{-- No Polisi --}}
                <div class="pnb-field">
                    <label>No Polisi</label>
                    <input name="nopol" type="text" class="pnb-input" placeholder="Contoh: AB 1234 CD">
                </div>

                {{-- Jam Awal --}}
                <div class="pnb-field">
                    <label>Jam Awal Bongkaran</label>
                    <div class="pnb-input-icon">
                        <span class="pnb-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                            </svg>
                        </span>
                        <input x-ref="jamAwalDisplay" type="text" disabled class="pnb-input pnb-input-disabled">
                        <input x-ref="jamAwal" type="hidden" name="jam_awal">
                        <input x-ref="jamAkhir" type="hidden" name="jam_akhir">
                    </div>
                </div>

                {{-- Tempat Simpan --}}
                <div class="pnb-field">
                    <label>Tempat Simpan</label>
                    <input name="tempat_simpan" type="text" class="pnb-input" placeholder="Gudang / Lokasi penyimpanan">
                </div>

                {{-- Supplier --}}
                <div class="pnb-field pnb-span2"
                    x-data="searchableSelect(@js(collect($suppliers ?? [])->map(fn($s) => ['value' => $s->kode_cust, 'label' => $s->nama_cust])->values()), 'Cari nama supplier...')"
                    style="position:relative;">
                    <label>Nama Supplier</label>
                    <div style="position:relative;">
                        <input type="text" class="pnb-input" style="padding-right:2.5rem;"
                            :placeholder="placeholder"
                            x-model="query"
                            x-on:focus="open = true"
                            x-on:click="open = true"
                            x-on:input="open = true; value = ''"
                            x-on:keydown.enter.prevent="resolveTypedValue()"
                            x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
                        <button type="button" style="position:absolute;right:0;top:0;bottom:0;padding:0 0.75rem;color:#9ca3af;" x-on:click="open = !open">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <input type="hidden" name="kode_supplier" :value="value">
                        <div x-cloak x-show="open" x-transition
                            style="position:absolute;z-index:50;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.08);max-height:200px;overflow-y:auto;">
                            <template x-for="option in filteredOptions" :key="option.value">
                                <button type="button" class="pnb-dropdown-item" x-on:mousedown.prevent="choose(option)" x-text="option.label"></button>
                            </template>
                            <div x-show="filteredOptions.length === 0" style="padding:0.625rem 0.875rem;font-size:0.8125rem;color:#9ca3af;">Data tidak ditemukan.</div>
                        </div>
                    </div>
                    @error('kode_supplier')
                        <p style="margin-top:4px;font-size:0.75rem;color:#dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Beras --}}
                <div class="pnb-field pnb-span2"
                    x-data="searchableSelect(@js(collect($varietasBeras ?? [])->map(fn($v) => ['value' => $v->id, 'label' => $v->alias])->values()), 'Cari jenis beras...')"
                    style="position:relative;">
                    <label>Jenis Beras</label>
                    <div style="position:relative;">
                        <input type="text" class="pnb-input" style="padding-right:2.5rem;"
                            :placeholder="placeholder"
                            x-model="query"
                            x-on:focus="open = true"
                            x-on:click="open = true"
                            x-on:input="open = true; value = ''"
                            x-on:keydown.enter.prevent="resolveTypedValue()"
                            x-on:blur="setTimeout(() => { open = false; resolveTypedValue(); }, 150)">
                        <button type="button" style="position:absolute;right:0;top:0;bottom:0;padding:0 0.75rem;color:#9ca3af;" x-on:click="open = !open">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <input type="hidden" name="id_jenis" :value="value">
                        <div x-cloak x-show="open" x-transition
                            style="position:absolute;z-index:50;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.08);max-height:200px;overflow-y:auto;">
                            <template x-for="option in filteredOptions" :key="option.value">
                                <button type="button" class="pnb-dropdown-item" x-on:mousedown.prevent="choose(option)" x-text="option.label"></button>
                            </template>
                            <div x-show="filteredOptions.length === 0" style="padding:0.625rem 0.875rem;font-size:0.8125rem;color:#9ca3af;">Data tidak ditemukan.</div>
                        </div>
                    </div>
                    @error('id_jenis')
                        <p style="margin-top:4px;font-size:0.75rem;color:#dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kemasan Pakai --}}
                <div class="pnb-field">
                    <label>Kemasan Pakai</label>
                    <input name="kemasan_pakai" type="text" class="pnb-input" placeholder="Jenis kemasan">
                </div>

                {{-- Penggunaan Palet --}}
                <div class="pnb-field">
                    <label>Penggunaan Palet</label>
                    <div style="position:relative;">
                        <select name="penggunaan_palet" class="pnb-input pnb-select">
                            <option value="">Pilih</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                        <span style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:0.625rem;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid #f3f4f6;">
                <button type="submit" class="pnb-btn pnb-btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Submit
                </button>
                <button type="button" class="pnb-btn pnb-btn-ghost" x-on:click.prevent="$dispatch('close-modal', 'penerimaan-beras-create-modal')">
                    Batal
                </button>
            </div>
        </form>
    </div>
</x-modal>

<style>
.pnb-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.875rem;
}

@media (max-width: 540px) {
    .pnb-grid { grid-template-columns: 1fr; }
    .pnb-span2 { grid-column: span 1 !important; }
}

.pnb-span2 { grid-column: span 2; }

.pnb-field label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.375rem;
}

.pnb-input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.625rem 0.75rem;
    font-size: 0.9375rem;
    color: #111827;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    box-sizing: border-box;
    -webkit-appearance: none;
    appearance: none;
}

.pnb-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}

.pnb-input-disabled {
    background: #f9fafb;
    color: #6b7280;
}

.pnb-select { padding-right: 2.25rem; cursor: pointer; }

.pnb-input-icon {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.15s, box-shadow 0.15s;
    background: #fff;
}

.pnb-input-icon:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}

.pnb-input-icon .pnb-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    flex-shrink: 0;
    color: #9ca3af;
    border-right: 1px solid #e5e7eb;
}

.pnb-input-icon .pnb-input {
    border: none;
    border-radius: 0;
    box-shadow: none;
    flex: 1;
    min-width: 0;
}

.pnb-input-icon .pnb-input:focus { box-shadow: none; }

.pnb-dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 0.875rem;
    text-align: left;
    font-size: 0.875rem;
    color: #374151;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background 0.1s;
}

.pnb-dropdown-item:hover { background: #eff6ff; color: #1d4ed8; }

.pnb-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.625rem 1.125rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: opacity 0.15s, transform 0.1s;
    flex: 1;
    justify-content: center;
}

.pnb-btn:active { transform: scale(0.97); }

.pnb-btn-primary { background: #2563eb; color: #fff; }
.pnb-btn-primary:hover { background: #1d4ed8; }

.pnb-btn-ghost { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.pnb-btn-ghost:hover { background: #e5e7eb; }

@media (min-width: 400px) {
    .pnb-btn { flex: 0 0 auto; }
}
</style>