<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Jobs\SendNewCommentNotification;
use App\Models\Comment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommentRepository implements CommentRepositoryInterface
{
    private int $perPage = 15;

    public function list(array $data)
    {
        $taskId = $data['task_id'];
        $cacheKey = 'comment_list';
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($taskId) {
            return Comment::where('task_id', $taskId)->paginate($perPage ?? $this->perPage);
        });
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $data['user_id'] = auth()->id();
            $comment = Comment::create($data);
            if ($comment) {
                $this->sendNotificationEmail($comment);
            }
            DB::commit();
            return $comment;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $task = Comment::findOrFail($id);
            $task->update($data);
            DB::commit();
            return $task;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id): void
    {
        try {
            DB::beginTransaction();
            $task = Comment::findOrFail($id);
            $task->delete();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(int $id)
    {
        return Comment::findOrFail($id);
    }

    public function sendNotificationEmail(Comment $comment)
    {
        // Dispatch the SendNewCommentNotification job
        dispatch(new SendNewCommentNotification($comment));
    }
}
