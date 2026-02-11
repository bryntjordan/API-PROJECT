<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoxMst;
use App\Models\ItemMst;
use App\Models\ItemScanMst;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Validate barcode {job,suffix,item} terhadap box yang dipilih.
     * - cek box ada dan stat masih 0
     * - cek job/suffix cocok
     * - cek item ada dalam item_mst untuk box_id tersebut
     * Return detail untuk ditampilkan sebelum simpan.
     */
    public function validateScan(Request $request)
    {
        $data = $request->validate([
            'box_id' => ['required', 'string'],
            'job'    => ['required', 'string'],
            'suffix' => ['required', 'string'],
            'item'   => ['required', 'string'],
        ]);

        $box = BoxMst::query()
            ->where('box_id', $data['box_id'])
            ->first();

        if (!$box) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Box tidak ditemukan.',
            ], 404);
        }

        // opsional: hanya izinkan box yang masih stat 0
        if ((int)$box->stat !== 0) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Box sudah tidak aktif / sudah diproses.',
            ], 422);
        }

        // Cocokkan job + suffix barcode dengan box yang dipilih
        if ($box->job !== $data['job'] || $box->suffix !== $data['suffix']) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Job/Suffix barcode tidak cocok dengan box yang dipilih.',
            ], 422);
        }

        // Cari item dalam box tersebut
        $itemRow = ItemMst::query()
            ->where('box_id', $box->box_id)
            ->where('job', $box->job)
            ->where('suffix', $box->suffix)
            ->where('item', $data['item'])
            ->first(['item', 'qty', 'description', 'status']);

        if (!$itemRow) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Item tidak ditemukan di box ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'valid' => true,
            'data' => [
                'job'         => $box->job,
                'suffix'      => $box->suffix,
                'box_id'      => $box->box_id,
                'box_no'      => $box->box_no,
                'item'        => $itemRow->item,
                'description' => $itemRow->description,
                'qty'         => (int)$itemRow->qty,
            ]
        ]);
    }

    /**
     * Simpan scan ke itemscan_mst.
     * Input minimal dari Android:
     * - box_id (box yang dipilih)
     * - item
     * - qty
     *
     * Server akan:
     * - ambil job/suffix/box_no dari box_mst
     * - set tgl_scan = now()
     * - set user_id dari token (auth:sanctum)
     * - stat = 1
     */
    public function saveScan(Request $request)
    {
        $data = $request->validate([
            'box_id' => ['required', 'string'],
            'item'   => ['required', 'string'],
            'qty'    => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        $box = BoxMst::query()
            ->where('box_id', $data['box_id'])
            ->first();

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box tidak ditemukan.',
            ], 404);
        }

        // opsional: pastikan masih stat 0
        if ((int)$box->stat !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Box sudah tidak aktif / sudah diproses.',
            ], 422);
        }

        // Validasi item benar untuk box ini
        $exists = ItemMst::query()
            ->where('box_id', $box->box_id)
            ->where('job', $box->job)
            ->where('suffix', $box->suffix)
            ->where('item', $data['item'])
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak valid untuk box ini.',
            ], 422);
        }

        // Insert ke itemscan_mst
        ItemScanMst::create([
            'job'      => $box->job,
            'suffix'   => $box->suffix,
            'box_id'   => $box->box_id,
            'box_no'   => $box->box_no,
            'item'     => $data['item'],
            'qty'      => $data['qty'],
            'tgl_scan' => now(),
            'user_id'  => $user->id,
            'stat'     => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scan tersimpan.',
        ]);
    }
}
