<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::query();

        // Filter by status
        if ($request->has('status')) {
            $tasks->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $tasks->whereDate('due_date', $request->date);
        }

        // Filter by assigned user
        if ($request->has('user_id')) {
            $tasks->where('user_id', $request->user_id);
        }

        return response()->json($tasks->get());
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id', // Ensure user_id exists in the users table
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in progress,completed',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response with validation errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validation passed, create new task
        $task = Task::create($request->all());
        
        // Return success response with created task
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Validation rules
        $rules = [
            'user_id' => 'sometimes|required|exists:users,id', // Ensure user_id exists in the users table if provided
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pending,in progress,completed',
        ];
    
        // Validate the request
        $validator = Validator::make($request->all(), $rules);
    
        // If validation fails, return error response with validation errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Find the task by ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        // Validation passed, update the task
        $task->update($request->all());
        
        // Return success response with updated task
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ], 200);
    }

    public function destroy(string $task)
    {
        $task_find = Task::find($task);

        // Check if the record exists
        if (!$task_find) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    
        // Delete the record
        $task_find->delete();
    
        // Return a success response
        return response()->json(['message' => 'Record deleted successfully'], 200);
    
    }

    public function assignUser(Request $request, Task $taskId)
    {
        // Define validation rules
        $rules = [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id', // Ensure each user ID exists in the users table
        ];
    
        // Create a new validator instance
        $validator = Validator::make($request->all(), $rules);
    
        // If validation fails, return error response with validation errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Attach the users to the task
        $taskId->users()->attach($request->user_ids);
    
        // Reload the task with the updated relationships
        $taskId->load('users');
    
        // Return the updated task with assigned users
        return response()->json($taskId, 200);
    }
    
    

    public function unassignUser(Request $request, Task $task)
    {
        $task->users()->detach($request->user_id);
        return response()->json($task, 200);
    }

    public function changeStatus(Request $request, Task $task)
    {
        $task->update(['status' => $request->status]);
        return response()->json($task, 200);
    }}