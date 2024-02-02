<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;

class FolderController extends Controller
{

    public function show($id)
    {
        $folder = Folder::with(['children', 'files'])->find($id);

        return view('folders.show', compact('folder'));
    }


    public function create(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string',
                'parent_id' => 'nullable|exists:folders,id',
            ]);

            $parentFolderId = $request->parent_id;
            $parentFolder = $parentFolderId ? Folder::find($parentFolderId) : null;

            $newFolder = new Folder([
                'name' => $request->name,
            ]);

            if ($parentFolder) {
                $parentFolder->children()->save($newFolder);
            } else {
                $newFolder->save();
            }
            $storagePath = $parentFolder ? $parentFolder->name . '/' : ''; // Concatenate parent folder path if exists
            Storage::disk('public')->makeDirectory($storagePath . $newFolder->name);
            return response()->json(['message' => 'Folder created successfully', 'data' => $newFolder], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create folder. Database error.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create folder.'], 500);
        }
    }
    public function uploadFile($folderId, Request $request)
    {
        $folder = Folder::find($folderId);

      
        $request->validate([
            'name.*' => 'required|mimes:jpeg,png,pdf,gif,svg,txt|max:2048',
        ]);

        $uploadedFile = $request->file('name');
        $name = $uploadedFile->getClientOriginalName();


        $filePath = '';

        if ($folder->parent) {
            $filePath .= $folder->parent->name . '/';
        }

        $filePath .= $folder->name ? $folder->name . '/' : '';
        $filePath .= $name;

        // Save the file to storage
        Storage::disk('public')->put($filePath, file_get_contents($uploadedFile));


        $fileSize = $uploadedFile->getSize();
        $sizeInKB = $fileSize / 1024;


        $newFile = File::create([
            'name' => $name,
            'size' => $sizeInKB,
        ]);

        // Associate the file with the folder
        $folder->files()->save($newFile);

        return response()->json(['message' => 'File uploaded successfully'], 201);




    }



}
