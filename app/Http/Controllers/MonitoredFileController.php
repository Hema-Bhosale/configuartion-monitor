<?php

namespace App\Http\Controllers;

use App\Models\MonitoredFile;
use Illuminate\Http\Request;

class MonitoredFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->path;

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File does not exist.',
                'path' => $path,
            ], 404);
        }

        $filename = basename($path);

        // Detect file type
        if ($filename === '.env' || strpos($filename, '.env.') === 0) {
            $fileType = 'env';
        } else {
            $extension = strtolower(
                pathinfo($path, PATHINFO_EXTENSION)
            );

            if (!in_array($extension, [
                'json',
                'yaml',
                'yml',
                'properties'
            ])) {
                return response()->json([
                    'message' => 'Unsupported file type.'
                ], 422);
            }

            $fileType = $extension;
        }

        $existingFile = MonitoredFile::where(
            'path',
            $path
        )->first();

        if ($existingFile) {
            return response()->json([
                'message' => 'File is already being monitored.',
                'data' => $existingFile,
            ], 409);
        }

        $monitoredFile = MonitoredFile::create([
            'path' => $path,
            'file_type' => $fileType,
            'status' => 'active',
            'last_hash' => null,
            'last_snapshot' => null,
        ]);

        return response()->json([
            'message' => 'File added for monitoring.',
            'data' => $monitoredFile,
        ], 201);
    }
}