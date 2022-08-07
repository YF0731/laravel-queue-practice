<?php

namespace App\Jobs;

use App\Action\GetQueueUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SQSSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $queueUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queueUrl = GetQueueUrl::get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
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
}
