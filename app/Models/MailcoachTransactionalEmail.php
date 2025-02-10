<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailcoachTransactionalEmail extends Model
{
    use HasSnowflakes;
    use SoftDeletes;

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }
}
