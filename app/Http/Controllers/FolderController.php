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

        if (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        }

        return response()->json(['data' => $folder], 200);

    }

    public function showAll()
    {
        $folders = Folder::with(['children', 'files'])->get();

        return response()->json(['data' => $folders], 200);
    }

    public function create(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'name' => 'required|string',
                'parent_id' => 'nullable|exists:folders,id',
            ]);

            $parentFolder = null;

            // Check for duplicate folder name
            $newFolderName = $request->name;
            $baseFolderName = $newFolderName;

            // If parent folder is specified, check for duplicates in its children
            if ($request->parent_id) {
                $parentFolder = Folder::find($request->parent_id);
                $existingNames = $parentFolder->children()->pluck('name')->toArray();
            } else {
                // Check for duplicates in the root folders
                $existingNames = Folder::whereNull('parent_id')->pluck('name')->toArray();
            }

            // Append a number to the folder name until it's unique
            $count = 1;
            while (in_array($newFolderName, $existingNames)) {
                $newFolderName = $baseFolderName . '-' . $count;
                $count++;
            }

            // Create a new folder
            $newFolder = new Folder([
                'name' => $newFolderName,
            ]);

            // Save the new folder
            if ($parentFolder) {
                $parentFolder->children()->save($newFolder);
            } else {
                $newFolder->save();
            }

           
            $storagePath = $parentFolder ? $parentFolder->name . '/' : '';
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

    public function getContents($id)
    {
        $folder = Folder::findOrFail($id);

        return response()->json([
            'folder' => $folder,
            'files' => $folder->files,
            'subfolders' => $folder->subfolders,
        ]);
    }
    public function getFiles($id)
    {
        $folder = Folder::with('files')->find($id);

        if (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        }

        $files = $folder->files;

        return response()->json(['data' => $files], 200);
    }

    public function delete($id)
    {
         $item = Folder::find($id) ?? File::find($id);

        if (!$item) {

            return response()->json(['message' => 'Item not found.']);
        }

        $parentId = $item->parent_id;

        // Delete the folder or file
        $item->delete();

        return response()->json(['message' => 'Folder deleted successfully']);
    }


public function deleteFile($folderId, $fileId)
{
    try {
        $folder = Folder::findOrFail($folderId);
        $file = $folder->files()->findOrFail($fileId);

        // Construct the file path based on the folder structure
        $filePath = $folder->getPath() . '/' . $file->name;

        // Delete the file from storage
        Storage::disk('public')->delete($filePath);

        // Delete the file from the database
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete file.'], 500);
    }
}


}
