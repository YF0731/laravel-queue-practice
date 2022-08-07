<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use BaoPham\DynamoDb\DynamoDbModel;

class SqsSample extends DynamoDbModel
{
    use HasFactory;

    protected $fillable = [
        'sqs_id',
        'message_id',
        'body',
    ];

    protected $table = 'sqs-sample';
}
