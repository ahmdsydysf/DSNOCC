<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Display projects in modern card layout.
     */
    public function display(): View
    {
        $projects = Project::latest()->get();
        return view('projects.display', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:projects'],
            'code' => ['nullable', 'string', 'max:255'],
        ]);

        $project = Project::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => auth()->user()->id,
        ]);

        // Generate project path and create folder
        $projectPath = 'uploads/projects/' . Str::slug($project->name);
        $fullPath = public_path($projectPath);

        // Create the project folder
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        // Update the project with the generated path
        $project->update(['project_path' => $projectPath]);

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'project' => $project
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        $project->load(['creator', 'folders.creator']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:projects,name,' . $project->id],
            'code' => ['nullable', 'string', 'max:255'],
        ]);

        $oldName = $project->name;
        $oldPath = $project->project_path;

        $project->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        // If name changed, update folder structure
        if ($oldName !== $request->name) {
            $newPath = 'uploads/projects/' . Str::slug($request->name);
            $oldFullPath = public_path($oldPath);
            $newFullPath = public_path($newPath);

            // Rename the folder if it exists
            if (File::exists($oldFullPath)) {
                File::moveDirectory($oldFullPath, $newFullPath);
            } else {
                // Create new folder if old one doesn't exist
                if (!File::exists($newFullPath)) {
                    File::makeDirectory($newFullPath, 0755, true);
                }
            }

            // Update the project path
            $project->update(['project_path' => $newPath]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        // Delete the project folder
        $projectPath = public_path($project->project_path);
        if (File::exists($projectPath)) {
            File::deleteDirectory($projectPath);
        }

        $project->delete();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function downloadZip(Project $project)
    {
        $project->load(['folders.children', 'files']);
        $zip = new \ZipArchive();
        $zipFileName = $project->name . '.zip';
        $tmpFile = tempnam(sys_get_temp_dir(), $zipFileName);
        $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Add project-level files
        foreach ($project->files as $file) {
            $filePath = public_path($file->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $file->name);
            }
        }

        // Add all top-level folders recursively
        foreach ($project->folders()->whereNull('parent_id')->get() as $folder) {
            $this->addFolderToZip($zip, $folder, $folder->name);
        }

        $zip->close();

        return response()->download($tmpFile, $zipFileName)->deleteFileAfterSend(true);
    }

    private function addFolderToZip($zip, $folder, $path)
    {
        $folder->loadMissing(['children', 'files']);
        foreach ($folder->files as $file) {
            $filePath = public_path($file->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $path . '/' . $file->name);
            }
        }
        foreach ($folder->children as $subfolder) {
            $this->addFolderToZip($zip, $subfolder, $path . '/' . $subfolder->name);
        }
    }
}
