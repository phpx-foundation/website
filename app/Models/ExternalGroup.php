<?php

namespace App\Models;

use Exception;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalGroup extends Model
{
    use HasDomain;
    use HasSnowflakes;
    use SoftDeletes;

    protected $appends = [
        'label',
    ];

    protected static function booted()
    {
        static::creating(function (self $external) {
            if (Group::whereDomain($external->domain)->exists()) {
                throw new Exception('Cannot create an external group with a domain for an existing group.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    protected function label(): Attribute
    {
        return Attribute::get(fn () => $this->region ?? str($this->name)->afterLast('×')->trim()->toString());
    }
}
