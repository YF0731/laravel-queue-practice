<?php

namespace App\Http\Controllers;

use App\Jobs\SQSSendJob;

class JobDispatchController extends Controller
{
    public function send()
    {
        SQSSendJob::dispatch()->delay(\now()->addMinutes(1));
    }
}
