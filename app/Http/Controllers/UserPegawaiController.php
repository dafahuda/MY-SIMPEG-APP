<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UnitKerja;

class UserPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where("role", "pegawai")->get();

        return view("pages.dashboard.manajemen_setup.userPegawai.data_user_pegawai", [
            "user" => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pages.dashboard.manajemen_setup.userPegawai.tambah_user_pegawai");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            $validateData['password'] = bcrypt($validateData['password']);
            $validateData['role'] = 'pegawai';

            User::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Berhasil menambahkan akun!');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal membuat akun : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $unitKerja = UnitKerja::all();
        return view("pages.dashboard.manajemen_setup.userPegawai.edit_user_pegawai", [
            'unitKerja' => $unitKerja,
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validateData = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id'
        ]);

        try {
            DB::beginTransaction();

            $user->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Berhasil mengubah data atau akun!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data user pegawai!');

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariUserPegawai(Request $request)
    {
        $cariUserPegawai = $request->input('cariUserPegawai');
        $user = User::where('role', 'pegawai')
                   ->where(function($q) use ($cariUserPegawai) {
                       $q->where('username', 'like', '%' . $cariUserPegawai . '%')
                         ->orWhere('name', 'like', '%' . $cariUserPegawai . '%');
                   })
                   ->paginate(5);

        return view("pages.dashboard.manajemen_setup.userPegawai.data_user_pegawai", [
            'user' => $user
        ]);
    }
}
