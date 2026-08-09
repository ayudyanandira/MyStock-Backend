<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = AuditLog::with('user')->latest('id');

        // Filter berdasarkan Modul
        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        // Filter berdasarkan User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Rentang Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $logs = $query->paginate(20);

        return AuditLogResource::collection($logs);
    }

    public function show(AuditLog $auditLog): AuditLogResource
    {
        $auditLog->load('user');
        return new AuditLogResource($auditLog);
    }
}