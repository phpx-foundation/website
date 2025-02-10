<?php

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('cloudflare:reload')->daily();

Artisan::command('newsletter:list', function () {
    foreach (NewsletterSubscriber::query()->cursor() as $subscriber) {
        $this->info("[{$subscriber->id}] {$subscriber->full_name} <{$subscriber->email}>");
    }
});
