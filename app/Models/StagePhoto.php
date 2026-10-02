<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A site photo. Stored on the private disk and served through an authorised route. */
class StagePhoto extends Model
{
    protected $fillable = ['project_stage_id', 'file_path', 'caption'];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ProjectStage::class, 'project_stage_id');
    }
}
