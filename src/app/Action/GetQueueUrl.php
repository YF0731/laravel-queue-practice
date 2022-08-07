<?php

namespace App\Action;

class GetQueueUrl
{
    /**
     * SQSのURL取得
     *
     * @return string
     */
    public static function get()
    {
        return \config("queue.connections.sqs.prefix") . '/' . \config("queue.connections.sqs.queue");
    }
}
