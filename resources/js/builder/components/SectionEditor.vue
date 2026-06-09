<script setup>
import { ref } from 'vue';
import { useBuilderStore } from '../store';

const store = useBuilderStore();

const uploading = ref({ groom: false, bride: false });
const uploadError = ref({ groom: null, bride: null });

async function uploadCouplePhoto(role, file) {
    if (!file) return;
    uploading.value[role] = true;
    uploadError.value[role] = null;
    try {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('role', role);
        const res = await fetch(`/builder/${store.invitationId}/couple/upload`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept': 'application/json',
            },
            body: formData,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.error || 'Upload gagal');
        }
        const data = await res.json();
        store.config.content = {
            ...store.config.content,
            couple: { ...store.config.content.couple, [`${role}_photo`]: data.url },
        };
    } catch (e) {
        uploadError.value[role] = e.message;
    } finally {
        uploading.value[role] = false;
    }
}

function onPhotoInput(role, e) {
    const file = e.target.files?.[0];
    if (file) uploadCouplePhoto(role, file);
    e.target.value = '';
}

function removePhoto(role) {
    store.config.content = {
        ...store.config.content,
        couple: { ...store.config.content.couple, [`${role}_photo`]: null },
    };
}
</script>

<template>
    <div class="space-y-5">
        <div>
            <h2 class="text-sm font-semibold text-base-content mb-0.5">Konten Undangan</h2>
            <p class="text-xs text-base-content/50 mb-4">Lengkapi detail konten undangan Anda</p>
        </div>

        <!-- Cover -->
        <details open class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Cover</summary>
            <div class="p-4 space-y-3 border-t border-base-200">
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Pembuka (mis. Bismillah...)</span></div>
                    <input v-model="store.config.content.cover.opening_text"
                        type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Tagline</span></div>
                    <input v-model="store.config.content.cover.tagline"
                        type="text" placeholder="Save the Date"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Couple -->
        <details open class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Mempelai</summary>
            <div class="p-4 space-y-4 border-t border-base-200">

                <!-- Groom -->
                <div class="space-y-3">
                    <p class="text-xs font-bold text-base-content/40 uppercase tracking-wider">Pria</p>

                    <!-- Groom photo -->
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-base-200 border-2 border-base-300 flex items-center justify-center">
                            <img v-if="store.config.content.couple.groom_photo"
                                :src="store.config.content.couple.groom_photo"
                                class="w-full h-full object-cover">
                            <span v-else class="text-2xl">🤵</span>
                            <button v-if="store.config.content.couple.groom_photo"
                                @click="removePhoto('groom')"
                                type="button"
                                class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-error text-white flex items-center justify-center leading-none text-xs">✕</button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="btn btn-sm btn-outline w-full relative overflow-hidden"
                                :class="{ 'opacity-60 pointer-events-none': uploading.groom }">
                                <span v-if="uploading.groom" class="loading loading-spinner loading-xs mr-1"></span>
                                {{ uploading.groom ? 'Mengunggah...' : 'Pilih Foto Pria' }}
                                <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer"
                                    @change="onPhotoInput('groom', $event)">
                            </label>
                            <p v-if="uploadError.groom" class="text-xs text-error mt-1">{{ uploadError.groom }}</p>
                            <p class="text-xs text-base-content/40 mt-1">JPG/PNG, maks 10 MB</p>
                        </div>
                    </div>

                    <label class="form-control w-full">
                        <div class="label py-0.5"><span class="label-text text-xs">Nama Lengkap Pria</span></div>
                        <input v-model="store.config.content.couple.groom_fullname" type="text"
                            class="input input-sm input-bordered w-full">
                    </label>
                    <label class="form-control w-full">
                        <div class="label py-0.5"><span class="label-text text-xs">Putra dari</span></div>
                        <input v-model="store.config.content.couple.groom_parents" type="text"
                            placeholder="Bpk. Ahmad &amp; Ibu Siti"
                            class="input input-sm input-bordered w-full">
                    </label>
                </div>

                <div class="divider my-1"></div>

                <!-- Bride -->
                <div class="space-y-3">
                    <p class="text-xs font-bold text-base-content/40 uppercase tracking-wider">Wanita</p>

                    <!-- Bride photo -->
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-base-200 border-2 border-base-300 flex items-center justify-center">
                            <img v-if="store.config.content.couple.bride_photo"
                                :src="store.config.content.couple.bride_photo"
                                class="w-full h-full object-cover">
                            <span v-else class="text-2xl">👰</span>
                            <button v-if="store.config.content.couple.bride_photo"
                                @click="removePhoto('bride')"
                                type="button"
                                class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-error text-white flex items-center justify-center leading-none text-xs">✕</button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="btn btn-sm btn-outline w-full relative overflow-hidden"
                                :class="{ 'opacity-60 pointer-events-none': uploading.bride }">
                                <span v-if="uploading.bride" class="loading loading-spinner loading-xs mr-1"></span>
                                {{ uploading.bride ? 'Mengunggah...' : 'Pilih Foto Wanita' }}
                                <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer"
                                    @change="onPhotoInput('bride', $event)">
                            </label>
                            <p v-if="uploadError.bride" class="text-xs text-error mt-1">{{ uploadError.bride }}</p>
                            <p class="text-xs text-base-content/40 mt-1">JPG/PNG, maks 10 MB</p>
                        </div>
                    </div>

                    <label class="form-control w-full">
                        <div class="label py-0.5"><span class="label-text text-xs">Nama Lengkap Wanita</span></div>
                        <input v-model="store.config.content.couple.bride_fullname" type="text"
                            class="input input-sm input-bordered w-full">
                    </label>
                    <label class="form-control w-full">
                        <div class="label py-0.5"><span class="label-text text-xs">Putri dari</span></div>
                        <input v-model="store.config.content.couple.bride_parents" type="text"
                            placeholder="Bpk. Hasan &amp; Ibu Rina"
                            class="input input-sm input-bordered w-full">
                    </label>
                </div>

            </div>
        </details>

        <!-- Event - Akad -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Akad Nikah</summary>
            <div class="p-4 grid grid-cols-2 gap-3 border-t border-base-200">
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Tanggal</span></div>
                    <input v-model="store.config.content.event.akad.date" type="date"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Jam</span></div>
                    <input v-model="store.config.content.event.akad.time" type="time"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Lokasi</span></div>
                    <input v-model="store.config.content.event.akad.venue" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Alamat</span></div>
                    <input v-model="store.config.content.event.akad.address" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Event - Resepsi -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Resepsi</summary>
            <div class="p-4 grid grid-cols-2 gap-3 border-t border-base-200">
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Tanggal</span></div>
                    <input v-model="store.config.content.event.resepsi.date" type="date"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Jam</span></div>
                    <input v-model="store.config.content.event.resepsi.time" type="time"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Lokasi</span></div>
                    <input v-model="store.config.content.event.resepsi.venue" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Alamat</span></div>
                    <input v-model="store.config.content.event.resepsi.address" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Closing -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Penutup</summary>
            <div class="p-4 border-t border-base-200">
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Pesan Penutup</span></div>
                    <textarea v-model="store.config.content.closing.thank_you" rows="3"
                        class="textarea textarea-bordered textarea-sm w-full"></textarea>
                </label>
            </div>
        </details>
    </div>
</template>
