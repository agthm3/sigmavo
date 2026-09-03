<div x-show="openImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60">
    <div @click.away="openImportModal = false" class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between">
            <h3 class="font-bold"><i class="fas fa-file-excel mr-2"></i> Upload Data Excel</h3>
            <button @click="openImportModal = false"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('dashboard-manajemen-aktivasi-import-preview') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="p-3 bg-emerald-50 rounded-xl text-xs text-emerald-800">
                <p>Unggah berkas <strong>Template Excel (.xlsx)</strong> yang telah diisi.</p>
            </div>
            <input type="file" name="file_excel" required accept=".xlsx, .xls" class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-lg file:bg-emerald-50 file:text-emerald-700">
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="openImportModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Preview</button>
            </div>
        </form>
    </div>
</div>