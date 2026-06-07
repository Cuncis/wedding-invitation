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
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">

        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Amplop Digital</p>
                <p class="text-xs text-base-content/50">Rekening bank &amp; e-wallet penerima</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Receiver info -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Nama Penerima Kado (opsional)</span></div>
            <input v-model="store.config.content.gift.receiver_name" type="text"
                placeholder="mis. Budi & Siti"
                class="input input-sm input-bordered w-full">
        </label>
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Alamat Pengiriman Kado (opsional)</span></div>
            <textarea v-model="store.config.content.gift.address" rows="2"
                placeholder="Jl. Contoh No.1, Kota"
                class="textarea textarea-sm textarea-bordered w-full resize-none"></textarea>
        </label>

        <div class="divider my-0 text-xs text-base-content/40">Rekening Bank</div>

        <!-- Bank accounts -->
        <div class="space-y-2">
            <div v-if="store.config.content.gift.banks.length === 0"
                class="text-xs text-base-content/40 italic py-1 text-center">
                Belum ada rekening.
            </div>
            <div v-for="(bank, i) in store.config.content.gift.banks" :key="i"
                class="bg-base-100 border border-base-300 rounded-lg p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">Rekening #{{ i + 1 }}</span>
                    <button @click="removeBank(i)" type="button" class="btn btn-xs btn-error btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hapus
                    </button>
                </div>
                <input v-model="bank.name" type="text" placeholder="BCA, Mandiri, BRI, dst."
                    class="input input-xs input-bordered w-full">
                <input v-model="bank.account_no" type="text" placeholder="Nomor Rekening"
                    class="input input-xs input-bordered w-full font-mono">
                <input v-model="bank.account_holder" type="text" placeholder="a.n. Nama Pemilik"
                    class="input input-xs input-bordered w-full">
            </div>
            <button @click="addBank" type="button" class="btn btn-sm btn-primary btn-outline w-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Rekening Bank
            </button>
        </div>

        <div class="divider my-0 text-xs text-base-content/40">E-Wallet</div>

        <!-- E-wallets -->
        <div class="space-y-2">
            <div v-if="store.config.content.gift.ewallets.length === 0"
                class="text-xs text-base-content/40 italic py-1 text-center">
                Belum ada e-wallet.
            </div>
            <div v-for="(ew, i) in store.config.content.gift.ewallets" :key="i"
                class="bg-base-100 border border-base-300 rounded-lg p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">E-Wallet #{{ i + 1 }}</span>
                    <button @click="removeEwallet(i)" type="button" class="btn btn-xs btn-error btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hapus
                    </button>
                </div>
                <select v-model="ew.provider" class="select select-xs select-bordered w-full">
                    <option v-for="opt in EWALLET_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <input v-model="ew.account_no" type="text" placeholder="Nomor / ID Akun"
                    class="input input-xs input-bordered w-full font-mono">
                <input v-model="ew.account_holder" type="text" placeholder="a.n. Nama Pemilik"
                    class="input input-xs input-bordered w-full">
            </div>
            <button @click="addEwallet" type="button" class="btn btn-sm btn-primary btn-outline w-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah E-Wallet
            </button>
        </div>

    </div>
</template>
