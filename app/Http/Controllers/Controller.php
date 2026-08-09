<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="MyStock API Documentation",
 *      description="Dokumentasi API untuk Sistem Manajemen Stok Barang (MyStock)",
 *      @OA\Contact(
 *          email="admin@mystock.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server Utama"
 * )
 */
abstract class Controller
{
    // ...
}