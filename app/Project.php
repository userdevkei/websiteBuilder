<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';
    protected $casts = [
        'is_ai_generated' => 'boolean',
        'ai_options' => 'array', // Automatically handle JSON encoding/decoding
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function members(){
    	return $this->belongsToMany('App\User','project_members', 'project_id', 'user_id');
    }

    public function setCustomDomainAttribute($value) {
        if ( empty($value) ) { 
        $this->attributes['custom_domain'] = NULL;
        } else {
            $this->attributes['custom_domain'] = $value;
        }
    }
    
     public function setSubDomainAttribute($value) {
        if ( empty($value) ) { 
        $this->attributes['sub_domain'] = NULL;
        } else {
            $this->attributes['sub_domain'] = $value;
        }
    }
    /**
     * Get the user who owns the project
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if project is AI generated
     */
    public function isAIGenerated()
    {
        return $this->is_ai_generated;
    }

    /**
     * Check if project is in preview status
     */
    public function isInPreview()
    {
        return $this->status === 'preview';
    }

    /**
     * Check if project is ready for editing
     */
    public function isEditable()
    {
        return in_array($this->status, ['lara', 'draft']);
    }

    /**
     * Get the AI options as array
     */
    public function getAIOptions()
    {
        return $this->ai_options ?: [];
    }

    /**
     * Get the color scheme from AI options
     */
    public function getColorScheme()
    {
        $options = $this->getAIOptions();
        return $options['color_scheme'] ?? 'Not specified';
    }

    /**
     * Get the style from AI options
     */
    public function getStyle()
    {
        $options = $this->getAIOptions();
        return $options['style'] ?? 'Not specified';
    }

    /**
     * Get the industry from AI options
     */
    public function getIndustry()
    {
        $options = $this->getAIOptions();
        return $options['industry'] ?? 'Not specified';
    }

    /**
     * Get the pages from AI options
     */
    public function getPages()
    {
        $options = $this->getAIOptions();
        return $options['pages'] ?? ['home'];
    }

    /**
     * Scope for AI generated projects
     */
    public function scopeAIGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    /**
     * Scope for projects by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for projects in preview status
     */
    public function scopeInPreview($query)
    {
        return $query->where('status', 'preview');
    }

    /**
     * Get route key name for route model binding
     */
    public function getRouteKeyName()
    {
        return 'id'; // or 'slug' if you prefer
    }

    /**
     * Get the project's full content (HTML with embedded CSS/JS)
     */
    public function getFullContent()
    {
        $html = $this->html_content;

        // If CSS and JS are stored separately, embed them
        if (!empty($this->css_content) && !str_contains($html, '<style>')) {
            $html = str_replace('</head>', "<style>\n{$this->css_content}\n</style>\n</head>", $html);
        }

        if (!empty($this->js_content) && !str_contains($html, '<script>')) {
            $html = str_replace('</body>', "<script>\n{$this->js_content}\n</script>\n</body>", $html);
        }

        return $html;
    }

    /**
     * Create a new AI generated project
     */
    public static function createAIProject($data, $generatedContent)
    {
        return static::create([
            'name' => $data['project_name'],
            'slug' => \Illuminate\Support\Str::slug($data['project_name'] . '-' . time()),
            'status' => 'preview',
            'html_content' => $generatedContent['html'] ?? '',
            'css_content' => $generatedContent['css'] ?? '',
            'js_content' => $generatedContent['js'] ?? '',
            'user_id' => auth()->id(),
            'is_ai_generated' => true,
            'ai_prompt' => $data['description'],
            'ai_options' => [
                'color_scheme' => $data['color_scheme'] ?? '',
                'style' => $data['style'] ?? '',
                'industry' => $data['industry'] ?? '',
                'pages' => $data['pages'] ?? ['home']
            ]
        ]);
    }

    /**
     * Get all images for this project
     */
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    /**
     * Get the latest images for this project
     */
    public function latestImages($limit = 10)
    {
        return $this->images()->latest()->limit($limit);
    }

    /**
     * Get total number of images
     */
    public function getTotalImagesAttribute()
    {
        return $this->images()->count();
    }

    /**
     * Get total size of all images in bytes
     */
    public function getTotalImagesSizeAttribute()
    {
        return $this->images()->sum('size');
    }
}