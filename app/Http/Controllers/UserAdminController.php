<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UnitKerja;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizeSuperadmin();

        $validated = $request->validate([
            'cariUserAdmin' => 'nullable|string|max:100',
        ]);

        $cariUserAdmin = trim($validated['cariUserAdmin'] ?? '');
        $query = User::where('role', 'admin')->with('unit_kerja');

        if ($cariUserAdmin !== '') {
            $query->where(function ($q) use ($cariUserAdmin) {
                $q->where('username', 'like', '%' . $cariUserAdmin . '%')
                    ->orWhere('name', 'like', '%' . $cariUserAdmin . '%')
                    ->orWhere('email', 'like', '%' . $cariUserAdmin . '%');
            });
        }

        $user = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('pages.dashboard.manajemen_setup.userAdmin.data_user_admin', [
            'user' => $user,
            'cariUserAdmin' => $cariUserAdmin,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeSuperadmin();

        $unitKerja = UnitKerja::all();
        return view('pages.dashboard.manajemen_setup.userAdmin.tambah_user_admin', [
            'unitKerja' => $unitKerja
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeSuperadmin();

        $validateData = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id',
            'password' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $validateData['password'] = bcrypt($validateData['password']);
            $validateData['role'] = 'admin';

            User::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_admin')->with('success', 'Berhasil menambahkan data user admin!');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan user admin : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorizeSuperadmin();
        abort_unless($user->role === 'admin', 403);

        $unitKerja = UnitKerja::all();
        return view("pages.dashboard.manajemen_setup.userAdmin.edit_user_admin", [
            'user' => $user,
            'unitKerja' => $unitKerja
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeSuperadmin();
        abort_unless($user->role === 'admin', 403);

        $validateData = $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id'
        ]);

        try {
            DB::beginTransaction();

            $validateData['role'] = 'admin';

            $user->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_admin')->with('success', 'Berhasil mengubah data user admin!');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah user admin : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeSuperadmin();
        abort_unless($user->role === 'admin', 403);

        try {
            $user->delete();

            return redirect('/manajemen_setup/data_user_admin')->with('success', 'Berhasil menghapus data!');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Error, terjadi kesalahan pada sistem!');
        }
    }

    public function cariUserAdmin(Request $request)
    {
        return $this->index($request);
    }

    private function authorizeSuperadmin(): void
    {
        abort_unless(Auth::user()?->role === 'superadmin', 403);
    }
}
