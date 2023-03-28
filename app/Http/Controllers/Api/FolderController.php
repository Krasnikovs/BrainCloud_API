<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FolderResource::collection(Folder::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FolderRequest $request)
    {
        $validated = $request->validated();
        if (Storage::disk('local')->exists('public/'.$validated['user_id'].'/'.$validated['title'])) {
            return response()->json([
                'error' => 'You have already folder with this name!'
            ], 400);
        }
        Storage::disk('local')->makeDirectory('public/'.$validated['user_id'].'/'.$validated['title']);
        $validated['folder_location'] = 'public/'.$validated['user_id'].'/'.$validated['title'];
        $folder = Folder::create($validated);
        return new FolderResource($folder);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Folder $folder)
    {
        return new FolderResource($folder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Folder $folder)
    {
        $validated = $request->validate([
            'title' => ''
        ]);
        if (Storage::disk('local')->exists('public/'.$folder['user_id'].'/'.$validated['title'])) {
            return response()->json([
                'error' => 'You have already folder with this name!'
            ], 400);
        }
        Storage::disk('local')->move($folder->folder_location, 'public/'.$folder['user_id'].'/'.$validated['title']);
        $validated['folder_location'] = 'public/'.$folder['user_id'].'/'.$validated['title'];
        $folder->update($validated);
        return new FolderResource($folder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Folder $folder)
    {
        File::where('folder_id', $folder['id'])
            ->get()
            ->map(function ($file) {
                $file->delete();
            });
        Storage::disk('local')->deleteDirectory($folder->folder_location);
        $folder->delete();
        return new FolderResource($folder);
    }

    public function getUserFolders () {
        $userFolders = Folder::where('user_id', auth()->user()->id)->get();
        return FolderResource::collection($userFolders);
    }
}
