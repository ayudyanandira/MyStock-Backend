<?php
namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Http\Request;

trait HasAuditLog
{
    public function recordAuditLog(string $modul, string $aktivitas, ?int $referenceId = null): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'modul'        => $modul,
            'aktivitas'    => $aktivitas,
            'reference_id' => $referenceId,
            'ip_address'   => request()->ip(),
        ]);
    }
}