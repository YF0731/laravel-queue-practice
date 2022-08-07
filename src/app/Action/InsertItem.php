<?php

namespace App\Action;

use App\Models\SqsSample;
use Exception;
use Illuminate\Support\Facades\Log;

class InsertItem
{
    /**
     * @param array $message
     * @return JsonResponse
     */
    public static function register(array $message)
    {
        $maxId = SqsSample::all()->max()->sqs_id;

        try {
            SqsSample::create([
                'sqs_id' => \strval(++$maxId),
                'message_id' => $message['MessageId'],
                'body' => $message['Body'],
            ]);
        } catch (Exception $th) {
            Log::error($th->getMessage());
        }

        return \response()->json([
            'message' => 'success',
        ]);
    }
}
