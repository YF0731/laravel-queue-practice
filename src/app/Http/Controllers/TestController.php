<?php

namespace App\Http\Controllers;

use App\Action\ListenSQSQueue;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        \app(ListenSQSQueue::class)();
    }
}
