<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Storage;
use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
