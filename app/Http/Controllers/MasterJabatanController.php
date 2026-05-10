<?php

namespace App\Http\Controllers;

use App\Models\MasterJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MasterJabatanController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_jabatan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $masterJabatan = MasterJabatan::create($validateData);

            DB::commit();

            return response()->json($masterJabatan);
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterJabatan $masterJabatan)
    {
        return view("pages.dashboard.kepegawaian.jabatan.master_jabatan.editMasterJabatan", [
            'masterJabatan' => $masterJabatan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterJabatan $masterJabatan)
    {
        $validateData = $request->validate([
            'nama_jabatan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $masterJabatan->update($validateData);

            DB::commit();

            return response()->json($masterJabatan);
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data : ' . $e->getMessage());

            return response()->json(['error' => 'Gagal mengubah data'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterJabatan $masterJabatan)
    {
        try {
            $masterJabatan->delete();
            return response()->json(['message' => 'Berhasil menghapus data']);
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus data'], 500);
        }
    }
}
