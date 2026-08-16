<?php

namespace App\Http\Controllers;

use App\Models\TemplateDokumen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadTemplateController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = TemplateDokumen::with('prodi');

        $isSuperOrAdmin = $user->hasAnyRole(['superadmin', 'admin']);

        // Hak Akses Tampilan Scope
        if ($isSuperOrAdmin) {
            // Superadmin / Admin: Bisa filter per prodi jika memilih dari dropdown filter
            if ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
                if ($request->prodi_id === 'umum') {
                    $query->whereNull('prodi_id');
                } else {
                    $query->where('prodi_id', $request->prodi_id);
                }
            }
        } elseif ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            $query->where(function($q) use ($adminProdiId) {
                $q->where('prodi_id', $adminProdiId)->orWhereNull('prodi_id');
            });
        } elseif ($user->hasRole('dosen')) {
            $dosenProdiId = $user->dosenProfile?->prodi_id;
            $query->where(function($q) use ($dosenProdiId) {
                $q->where('prodi_id', $dosenProdiId)->orWhereNull('prodi_id');
            });
        } elseif ($user->hasRole('mahasiswa')) {
            $mhsProdiId = $user->mahasiswaProfile?->prodi_id;
            $query->where(function($q) use ($mhsProdiId) {
                $q->where('prodi_id', $mhsProdiId)->orWhereNull('prodi_id');
            });
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_dokumen', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $templates = $query->latest()->get();
        $prodis = Prodi::orderBy('nama_prodi', 'asc')->get();

        // Izin Manajemen (Superadmin, Admin, Admin Prodi, Dosen)
        $canManage = $user->hasAnyRole(['superadmin', 'admin', 'admin_prodi', 'dosen']);

        return view('dashboard.download-template.index', compact('templates', 'prodis', 'canManage', 'isSuperOrAdmin', 'user'));
    }

    /**
     * Tambah Template Dokumen Baru
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['superadmin', 'admin', 'admin_prodi', 'dosen'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambahkan template.');
        }

        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'prodi_id'      => 'nullable',
            'kategori'      => 'required|string|max:50',
            'versi'         => 'nullable|string|max:20',
            'deskripsi'     => 'nullable|string',
            'file_template' => 'required|file|mimes:docx,doc,pptx,ppt,pdf,xlsx,xls,zip,rar|max:20480',
        ]);

        $prodiId = null;

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            // Superadmin / Admin menentukan prodi sendiri (bisa 'all' atau ID prodi)
            $prodiId = ($request->prodi_id === 'all' || empty($request->prodi_id)) ? null : $request->prodi_id;
        } elseif ($user->hasRole('admin_prodi')) {
            $prodiId = $user->adminProdiProfile?->prodi_id;
        } elseif ($user->hasRole('dosen')) {
            $prodiId = $user->dosenProfile?->prodi_id;
        }

        $file = $request->file('file_template');
        $extension = $file->getClientOriginalExtension();
        $fileSize = $this->formatBytes($file->getSize());
        $originalName = $file->getClientOriginalName();
        $path = $file->store('template_dokumen', 'public');

        TemplateDokumen::create([
            'prodi_id'       => $prodiId,
            'judul_dokumen'  => $request->judul_dokumen,
            'deskripsi'      => $request->deskripsi,
            'file_path'      => $path,
            'file_name'      => $originalName,
            'file_extension' => strtolower($extension),
            'file_size'      => $fileSize,
            'kategori'       => $request->kategori,
            'versi'          => $request->versi ?? 'v1.0',
        ]);

        return redirect()->back()->with('success', "Template dokumen '{$request->judul_dokumen}' berhasil ditambahkan.");
    }

    /**
     * Perbarui Template Dokumen
     */
    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['superadmin', 'admin', 'admin_prodi', 'dosen'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui template.');
        }

        $template = TemplateDokumen::findOrFail($id);

        // Validasi Scope Hak Akses
        if ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            if ($template->prodi_id && $template->prodi_id != $adminProdiId) {
                return redirect()->back()->with('error', 'Anda hanya dapat mengedit template di lingkup program studi Anda.');
            }
        } elseif ($user->hasRole('dosen')) {
            $dosenProdiId = $user->dosenProfile?->prodi_id;
            if ($template->prodi_id && $template->prodi_id != $dosenProdiId) {
                return redirect()->back()->with('error', 'Anda hanya dapat mengedit template di lingkup program studi Anda.');
            }
        }

        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'prodi_id'      => 'nullable',
            'kategori'      => 'required|string|max:50',
            'versi'         => 'nullable|string|max:20',
            'deskripsi'     => 'nullable|string',
            'file_template' => 'nullable|file|mimes:docx,doc,pptx,ppt,pdf,xlsx,xls,zip,rar|max:20480',
        ]);

        $dataUpdate = [
            'judul_dokumen' => $request->judul_dokumen,
            'kategori'      => $request->kategori,
            'versi'         => $request->versi ?? $template->versi,
            'deskripsi'     => $request->deskripsi,
        ];

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            $dataUpdate['prodi_id'] = ($request->prodi_id === 'all' || empty($request->prodi_id)) ? null : $request->prodi_id;
        }

        if ($request->hasFile('file_template')) {
            if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            $file = $request->file('file_template');
            $dataUpdate['file_extension'] = strtolower($file->getClientOriginalExtension());
            $dataUpdate['file_size'] = $this->formatBytes($file->getSize());
            $dataUpdate['file_name'] = $file->getClientOriginalName();
            $dataUpdate['file_path'] = $file->store('template_dokumen', 'public');
        }

        $template->update($dataUpdate);

        return redirect()->back()->with('success', "Template dokumen '{$template->judul_dokumen}' berhasil diperbarui.");
    }

    /**
     * Hapus Template Dokumen
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['superadmin', 'admin', 'admin_prodi', 'dosen'])) {
            abort(403, 'Akses ditolak.');
        }

        $template = TemplateDokumen::findOrFail($id);

        if ($user->hasRole('admin_prodi')) {
            $adminProdiId = $user->adminProdiProfile?->prodi_id;
            if ($template->prodi_id && $template->prodi_id != $adminProdiId) {
                return redirect()->back()->with('error', 'Anda hanya dapat menghapus template milik program studi Anda.');
            }
        } elseif ($user->hasRole('dosen')) {
            $dosenProdiId = $user->dosenProfile?->prodi_id;
            if ($template->prodi_id && $template->prodi_id != $dosenProdiId) {
                return redirect()->back()->with('error', 'Anda hanya dapat menghapus template milik program studi Anda.');
            }
        }

        if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return redirect()->back()->with('success', 'Template dokumen berhasil dihapus.');
    }

    /**
     * Unduh Berkas Fisik
     */
    public function download($id)
    {
        $template = TemplateDokumen::findOrFail($id);

        if (!Storage::disk('public')->exists($template->file_path)) {
            return redirect()->back()->with('error', 'Berkas fisik template tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($template->file_path, $template->file_name);
    }

    private function formatBytes($bytes, $precision = 1)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}