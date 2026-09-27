<?php

use Illuminate\Support\Facades\Request;

if (! function_exists('simpeg_per_page')) {
    /**
     * Jumlah baris per halaman untuk daftar data.
     * Bisa diatur user lewat query string ?per_page=25,
     * dibatasi 1-100 agar tidak bisa dipakai untuk membebani server.
     */
    function simpeg_per_page(int $default = 10): int
    {
        $raw = Request::input('per_page', $default);

        if (! is_numeric($raw)) {
            return $default;
        }

        $perPage = (int) $raw;

        return max(1, min($perPage, 100));
    }
}
