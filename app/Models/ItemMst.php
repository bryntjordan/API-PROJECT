<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMst extends Model
{
    protected $table = 'item_mst';
    public $timestamps = false;

    protected $fillable = [
        'item', 'job', 'suffix', 'box_id','description','qty', 'status'
    ];
}
