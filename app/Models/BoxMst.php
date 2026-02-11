<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxMst extends Model
{
    protected $table = 'box_mst';
    public $timestamps = false;

    protected $fillable = [
        'box_id', 'box_no','box_of', 'job', 'suffix', 'box_desc', 'stat'
    ];
}
