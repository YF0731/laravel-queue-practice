<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Http\Request;

class ListQueueController extends Controller
{
    public function list()
    {
        $client = new SqsClient([
            'credentials' => [
                'key' => \config("queue.connections.sqs.key"),
                'secret' => \config("queue.connections.sqs.secret"),
            ],
            'region' => 'ap-northeast-1',
            'version' => '2012-11-05',
        ]);

        try {
            $result = $client->listQueues();
            foreach ($result->get('QueueUrls') as $queueUrl) {
                echo "$queueUrl\n";
            }
        } catch (AwsException $e) {
            // output error message if fails
            error_log($e->getMessage());
        }
    }
}
