<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Todo;
use App\Http\Requests\TodoRequest;
use App\Http\Resources\TodoResource;
use Auth;

class TodoController extends Controller
{   

    public function index()
    {
        $todos = Todo::whereUserId(Auth::id())->paginate(1);
        return $this->response(200, 'record retrived!', $todos);
    }
    
    public function store(TodoRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id']= Auth::id();
        Todo::create($validated);
        return $this->response(200, 'record saved!');
    }
    
    public function show($id)
    {
        $todo = Todo::whereId($id)->first();
        return $this->response(200, 'record retrived!', TodoResource::make($todo));
    }
    
    public function update($id, TodoRequest $request)
    {
        $todo = Todo::whereId($id)->first();
        $todo->update($request->validated());
        return $this->response(200, 'record updated!');   
    }
    
    public function destroy($id)
    {
        $todo = Todo::whereId($id)->delete();
        return $this->response(200, 'record deleted!');   
    }
}
