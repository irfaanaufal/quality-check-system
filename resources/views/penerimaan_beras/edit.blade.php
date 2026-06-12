<x-modal name="penerimaan-beras-edit-modal-{{ $item->id }}" maxWidth="2xl" focusable>
    <div
        x-data="{
            searchableSelect(options, placeholder, initialValue, initialLabel) {
                return {
                    options, placeholder,
                    open: false,
                    query: initialLabel || '',
                    value: initialValue || '',
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
    >
        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:10px;padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb;">
            <div style="width:32px;height:32px;border-radius:8px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213l-4.182.465.465-4.182L16.862 3.487z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size:0.9375rem;font-weight:600;color:#111827;margin:0;line-height:1.3;">Edit Penerimaan Beras</h3>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0;">ID: {{ $item->id }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('penerimaan_beras.update', $item->id) }}" style="padding:1.25rem;">
            @csrf
            @method('PUT')

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
                        <input type="date" disabled
                            value="{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('Y-m-d') }}"
                            class="pnb-input pnb-input-disabled">
                        <input type="hidden" name="tgl_terima" value="{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('Y-m-d') }}">
                    </div>
                </div>

                {{-- No Polisi --}}
                <div class="pnb-field">
                    <label>No Polisi</label>
                    <input name="nopol" type="text" class="pnb-input"
                        value="{{ old('nopol', $item->nopol) }}"
                        placeholder="Contoh: AB 1234 CD">
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
                        <input type="time" name="jam_awal" disabled
                            value="{{ old('jam_awal', $item->jam_awal) }}"
                            class="pnb-input pnb-input-disabled">
                    </div>
                </div>

                {{-- Tempat Simpan --}}
                <div class="pnb-field">
                    <label>Tempat Simpan</label>
                    <input name="tempat_simpan" type="text" class="pnb-input"
                        value="{{ old('tempat_simpan', $item->tempat_simpan) }}"
                        placeholder="Gudang / Lokasi penyimpanan">
                </div>

                {{-- Supplier --}}
                <div class="pnb-field pnb-span2"
                    x-data="searchableSelect(
                        @js(collect($suppliers ?? [])->map(fn($s) => ['value' => $s->kode_cust, 'label' => $s->nama_cust])->values()),
                        'Cari nama supplier...',
                        '{{ $item->supplier_code }}',
                        '{{ $item->supplier_name }}'
                    )"
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
                </div>

                {{-- Jenis Beras --}}
                <div class="pnb-field pnb-span2"
                    x-data="searchableSelect(
                        @js(collect($varietasBeras ?? [])->map(fn($v) => ['value' => $v->id, 'label' => $v->alias])->values()),
                        'Cari jenis beras...',
                        '{{ $item->id_jenis }}',
                        '{{ $item->varietas_alias }}'
                    )"
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
                </div>

                {{-- Kemasan Pakai --}}
                <div class="pnb-field">
                    <label>Kemasan Pakai</label>
                    <input name="kemasan_pakai" type="text" class="pnb-input"
                        value="{{ old('kemasan_pakai', $item->kemasan_pakai) }}"
                        placeholder="Jenis kemasan">
                </div>

                {{-- Penggunaan Palet --}}
                <div class="pnb-field">
                    <label>Penggunaan Palet</label>
                    <div style="position:relative;">
                        <select name="penggunaan_palet" class="pnb-input pnb-select">
                            <option value="">Pilih</option>
                            <option value="Ya" {{ old('penggunaan_palet', $item->penggunaan_palet) == 'Ya' ? 'selected' : '' }}>Ya</option>
                            <option value="Tidak" {{ old('penggunaan_palet', $item->penggunaan_palet) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                        <span style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:0.625rem;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid #f3f4f6;">
                <button type="submit" class="pnb-btn pnb-btn-warning">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update
                </button>
                <button type="button" class="pnb-btn pnb-btn-ghost"
                    x-on:click.prevent="$dispatch('close-modal', 'penerimaan-beras-edit-modal-{{ $item->id }}')">
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

.pnb-btn-warning { background: #ea580c; color: #fff; }
.pnb-btn-warning:hover { background: #c2410c; }

.pnb-btn-ghost { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.pnb-btn-ghost:hover { background: #e5e7eb; }

@media (min-width: 400px) {
    .pnb-btn { flex: 0 0 auto; }
}
</style>