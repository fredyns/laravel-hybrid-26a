<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property User $updater
 *
 */
trait Updater
{
    /**
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @return BelongsTo<TRelatedModel, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
