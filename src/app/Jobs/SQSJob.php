<?php

namespace App\Jobs;

use App\Action\GetQueueUrl;
use App\Action\InsertItem;
use Aws\Exception\AwsException;
use Aws\Sqs\Exception\SqsException;
use Aws\Sqs\SqsClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\SqsJob as JobsSqsJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Facades\Log;

class SQSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
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
        } catch (SqsException $e) {
            Log::error($e->getMessage());
        } catch (AwsException $e) {
            Log::error($e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
