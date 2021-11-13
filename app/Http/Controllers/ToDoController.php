<?php

namespace App\Http\Controllers;

use App\Models\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ToDo[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index()
    {
        $todos = ToDo::query()->where('user_id', Auth::user()->id)->latest()->get();
        foreach ($todos as $todo) {
            $todo->data = $todo->created_at->format('d/m/Y');
        }
        if (ToDo::query()->first()) {
            return $todos;
        }
        return [];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'done' => 'nullable',
        ]);

        $todo = new ToDo;
        $todo->title = $request->title;
        $todo->user_id = Auth::user()->id;
        $todo->save();
        return response($todo, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ToDo $toDo
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\never
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'done' => 'nullable',
            'user_id' => 'nullable',
        ]);


        $todo = ToDo::query()->where('id', $id)->first();

        if (!$todo) {
            return abort(404);
        }

        if ($todo->user_id != Auth::user()->id) {
            return response()->json('You don\'t have permission!');
        }

        $todo->update($data);

        return response($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\never
     */
    public function destroy($id)
    {
        $todo = ToDo::query()->where('id', $id)->first();

        if (!$todo) {
            return abort(404);
        }

        if ($todo->user_id != Auth::user()->id) {
            return response()->json('You don\'t have permission!');
        }

        if ($todo->delete()) {
            $message = 'deleted';
        } else {
            $message = 'some error in here';
        }

        return response($message);
    }
}
