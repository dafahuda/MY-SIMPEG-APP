<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\UnitKerja;

class UserPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $actor = Auth::user();
        $this->authorizeUserPegawaiAccess($actor);

        $filters = $request->validate([
            'cariUserPegawai' => 'nullable|string|max:100',
        ]);

        $search = trim((string) ($filters['cariUserPegawai'] ?? ''));
        $query = $this->scopedPegawaiUserQuery($actor)->with('unit_kerja')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $user = $query->paginate(10)->withQueryString();
        $totalPegawaiUsers = $this->scopedPegawaiUserQuery($actor)->count();

        return view("pages.dashboard.manajemen_setup.userPegawai.data_user_pegawai", [
            "user" => $user,
            'search' => $search,
            'totalPegawaiUsers' => $totalPegawaiUsers,
            'scopeLabel' => $this->scopeLabel($actor),
        ]);
    }

    public function create()
    {
        $actor = Auth::user();
        $this->authorizeUserPegawaiAccess($actor);

        return view("pages.dashboard.manajemen_setup.userPegawai.tambah_user_pegawai", [
            'unitKerja' => $this->unitKerjaOptions($actor),
            'scopeLabel' => $this->scopeLabel($actor),
        ]);
    }

    public function store(Request $request)
    {
        $actor = Auth::user();
        $this->authorizeUserPegawaiAccess($actor);

        $validateData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id',
        ]);

        if ($actor->role === 'admin' && (int) $validateData['unit_kerja_id'] !== (int) $actor->unit_kerja_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $validateData['password'] = bcrypt($validateData['password']);
            $validateData['role'] = 'pegawai';

            User::create($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Akun pegawai berhasil ditambahkan.');

        } catch(Exception $e) {
            DB::rollBack();

            Log::error("Gagal membuat akun : " . $e->getMessage());

            return back()->withInput()->with('error', 'Akun pegawai gagal disimpan karena terjadi kesalahan sistem.');
        }
    }

    public function edit(User $user)
    {
        $actor = Auth::user();
        $this->authorizeTargetUserPegawaiAccess($user, $actor);

        return view("pages.dashboard.manajemen_setup.userPegawai.edit_user_pegawai", [
            'unitKerja' => $this->unitKerjaOptions($actor),
            'user' => $user,
            'scopeLabel' => $this->scopeLabel($actor),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $actor = Auth::user();
        $this->authorizeTargetUserPegawaiAccess($user, $actor);

        $validateData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'unit_kerja_id' => 'required|exists:tb_unit_kerja,id'
        ]);

        if ($actor->role === 'admin' && (int) $validateData['unit_kerja_id'] !== (int) $actor->unit_kerja_id) {
            abort(403);
        }

        $validateData['role'] = 'pegawai';

        try {
            DB::beginTransaction();

            $user->update($validateData);

            DB::commit();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Akun pegawai berhasil diubah.');
        } catch(Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengubah data user pegawai! ' . $e->getMessage());

            return back()->withInput()->with('error', 'Akun pegawai gagal diubah karena terjadi kesalahan sistem.');
        }
    }

    public function destroy(User $user)
    {
        $this->authorizeTargetUserPegawaiAccess($user);

        try {
            $user->delete();

            return redirect('/manajemen_setup/data_user_pegawai')->with('success', 'Akun pegawai berhasil dihapus.');
        } catch(Exception $e) {
            Log::error('Gagal menghapus data : ' . $e->getMessage());

            return back()->withInput()->with('error', 'Akun pegawai gagal dihapus karena terjadi kesalahan sistem.');
        }
    }

    public function cariUserPegawai(Request $request)
    {
        return $this->index($request);
    }

    private function scopedPegawaiUserQuery(User $actor)
    {
        $query = User::where('role', 'pegawai');

        if ($actor->role === 'admin') {
            $query->where('unit_kerja_id', $actor->unit_kerja_id);
        }

        return $query;
    }

    private function unitKerjaOptions(User $actor)
    {
        $query = UnitKerja::query()->orderBy('nama_unit');

        if ($actor->role === 'admin') {
            $query->where('id', $actor->unit_kerja_id);
        }

        return $query->get();
    }

    private function scopeLabel(User $actor): string
    {
        if ($actor->role === 'admin') {
            return 'Scope admin: hanya akun pegawai unit ' . ($actor->unit_kerja?->nama_unit ?? 'unit Anda');
        }

        return 'Scope superadmin: semua akun pegawai';
    }

    private function authorizeUserPegawaiAccess(?User $actor): void
    {
        abort_unless($actor && in_array($actor->role, ['superadmin', 'admin'], true), 403);
    }

    private function authorizeTargetUserPegawaiAccess(User $user, ?User $actor = null): void
    {
        $actor ??= Auth::user();
        $this->authorizeUserPegawaiAccess($actor);

        abort_unless($user->role === 'pegawai', 403);

        if ($actor->role === 'admin') {
            abort_unless((int) $user->unit_kerja_id === (int) $actor->unit_kerja_id, 403);
        }
    }
}
