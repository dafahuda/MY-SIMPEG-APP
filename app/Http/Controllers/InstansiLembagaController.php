<?php

namespace App\Http\Controllers;

use App\Models\InstansiLembaga;
use App\Support\FileUploadHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstansiLembagaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instansiLembaga = InstansiLembaga::first();

        return view("pages.dashboard.manajemen_setup.instansiLembaga.IndexInstansiLembaga", [
            'instansiLembaga' => $instansiLembaga
        ]);
    }

    public function create()
    {
        return view("pages.dashboard.manajemen_setup.instansiLembaga.tambahDataInstansi");
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_instansi_lembaga' => 'required|string',
            'kabupaten_kota' => 'required',
            'nama_kota_kabupaten' => 'required|string',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|string',
            'kepala_dinas' => 'required|string',
            'nip' => 'required|string',
            'gambar_logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('gambar_logo')) {
                $storedFile = FileUploadHelper::validateAndStore(
                    $request->file('gambar_logo'),
                    ['image/jpeg', 'image/png', 'image/gif'],
                    2 * 1024 * 1024,
                    'public',
                    'images',
                    'gambar_logo'
                );

                $validateData['gambar_logo'] = $storedFile['file_path'];
            }

            InstansiLembaga::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/instansi_lembaga')->with('success', 'Berhasil membuat data instansi!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal membuat data instansi : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InstansiLembaga $instansiLembaga)
    {
        return view("pages.dashboard.manajemen_setup.instansiLembaga.SetupInstansiLembaga", [
            'instansiLembaga' => $instansiLembaga
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InstansiLembaga $instansiLembaga)
    {
        $validateData = $request->validate([
            'nama_instansi_lembaga' => 'required|string',
            'kabupaten_kota' => 'required',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|string',
            'kepala_dinas' => 'required|string',
            'gambar_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $oldLogo = $instansiLembaga->gambar_logo;

            if($request->hasFile('gambar_logo')) {
                $storedFile = FileUploadHelper::validateAndStore(
                    $request->file('gambar_logo'),
                    ['image/jpeg', 'image/png', 'image/gif'],
                    2 * 1024 * 1024,
                    'public',
                    'images',
                    'gambar_logo'
                );

                $validateData['gambar_logo'] = $storedFile['file_path'];
            } else {
                unset($validateData['gambar_logo']);
            }

            $instansiLembaga->update($validateData);

            if($request->hasFile('gambar_logo')) {
                FileUploadHelper::delete($oldLogo, 'public');
            }

            DB::commit();

            return redirect('/manajemen_setup/instansi_lembaga')->with('success', 'Berhasil setup Instansi lembaga!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal melakukan setup instansi lembaga : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }
}
