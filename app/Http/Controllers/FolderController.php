<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = Folder::create([
            'project_id' => $project->id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        // Create the folder in the filesystem
        $basePath = public_path($project->project_path);
        $folderPath = $basePath . DIRECTORY_SEPARATOR . $request->name;
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    public function destroy(Folder $folder)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }
        $folder->delete();
        return response()->json(['success' => true]);
    }

    public function show(Folder $folder)
    {
        $folder->load(['children', 'files', 'creator']);
        return view('folders.show', compact('folder'));
    }

    public function downloadZip(Folder $folder)
    {
        $folder->load(['children', 'files']);
        $zip = new \ZipArchive();
        $zipFileName = $folder->name . '.zip';
        $tmpFile = tempnam(sys_get_temp_dir(), $zipFileName);
        $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $this->addFolderToZip($zip, $folder, $folder->name);

        $zip->close();

        return response()->download($tmpFile, $zipFileName)->deleteFileAfterSend(true);
    }

    private function addFolderToZip($zip, $folder, $path)
    {
        // Add files in this folder
        foreach ($folder->files as $file) {
            $filePath = public_path($file->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $path . '/' . $file->name);
            }
        }
        // Add subfolders recursively
        foreach ($folder->children as $subfolder) {
            $subfolder->loadMissing(['children', 'files']);
            $this->addFolderToZip($zip, $subfolder, $path . '/' . $subfolder->name);
        }
    }
}
