<?php

namespace App\Http\Controllers\World;

use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController
{
	public function __invoke(Request $request)
	{
		return view('world.home', [
			'show_1080p' => $request->has('1080p'),
			'points' => Cache::remember(
				key: 'homepage-points', 
				ttl: now()->addDay(), 
				callback: fn() => $this->maximizeDistance($this->points()),
			),
		]);
	}
	
	protected function points()
	{
		$groups = Group::query()->whereNotNull(['latitude', 'longitude'])->orderBy('id')->get();
		$external = ExternalGroup::query()->whereNotNull(['latitude', 'longitude'])->orderBy('id')->get();
		
		return $groups
			->merge($external)
			->map(fn(Group|ExternalGroup $row) => [
				'lat' => $row->latitude,
				'lng' => $row->longitude,
				'name' => $row->label,
			])
			->reject(fn($data) => empty($data['lat']) || empty($data['lat']))
			->values();
	}
	
	protected function distance($point1, $point2)
	{
		$earth = 6371; // Earth's radius in kilometers
		
		$lat1 = deg2rad($point1['lat']);
		$lng1 = deg2rad($point1['lng']);
		$lat2 = deg2rad($point2['lat']);
		$lng2 = deg2rad($point2['lng']);
		
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		
		$a = sin($dlat / 2) * sin($dlat / 2) +
			cos($lat1) * cos($lat2) *
			sin($dlng / 2) * sin($dlng / 2);
		
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		
		return $earth * $c;
	}
	
	function maximizeDistance(Collection $points)
	{
		$result = collect([$points->first()]);
		$remaining = $points->slice(1);
		
		while ($remaining->isNotEmpty()) {
			$furthest = $remaining->sortByDesc(function($point) use ($result) {
				$targets = $result->take(-2);
				
				$distance = $this->distance($targets->pop(), $point);
				
				if ($targets->isNotEmpty()) {
					$distance += $this->distance($targets->pop(), $point);
					$distance = $distance / 2;
				}
				
				return $distance;
			})->first();
			
			$result->push($furthest);
			$remaining = $remaining->filter(fn($point) => $point !== $furthest);
		}
		
		return $result;
	}
}
