<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_index_success_task()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ],
            ],
        ]);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_task_422_error()
    {
        // Arrange
        $taskData = [
            'title' => 'New Task',
            'description' => 'This is a new task',
            'status_id' => 1,
        ];

        // Act
        $response = $this->postJson(route('tasks.store'), $taskData);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY); // Expected 422 instead of 201
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'status_id'
            ]
        ]);
        $this->assertDatabaseMissing('tasks', $taskData);
    }

    public function test_update_task()
    {
        $task = Task::factory()->create();

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'This is an updated task',
        ];

        $response = $this->putJson(route('tasks.update', $task->id), $updateData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'title',
                'description',
            ],
        ]);
        $this->assertDatabaseHas('tasks', $updateData);
    }

    public function test_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson(route('tasks.destroy', $task->id));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_show_task()
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $response = $this->getJson(route('tasks.show', $task->id));

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
            ],
        ]);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
            ],
        ]);
    }
}
