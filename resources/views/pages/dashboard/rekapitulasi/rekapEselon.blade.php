<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-4">
            <div>
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Rekapitulasi</span>
                    <span>/</span>
                    <span>Eselon</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Rekapitulasi <span class="text-base font-normal text-gray-500 dark:text-gray-400">Data Eselon</span>
                </h1>
            </div>
            <div class="mt-4 sm:mt-0">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm transition-colors duration-150">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11 0H5v4H1v8h4v4h6v-4h4V4h-4V0zm-1 1v3H6V1h4zm1 10H5v-1h6v1zm0-2H5v-1h6v1zm2-2H3V5h10v2z" />
                    </svg>
                    Print
                </button>
            </div>
        </div>

        @if ($pegawaiTanpaData > 0)
            <div id="alertBanner"
                class="flex items-start justify-between gap-3 bg-green-50 border border-green-300 text-green-800 text-sm rounded px-4 py-3 mb-6">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0 fill-current text-green-600" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm0 12a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm1-3H7V4h2v5z" />
                    </svg>
                    <span>
                        <strong>Perhatian.</strong>
                        Terdapat <strong>{{ $pegawaiTanpaData }}</strong> data pegawai tidak dilengkapi dengan
                        {{ $labelData }}.
                        <a href="/data_pegawai/pegawai" class="underline font-medium hover:text-green-900">Lihat
                            detail</a>
                    </span>
                </div>
                <button onclick="document.getElementById('alertBanner').remove()"
                    class="shrink-0 text-green-600 hover:text-green-800 transition-colors">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path
                            d="M12.7 4.7l-1.4-1.4L8 6.6 4.7 3.3 3.3 4.7 6.6 8l-3.3 3.3 1.4 1.4L8 9.4l3.3 3.3 1.4-1.4L9.4 8z" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="no-print grid grid-cols-12 gap-6">

            <div
                class="col-span-full xl:col-span-5 bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Daftar Eselon</h2>
                </header>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead
                            class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Eselon</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($eselon as $index => $data)
                                <tr>
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->nama_eselon }}</td>
                                    <td class="px-4 py-3 text-center">{{ $data->pegawai_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr class="font-bold text-gray-800 dark:text-gray-100">
                                <td colspan="2" class="px-4 py-3 text-right">Total</td>
                                <td class="px-4 py-3 text-center">{{ $eselon->sum('pegawai_count') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div
                class="col-span-full xl:col-span-7 bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Statistik Eselon</h2>
                </header>
                <div class="p-3">
                    <canvas id="eselonChart" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- ===== AREA PRINT ===== --}}
        <div class="print-area">

            {{-- Kop Surat --}}
            <div class="kop-surat">
                @if ($instansi && $instansi->gambar_logo)
                    <img src="{{ asset($instansi->gambar_logo) }}" alt="Logo" class="kop-logo">
                @else
                    <div class="kop-logo-placeholder"></div>
                @endif
                <div class="kop-text">
                    <p class="kop-pemerintah">PEMERINTAH {{ strtoupper($instansi->kabupaten_kota ?? 'KABUPATEN/KOTA') }}
                        {{ strtoupper($instansi->nama_kota_kabupaten ?? '') }}</p>
                    <p class="kop-instansi">{{ strtoupper($instansi->nama_instansi_lembaga ?? 'BADAN KEPEGAWAIAN') }}
                    </p>
                    <p class="kop-alamat">{{ $instansi->alamat ?? '' }}</p>
                </div>
            </div>
            <div class="kop-garis"></div>

            {{-- Judul --}}
            <p class="print-judul">REKAPITULASI PEGAWAI BERDASARKAN ESELON</p>

            {{-- Tabel --}}
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width:8%">NO.</th>
                        <th>ESELON</th>
                        <th style="width:20%">JUMLAH PEGAWAI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eselon as $index => $data)
                        <tr>
                            <td style="text-align:center">{{ $index + 1 }}</td>
                            <td>{{ $data->nama_eselon }}</td>
                            <td style="text-align:center">{{ $data->pegawai_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Tanda Tangan --}}
            <div class="ttd-wrapper">
                <p class="ttd-kota">{{ $instansi->nama_kota_kabupaten ?? 'Cilacap' }},
                    {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}</p>
                <p class="ttd-jabatan">
                    KEPALA
                    {{ strtoupper($instansi->nama_instansi_lembaga ?? 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA') }}<br>
                    <strong>KABUPATEN {{ strtoupper($instansi->nama_kota_kabupaten ?? 'CILACAP') }}</strong>
                </p>
                <div class="ttd-ruang"></div>
                <p class="ttd-nama"><strong>{{ $instansi->kepala_dinas ?? '' }}</strong></p>
                @if ($instansi && $instansi->nip)
                    <p class="ttd-nip">NIP. {{ $instansi->nip }}</p>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('eselonChart');
                if (ctx) {
                    new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($chartCategories),
                            datasets: [{
                                label: 'Jumlah Pegawai',
                                data: @json($chartData),
                                backgroundColor: ['rgba(54,162,235,0.6)', 'rgba(255,99,132,0.6)',
                                    'rgba(75,192,192,0.6)', 'rgba(255,206,86,0.6)',
                                    'rgba(153,102,255,0.6)', 'rgba(255,159,64,0.6)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6B7280'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6B7280'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        </script>

        <style>
            /* ===== SCREEN: sembunyikan print-area ===== */
            .print-area {
                display: none;
            }

            /* ===== PRINT STYLES ===== */
            @media print {

                /* Sembunyikan semua elemen UI */
                body * {
                    visibility: hidden;
                }

                /* Tampilkan hanya area print */
                .print-area,
                .print-area * {
                    visibility: visible;
                }

                .print-area {
                    display: block !important;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    padding: 20mm 20mm 15mm 20mm;
                    font-family: 'Times New Roman', Times, serif;
                    font-size: 11pt;
                    color: #000;
                    box-sizing: border-box;
                }

                .no-print {
                    display: none !important;
                }

                /* Kop Surat */
                .kop-surat {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    margin-bottom: 6px;
                }

                .kop-logo {
                    width: 80px;
                    height: 80px;
                    object-fit: contain;
                }

                .kop-logo-placeholder {
                    width: 80px;
                    height: 80px;
                    flex-shrink: 0;
                }

                .kop-text {
                    flex: 1;
                    text-align: center;
                }

                .kop-pemerintah {
                    font-size: 11pt;
                    margin: 0;
                }

                .kop-instansi {
                    font-size: 14pt;
                    font-weight: bold;
                    margin: 2px 0;
                    line-height: 1.3;
                }

                .kop-alamat {
                    font-size: 10pt;
                    margin: 0;
                }

                .kop-garis {
                    border-top: 3px solid #000;
                    border-bottom: 1px solid #000;
                    margin-bottom: 16px;
                    padding-bottom: 2px;
                }

                /* Judul */
                .print-judul {
                    text-align: center;
                    font-weight: bold;
                    font-size: 12pt;
                    margin-bottom: 12px;
                    text-transform: uppercase;
                }

                /* Tabel */
                .print-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 11pt;
                    margin-bottom: 24px;
                }

                .print-table th,
                .print-table td {
                    border: 1px solid #000;
                    padding: 5px 8px;
                }

                .print-table thead tr {
                    font-weight: bold;
                    text-align: center;
                }

                /* Tanda Tangan */
                .ttd-wrapper {
                    margin-top: 24px;
                    text-align: right;
                    font-size: 11pt;
                    line-height: 1.6;
                }

                .ttd-wrapper p {
                    margin: 0;
                }

                .ttd-kota {
                    margin-bottom: 4px;
                }

                .ttd-jabatan {
                    line-height: 1.4;
                }

                .ttd-ruang {
                    height: 70px;
                }

                .ttd-nama {
                    font-size: 12pt;
                    text-decoration: underline;
                }

                .ttd-nip {
                    font-size: 11pt;
                }

                @page {
                    size: A4 portrait;
                    margin: 0;
                }
            }
        </style>
    @endpush
</x-app-layout>
