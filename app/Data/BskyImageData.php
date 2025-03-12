<?php

namespace App\Data;

class BskyImageData
{
	public function __construct(
		public string $imageFile,
		public string $imageTitle,
		public ?string $imageDescription = null,
		public ?string $imageUri = null
	) {
	}
}
