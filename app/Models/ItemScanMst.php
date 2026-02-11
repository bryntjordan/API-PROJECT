<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemScanMst extends Model
{
    protected $table = 'itemscan_mst';
    public $timestamps = false;

    protected $fillable = [
        'job', 'suffix', 'box_id', 'box_no', 'item', 'qty', 'tgl_scan', 'user_id', 'stat'
    ];
}
