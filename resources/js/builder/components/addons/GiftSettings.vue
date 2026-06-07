<script setup>
import { useBuilderStore } from '../../store';

const store = useBuilderStore();

const EWALLET_OPTIONS = [
    { value: 'gopay',     label: 'GoPay' },
    { value: 'dana',      label: 'DANA' },
    { value: 'ovo',       label: 'OVO' },
    { value: 'shopeepay', label: 'ShopeePay' },
    { value: 'linkaja',   label: 'LinkAja' },
    { value: 'qris',      label: 'QRIS' },
];

function addBank() {
    store.config.content.gift.banks.push({ name: '', account_no: '', account_holder: '', logo: null });
}
function removeBank(i) {
    store.config.content.gift.banks.splice(i, 1);
}

function addEwallet() {
    store.config.content.gift.ewallets.push({ provider: 'gopay', account_no: '', account_holder: '' });
}
function removeEwallet(i) {
    store.config.content.gift.ewallets.splice(i, 1);
}
</script>

<template>
    <div class="space-y-4">

        <!-- Receiver info -->
        <div class="space-y-3">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Nama Penerima Kado (opsional)</span>
                <input v-model="store.config.content.gift.receiver_name" type="text"
                    placeholder="mis. Budi & Siti"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Alamat Pengiriman Kado (opsional)</span>
                <textarea v-model="store.config.content.gift.address" rows="2"
                    placeholder="Jl. Contoh No.1, Kota"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
            </label>
        </div>

        <hr class="border-slate-100">

        <!-- Bank accounts -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-700">Rekening Bank</span>
                <button @click="addBank"
                    class="text-xs text-rose-600 hover:text-rose-700 font-medium">+ Tambah</button>
            </div>

            <div v-if="store.config.content.gift.banks.length === 0"
                class="text-xs text-slate-400 italic py-2 text-center">
                Belum ada rekening. Klik "+ Tambah" untuk menambahkan.
            </div>

            <div v-for="(bank, i) in store.config.content.gift.banks" :key="i"
                class="mb-3 p-3 rounded-lg border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-600">Rekening #{{ i + 1 }}</span>
                    <button @click="removeBank(i)" class="text-xs text-red-400 hover:text-red-600">Hapus</button>
                </div>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Nama Bank</span>
                    <input v-model="bank.name" type="text" placeholder="BCA, Mandiri, BRI, dst."
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Nomor Rekening</span>
                    <input v-model="bank.account_no" type="text" placeholder="1234567890"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Atas Nama</span>
                    <input v-model="bank.account_holder" type="text" placeholder="a.n. Nama Pemilik"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- E-wallets -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-700">E-Wallet</span>
                <button @click="addEwallet"
                    class="text-xs text-rose-600 hover:text-rose-700 font-medium">+ Tambah</button>
            </div>

            <div v-if="store.config.content.gift.ewallets.length === 0"
                class="text-xs text-slate-400 italic py-2 text-center">
                Belum ada e-wallet. Klik "+ Tambah" untuk menambahkan.
            </div>

            <div v-for="(ew, i) in store.config.content.gift.ewallets" :key="i"
                class="mb-3 p-3 rounded-lg border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-600">E-Wallet #{{ i + 1 }}</span>
                    <button @click="removeEwallet(i)" class="text-xs text-red-400 hover:text-red-600">Hapus</button>
                </div>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Provider</span>
                    <select v-model="ew.provider"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                        <option v-for="opt in EWALLET_OPTIONS" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Nomor / ID Akun</span>
                    <input v-model="ew.account_no" type="text" placeholder="08xxxxxxxxxx"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Atas Nama</span>
                    <input v-model="ew.account_holder" type="text" placeholder="a.n. Nama Pemilik"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </div>

    </div>
</template>
