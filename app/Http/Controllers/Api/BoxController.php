<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoxMst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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


    //2122026

    public function viewBox(Request $request)
{
    try {

        $boxId = $request->query('box_id');

        if (!$boxId) {
            return response()->json([
                'success' => false,
                'message' => 'box_id is required'
            ], 400);
        }

        // 1️⃣ Ambil box
        $box = DB::table('box_mst')
            ->where('box_id', $boxId)
            ->first();

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box tidak ditemukan'
            ], 404);
        }

        // 2️⃣ Ambil job detail
        $job = DB::table('job_mst')
            ->where('job', $box->job)
            ->where('suffix', $box->suffix)
            ->first();

        // 3️⃣ Ambil item berdasarkan job + suffix
        $items = DB::table('item_mst')
            ->where('job', $box->job)
            ->where('suffix', $box->suffix)
            ->where('box_id', $box->box_id)
            ->get();


        return response()->json([
            'success' => true,
            'data' => [
                'box_id'   => $box->box_id,
                'box_no'   => $box->box_no,
                'status'   => $box->stat,
                'job'      => $box->job,
                'suffix'   => $box->suffix,
                'job_detail' => $job,
                'items'    => $items
            ]
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

}
