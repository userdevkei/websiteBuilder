<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240', // 10MB max
            'project_id' => 'required|exists:projects,id'
        ]);

        $projectId = $request->project_id;
        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            try {
                // Generate unique filename
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store in public/storage/project_images directory
                $path = $image->storeAs('project_images', $filename, 'public');

                // Save to database
                $projectImage = ProjectImage::create([
                    'project_id' => $projectId,
                    'filename' => $filename,
                    'original_name' => $image->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ]);

                $uploadedImages[] = [
                    'id' => $projectImage->id,
                    'url' => Storage::url($path),
                    'filename' => $filename,
                    'original_name' => $image->getClientOriginalName()
                ];

            } catch (\Exception $e) {
                \Log::error('Image upload failed: ' . $e->getMessage());
                continue;
            }
        }

        if (empty($uploadedImages)) {
            return response()->json([
                'success' => false,
                'message' => 'No images were uploaded successfully'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'images' => $uploadedImages
        ]);
    }

    /**
     * Download image from Unsplash and save to project
     */
    public function downloadUnsplashImage(Request $request)
    {
        $request->validate([
            'image_url' => 'required|url',
            'image_id' => 'required|string',
            'author_name' => 'required|string',
            'project_id' => 'required|exists:projects,id'
        ]);

        try {
            // Download image from Unsplash
            $response = Http::timeout(30)->get($request->image_url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image from Unsplash'
                ], 400);
            }

            // Generate unique filename
            $extension = $this->getImageExtensionFromUrl($request->image_url);
            $path = 'unsplash_' . $request->image_id . '_' . time() . '.' . $extension;

            \Log::info('Saving to: ' . Storage::disk('project')->path($path));

            Storage::disk('project')->put($path, $response->body());


//            // Save image to storage
//            $path = 'project_images/' . $filename;
//            Storage::disk('project')->storeAs($path, $response->body());

            // Save to database
            $projectImage = ProjectImage::create([
                'project_id' => $request->project_id,
                'filename' => $path,
                'original_name' => 'Unsplash image by ' . $request->author_name,
                'path' => $path,
                'url' => Storage::disk('project')->url($path),
                'size' => strlen($response->body()),
                'mime_type' => $response->header('Content-Type', 'image/jpeg'),
                'source' => 'unsplash',
                'source_id' => $request->image_id,
                'source_author' => $request->author_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image downloaded successfully',
                'image_url' => Storage::url($path),
                'image_id' => $projectImage->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Unsplash image download failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to download and save image'
            ], 500);
        }
    }

    /**
     * Get all images for a specific project
     */
    public function getProjectImages($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            $images = ProjectImage::where('project_id', $projectId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                        'filename' => $image->filename,
                        'original_name' => $image->original_name,
                        'size' => $image->size,
                        'created_at' => $image->created_at->format('Y-m-d H:i:s'),
                        'source' => $image->source ?? 'upload',
                        'source_author' => $image->source_author
                    ];
                });

            return response()->json([
                'success' => true,
                'images' => $images
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project images'
            ], 500);
        }
    }

    /**
     * Delete a specific image
     */
    public function deleteImage($imageId)
    {
        try {
            $image = ProjectImage::findOrFail($imageId);

            // Delete file from storage
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete from database
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }

    /**
     * Get image extension from URL
     */
    private function getImageExtensionFromUrl($url)
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Default to jpg if no extension found
        if (empty($extension)) {
            $extension = 'jpg';
        }

        // Validate extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            $extension = 'jpg';
        }

        return strtolower($extension);
    }
}
