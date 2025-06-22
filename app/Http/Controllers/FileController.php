<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function __construct()
    {
        // Set PHP upload limits at runtime
        ini_set('upload_max_filesize', '500M');
        ini_set('post_max_size', '500M');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');
    }

    public function store(Request $request, Folder $folder)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:524288', // 500MB max (500 * 1024 KB)
            ]);

            $uploadedFile = $request->file('file');
            $fileName = $uploadedFile->getClientOriginalName();

            // Check file size manually as well
            $fileSize = $uploadedFile->getSize();
            $maxSize = 524288 * 1024; // 500MB in bytes

            if ($fileSize > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'File size exceeds the maximum allowed size of 500MB.'
                ], 413);
            }

            $folderPath = public_path($folder->project->project_path . '/' . $folder->name);
            if (!file_exists($folderPath)) {
                if (!mkdir($folderPath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create folder directory. Please check permissions.'
                    ], 500);
                }
            }

            $filePath = $uploadedFile->move($folderPath, $fileName);
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to move uploaded file.'
                ], 500);
            }

            $dbPath = str_replace(public_path(), '', $filePath);

            $file = File::create([
                'folder_id' => $folder->id,
                'name' => $fileName,
                'path' => $dbPath,
                'created_by' => Auth::id(),
            ]);

            return response()->json(['success' => true, 'file' => $file]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'The file failed to upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeForProject(Request $request, Project $project)
    {
        try {
            // Check current PHP limits
            $currentUploadMax = ini_get('upload_max_filesize');
            $currentPostMax = ini_get('post_max_size');

            // Convert to bytes for comparison
            $uploadMaxBytes = $this->convertToBytes($currentUploadMax);
            $postMaxBytes = $this->convertToBytes($currentPostMax);

            $request->validate([
                'file' => 'required|file|max:' . min($uploadMaxBytes / 1024, $postMaxBytes / 1024), // Use the smaller limit
            ]);

            $uploadedFile = $request->file('file');
            $fileName = $uploadedFile->getClientOriginalName();

            // Check file size manually as well
            $fileSize = $uploadedFile->getSize();
            $maxAllowedSize = min($uploadMaxBytes, $postMaxBytes);

            if ($fileSize > $maxAllowedSize) {
                return response()->json([
                    'success' => false,
                    'message' => "File size ({$this->formatBytes($fileSize)}) exceeds the server limit. Current limits: upload_max_filesize={$currentUploadMax}, post_max_size={$currentPostMax}. Please contact your server administrator to increase these limits."
                ], 413);
            }

            // Ensure project_path is set
            if (!$project->project_path) {
                $project->generateProjectPath();
                $project->save();
            }

            $projectPath = public_path($project->project_path);

            // Create directory if it doesn't exist
            if (!file_exists($projectPath)) {
                if (!mkdir($projectPath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create project directory. Please check permissions.'
                    ], 500);
                }
            }

            // Check if directory is writable
            if (!is_writable($projectPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project directory is not writable. Please check permissions.'
                ], 500);
            }

            // Move the uploaded file
            $filePath = $uploadedFile->move($projectPath, $fileName);
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to move uploaded file.'
                ], 500);
            }

            $dbPath = str_replace(public_path(), '', $filePath);

            $file = File::create([
                'project_id' => $project->id,
                'name' => $fileName,
                'path' => $dbPath,
                'created_by' => Auth::id(),
            ]);

            return response()->json(['success' => true, 'file' => $file]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'The file failed to upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert PHP size string to bytes
     */
    private function convertToBytes($sizeStr)
    {
        $sizeStr = strtolower(trim($sizeStr));
        $last = strtolower($sizeStr[strlen($sizeStr) - 1]);
        $size = (int) $sizeStr;

        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function download(File $file)
    {
        $filePath = public_path($file->path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $file->name);
        }
        abort(404);
    }
}
