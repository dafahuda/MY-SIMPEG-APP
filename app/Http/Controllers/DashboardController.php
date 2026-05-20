<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\Pegawai;
use App\Models\Cuti;
use App\Models\Diklat;
use App\Models\Penghargaan;
use App\Models\Jabatan;
use App\Models\Eselon;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\Pangkat;
use App\Models\MasterGolongan;
use App\Models\MasterEselon;
use App\Services\DiklatGapAnalyticsService;
use App\Services\DiklatScopeService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private DiklatScopeService $diklatScopeService,
        private DiklatGapAnalyticsService $diklatGapAnalyticsService,
    ) {
    }

    public function index()
    {
        // Role pegawai langsung diarahkan ke halaman profil
        if (auth()->user()->role === 'pegawai') {
            return redirect()->route('profile.pegawai');
        }

        // Statistik Kartu
        $totalPegawai = Pegawai::count();
        $totalOPD = UnitKerja::count();
        $totalDiklat = Diklat::count();
        $totalPenghargaan = Penghargaan::count();

        // Data untuk chart - Statistik Jumlah Pegawai Berdasarkan OPD/SKPD/Unit Kerja
        // Coba ambil dari database, fallback ke dummy jika kosong
        $pegawaiPerUnitKerjaDb = Pegawai::select('unit_kerja_id', DB::raw('count(*) as total'))
            ->groupBy('unit_kerja_id')
            ->with('unit_kerja')
            ->get();

        if ($pegawaiPerUnitKerjaDb->count() > 0) {
            $pegawaiPerUnitKerja = $pegawaiPerUnitKerjaDb->map(function($item) {
                return [
                    'unit_kerja' => $item->unit_kerja->nama_unit ?? 'Tidak Ada Unit',
                    'total' => $item->total
                ];
            })->values()->toArray();
        } else {
            // Data dummy OPD/Unit Kerja
            $pegawaiPerUnitKerja = [
                ['unit_kerja' => 'Sekretariat Daerah', 'total' => 8],
                ['unit_kerja' => 'Sekretariat DPRD', 'total' => 5],
                ['unit_kerja' => 'Inspektorat', 'total' => 4],
                ['unit_kerja' => 'Dinas Pendidikan dan Kebudayaan', 'total' => 11],
                ['unit_kerja' => 'Dinas Lingkup SD', 'total' => 7],
                ['unit_kerja' => 'Dinas Kesehatan', 'total' => 3],
                ['unit_kerja' => 'Dinas PUPR', 'total' => 2],
                ['unit_kerja' => 'Badan Perencanaan Pembangunan Daerah', 'total' => 6],
                ['unit_kerja' => 'Badan Pengelolaan Keuangan Daerah', 'total' => 10],
                ['unit_kerja' => 'Badan Kepegawaian dan Pengembangan SDM (BKPSDM)', 'total' => 9],
                ['unit_kerja' => 'BPBD', 'total' => 4],
                ['unit_kerja' => 'RSUD Cilacap', 'total' => 7],
            ];
        }

        // Berkala Gaji 1 Bulan Kedepan - dari data pegawai berdasarkan tmt_pns
        $now = now();
        $oneMonthLater = now()->addMonth();
        $gajiMendatangDb = Pegawai::whereNotNull('tmt_pns')
            ->whereRaw("DATE_FORMAT(tmt_pns, '%m-%d') BETWEEN DATE_FORMAT(?, '%m-%d') AND DATE_FORMAT(?, '%m-%d')", [$now, $oneMonthLater])
            ->get(['nip', 'nama', 'tmpt_lahir', 'tgl_lahir', 'tmt_pns']);

        $gajiMendatang = $gajiMendatangDb->map(function($p) {
            return [
                'nip'     => $p->nip,
                'nama'    => $p->nama,
                'ttl'     => ($p->tmpt_lahir ?? '-') . ', ' . ($p->tgl_lahir ?? '-'),
                'periode' => $p->tmt_pns,
            ];
        })->values()->toArray();

        // Berkala Pangkat 1 Bulan Kedepan - dari data pangkat berdasarkan tmt_pangkat_selesai
        $pangkatMendatangDb = Pangkat::with(['pegawai', 'master_golongan'])
            ->whereBetween('tmt_pangkat_selesai', [$now->toDateString(), $oneMonthLater->toDateString()])
            ->get();

        $pangkatMendatang = $pangkatMendatangDb->map(function($p) {
            return [
                'nip'     => $p->pegawai->nip ?? '-',
                'nama'    => $p->pegawai->nama ?? '-',
                'ttl'     => ($p->pegawai->tmpt_lahir ?? '-') . ', ' . ($p->pegawai->tgl_lahir ?? '-'),
                'periode' => $p->tmt_pangkat_selesai,
            ];
        })->values()->toArray();

        // Statistik Golongan - dari data pangkat aktif (tmt_pangkat_selesai terbaru per pegawai)
        $statistikGolonganDb = Pangkat::select('master_golongan_id', DB::raw('count(*) as jumlah'))
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('tb_pangkat')
                    ->groupBy('pegawai_id');
            })
            ->with('master_golongan')
            ->groupBy('master_golongan_id')
            ->get();

        $statistikGolongan = $statistikGolonganDb->map(function($item) {
            return [
                'golongan' => $item->master_golongan->nama_golongan ?? 'Tidak Diketahui',
                'jumlah'   => $item->jumlah,
            ];
        })->values()->toArray();

        // Statistik Eselon - dari data jabatan aktif (jabatan terbaru per pegawai)
        $statistikEselonDb = Jabatan::select('master_eselon_id', DB::raw('count(*) as jumlah'))
            ->whereNotNull('master_eselon_id')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('tb_jabatan')
                    ->groupBy('pegawai_id');
            })
            ->with('master_eselon')
            ->groupBy('master_eselon_id')
            ->get();

        $statistikEselon = $statistikEselonDb->map(function($item) {
            return [
                'eselon' => $item->master_eselon->nama_eselon ?? 'Tidak Diketahui',
                'jumlah' => $item->jumlah,
            ];
        })->values()->toArray();

        // Statistik Jenis Kelamin - dari field jenis_kelamin di tb_pegawai
        $statistikJenisKelaminDb = Pegawai::select('jenis_kelamin', DB::raw('count(*) as jumlah'))
            ->whereNotNull('jenis_kelamin')
            ->groupBy('jenis_kelamin')
            ->get();

        $statistikJenisKelamin = $statistikJenisKelaminDb->map(function($item) {
            return [
                'jenis'  => $item->jenis_kelamin,
                'jumlah' => $item->jumlah,
            ];
        })->values()->toArray();

        // Statistik Status Kepegawaian - dari field status_kepegawaian di tb_pegawai
        $statistikStatusDb = Pegawai::select('status_kepegawaian', DB::raw('count(*) as jumlah'))
            ->whereNotNull('status_kepegawaian')
            ->groupBy('status_kepegawaian')
            ->get();

        $statistikStatus = $statistikStatusDb->map(function($item) {
            return [
                'status' => $item->status_kepegawaian,
                'jumlah' => $item->jumlah,
            ];
        })->values()->toArray();

        $currentYear = (int) now()->year;
        $user = auth()->user();
        $diklatRencana = $this->diklatScopeService
            ->scopeRencanaDiklatQuery(
                RencanaDiklat::with(['diklat', 'pegawai.unit_kerja'])->where('tahun_rencana', $currentYear),
                $user
            )
            ->get();
        $diklatRealisasi = $this->diklatScopeService
            ->scopeDiklatQuery(
                Diklat::with(['rencanaDiklat', 'pegawai.unit_kerja'])->where('tahun', $currentYear),
                $user
            )
            ->get();
        $diklatAnalytics = $this->diklatGapAnalyticsService->summary($diklatRencana, $diklatRealisasi);
        $diklatAnalytics['year'] = $currentYear;
        $diklatAnalytics['unit_name'] = $user->role === 'admin'
            ? ($user->unit_kerja->nama_unit ?? 'Unit kerja Anda')
            : 'Semua Unit Kerja';

        return view('pages/dashboard/dashboard', compact(
            'totalPegawai',
            'totalOPD',
            'totalDiklat',
            'totalPenghargaan',
            'pegawaiPerUnitKerja',
            'gajiMendatang',
            'pangkatMendatang',
            'statistikGolongan',
            'statistikEselon',
            'statistikJenisKelamin',
            'statistikStatus',
            'diklatAnalytics'
        ));
    }

    /**
     * Displays the analytics screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    /**
     * Displays the fintech screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
