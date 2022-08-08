<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $perPage = 20;

    public function index()
    {
       $posts = Post::paginate($this->perPage);

        return view('admin.posts.index', compact('posts'));
    }

    public function myIndex()
    {

        $posts = Auth::user()->posts()->paginate($this->perPage);

        return view('admin.posts.index', compact('posts'));
    }
    public function create()
    {
        return view('admin.posts.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:100',
            'slug'      => 'required|string|max:100|unique:posts',
            'image'     => 'required_without:content|nullable|url',
            'content'   => 'required_without:image|nullable|string|max:5000',
        ]);
//con il + aggiungiamo all'array l'Id dell'utente
        $data = $request->all() + [
            'user_id' => Auth::id(),
        ];

        $post = Post::create($data);

        return redirect()->route('admin.posts.show', ['post' => $post]);
    }


    public function show(Post $post)
    {
        return view('admin.posts.show', [
            'post'          => $post
        ]);
    }


    public function edit(Post $post)
    {
        if(Auth::id() != $post->user_id) abort(401);



        return view('admin.posts.edit', [
            'post'          => $post,
        ]);
    }


    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'     => 'required|string|max:100',
            'slug'      => [
                'required',
                'string',
                'max:100',
                //ignora il fatto che lo slug deve essere univoco siccome serve per lo stesso post
                Rule::unique('posts')->ignore($post->id),
            ],

            'image'     => 'required_without:content|nullable|url',
            'content'   => 'required_without:image|nullable|string|max:5000',
        ]);

        $data = $request->all();

        $post->update($data);

        return redirect()->route('admin.posts.show', ['post' => $post]);
    }


    public function destroy(Post $post)
    {
        if (Auth::id() != $post->user_id) abort(401);

        $post->delete();

        return redirect()->route('admin.posts.index')->with('deleted', "Il post {$post->title} è stato eliminato");
    }
}
