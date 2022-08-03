<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Support\Facades\Log;

class SqsController extends Controller
{
    public function send()
    {
        $client = new SqsClient([
            'credentials' => [
                'key' => \config("queue.connections.sqs.key"),
                'secret' => \config("queue.connections.sqs.secret"),
            ],
            'region' => 'ap-northeast-1',
            'version' => '2012-11-05',
        ]);

        $params = [
            'DelaySeconds' => 10,
            'MessageAttributes' => [
                "Title" => [
                    'DataType' => "String",
                    'StringValue' => "The Hitchhiker's Guide to the Galaxy"
                ],
                "Author" => [
                    'DataType' => "String",
                    'StringValue' => "Douglas Adams."
                ],
                "WeeksOn" => [
                    'DataType' => "Number",
                    'StringValue' => "6"
                ]
            ],
            'MessageBody' => "Information about current NY Times fiction bestseller for week of 12/11/2016.",
            'QueueUrl' => 'https://sqs.ap-northeast-1.amazonaws.com/695791177220/MyQueue'
        ];

        try {
            $result = $client->sendMessage($params);
            Log::info($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

    public function receive()
    {
        $queueUrl = \config("queue.connections.sqs.prefix") . '/' . \config("queue.connections.sqs.queue");

        $client = new SqsClient([
            'credentials' => [
                'key' => \config("queue.connections.sqs.key"),
                'secret' => \config("queue.connections.sqs.secret"),
            ],
            'region' => 'ap-northeast-1',
            'version' => '2012-11-05',
        ]);

        try {
            $result = $client->receiveMessage(array(
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $queueUrl,
                'WaitTimeSeconds' => 0,
            ));
            if (!empty($result->get('Messages'))) {
                var_dump($result->get('Messages')[0]);
                $result = $client->deleteMessage([
                    'QueueUrl' => $queueUrl,
                    'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle']
                ]);
            } else {
                echo "No messages in queue. \n";
            }
        } catch (AwsException $e) {
            \dd($e->getMessage());
        }
    }
}
