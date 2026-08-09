<?php

namespace App\Observers;

use App\Models\Barang;

class BarangObserver
{
    public function creating(Barang $barang): void
    {
        $last = Barang::latest('id')->first();

        $number = $last ? $last->id + 1 : 1;

        $barang->kode_barang = 'BRG-' . str_pad($number,6,'0',STR_PAD_LEFT);
    }
}