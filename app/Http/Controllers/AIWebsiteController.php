<?php
///*
//namespace App\Http\Controllers;
//
//use App\Project;
//use App\Services\AIWebsiteGenerator;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Str;
//
//class AIWebsiteController extends Controller
//{
//    protected $aiGenerator;
//
//    public function __construct(AIWebsiteGenerator $aiGenerator)
//    {
//        $this->aiGenerator = $aiGenerator;
//    }
//
//    /**
//     * Show AI generation form
//     */
//    public function showGenerationForm()
//    {
//        return view('larabuilder.ai-generator');
//    }
//
//    /**
//     * Generate website using AI
//     */
//    public function generateWebsite(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'description' => 'required|string|min:10|max:1000',
//            'project_name' => 'required|string|max:255',
//            'color_scheme' => 'nullable|string|max:100',
//            'style' => 'nullable|string|max:100',
//            'industry' => 'nullable|string|max:100',
//            'pages' => 'nullable|array',
//            'pages.*' => 'string|max:50',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'success' => false,
//                'errors' => $validator->errors()
//            ], 422);
//        }
//
//        try {
//            // Generate website using AI
//            $options = [
//                'color_scheme' => $request->color_scheme ?? 'modern blue and white',
//                'style' => $request->style ?? 'modern and clean',
//                'industry' => $request->industry ?? 'general business',
//                'pages' => $request->pages ?? ['home'],
//            ];
//
//            $generatedContent = $this->aiGenerator->generateWebsite($request->description, $options);
//
//            // Create project in database
//            $project = $this->createProject($request->project_name, $generatedContent);
//
//            return response()->json([
//                'success' => true,
//                'message' => 'Website generated successfully!',
//                'project_id' => $project->id,
//                'redirect_url' => route('larabuilder.editor', $project->id)
//            ]);
//
//        } catch (\Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => $e->getMessage()
//            ], 500);
//        }
//    }
//
//    /**
//     * Generate specific section using AI
//     */
//    public function generateSection(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'description' => 'required|string|min:10|max:500',
//            'section_type' => 'nullable|string|max:50',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'success' => false,
//                'errors' => $validator->errors()
//            ], 422);
//        }
//
//        try {
//            $sectionCode = $this->aiGenerator->generateSection(
//                $request->description,
//                $request->section_type ?? 'general'
//            );
//
//            return response()->json([
//                'success' => true,
//                'html' => $sectionCode
//            ]);
//
//        } catch (\Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => $e->getMessage()
//            ], 500);
//        }
//    }
//
//    /**
//     * Create project in database
//     */
//    private function createProject($name, $content)
//    {
//        // Assuming you have a Project model - adjust according to your LaraBuilder structure
//        $project = new Project();
//        $project->name = $name;
//        $project->status = 'lara';
//        $project->slug = Str::slug($name . '-' . time());
//        $project->html_content = $content['html'];
//        $project->css_content = $content['css'];
//        $project->js_content = $content['js'];
//        $project->user_id = auth()->id();
//        $project->is_ai_generated = true;
//        $project->save();
//
//        return $project;
//    }
//}*/

namespace App\Http\Controllers;

