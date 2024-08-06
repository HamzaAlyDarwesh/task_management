<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Interfaces\TaskRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(private TaskRepositoryInterface $taskRepository)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tasks = $this->taskRepository->list($request->all());
            return TaskResource::collection($tasks)->response();
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_list_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_list_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param CreateTaskRequest $request
     * @return JsonResponse
     */
    public function store(CreateTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskRepository->create($request->validated());
            return $this->response(
                __('messages.resource.success.resource_create_success'),
                Response::HTTP_CREATED,
                $task->toArray()
            );
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_create_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_create_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $id
     * @return TaskResource|JsonResponse
     */
    public function show(string $id)
    {
        try {
            $task = $this->taskRepository->show($id);
            return new TaskResource($task);
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_show_fail') . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_show_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, int $id)
    {
        try {
            $task = $this->taskRepository->update($id, $request->validated());
            return $this->response(
                __('messages.resource.success.resource_update_success'),
                Response::HTTP_OK,
                $task->toArray()
            );
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_update_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_update_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $this->taskRepository->delete($id);
            return $this->response(
                __('messages.resource.success.resource_delete_success'),
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_delete_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_delete_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param AssignTaskRequest $request
     * @return JsonResponse
     */
    public function taskAssign(AssignTaskRequest $request)
    {
        try {
            $task = $this->taskRepository->assignTask($request->validated());
            return $this->response(
                __('messages.task.success.task_assigned_successfully'),
                Response::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            Log::error(__('messages.task.fail.task_assigned_failed') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.task.fail.task_assigned_failed')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
