<?php

namespace Tests\Feature;

use App\Http\Controllers\Task\TaskController;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_function_return_500_json_response()
    {
        $taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $taskRepository->shouldReceive('list')
            ->with(15)
            ->andReturn(new LengthAwarePaginator([], 10, 15));

        $controller = new TaskController($taskRepository);

        $response = $controller->index(request()->merge([]));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_store_function_create_task()
    {
        $taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $controller = new TaskController($taskRepository);

        $requestData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status_id' => 3,
        ];

        $request = Mockery::mock(CreateTaskRequest::class);
        $request->shouldReceive('validated')->andReturn($requestData);

        $task = Mockery::mock('App\Models\Task');
        $task->shouldReceive('toArray')->andReturn($requestData);

        $taskRepository->shouldReceive('create')->with($requestData)->andReturn($task);

        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        Mockery::close();
    }

    public function test_show_function_return_task()
    {
        $taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $taskRepository->shouldReceive('show')
            ->with('1')
            ->andReturn(new Task([
                'id' => 1,
                'title' => 'Test Task',
                'description' => 'This is a test task',
                'status_id' => 3,
            ]));

        $controller = new TaskController($taskRepository);

        $response = $controller->show('1');

        $this->assertInstanceOf(TaskResource::class, $response);
    }

    public function testUpdateMethod()
    {
        // Arrange
        $taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $controller = new TaskController($taskRepository);

        $taskId = 1;
        $requestData = [
            'title' => 'Updated Task',
            'description' => 'This is an updated task',
            'due_date' => '2023-09-01',
        ];

        $request = Mockery::mock(UpdateTaskRequest::class);
        $request->shouldReceive('validated')->andReturn($requestData);

        $task = Mockery::mock(Task::class);
        $task->shouldReceive('toArray')->andReturn($requestData);

        $taskRepository->shouldReceive('update')->with($taskId, $requestData)->andReturn($task);

        // Act
        $response = $controller->update($request, $taskId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        Mockery::close();
    }

    public function test_destroy_function_delete_task()
    {
        $taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $taskRepository->shouldReceive('delete')
            ->with('1');

        $controller = new TaskController($taskRepository);

        $response = $controller->destroy('1');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());;
    }
}
