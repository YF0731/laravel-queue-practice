<?php

namespace App\Action;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Support\Facades\Log;

class ListenSQSQueue
{
    public function __invoke()
    {
        $queueUrl = GetQueueUrl::get();

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

            if (empty($result->get('Messages'))) {
                Log::info("No messages in queue. \n");
            }

            InsertItem::register($result->get('Messages')[0]);

            $result = $client->deleteMessage([
                'QueueUrl' => $queueUrl,
                'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle']
            ]);

            Log::info($result);
        } catch (AwsException $e) {
            \dd($e->getMessage());
        }
    }
}
