<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function getAll(Request $request) {
        // Check if date filter is provided
        if ($request->has('date')) {
            // Validate the date input
            $request->validate([
                'date' => 'required|date',
            ]);

            // Get tasks filtered by date
            $date = $request->input('date');
            $tasks = Task::with('category')->where('user_id', auth()->id())->whereDate('due_date', $date)->get();
            // $tasks = Task::whereDate('due_date', $date)->get();
        } else {
            // Retrieve all tasks
            $tasks = Task::with('category')->where('user_id', auth()->id())->get();
        }
        return response()->json(['status' => true, 'data' => $tasks]);

    }

    public function createNew(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
        ]);
        if ($validator->passes()) {
            
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

            return response()->json($task, 201);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',  // Validate due_date as a date
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        return response()->json($task);
    }

    public function remove(Request $request, $id) {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
    
    public function filterByDate(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Task::where('user_id', Auth::id());

        if ($request->has('start_date')) {
            $query->where('due_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('due_date', '<=', $request->end_date);
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }
}
