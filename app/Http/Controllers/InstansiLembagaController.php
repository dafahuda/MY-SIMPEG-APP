<?php

namespace App\Http\Controllers;

use App\Models\InstansiLembaga;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                $file = $request->file('gambar_logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['gambar_logo'] = '/storage/' . $path;
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

            if($request->hasFile('gambar_logo')) {

                if($instansiLembaga->gambar_logo) {
                    $oldPath = str_replace('/storage/', '', $instansiLembaga->gambar_logo);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('gambar_logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['gambar_logo'] = '/storage/' . $path;
            } else {
                unset($validateData['gambar_logo']);
            }

            $instansiLembaga->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/instansi_lembaga')->with('success', 'Berhasil setup Instansi lembaga!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal melakukan setup instansi lembaga : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }
}
