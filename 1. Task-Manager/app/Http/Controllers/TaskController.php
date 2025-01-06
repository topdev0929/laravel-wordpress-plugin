<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::paginate(10); // Pagination for optimized database queries
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,completed',
        ]);

        $task->update($validated);

        return response()->json($task);
    }
}
