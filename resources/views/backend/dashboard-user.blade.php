@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    :root {
        --primary-color: #6366f1;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --dark-color: #1e293b;
        --light-bg: #f8fafc;
    }

    body {
        background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        min-height: 100vh;
    }

    .ai-generator-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .project-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .option-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .option-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .option-card.active {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.1) 100%);
    }

    .option-card .icon {
        width: 4rem;
        height: 4rem;
        background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .ai-form-container {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .ai-form-container.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .ai-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .btn-ai-generate {
        background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
        border: none;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-ai-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
    }

    .btn-ai-generate:disabled {
        opacity: 0.7;
        transform: none;
    }

    .loading-spinner {
        display: none;
        margin-left: 0.5rem;
    }

    .advanced-options {
        background: var(--light-bg);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .chip-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .chip {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1.5rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    .chip:hover, .chip.active {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
    }

    .progress-container {
        display: none;
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .progress-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 1rem;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: -1;
    }

    .step-icon {
        width: 2rem;
        height: 2rem;
        background: #e5e7eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .step-icon.active {
        background: var(--primary-color);
        color: white;
    }

    .step-icon.completed {
        background: var(--success-color);
        color: white;
    }

    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #f9fafb;
    }

    .upload-area:hover {
        border-color: var(--primary-color);
        background: rgba(99, 102, 241, 0.05);
    }

    .upload-area.dragover {
        border-color: var(--primary-color);
        background: rgba(99, 102, 241, 0.1);
    }

    .alert {
        border-radius: 0.75rem;
        border: none;
        padding: 1rem 1.5rem;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
        color: #065f46;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
        color: #991b1b;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

@if (\Session::has('paypal_success'))
  <div class="alert alert-success text-center">
	<b>{{ \Session::get('paypal_success') }}</b>
  </div>
  <br>
@endif

@php 
$currency = currency(); 
$date_format = get_company_option('date_format','Y-m-d');
@endphp


<div class="alert alert-warning text-center">
	<b>{{ _lang('You can assign a custom subdomains to display projects by changing it for each project from PROJECT SETTINGS') }}</b>
</div>

<!--Start Card-->
<div class="row d-flex align-items-stretch">
  <!-- Panel 1 -->
  <div class="col-md-12">
	 <div class="card h-100">
		<div class="card-body">
		    <h4 class="header-title mt-0">{{ _lang('My Recent Websites') }}</h4>
		    <div class="table-responsive card-scroll">
                <div class="d-flex justify-content-end mb-2">
                    <!-- Button to open modal -->
                    <button type="button" class="btn btn-sm btn-primary text-right" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        New Project
                    </button>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Create New Website</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="ai-generator-container">
                                    <!-- Header -->
                                    <div class="text-center mb-5">
                                        <h6 class="display-4 fw-bold text-dark mb-3" style="font-size: large !important;">
                                            <i class="fas fa-magic text-primary me-3"></i>
                                            AI Website Generator
                                        </h6>
                                        <p class="lead text-muted">Create stunning websites instantly with the power of AI</p>
                                    </div>

                                    <!-- Project Creation Options -->
                                    <div class="project-options" id="projectOptions">
                                        <div class="option-card d-flex align-items-center p-3 border rounded" data-option="ai-generate">
                                            <div class="icon me-3 fs-2 text-white">
                                                <i class="fas fa-robot"></i>
                                            </div>
                                            <div class="content">
                                                <h3 class="h5 fw-bold mb-1">AI Generate</h3>
                                                <p class="text-muted mb-0">Describe your website and let AI create it for you</p>
                                            </div>
                                        </div>

                                        <div class="option-card d-flex align-items-center p-3 border rounded" data-option="upload">
                                            <div class="icon me-3 fs-2 text-white">
                                                <i class="fas fa-upload"></i>
                                            </div>
                                            <div class="content">
                                                <h3 class="h5 fw-bold mb-1">Upload Website</h3>
                                                <p class="text-muted mb-0">Upload existing HTML, CSS, JS files</p>
                                            </div>
                                        </div>

                                        <div class="option-card d-flex align-items-center p-3 border rounded" data-option="template">
                                            <div class="icon me-3 fs-2 text-white">
                                                <i class="fas fa-th-large"></i>
                                            </div>
                                            <div class="content">
                                                <h3 class="h5 fw-bold mb-1">Choose Template</h3>
                                                <p class="text-muted mb-0">Start with pre-built templates</p>
                                            </div>
                                        </div>

                                        <div class="option-card d-flex align-items-center p-3 border rounded" data-option="blank">
                                            <div class="icon me-3 fs-2 text-white">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                            <div class="content">
                                                <h3 class="h5 fw-bold mb-1">Blank Project</h3>
                                                <p class="text-muted mb-0">Start from scratch with a blank canvas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AI Generation Form -->
                                    <div class="ai-form-container" id="aiGenerateForm">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <h2 class="h4 fw-bold mb-4">
                                                    <i class="fas fa-wand-magic text-primary me-2"></i>
                                                    Describe Your Website
                                                </h2>

                                                <form id="aiWebsiteForm">
                                                    <div class="form-group">
                                                        <label class="form-label" for="projectName">
                                                            <i class="fas fa-tag me-2"></i>Project Name *
                                                        </label>
                                                        <input type="text" class="form-control" id="projectName" name="project_name"
                                                               placeholder="Enter your project name" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label" for="aiDescription">
                                                            <i class="fas fa-comments me-2"></i>Website Description *
                                                        </label>
                                                        <textarea class="form-control ai-textarea" id="aiDescription" name="description"
                                                                  placeholder="Describe the website you want to create. Be specific about the purpose, target audience, features, and content you want. For example: 'Create a modern portfolio website for a graphic designer with a hero section, portfolio gallery, about section, services page, and contact form. Use a dark theme with vibrant accent colors.'"
                                                                  required></textarea>
                                                        <small class="form-text text-muted">
                                                            <i class="fas fa-lightbulb me-1"></i>
                                                            Be descriptive! Mention colors, layout, features, and content you want.
                                                        </small>
                                                    </div>

                                                    <!-- Advanced Options -->
                                                    <div class="advanced-options">
                                                        <h5 class="fw-bold mb-3">
                                                            <i class="fas fa-cog me-2"></i>Advanced Options
                                                        </h5>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Industry/Category</label>
                                                                    <select class="form-control" name="industry">
                                                                        <option value="">Select Industry</option>
                                                                        <option value="business">Business/Corporate</option>
                                                                        <option value="portfolio">Portfolio</option>
                                                                        <option value="ecommerce">E-commerce</option>
                                                                        <option value="blog">Blog/News</option>
                                                                        <option value="restaurant">Restaurant/Food</option>
                                                                        <option value="healthcare">Healthcare</option>
                                                                        <option value="education">Education</option>
                                                                        <option value="nonprofit">Non-profit</option>
                                                                        <option value="technology">Technology</option>
                                                                        <option value="creative">Creative/Art</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label">Design Style</label>
                                                                    <select class="form-control" name="style">
                                                                        <option value="">Select Style</option>
                                                                        <option value="modern">Modern & Clean</option>
                                                                        <option value="minimalist">Minimalist</option>
                                                                        <option value="corporate">Corporate</option>
                                                                        <option value="creative">Creative & Artistic</option>
                                                                        <option value="dark">Dark Theme</option>
                                                                        <option value="colorful">Colorful & Vibrant</option>
                                                                        <option value="elegant">Elegant & Sophisticated</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label">Color Scheme</label>
                                                            <div class="chip-container">
                                                                <div class="chip" data-color="blue and white">Blue & White</div>
                                                                <div class="chip" data-color="dark and gold">Dark & Gold</div>
                                                                <div class="chip" data-color="green and white">Green & White</div>
                                                                <div class="chip" data-color="purple and silver">Purple & Silver</div>
                                                                <div class="chip" data-color="red and black">Red & Black</div>
                                                                <div class="chip" data-color="orange and cream">Orange & Cream</div>
                                                                <div class="chip" data-color="monochrome">Monochrome</div>
                                                                <div class="chip" data-color="rainbow">Colorful</div>
                                                            </div>
                                                            <input type="hidden" name="color_scheme" id="colorScheme">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label">Pages to Include</label>
                                                            <div class="chip-container">
                                                                <div class="chip page-chip active" data-page="home">Home</div>
                                                                <div class="chip page-chip" data-page="about">About</div>
                                                                <div class="chip page-chip" data-page="services">Services</div>
                                                                <div class="chip page-chip" data-page="portfolio">Portfolio</div>
                                                                <div class="chip page-chip" data-page="contact">Contact</div>
                                                                <div class="chip page-chip" data-page="blog">Blog</div>
                                                                <div class="chip page-chip" data-page="pricing">Pricing</div>
                                                                <div class="chip page-chip" data-page="team">Team</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-4">
                                                        <button type="submit" class="btn btn-ai-generate btn-lg">
                                                            <i class="fas fa-magic me-2"></i>
                                                            Generate Website
                                                            <div class="loading-spinner">
                                                                <i class="fas fa-spinner fa-spin"></i>
                                                            </div>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <i class="fas fa-lightbulb text-warning me-2"></i>
                                                            Tips for Better Results
                                                        </h5>
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2">
                                                                <i class="fas fa-check text-success me-2"></i>
                                                                Be specific about your business type and target audience
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-check text-success me-2"></i>
                                                                Mention specific features you want (forms, galleries, etc.)
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-check text-success me-2"></i>
                                                                Describe your preferred layout and style
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-check text-success me-2"></i>
                                                                Include color preferences and branding details
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-check text-success me-2"></i>
                                                                Specify any special functionality needed
                                                            </li>
                                                        </ul>

                                                        <div class="alert alert-info mt-3">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            <strong>Example:</strong> "Create a modern restaurant website with online menu, reservation system, photo gallery, and contact information. Use warm colors like orange and brown."
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Upload Form -->
                                    <div class="ai-form-container" id="uploadForm">
                                        <h2 class="h4 fw-bold mb-4">
                                            <i class="fas fa-upload text-primary me-2"></i>
                                            Upload Website Files
                                        </h2>

                                        <div class="upload-area" id="uploadArea">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h5>Drag and drop your files here</h5>
                                            <p class="text-muted">Or click to select HTML, CSS, JS files</p>
                                            <input type="file" id="fileInput" multiple accept=".html,.css,.js,.zip" style="display: none;">
                                        </div>

                                        <div id="uploadedFiles" class="mt-3" style="display: none;">
                                            <h6>Uploaded Files:</h6>
                                            <div id="filesList"></div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-ai-generate btn-lg" id="processUploadBtn" disabled>
                                                <i class="fas fa-cog me-2"></i>
                                                Process Files
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Progress Container -->
                                    <div class="progress-container" id="progressContainer">
                                        <div class="progress-steps">
                                            <div class="progress-step">
                                                <div class="step-icon" id="step1">
                                                    <i class="fas fa-brain"></i>
                                                </div>
                                                <span class="small">Analyzing</span>
                                            </div>
                                            <div class="progress-step">
                                                <div class="step-icon" id="step2">
                                                    <i class="fas fa-code"></i>
                                                </div>
                                                <span class="small">Generating</span>
                                            </div>
                                            <div class="progress-step">
                                                <div class="step-icon" id="step3">
                                                    <i class="fas fa-paint-brush"></i>
                                                </div>
                                                <span class="small">Styling</span>
                                            </div>
                                            <div class="progress-step">
                                                <div class="step-icon" id="step4">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <span class="small">Complete</span>
                                            </div>
                                        </div>

                                        <div class="progress mb-3">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                 role="progressbar" style="width: 0%"></div>
                                        </div>

                                        <p class="text-muted" id="progressText">Initializing AI generation...</p>
                                    </div>

                                    <!-- Alerts -->
                                    <div id="alertContainer"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-sm table-bordered">
				      <thead>
					    <tr>
                            <th>#</th>
							<th>{{ _lang('Name') }}</th>	
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
						@foreach($projects->take(10) as $project)
						    <tr>
                                <td>{{ $loop->iteration }}</td>
								<td><a href="{{ route('larabuilder.editor', $project->id) }}">{{ $project->name }}</a></td>
                                <td>{{ $project->status }}</td>
								<td>
                                    <a href="{{ route('larabuilder.editor', $project->id) }}" data-title="{{_lang('Update Project')}}" class="btn btn-warning btn-sm"><i class="ti-pencil"></i> {{_lang('Edit')}}</a>
{{--										<a href="{{ action('ProjectController@edit', $project['id']) }}" data-title="{{_lang('Edit Project Settings')}}" class="btn btn-primary btn-sm ajax-modal"><i class="ti-notepad"></i> {{_lang('Settings')}}</a>--}}
                                    <a class="btn btn-sm btn-danger" href="{{ route('projects.delete', $project->id) }}">{{_lang('Delete')}}</a>
                                </td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	 </div>
  </div>
  <!-- End Panel 1 -->
</div>

@endsection

@section('js-script')
<script src="{{ asset('backend/assets/js/ajax-datatable/projects.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // CSRF Token Setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // DOM Elements
    const projectOptions = document.querySelectorAll('.option-card');
    const aiGenerateForm = document.getElementById('aiGenerateForm');
    const uploadForm = document.getElementById('uploadForm');
    const progressContainer = document.getElementById('progressContainer');
    const alertContainer = document.getElementById('alertContainer');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeProjectOptions();
        initializeAIForm();
        initializeUploadForm();
    });

    // Project Options Handler
    function initializeProjectOptions() {
        projectOptions.forEach(option => {
            option.addEventListener('click', function() {
                const optionType = this.getAttribute('data-option');

                // Remove active class from all options
                projectOptions.forEach(opt => opt.classList.remove('active'));

                // Add active class to clicked option
                this.classList.add('active');

                // Hide all forms
                document.querySelectorAll('.ai-form-container').forEach(form => {
                    form.classList.remove('active');
                });

                // Show appropriate form
                if (optionType === 'ai-generate') {
                    aiGenerateForm.classList.add('active');
                } else if (optionType === 'upload') {
                    uploadForm.classList.add('active');
                } else if (optionType === 'template') {
                    // Redirect to templates page or show template selection
                    // window.location.href = '#';
                } else if (optionType === 'blank') {
                    window.location.href = "{{ route('builder.lara') }}";
                }
            });
        });
    }

    // AI Form Handler
    function initializeAIForm() {
        const form = document.getElementById('aiWebsiteForm');
        const colorChips = document.querySelectorAll('[data-color]');
        const pageChips = document.querySelectorAll('.page-chip');

        // Color scheme selection
        colorChips.forEach(chip => {
            chip.addEventListener('click', function() {
                colorChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('colorScheme').value = this.getAttribute('data-color');
            });
        });

        // Page selection
        pageChips.forEach(chip => {
            chip.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            generateAIWebsite();
        });
    }

    // Upload Form Handler
    function initializeUploadForm() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadedFiles = document.getElementById('uploadedFiles');
        const filesList = document.getElementById('filesList');
        const processBtn = document.getElementById('processUploadBtn');

        // Click to upload
        uploadArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        // File input change
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                uploadedFiles.style.display = 'block';
                processBtn.disabled = false;

                filesList.innerHTML = '';
                Array.from(files).forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center';
                    fileItem.innerHTML = `
                            <span><i class="fas fa-file me-2"></i>${file.name}</span>
                            <span class="badge bg-primary">${formatFileSize(file.size)}</span>
                        `;
                    filesList.appendChild(fileItem);
                });
            }
        }
    }

    // Generate AI Website - Fixed Version with proper FormData debugging
    async function generateAIWebsite() {
        const form = document.getElementById('aiWebsiteForm');
        const formData = new FormData(form);

        // Debug FormData properly - FormData doesn't show in console.log directly
        console.log('=== FormData Debug ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Get selected pages
        const selectedPages = Array.from(document.querySelectorAll('.page-chip.active'))
            .map(chip => chip.getAttribute('data-page'));

        console.log('Selected pages:', selectedPages);

        // Add pages to form data
        selectedPages.forEach(page => {
            formData.append('pages[]', page);
        });

        // Debug FormData again after adding pages
        console.log('=== FormData After Adding Pages ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Check if required fields have values
        const requiredFields = ['project_name', 'description'];
        const missingFields = [];

        requiredFields.forEach(field => {
            if (!formData.get(field)) {
                missingFields.push(field);
            }
        });

        if (missingFields.length > 0) {
            showAlert('danger', `Please fill in required fields: ${missingFields.join(', ')}`);
            return;
        }

        // Show progress
        showProgress();

        // Disable form
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        const spinner = submitBtn.querySelector('.loading-spinner');
        if (spinner) {
            spinner.style.display = 'inline-block';
        }

        try {
            console.log('Sending request to:', '/larabuilder/generate-website');
            console.log('CSRF Token:', csrfToken);

            const response = await fetch('/larabuilder/generate-website', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                console.error('Non-JSON response received:', textResponse);
                throw new Error('Server returned non-JSON response');
            }

            const result = await response.json();
            console.log('Response result:', result);

            if (result.success) {
                completeProgress();
                showAlert('success', result.message);

                // Redirect to editor after delay
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 2000);
            } else {
                hideProgress();
                showAlert('danger', result.message || 'An unknown error occurred');

                if (result.errors) {
                    Object.keys(result.errors).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('is-invalid');

                            // Remove existing feedback first
                            const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                            if (existingFeedback) {
                                existingFeedback.remove();
                            }

                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = result.errors[key][0];
                            input.parentNode.appendChild(feedback);
                        }
                    });
                }

                // Log debug info if available
                if (result.debug_info) {
                    console.error('Debug info:', result.debug_info);
                }
            }
        } catch (error) {
            hideProgress();
            showAlert('danger', 'An error occurred while generating the website. Please try again.');
            console.error('Fetch Error:', error);
            console.error('Error stack:', error.stack);
        } finally {
            // Re-enable form
            submitBtn.disabled = false;
            const spinner = submitBtn.querySelector('.loading-spinner');
            if (spinner) {
                spinner.style.display = 'none';
            }
        }
    }

    // Helper function to convert FormData to regular object (for debugging)
    function formDataToObject(formData) {
        const object = {};
        for (let [key, value] of formData.entries()) {
            if (object[key]) {
                // Handle multiple values for same key (like arrays)
                if (Array.isArray(object[key])) {
                    object[key].push(value);
                } else {
                    object[key] = [object[key], value];
                }
            } else {
                object[key] = value;
            }
        }
        return object;
    }

    // Alternative debugging approach - use this instead of console.log(formData)
    function debugFormData(formData) {
        const obj = formDataToObject(formData);
        console.log('FormData as object:', obj);

        console.log('FormData entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }

        console.log('FormData has entries:', formData.entries().next().done === false);
    }

    // Updated form validation
    function validateForm() {
        const form = document.getElementById('aiWebsiteForm');
        const requiredInputs = form.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                clearFieldError(input);
            }
        });

        return isValid;
    }

    function showFieldError(input, message) {
        clearFieldError(input);
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        input.parentNode.appendChild(feedback);
    }

    function clearFieldError(input) {
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    // Test function to check if form is working
    function testFormData() {
        const form = document.getElementById('aiWebsiteForm');
        if (!form) {
            console.error('Form not found!');
            return;
        }

        const formData = new FormData(form);
        debugFormData(formData);

        // Check individual form fields
        const inputs = form.querySelectorAll('input, textarea, select');
        console.log('Form inputs:');
        inputs.forEach(input => {
            console.log(`${input.name || 'unnamed'}: ${input.value}`);
        });
    }

    // Progress Management
    function showProgress() {
        document.getElementById('projectOptions').style.display = 'none';
        document.querySelectorAll('.ai-form-container').forEach(form => {
            form.style.display = 'none';
        });
        progressContainer.style.display = 'block';

        simulateProgress();
    }

    function simulateProgress() {
        const steps = ['step1', 'step2', 'step3', 'step4'];
        const messages = [
            'Analyzing your requirements...',
            'Generating HTML structure...',
            'Creating beautiful styles...',
            'Finalizing your website...'
        ];

        let currentStep = 0;
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.getElementById('progressText');

        function updateStep() {
            if (currentStep < steps.length) {
                // Update step icon
                document.getElementById(steps[currentStep]).classList.add('active');

                // Update progress bar
                const progress = ((currentStep + 1) / steps.length) * 100;
                progressBar.style.width = progress + '%';

                // Update text
                progressText.textContent = messages[currentStep];

                currentStep++;

                if (currentStep < steps.length) {
                    setTimeout(updateStep, 2000);
                }
            }
        }

        updateStep();
    }

    function completeProgress() {
        const steps = ['step1', 'step2', 'step3', 'step4'];
        steps.forEach(step => {
            const stepEl = document.getElementById(step);
            stepEl.classList.remove('active');
            stepEl.classList.add('completed');
        });

        document.querySelector('.progress-bar').style.width = '100%';
        document.getElementById('progressText').textContent = 'Website generated successfully!';
    }

    function hideProgress() {
        progressContainer.style.display = 'none';
        document.getElementById('projectOptions').style.display = 'grid';
    }

    // Utility Functions
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

        alertContainer.appendChild(alert);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function createBlankProject() {
        // Implementation for creating blank project
        window.location.href = '/larabuilder/new-project';
    }

    // Clear form validation on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('is-invalid')) {
            e.target.classList.remove('is-invalid');
            const feedback = e.target.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.remove();
            }
        }
    });

</script>
@endsection
