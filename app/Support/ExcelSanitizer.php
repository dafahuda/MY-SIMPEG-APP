<?php

namespace App\Support;

/**
 * Shared hardening helpers for Excel/PDF exports.
 */
class ExcelSanitizer
{
    /**
     * Neutralize spreadsheet formula injection.
     *
     * Cells beginning with =, +, -, @, tab or CR are interpreted as formulas
     * by Excel/LibreOffice and can execute commands or exfiltrate data when
     * the workbook is opened. Prefixing a single quote forces plain text;
     * Excel hides the apostrophe when rendering.
     */
    public static function cell($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;

        if ($value !== '' && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
