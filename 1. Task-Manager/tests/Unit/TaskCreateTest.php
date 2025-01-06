<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_not_access_private_product_api()
    {
        $response = $this->get('/api/tasks');

        $response->assertStatus(401);
    }
    
    public function test_can_create_task()
    {
        $user = User::factory()->create([
            'name' => 'Denis Dovganiuc',
            'email' => 'denisdovganiuc65@gmail.com',
            'password' => bcrypt('password123')
        ]);
    
        Auth::login($user);
        $token = JWTAuth::fromUser(Auth::user());
        $totalTasks = Task::count();
    
        // Create a task using the factory and submit it via the API
        $response = $this->post('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
        ], [
            'Authorization' => 'Bearer ' . $token, 
        ]);
        
        // Assert the task was added to the database
        $this->assertDatabaseCount('tasks', $totalTasks + 1);
    
        // Assert that the task has the expected data
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
        ]);
    
        $response->assertStatus(201);
    }
}
