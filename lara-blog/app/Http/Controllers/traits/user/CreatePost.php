<?php

namespace App\Http\Controllers\traits\user;

use App\Http\Controllers\traits\file\FileCreate;
use App\Http\Requests\CreatePostRequest;
use App\Models\Image;
use App\Models\Post;

trait CreatePost
{
    use FileCreate;

    public function create(CreatePostRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $post = Post::query()->create($validated);

        if ($request->file('file')) {
            $name = $post->id . "_" . $request->file('file')->getClientOriginalName();
            $this->storeFile('posts', $name, $request->file('file'));

            $post->image()->create([
                'title' => $validated['title'],
                'alt' => $post->slug,
                'path' => 'storage/public/' . $name
            ]);
        }

        $post->tags()->sync($validated['tags_id']);
        $post->categories()->sync($validated['categories_id']);

        return redirect()->route('dashboard');
    }
}
