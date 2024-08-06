<?php

namespace App\Jobs;

use App\Mail\NewCommentNotification;
use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewCommentNotification implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Comment $comment)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $author = $this->comment->user;
        Mail::to($author->email)
            ->send(new NewCommentNotification($this->comment));
    }
}