use App\Project;
use App\Services\AIWebsiteGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIWebsiteController extends Controller
{
    protected $aiGenerator;

    public function __construct(AIWebsiteGenerator $aiGenerator)
    {
        $this->aiGenerator = $aiGenerator;
    }

    /**
     * Show AI generation form
     */
    public function showGenerationForm()
    {
        return view('larabuilder.ai-generator');
    }

    /**
     * Generate website using AI - Updated to redirect to preview
     */
    public function generateWebsite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10|max:1000',
            'project_name' => 'required|string|max:255',
            'color_scheme' => 'nullable|string|max:100',
            'style' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'pages' => 'nullable|array',
            'pages.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log the incoming request
            Log::info('AI Website Generation Request:', [
                'description' => $request->description,
                'project_name' => $request->project_name,
                'user_id' => auth()->id()
            ]);

            // Generate website using AI
            $options = [
                'color_scheme' => $request->color_scheme ?? 'modern blue and white',
                'style' => $request->style ?? 'modern and clean',
                'industry' => $request->industry ?? 'general business',
                'pages' => $request->pages ?? ['home'],
            ];

            $generatedContent = $this->aiGenerator->generateWebsite($request->description, $options);

            if (!$generatedContent || !$generatedContent['success']) {
                throw new \Exception('AI generation returned no content');
            }

            // Create project in database with preview status
            $project = $this->createProject($request->project_name, $generatedContent, $request->all());

            Log::info('AI Website Generated Successfully:', ['project_id' => $project->id]);

            return response()->json([
                'success' => true,
                'message' => 'Website generated successfully!',
                'project_id' => $project->id,
                'redirect_url' => route('projects.renderPreview', $project->id) // Redirect to preview
            ]);

        } catch (\Exception $e) {
            Log::error('AI Website Generation Error:', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate website: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show website preview
     */
    public function showPreview($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Check if user owns this project
        if ($project->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this project');
        }

        return view('backend.project.website_preview', compact('project'));
    }

    /**
     * Render website content for preview iframe
     */
    public function renderPreview($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Check permissions
        if ($project->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Return the raw HTML content
        $htmlContent = $project->html_content;

        // Add viewport meta tag if not present
        if (!str_contains($htmlContent, 'viewport')) {
            $htmlContent = str_replace(
                '<head>',
                '<head><meta name="viewport" content="width=device-width, initial-scale=1.0">',
                $htmlContent
            );
        }

        // Add base tag to prevent relative URL issues
        $baseTag = '<base href="' . url('/') . '/">';
        $htmlContent = str_replace('<head>', '<head>' . $baseTag, $htmlContent);

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    /**
     * Accept preview and redirect to editor
     */
    public function acceptPreview($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Check permissions
        if ($project->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Update project status to accepted
        $project->update(['status' => 'lara']); // Using your existing 'lara' status

        return redirect()->route('larabuilder.editor', $project->id)
            ->with('success', 'Ready to edit your website!');
    }

    /**
     * Regenerate website with same parameters
     */
    public function regenerateWebsite($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            // Check permissions
            if ($project->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (!$project->is_ai_generated || !$project->ai_prompt) {
                return response()->json(['success' => false, 'message' => 'Cannot regenerate non-AI project'], 400);
            }

            // Get original options
            $options = json_decode($project->ai_options, true) ?: [];

            // Regenerate with same prompt and options
            $generatedContent = $this->aiGenerator->generateWebsite($project->ai_prompt, $options);

            if (!$generatedContent || !$generatedContent['success']) {
                throw new \Exception('AI generation failed');
            }

            // Update project with new content
            $project->update([
                'html_content' => $generatedContent['html'],
                'css_content' => $generatedContent['css'],
                'js_content' => $generatedContent['js'],
                'updated_at' => now(),
            ]);

            Log::info('Website regenerated successfully:', ['project_id' => $project->id]);

            return response()->json([
                'success' => true,
                'message' => 'Website regenerated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Website regeneration failed:', [
                'project_id' => $projectId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate specific section using AI
     */
    public function generateSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10|max:500',
            'section_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sectionCode = $this->aiGenerator->generateSection(
                $request->description,
                $request->section_type ?? 'general'
            );

            return response()->json([
                'success' => true,
                'html' => $sectionCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug AI configuration and test connection
     */
    public function debugAI()
    {
        try {
            // Check configuration
            $config = [
                'api_key_exists' => !empty(get_option('open_ai_key')),
                'api_key_preview' => get_option('open_ai_key') ? 'sk-...' . substr(get_option('open_ai_key'), -4) : 'Not set',
                'model' => $this->aiGenerator->getModelInfo()['model'],
            ];

            // Test connection
            $connectionTest = $this->aiGenerator->testConnection();

            return response()->json([
                'config' => $config,
                'connection_test' => $connectionTest,
                'model_info' => $this->aiGenerator->getModelInfo()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Create project in database - Updated to match your structure
     */
    private function createProject($name, $content, $requestData = [])
    {
        try {
            $project = new Project();
            $project->name = $name;
            $project->status = 'preview'; // Set initial status to preview
            $project->slug = Str::slug($name . '-' . time());
            $project->html_content = $content['html'] ?? '';
            $project->css_content = $content['css'] ?? '';
            $project->js_content = $content['js'] ?? '';
            $project->user_id = auth()->id(); // Using your existing user_id field
            $project->is_ai_generated = true;

            // Store AI generation data for regeneration
            $project->ai_prompt = $requestData['description'] ?? '';
            $project->ai_options = json_encode([
                'color_scheme' => $requestData['color_scheme'] ?? '',
                'style' => $requestData['style'] ?? '',
                'industry' => $requestData['industry'] ?? '',
                'pages' => $requestData['pages'] ?? []
            ]);

            $project->save();

            Log::info('Project created in database:', ['project_id' => $project->id]);

            return $project;

        } catch (\Exception $e) {
            Log::error('Error creating project in database: ' . $e->getMessage());
            throw new \Exception('Failed to save project: ' . $e->getMessage());
        }
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $project->html_content = $request->input('html_content', $project->html_content);
        $project->css_content  = $request->input('css_content', $project->css_content);
        $project->js_content   = $request->input('js_content', $project->js_content);
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully',
            'project' => $project
        ]);
    }

}