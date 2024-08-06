<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskRepository implements TaskRepositoryInterface
{
    private int $perPage = 15;

    /**
     * @param int|null $perPage
     * @return mixed
     */
    public function list(array $request)
    {
        if (isset($request['user_id'])) {
            return Task::where('user_id', $request['user_id'])->paginate($this->perPage);
        }
        $cacheKey = 'task_list';
        return Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return Task::paginate($this->perPage);
        });
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $task = Task::create($data);
            DB::commit();
            return $task;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $task = Task::findOrFail($id);
            $task->update($data);
            DB::commit();
            return $task;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id): void
    {
        try {
            DB::beginTransaction();
            $task = Task::findOrFail($id);
            $task->delete();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        return Task::findOrFail($id);
    }

    /**
     * @param array $request
     * @return void
     */
    public function assignTask(array $request): void
    {
        $task = Task::findOrFail($request['task_id']);
        $task->user_id = $request['user_id'];
        $task->save();
    }
}
