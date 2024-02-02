<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;

class FileController extends Controller
{
    public function index(Request $request){
        $fills=File::all();

        return response()->json(['name' => $fills], 200);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'name.*' => 'required|mimes:jpeg,png,pdf,gif,svg,txt|max:2048',
            'folder' => 'nullable|string',
        ]);
          $folder = $request->folder;

          if ($folder) {
            Storage::disk('public')->makeDirectory($folder);
            $name=$request->name->getClientOriginalName();
            $filePath = $folder ? $folder . '/' . $name : $name;
            Storage::disk('public')->put($filePath, file_get_contents($request->name));
            $fileSize =$request->name->getSize();
            $sizeInKB = $fileSize / 1024;
            $folderId = $folder ? Folder::firstOrCreate(['name' => $folder])->id : null;
            File::create([
                'name' =>  $name,
                'size' =>  $sizeInKB,
                'folder_id' => $folderId,


            ]);
            return response()->json(['message' => 'Files uploaded successfully']);

        }
        else{
            $name=$request->name->getClientOriginalName();
            Storage::disk('public')->put($name,file_get_contents($request->name));
            $fileSize =$request->name->getSize();

             $sizeInKB = $fileSize / 1024;
              File::create([
                  'name' =>  $name,
                  'size' =>  $sizeInKB,

              ]);
          return response()->json(['message' => 'Files uploaded successfully']);
        }

    }
    public function show($id){
        $fills=File::find($id);
        if(!$fills){
            return response()->json(['message' => 'not found'], 404);

        }
        return response()->json(['name' => $fills], 200);
    }
    public function update(Request $request,$id){
        $fills=File::find($id);
        if(!$fills){
            return response()->json(['message' => 'not found'], 404);

        }

        if($request->name){
            $storge=Storage::disk('public');
            if($storge->exists(  $fills->name)){
                $storge->delete( $fills->name);

                $name=$request->name->getClientOriginalName();
                $fills->name=$name;
                $storge->put($name,file_get_contents($request->name));

                $fills->save();
            }
            return response()->json(['message' => 'file update'], 200);
        }
        return response()->json(['message' => ' not file update'], 200);

    }

    public function delete($id)
    {
        $file = File::findOrFail($id);
        Storage::disk('public')->delete($file->name);
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
