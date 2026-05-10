<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::with('unit_kerja')
                ->where('unit_kerja_id', $user->unit_kerja_id)
                ->paginate(5);
        } else {
            // super admin / lainnya bisa lihat semua
            $pegawai = Pegawai::with('unit_kerja')->paginate(5);
        }

        return view("pages.dashboard.data_pegawai.indexPegawai", [
            "pegawai" => $pegawai
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitKerja   = UnitKerja::all();
        $userPegawai = User::where('role', 'pegawai')
            ->whereNotIn('id', \App\Models\Pegawai::whereNotNull('user_id')->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view("pages.dashboard.data_pegawai.tambahPegawai", [
            'unitKerja'   => $unitKerja,
            'userPegawai' => $userPegawai,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nip' => 'required|string',
            'nama' => 'required|string',
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id',
            'gelar' => 'nullable|string',
            'gelar_depan' => 'nullable|string',
            'tmpt_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'golongan_darah' => 'required',
            'status_pernikahan' => 'required',
            'nik' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'email_gov' => 'required|email',
            'no_npwp' => 'required|string',
            'no_bpjs' => 'required|string',
            'status_kepegawaian' => 'required',
            'karpeg' => 'required|string',
            'no_sk_cpns' => 'nullable',
            'tmt_cpns' => 'required|date',
            'no_sk_pns' => 'nullable',
            'tmt_pns' => 'required|date',
            'gol_awal' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nilai_tpp' => 'required',
        ]);

        try {
            DB::beginTransaction();

            if($request->has('foto')) {
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['foto'] = '/storage/' . $path;
            }

            $validateData['user_id'] = $request->input('user_id');

            Pegawai::create($validateData);

            DB::commit();

            return redirect('/data_pegawai/pegawai')->with('success', 'Berhasil menambahkan data pegawai!');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal menyimpan data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan sistem!');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        return view("pages.dashboard.data_pegawai.detailPegawai", [
            'pegawai' => $pegawai
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $unitKerja   = UnitKerja::all();
        $userPegawai = User::where('role', 'pegawai')
            ->where(function ($q) use ($pegawai) {
                // Tampilkan user yang belum terhubung ke pegawai lain, ATAU user yang sudah terhubung ke pegawai ini
                $q->whereNotIn('id', \App\Models\Pegawai::whereNotNull('user_id')->where('id', '!=', $pegawai->id)->pluck('user_id'))
                  ->orWhere('id', $pegawai->user_id);
            })
            ->orderBy('name')
            ->get();

        return view("pages.dashboard.data_pegawai.editPegawai", [
            'pegawai'     => $pegawai,
            'unitKerja'   => $unitKerja,
            'userPegawai' => $userPegawai,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validateData = $request->validate([
            'nip' => 'required|string',
            'nama' => 'required|string',
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id',
            'gelar' => 'nullable|string',
            'gelar_depan' => 'nullable|string',
            'tmpt_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'golongan_darah' => 'required',
            'status_pernikahan' => 'required',
            'nik' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'email_gov' => 'required|email',
            'no_npwp' => 'required|string',
            'no_bpjs' => 'required|string',
            'status_kepegawaian' => 'required',
            'karpeg' => 'required|string',
            'no_sk_cpns' => 'nullable',
            'tmt_cpns' => 'required|date',
            'no_sk_pns' => 'nullable',
            'tmt_pns' => 'required|date',
            'gol_awal' => 'required|string',
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'nilai_tpp' => 'required'
        ]);

        try {
            DB::beginTransaction();

            if($request->hasFile('foto')) {

                if($request->gambarLama) {
                    Storage::disk('public')->delete($request->gambarLama);
                }

                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $validateData['foto'] = '/storage/' . $path;
            }

            $validateData['user_id'] = $request->input('user_id');

            $pegawai->update($validateData);


            DB::commit();

            return redirect('/data_pegawai/pegawai')->with('success', 'Berhasil mengubah data!');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal mengubah data : " . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan dengan sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return redirect('/data_pegawai/pegawai')->with('success', 'Berhasil menghapus data!');
    }

    public function cariPegawai(Request $request)
    {
        $user = Auth::user();
        $cariPegawai = $request->input('cariPegawai');

        $query = Pegawai::with('unit_kerja')
                    ->where('nama', 'like', '%' . $cariPegawai . '%');

        if ($user->role === 'admin') {
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        }

        $pegawai = $query->paginate(5);

        return view("pages.dashboard.data_pegawai.indexPegawai", [
            'pegawai' => $pegawai
        ]);
    }
}
