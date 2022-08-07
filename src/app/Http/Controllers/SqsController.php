<?php

namespace App\Http\Controllers;

use App\Action\GetQueueUrl;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Support\Facades\Log;

class SqsController extends Controller
{
    private string $queueUrl;

    public function __construct()
    {
        $this->queueUrl = GetQueueUrl::get();
    }

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
            'QueueUrl' => $this->queueUrl,
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
                'QueueUrl' => $this->queueUrl,
                'WaitTimeSeconds' => 0,
            ));
            if (!empty($result->get('Messages'))) {
                var_dump($result->get('Messages')[0]);
                $result = $client->deleteMessage([
                    'QueueUrl' => $this->queueUrl,
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
