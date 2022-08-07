<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use BaoPham\DynamoDb\DynamoDbModel;

class User extends DynamoDbModel
{
    use HasFactory;

    protected $table = 'sqs-sample';
}
