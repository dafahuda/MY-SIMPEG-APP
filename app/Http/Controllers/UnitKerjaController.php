<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class UnitKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unitkerja = UnitKerja::paginate(5);

        return view("pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja.opd_skpd_unitkerja", [
            'unitkerja' => $unitkerja
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja.tambahUnitKerja");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_unit' => 'required|string',
            'alamat' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            UnitKerja::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/opd_skpd_unitkerja')->with('success', 'Berhasil menambahkan data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitKerja $unitKerja)
    {
        return view("pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja.editUnitKerja", [
            'unitKerja' => $unitKerja
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitKerja $unitKerja)
    {
        $validateData = $request->validate([
            'nama_unit' => 'required|string',
            'alamat' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $unitKerja->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/opd_skpd_unitkerja')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
           DB::rollBack();

           Log::error('Gagal mengubah data : ' . $e->getMessage());

           return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitKerja $unitKerja)
    {
        try {
            $unitKerja->delete();

            return redirect('/manajemen_setup/opd_skpd_unitkerja')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariUnitKerja(Request $request)
    {
        $cariPegawai = $request->input('cariUnitKerja');
        $unitkerja = UnitKerja::query()
                   ->where('nama_unit', 'like', '%' . $cariPegawai . '%')
                   ->orWhere('alamat', 'like', '%' . $cariPegawai . '%')
                   ->paginate(5);

        return view("pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja.opd_skpd_unitkerja", [
            'unitkerja' => $unitkerja
        ]);
    }
}
