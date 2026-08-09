<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TransactionNumberService
{
    /**
     * Generate transaction number.
     *
     * Format:
     * TRX-RCV-20260715193045-0001
     */
    public static function generate(string $type): string
    {
        $timestamp = now()->format('YmdHis');

        $prefix = "TRX-{$type}-{$timestamp}";

        $last = DB::table('penerimaan')
            ->where('nomor_transaksi', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;

        if ($last) {

            $parts = explode('-', $last->nomor_transaksi);

            $sequence = intval(end($parts)) + 1;
        }

        return sprintf(
            "%s-%04d",
            $prefix,
            $sequence
        );
    }
}