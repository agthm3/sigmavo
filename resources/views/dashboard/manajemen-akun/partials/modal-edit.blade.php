<div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="openEditModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border flex flex-col max-h-[90vh]">
        <div class="p-6 bg-vokasi-primary text-white flex justify-between shrink-0">
            <h3 class="font-bold"><i class="fas fa-user-edit"></i> Edit Pengguna</h3>
            <button type="button" @click="openEditModal = false"><i class="fas fa-times"></i></button>
        </div>
        <form :action="editUser.updateUrl" method="POST" class="p-6 space-y-4 text-xs overflow-y-auto custom-scrollbar flex-1">
            @csrf @method('PUT')
            <div>
                <label class="block font-bold mb-1">Nama</label>
                <input type="text" name="name" :value="editUser.name" class="w-full px-3.5 py-2.5 border rounded-xl bg-gray-50" required>
            </div>
            <div>
                <label class="block font-bold mb-1">Email</label>
                <input type="email" name="email" :value="editUser.email" class="w-full px-3.5 py-2.5 border rounded-xl bg-gray-50" required>
            </div>
            <div class="hidden"><input type="text" name="role" :value="editRole"></div>
            
            <div x-show="editRole === 'mahasiswa'">
                <label class="block font-bold mb-1">NIM</label>
                <input type="text" name="nim" :value="editUser.nim" class="w-full px-3.5 py-2.5 border rounded-xl bg-gray-50">
            </div>
            <div x-show="editRole === 'dosen' || editRole === 'admin_prodi'">
                <label class="block font-bold mb-1">NIP/NIDN</label>
                <input type="text" name="nip_nidn" :value="editUser.nip_nidn" class="w-full px-3.5 py-2.5 border rounded-xl bg-gray-50">
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-vokasi-primary text-white font-bold rounded-xl">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>