<?php

namespace App\Http\Controllers;

use App\Models\SqsSample;

class DynamoDbController extends Controller
{
    public function test()
    {
        $users = SqsSample::all();
        \dd($users);
    }
}
