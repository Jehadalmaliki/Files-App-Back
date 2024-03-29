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

            if ($request->parent_id) {
                // If parent folder is specified, check for duplicates in its children
                $parentFolder = Folder::find($request->parent_id);
                $existingNames = $parentFolder->children()->pluck('name')->toArray();
            } else {
                // Check for duplicates in the root folders
                $existingNames = Folder::whereNull('parent_id')->pluck('name')->toArray();
            }

            $count = 1;
            while (in_array($newFolderName, $existingNames)) {
                $newFolderName = $baseFolderName . '-' . $count++;
            }

            $newFolder = new Folder([
                'name' => $newFolderName,
            ]);

            // Save the new folder
            if ($parentFolder) {
                $parentFolder->children()->save($newFolder);
            } else {
                $newFolder->save();
            }

            // Construct the full storage path for the new folder
            $storagePath = $this->buildStoragePath($newFolder);
            $newFolder->path = $storagePath;

            // Save the path to the database
            $newFolder->save();

            Storage::disk('public')->makeDirectory($storagePath);

            return response()->json(['message' => 'Folder created successfully', 'data' => $newFolder], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create folder. Database error.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create folder.'], 500);
        }
    }


    private function buildStoragePath($folder)
    {
        $path = $folder->name;
        while ($folder->parent_id) {
            $folder = Folder::find($folder->parent_id);
            $path = $folder->name . '/' . $path;
        }
        return $path;
    }

    public function uploadFile($folderId, Request $request)
    {
        $folder = Folder::find($folderId);

        // Validate request for multiple files
        $request->validate([
            'name' => 'required', // Ensure 'name' is an array of files
            'name.*' => 'mimes:jpeg,png,pdf,gif,svg,txt,doc,docx|max:2048', // Validate each file
        ]);

        $files = $request->file('name'); // Get all files from the request

        // Initialize an array to hold response data for all uploaded files
        $uploadedFilesData = [];

        foreach ($files as $uploadedFile) {
            $name = $uploadedFile->getClientOriginalName();
            $filePath = $this->buildStoragePath($folder) . '/' . $name;

            // Save the file to storage
            Storage::disk('public')->put($filePath, file_get_contents($uploadedFile));

            $fileSize = $uploadedFile->getSize();
            $sizeInKB = $fileSize / 1024;

            $newFile = File::create([
                'name' => $name,
                'size' => $sizeInKB,
                'folder_id' => $folder->id, // Assuming you have a 'folder_id' column to associate files with folders
            ]);

            $folder->files()->save($newFile);

            $uploadedFilesData[] = [
                'name' => $name,
                'size' => $sizeInKB,
                'path' => $filePath
            ];

        }

        // Return a response with the data of all uploaded files
        return response()->json([
            'message' => 'Files uploaded successfully',
            'data' => $uploadedFilesData
        ], 201);
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
