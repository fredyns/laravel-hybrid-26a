<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property User $creator
 */
trait Creator
{
    /**
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @return BelongsTo<TRelatedModel, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
