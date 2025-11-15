<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdatePersonalAccessToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $personalAccessToken;
    public $newAttributes;

    public function __construct($personalAccessToken, $newAttributes)
    {
        $this->personalAccessToken = $personalAccessToken;
        $this->newAttributes = $newAttributes;
    }

    public function handle(): void
    {
        DB::table($this->personalAccessToken->getTable())
            ->where('id', $this->personalAccessToken->id)
            ->update($this->newAttributes);
    }
}