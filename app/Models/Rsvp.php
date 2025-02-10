<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Rsvp extends Pivot
{
    use HasSnowflakes;

    public $timestamps = true;

    protected $table = 'rsvps';

    protected $casts = [
        'interests' => 'collection',
    ];

    // protected static function booted()
    // {
    // 	static::saved(function(Rsvp $rsvp) {
    // 		$meetup = Meetup::find($rsvp->meetup_id);
    // 		return Cache::forget("group:{$meetup->group_id}:next-meetup");
    // 	});
    // }
}
