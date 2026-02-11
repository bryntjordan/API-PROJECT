<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoxMst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class BoxController extends Controller
{
    /**
     * Menampilkan box yang masih aktif (stat = 0)
     * Untuk ditampilkan di ScanFragment sebagai list.
     */
    public function active(Request $request)
    {
        $rows = BoxMst::query()
            ->where('stat', 0)
            ->orderBy('job')
            ->orderBy('suffix')
            ->orderBy('box_no')
            ->limit(200)
            ->get([
                'box_id',
                'box_no',
                'job',
                'suffix',
                'stat',
            ]);

           

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }
}
