<?php

namespace AeroCrossSelling\Models;

use Aero\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class CrossProductDownload extends Model
{
    protected $fillable = ['location', 'collections'];

    protected $casts = [
        'collections' => 'collection',
    ];

    /**
     * The admin user that downloaded the data.
     *
     * @return BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
