<?php

namespace App\Http\Controllers;

use App\Models\Sekretariat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class SekretariatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sekretariat = Sekretariat::first();

        return view("pages.dashboard.manajemen_setup.sekretariat.indexSekretariat", [
            'sekretariat' => $sekretariat
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pages.dashboard.manajemen_setup.sekretariat.tambahSekretariat");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_sekretariat' => 'required|string',
            'kabupaten_kota' => 'required',
            'nama_kabupaten_kota' => 'required|string',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|string',
            'sekretaris' => 'required|string',
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

            Sekretariat::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/sekretariat')->with('success', 'Berhasil membuat data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal membuat data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sekretariat $sekretariat)
    {
        return view("pages.dashboard.manajemen_setup.sekretariat.SetupSekretariat", [
            'sekretariat' => $sekretariat
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sekretariat $sekretariat)
    {
        $validateData = $request->validate([
            'nama_sekretariat' => 'required|string',
            'kabupaten_kota' => 'required',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|string',
            'sekretaris' => 'required|string',
            'gambar_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('gambar_logo')) {

                if($sekretariat->gambar_logo) {
                    $oldPath = str_replace('/storage/', '', $sekretariat->gambar_logo);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('gambar_logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['gambar_logo'] = '/storage/' . $path;
            } else {
                unset($validateData['gambar_logo']);
            }

            $sekretariat->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/sekretariat')->with('success', 'Berhasil setup sekretariat!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal setup sekretariat : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }
}
