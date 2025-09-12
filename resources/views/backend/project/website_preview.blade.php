{{--@extends('layouts.app')--}}
{{--    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">--}}
{{--    <style>--}}
{{--        /* Override app layout for editor page */--}}
{{--        .main-content {--}}
{{--            margin: 0 !important;--}}
{{--            padding: 0 !important;--}}
{{--            width: 100vw !important;--}}
{{--            height: 100vh !important;--}}
{{--            position: fixed !important;--}}
{{--            top: 0 !important;--}}
{{--            left: 0 !important;--}}
{{--            z-index: 9999;--}}
{{--            background: white;--}}
{{--        }--}}

{{--        /* Hide sidebar and header when editing */--}}
{{--        .sidebar,--}}
{{--        .navbar,--}}
{{--        .header {--}}
{{--            display: none !important;--}}
{{--        }--}}

{{--        #gjs {--}}
{{--            width: 100%;--}}
{{--            height: 100vh;--}}
{{--        }--}}

{{--        .editor-toolbar {--}}
{{--            position: absolute;--}}
{{--            top: 10px;--}}
{{--            right: 10px;--}}
{{--            z-index: 10000;--}}
{{--            display: flex;--}}
{{--            gap: 10px;--}}
{{--        }--}}

{{--        .editor-btn {--}}
{{--            background: #007bff;--}}
{{--            color: white;--}}
{{--            border: none;--}}
{{--            padding: 8px 16px;--}}
{{--            border-radius: 4px;--}}
{{--            cursor: pointer;--}}
{{--            font-size: 14px;--}}
{{--        }--}}

{{--        .editor-btn:hover {--}}
{{--            background: #0056b3;--}}
{{--        }--}}

{{--        .editor-btn.secondary {--}}
{{--            background: #6c757d;--}}
{{--        }--}}

{{--        .editor-btn.secondary:hover {--}}
{{--            background: #545b62;--}}
{{--        }--}}
{{--    </style>--}}

{{--@section('content')--}}
{{--    <div class="main-content">--}}
{{--        <div class="editor-toolbar">--}}
{{--            <button class="editor-btn secondary" onclick="exitEditor()">← Exit</button>--}}
{{--            <button class="editor-btn" onclick="saveProject()">Save</button>--}}
{{--        </div>--}}

{{--        <div id="gjs"></div>--}}
{{--    </div>--}}
{{--@endsection--}}

{{--@push('scripts')--}}
{{--    <script src="https://unpkg.com/grapesjs"></script>--}}
{{--    <script>--}}
{{--        let editor;--}}

{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            // Hide body overflow to prevent scrolling issues--}}
{{--            document.body.style.overflow = 'hidden';--}}

{{--            editor = grapesjs.init({--}}
{{--                container: '#gjs',--}}
{{--                height: '100vh',--}}
{{--                width: '100%',--}}

{{--                storageManager: false,--}}

{{--                // Built-in blocks--}}
{{--                blockManager: {--}}
{{--                    blocks: [--}}
{{--                        {--}}
{{--                            id: 'text',--}}
{{--                            label: 'Text',--}}
{{--                            content: '<div data-gjs-type="text">Insert your text here</div>',--}}
{{--                        },--}}
{{--                        {--}}
{{--                            id: 'image',--}}
{{--                            label: 'Image',--}}
{{--                            content: { type: 'image' },--}}
{{--                            activate: true,--}}
{{--                        },--}}
{{--                        {--}}
{{--                            id: 'video',--}}
{{--                            label: 'Video',--}}
{{--                            content: {--}}
{{--                                type: 'video',--}}
{{--                                src: 'img/video2.webm',--}}
{{--                                style: {--}}
{{--                                    height: '350px',--}}
{{--                                    width: '615px'--}}
{{--                                }--}}
{{--                            },--}}
{{--                        },--}}
{{--                    ]--}}
{{--                },--}}

{{--                // Device manager--}}
{{--                deviceManager: {--}}
{{--                    devices: [--}}
{{--                        {--}}
{{--                            name: 'Desktop',--}}
{{--                            width: '',--}}
{{--                        },--}}
{{--                        {--}}
{{--                            name: 'Mobile',--}}
{{--                            width: '320px',--}}
{{--                            widthMedia: '480px',--}}
{{--                        }--}}
{{--                    ]--}}
{{--                },--}}

{{--                // Panels--}}
{{--                panels: {--}}
{{--                    defaults: [--}}
{{--                        {--}}
{{--                            id: 'basic-actions',--}}
{{--                            el: '.panel__basic-actions',--}}
{{--                            buttons: [--}}
{{--                                {--}}
{{--                                    id: 'visibility',--}}
{{--                                    active: true,--}}
{{--                                    className: 'btn-toggle-borders',--}}
{{--                                    label: '<i class="fa fa-clone"></i>',--}}
{{--                                    command: 'sw-visibility',--}}
{{--                                }--}}
{{--                            ],--}}
{{--                        },--}}
{{--                        {--}}
{{--                            id: 'panel-devices',--}}
{{--                            el: '.panel__devices',--}}
{{--                            buttons: [--}}
{{--                                {--}}
{{--                                    id: 'device-desktop',--}}
{{--                                    label: '<i class="fa fa-television"></i>',--}}
{{--                                    command: 'set-device-desktop',--}}
{{--                                    active: true,--}}
{{--                                    togglable: false,--}}
{{--                                },--}}
{{--                                {--}}
{{--                                    id: 'device-mobile',--}}
{{--                                    label: '<i class="fa fa-mobile"></i>',--}}
{{--                                    command: 'set-device-mobile',--}}
{{--                                    togglable: false,--}}
{{--                                }--}}
{{--                            ],--}}
{{--                        }--}}
{{--                    ]--}}
{{--                }--}}
{{--            });--}}

{{--            // Load content--}}
{{--            editor.on('load', function() {--}}
{{--                const htmlContent = @json($project->html_content ?? '<div style="padding: 50px; text-align: center;"><h1>Welcome!</h1><p>Start building your website here.</p></div>');--}}
{{--                const cssContent = @json($project->css_content ?? '');--}}

{{--                editor.setComponents(htmlContent);--}}
{{--                if (cssContent.trim()) {--}}
{{--                    editor.setStyle(cssContent);--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}

{{--        function saveProject() {--}}
{{--            if (!editor) return;--}}

{{--            const saveBtn = event.target;--}}
{{--            saveBtn.textContent = 'Saving...';--}}
{{--            saveBtn.disabled = true;--}}

{{--            fetch('/api/projects/{{ $project->id }}/save', {--}}
{{--                method: 'POST',--}}
{{--                headers: {--}}
{{--                    'X-CSRF-TOKEN': '{{ csrf_token() }}',--}}
{{--                    'Content-Type': 'application/json',--}}
{{--                },--}}
{{--                body: JSON.stringify({--}}
{{--                    html_content: editor.getHtml(),--}}
{{--                    css_content: editor.getCss(),--}}
{{--                })--}}
{{--            })--}}
{{--                .then(response => response.json())--}}
{{--                .then(data => {--}}
{{--                    saveBtn.textContent = 'Saved!';--}}
{{--                    setTimeout(() => {--}}
{{--                        saveBtn.textContent = 'Save';--}}
{{--                        saveBtn.disabled = false;--}}
{{--                    }, 2000);--}}
{{--                })--}}
{{--                .catch(error => {--}}
{{--                    console.error('Error:', error);--}}
{{--                    saveBtn.textContent = 'Error';--}}
{{--                    setTimeout(() => {--}}
{{--                        saveBtn.textContent = 'Save';--}}
{{--                        saveBtn.disabled = false;--}}
{{--                    }, 2000);--}}
{{--                });--}}
{{--        }--}}

{{--        function exitEditor() {--}}
{{--            if (confirm('Are you sure you want to exit? Any unsaved changes will be lost.')) {--}}
{{--                window.location.href = '{{ route("projects.index") }}';--}}
{{--            }--}}
{{--        }--}}

{{--        // Cleanup on page unload--}}
{{--        window.addEventListener('beforeunload', function() {--}}
{{--            document.body.style.overflow = '';--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}

{{--@extends('layouts.app')
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Reset / layout */
    body.editor-mode {
        overflow: hidden !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 9999;
        background: #2c2d30;
        display: flex;
        flex-direction: column;
    }

    /* Editor layout */
    .editor-container {
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    .editor-sidebar {
        width: 300px;
        background: #3c4043;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #4a4a4a;
        z-index: 1000;
    }

    .editor-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .editor-toolbar {
        background: #2c2d30;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #4a4a4a;
        height: 50px;
        flex-shrink: 0;
    }

    .editor-canvas-area {
        flex: 1;
        background: #212529;
        position: relative;
        overflow: hidden;
    }

    #gjs {
        height: 100%;
        width: 100%;
    }

    /* Buttons */
    .editor-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .editor-btn:hover { background: #0056b3; }
    .editor-btn.secondary { background: #6c757d; }
    .editor-btn.secondary:hover { background: #545b62; }
    .editor-btn.success { background: #28a745; }
    .editor-btn.success:hover { background: #1e7e34; }

    /* Sidebar tabs */
    .sidebar-tabs {
        display: flex;
        background: #2c2d30;
        border-bottom: 1px solid #4a4a4a;
    }
    .sidebar-tab {
        flex: 1;
        padding: 12px 8px;
        text-align: center;
        background: #3c4043;
        color: #ccc;
        border: none;
        cursor: pointer;
        font-size: 11px;
        text-transform: uppercase;
        border-right: 1px solid #4a4a4a;
    }
    .sidebar-tab.active { background: #007bff; color: white; }
    .sidebar-content { flex: 1; overflow-y: auto; }
    .panel-content { display: none; height: 100%; padding: 12px; }
    .panel-content.active { display: block; }

    /* Device buttons */
    .device-buttons { display: flex; gap: 4px; margin: 0 15px; }
    .device-btn { background: #4a4a4a; color: #ccc; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer; }
    .device-btn.active { background: #007bff; color: white; }

    /* Hide default GrapesJS panels */
    .gjs-pn-views-container { display: none !important; }

    /* Loading */
    .loading {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: #ccc; font-size: 18px;
    }
</style>

@section('content')
    <div class="main-content">
        <div class="editor-container">
            <!-- Sidebar -->
            <div class="editor-sidebar">
                <div class="sidebar-tabs">
                    <button class="sidebar-tab active" onclick="showPanel('blocks')">Blocks</button>
                    <button class="sidebar-tab" onclick="showPanel('layers')">Layers</button>
                    <button class="sidebar-tab" onclick="showPanel('styles')">Styles</button>
                    <button class="sidebar-tab" onclick="showPanel('traits')">Settings</button>
                </div>
                <div class="sidebar-content">
                    <div id="blocks-panel" class="panel-content active"></div>
                    <div id="layers-panel" class="panel-content"></div>
                    <div id="styles-panel" class="panel-content"></div>
                    <div id="traits-panel" class="panel-content"></div>
                </div>
            </div>

            <!-- Main area -->
            <div class="editor-main">
                <div class="editor-toolbar">
                    <div class="toolbar-left">
                        <button class="editor-btn secondary" onclick="exitEditor()">← Exit</button>
                        <div class="device-buttons">
                            <button class="device-btn active" onclick="setDevice('Desktop')" data-device="Desktop">💻</button>
                            <button class="device-btn" onclick="setDevice('Tablet')" data-device="Tablet">📱</button>
                            <button class="device-btn" onclick="setDevice('Mobile')" data-device="Mobile">📱</button>
                        </div>
                        <button class="editor-btn" onclick="toggleBorders()" id="borders-btn">Show Borders</button>
                        <button class="editor-btn" onclick="previewMode()" id="preview-btn">Preview</button>
                    </div>
                    <div class="toolbar-right">
                        <button class="editor-btn" onclick="undoAction()">↶ Undo</button>
                        <button class="editor-btn" onclick="redoAction()">↷ Redo</button>
                        <button class="editor-btn success" onclick="saveProject()" id="save-btn">💾 Save</button>
                    </div>
                </div>
                <div class="editor-canvas-area">
                    <div id="gjs"><div class="loading">Loading editor...</div></div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://unpkg.com/grapesjs"></script>
<script>
    let editor, isPreviewMode = false, bordersVisible = false;

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('editor-mode');

        editor = grapesjs.init({
            container: '#gjs',
            height: '100%', width: '100%',
            storageManager: false,
            blockManager: { appendTo: '#blocks-panel' },
            layerManager: { appendTo: '#layers-panel' },
            styleManager: { appendTo: '#styles-panel' },
            traitManager: { appendTo: '#traits-panel' },
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px', widthMedia: '992px' },
                    { name: 'Mobile', width: '320px', widthMedia: '768px' }
                ]
            },
            panels: { defaults: [] }
        });

        // Load project content from DB
        editor.on('load', () => {
            const html = @json($project->html_content ?? '<h1>Welcome!</h1><p>Start building...</p>');
            const css  = @json($project->css_content ?? '');
            editor.setComponents(html);
            if (css.trim()) editor.setStyle(css);
        });

        // Auto-save every 60s
        setInterval(() => { if (!isPreviewMode) saveProject(true); }, 60000);
    });

    function showPanel(panelName) {
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
        document.querySelectorAll('.panel-content').forEach(p => p.classList.remove('active'));
        document.getElementById(panelName + '-panel').classList.add('active');
    }

    function setDevice(name) {
        editor.setDevice(name);
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-device="${name}"]`).classList.add('active');
    }

    function toggleBorders() {
        editor.runCommand('sw-visibility');
        bordersVisible = !bordersVisible;
        document.getElementById('borders-btn').textContent = bordersVisible ? 'Hide Borders' : 'Show Borders';
    }

    function previewMode() {
        const btn = document.getElementById('preview-btn');
        if (!isPreviewMode) {
            editor.runCommand('preview');
            btn.textContent = 'Edit'; btn.style.background = '#dc3545';
            document.querySelector('.editor-sidebar').style.display = 'none';
            isPreviewMode = true;
        } else {
            editor.stopCommand('preview');
            btn.textContent = 'Preview'; btn.style.background = '#007bff';
            document.querySelector('.editor-sidebar').style.display = 'flex';
            isPreviewMode = false;
        }
    }

    function undoAction() { editor.UndoManager.undo(); }
    function redoAction() { editor.UndoManager.redo(); }

    function saveProject(silent=false) {
        const btn = document.getElementById('save-btn');
        if (!silent) { btn.textContent = '⏳ Saving...'; btn.disabled = true; }
        fetch('/api/projects/{{ $project->id }}/save', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type':'application/json'},
            body: JSON.stringify({ html_content: editor.getHtml(), css_content: editor.getCss() })
        })
            .then(r=>r.json())
            .then(()=> {
                if (!silent) {
                    btn.textContent = '✅ Saved!'; btn.style.background = '#28a745';
                    setTimeout(()=>{ btn.textContent = '💾 Save'; btn.disabled = false; },2000);
                }
            })
            .catch(()=> {
                if (!silent) return;
                btn.textContent = '❌ Error'; btn.style.background = '#dc3545';
                setTimeout(()=>{ btn.textContent = '💾 Save'; btn.disabled = false; },2000);
            });
    }

    function exitEditor() {
        if (confirm('Exit without saving?')) window.location.href = '{{ route("projects.index") }}';
    }
</script>--}}
{{--@extends('layouts.app')
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body.editor-mode {
        overflow: hidden !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 9999;
        background: #2c2d30;
        display: flex;
        flex-direction: column;
    }

    .editor-container {
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    .editor-sidebar {
        width: 300px;
        background: #3c4043;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #4a4a4a;
        z-index: 1000;
    }

    .editor-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .editor-toolbar {
        background: #2c2d30;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #4a4a4a;
        height: 50px;
        flex-shrink: 0;
        flex-wrap: nowrap; /* 👈 keep one line */
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .editor-canvas-area {
        flex: 1;
        background: #212529;
        position: relative;
        overflow: hidden;
    }

    #gjs {
        height: 100%;
        width: 100%;
    }

    .editor-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .editor-btn:hover { background: #0056b3; }
    .editor-btn.secondary { background: #6c757d; }
    .editor-btn.secondary:hover { background: #545b62; }
    .editor-btn.success { background: #28a745; }
    .editor-btn.success:hover { background: #1e7e34; }

    .sidebar-tabs {
        display: flex;
        background: #2c2d30;
        border-bottom: 1px solid #4a4a4a;
    }
    .sidebar-tab {
        flex: 1;
        padding: 10px 6px;
        text-align: center;
        background: #3c4043;
        color: #ccc;
        border: none;
        cursor: pointer;
        font-size: 11px;
        text-transform: uppercase;
        border-right: 1px solid #4a4a4a;
    }
    .sidebar-tab.active { background: #007bff; color: white; }
    .sidebar-content { flex: 1; overflow-y: auto; }
    .panel-content { display: none; height: 100%; padding: 12px; }
    .panel-content.active { display: block; }

    .device-buttons { display: flex; gap: 4px; }
    .device-btn { background: #4a4a4a; color: #ccc; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer; }
    .device-btn.active { background: #007bff; color: white; }

    .gjs-pn-views-container { display: none !important; }

    .loading {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: #ccc; font-size: 18px;
    }
</style>

@section('content')
    <div class="main-content">
        <div class="editor-container">
            <!-- Sidebar -->
            <div class="editor-sidebar">
                <div class="sidebar-tabs">
                    <button class="sidebar-tab active" onclick="showPanel('blocks')">Blocks</button>
                    <button class="sidebar-tab" onclick="showPanel('layers')">Layers</button>
                    <button class="sidebar-tab" onclick="showPanel('styles')">Styles</button>
                    <button class="sidebar-tab" onclick="showPanel('traits')">Settings</button>
                </div>
                <div class="sidebar-content">
                    <div id="blocks-panel" class="panel-content active"></div>
                    <div id="layers-panel" class="panel-content"></div>
                    <div id="styles-panel" class="panel-content"></div>
                    <div id="traits-panel" class="panel-content"></div>
                </div>
            </div>

            <!-- Main area -->
            <div class="editor-main">
                <div class="editor-toolbar">
                    <div class="toolbar-left">
                        <button class="editor-btn secondary" onclick="exitEditor()">← Exit</button>
                        <div class="device-buttons">
                            <button class="device-btn active" onclick="setDevice('Desktop')" data-device="Desktop">💻</button>
                            <button class="device-btn" onclick="setDevice('Tablet')" data-device="Tablet">📱</button>
                            <button class="device-btn" onclick="setDevice('Mobile')" data-device="Mobile">📱</button>
                        </div>
                        <button class="editor-btn" onclick="toggleBorders()" id="borders-btn">Show Borders</button>
                        <button class="editor-btn" onclick="previewMode()" id="preview-btn">Preview</button>
                    </div>
                    <div class="toolbar-right">
                        <button class="editor-btn" onclick="undoAction()">↶ Undo</button>
                        <button class="editor-btn" onclick="redoAction()">↷ Redo</button>
                        <button class="editor-btn success" onclick="saveProject()" id="save-btn">💾 Save</button>
                    </div>
                </div>
                <div class="editor-canvas-area">
                    <div id="gjs"><div class="loading">Loading editor...</div></div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>

<script>
    let editor, isPreviewMode = false, bordersVisible = false;

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('editor-mode');

        editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            width: '100%',
            storageManager: false,
            plugins: ['gjs-blocks-basic'], // ✅ basic blocks
            blockManager: { appendTo: '#blocks-panel' },
            layerManager: { appendTo: '#layers-panel' },
            styleManager: { appendTo: '#styles-panel' },
            traitManager: { appendTo: '#traits-panel' },
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px', widthMedia: '992px' },
                    { name: 'Mobile', width: '320px', widthMedia: '768px' }
                ]
            },
            panels: { defaults: [] }
        });

        // Load saved project
        editor.on('load', () => {
            const html = @json($project->html_content ?? '<h1>Welcome!</h1><p>Start building...</p>');
            const css  = @json($project->css_content ?? '');
            editor.setComponents(html);
            if (css.trim()) editor.setStyle(css);
        });

        // Auto-save every 60s
        setInterval(() => { if (!isPreviewMode) saveProject(true); }, 60000);
    });

    function showPanel(panelName) {
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
        document.querySelectorAll('.panel-content').forEach(p => p.classList.remove('active'));
        document.getElementById(panelName + '-panel').classList.add('active');
    }

    function setDevice(name) {
        editor.setDevice(name);
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-device="${name}"]`).classList.add('active');
    }

    function toggleBorders() {
        editor.runCommand('sw-visibility');
        bordersVisible = !bordersVisible;
        document.getElementById('borders-btn').textContent = bordersVisible ? 'Hide Borders' : 'Show Borders';
    }

    function previewMode() {
        const btn = document.getElementById('preview-btn');
        if (!isPreviewMode) {
            editor.runCommand('preview');
            btn.textContent = 'Edit'; btn.style.background = '#dc3545';
            document.querySelector('.editor-sidebar').style.display = 'none';
            isPreviewMode = true;
        } else {
            editor.stopCommand('preview');
            btn.textContent = 'Preview'; btn.style.background = '#007bff';
            document.querySelector('.editor-sidebar').style.display = 'flex';
            isPreviewMode = false;
        }
    }

    function undoAction() { editor.UndoManager.undo(); }
    function redoAction() { editor.UndoManager.redo(); }

    function saveProject(silent=false) {
        const btn = document.getElementById('save-btn');
        if (!silent) { btn.textContent = '⏳ Saving...'; btn.disabled = true; }
        fetch('/api/projects/{{ $project->id }}/save', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                html_content: editor.getHtml(),
                css_content: editor.getCss()
            })
        })
            .then(r => r.json())
            .then(() => {
                if (!silent) {
                    btn.textContent = '✅ Saved!'; btn.style.background = '#28a745';
                    setTimeout(() => {
                        btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';
                    }, 2000);
                }
            })
            .catch(() => {
                if (!silent) return;
                btn.textContent = '❌ Error'; btn.style.background = '#dc3545';
                setTimeout(() => {
                    btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';
                }, 2000);
            });
    }

    function exitEditor() {
        if (confirm('Exit without saving?')) {
            window.location.href = '{{ route("projects.index") }}';
        }
    }
</script>--}}
{{--@extends('layouts.app')--}}
{{--<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">--}}
{{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">--}}

{{--<style>--}}
{{--    body.editor-mode {--}}
{{--        overflow: hidden !important;--}}
{{--        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;--}}
{{--    }--}}

{{--    .main-content {--}}
{{--        margin: 0 !important;--}}
{{--        padding: 0 !important;--}}
{{--        width: 100vw !important;--}}
{{--        height: 100vh !important;--}}
{{--        position: fixed !important;--}}
{{--        top: 0 !important;--}}
{{--        left: 0 !important;--}}
{{--        z-index: 9999;--}}
{{--        background: #2c2d30;--}}
{{--        display: flex;--}}
{{--        flex-direction: column;--}}
{{--    }--}}

{{--    .editor-container { display: flex; height: 100vh; width: 100vw; }--}}
{{--    .editor-sidebar {--}}
{{--        width: 300px;--}}
{{--        background: #3c4043;--}}
{{--        display: flex;--}}
{{--        flex-direction: column;--}}
{{--        border-right: 1px solid #4a4a4a;--}}
{{--        z-index: 1000;--}}
{{--    }--}}

{{--    .editor-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }--}}

{{--    .editor-toolbar {--}}
{{--        background: #2c2d30;--}}
{{--        padding: 8px 12px;--}}
{{--        display: flex;--}}
{{--        justify-content: space-between;--}}
{{--        align-items: center;--}}
{{--        border-bottom: 1px solid #4a4a4a;--}}
{{--        height: 50px;--}}
{{--        flex-shrink: 0;--}}
{{--        flex-wrap: nowrap;--}}
{{--    }--}}
{{--    .toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 8px; }--}}

{{--    .editor-canvas-area { flex: 1; background: #212529; position: relative; overflow: hidden; }--}}
{{--    #gjs { height: 100%; width: 100%; }--}}

{{--    .editor-btn {--}}
{{--        background: #007bff; color: white; border: none; padding: 6px 12px;--}}
{{--        border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 500;--}}
{{--        transition: all 0.2s; white-space: nowrap;--}}
{{--    }--}}
{{--    .editor-btn:hover { background: #0056b3; }--}}
{{--    .editor-btn.secondary { background: #6c757d; }--}}
{{--    .editor-btn.secondary:hover { background: #545b62; }--}}
{{--    .editor-btn.success { background: #28a745; }--}}
{{--    .editor-btn.success:hover { background: #1e7e34; }--}}

{{--    .sidebar-tabs { display: flex; background: #2c2d30; border-bottom: 1px solid #4a4a4a; }--}}
{{--    .sidebar-tab {--}}
{{--        flex: 1; padding: 10px 6px; text-align: center;--}}
{{--        background: #3c4043; color: #ccc; border: none; cursor: pointer;--}}
{{--        font-size: 11px; text-transform: uppercase; border-right: 1px solid #4a4a4a;--}}
{{--    }--}}
{{--    .sidebar-tab.active { background: #007bff; color: white; }--}}
{{--    .sidebar-content { flex: 1; overflow-y: auto; }--}}
{{--    .panel-content { display: none; height: 100%; padding: 12px; }--}}
{{--    .panel-content.active { display: block; }--}}

{{--    .device-buttons { display: flex; gap: 4px; }--}}
{{--    .device-btn { background: #4a4a4a; color: #ccc; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer; }--}}
{{--    .device-btn.active { background: #007bff; color: white; }--}}

{{--    /* Fix oversized block icons */--}}
{{--    .gjs-block {--}}
{{--        width: auto !important;--}}
{{--        min-height: auto !important;--}}
{{--        padding: 6px !important;--}}
{{--        font-size: 12px !important;--}}
{{--    }--}}
{{--    .gjs-block-label { font-size: 11px !important; }--}}
{{--    .gjs-block svg, .gjs-block i, .gjs-block img {--}}
{{--        max-width: 20px !important;--}}
{{--        max-height: 20px !important;--}}
{{--    }--}}

{{--    .gjs-pn-views-container { display: none !important; }--}}

{{--    .loading {--}}
{{--        position: absolute; top: 50%; left: 50%;--}}
{{--        transform: translate(-50%, -50%); color: #ccc; font-size: 18px;--}}
{{--    }--}}
{{--</style>--}}

{{--@section('content')--}}
{{--    <div class="main-content">--}}
{{--        <div class="editor-container">--}}
{{--            <!-- Sidebar -->--}}
{{--            <div class="editor-sidebar">--}}
{{--                <div class="sidebar-tabs">--}}
{{--                    <button class="sidebar-tab active" onclick="showPanel('blocks')">Blocks</button>--}}
{{--                    <button class="sidebar-tab" onclick="showPanel('layers')">Layers</button>--}}
{{--                    <button class="sidebar-tab" onclick="showPanel('styles')">Styles</button>--}}
{{--                    <button class="sidebar-tab" onclick="showPanel('traits')">Settings</button>--}}
{{--                </div>--}}
{{--                <div class="sidebar-content">--}}
{{--                    <div id="blocks-panel" class="panel-content active"></div>--}}
{{--                    <div id="layers-panel" class="panel-content"></div>--}}
{{--                    <div id="styles-panel" class="panel-content"></div>--}}
{{--                    <div id="traits-panel" class="panel-content"></div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Main area -->--}}
{{--            <div class="editor-main">--}}
{{--                <div class="editor-toolbar">--}}
{{--                    <div class="toolbar-left">--}}
{{--                        <button class="editor-btn secondary" onclick="exitEditor()">← Exit</button>--}}
{{--                        <div class="device-buttons">--}}
{{--                            <button class="device-btn active" onclick="setDevice('Desktop')" data-device="Desktop">💻</button>--}}
{{--                            <button class="device-btn" onclick="setDevice('Tablet')" data-device="Tablet">📱</button>--}}
{{--                            <button class="device-btn" onclick="setDevice('Mobile')" data-device="Mobile">📱</button>--}}
{{--                        </div>--}}
{{--                        <button class="editor-btn" onclick="toggleBorders()" id="borders-btn">Show Borders</button>--}}
{{--                        <button class="editor-btn" onclick="previewMode()" id="preview-btn">Preview</button>--}}
{{--                    </div>--}}
{{--                    <div class="toolbar-right">--}}
{{--                        <button class="editor-btn" onclick="undoAction()">↶ Undo</button>--}}
{{--                        <button class="editor-btn" onclick="redoAction()">↷ Redo</button>--}}
{{--                        <button class="editor-btn success" onclick="saveProject()" id="save-btn">💾 Save</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="editor-canvas-area">--}}
{{--                    <div id="gjs"><div class="loading">Loading editor...</div></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}

{{--<script src="https://unpkg.com/grapesjs"></script>--}}
{{--<script src="https://unpkg.com/grapesjs-blocks-basic"></script>--}}

{{--<script>--}}
{{--    let editor, isPreviewMode = false, bordersVisible = false;--}}

{{--    document.addEventListener('DOMContentLoaded', () => {--}}
{{--        document.body.classList.add('editor-mode');--}}

{{--        editor = grapesjs.init({--}}
{{--            container: '#gjs',--}}
{{--            height: '100%',--}}
{{--            width: '100%',--}}
{{--            storageManager: false,--}}
{{--            plugins: ['gjs-blocks-basic'],--}}
{{--            blockManager: { appendTo: '#blocks-panel' },--}}
{{--            layerManager: { appendTo: '#layers-panel' },--}}
{{--            styleManager: { appendTo: '#styles-panel' },--}}
{{--            traitManager: { appendTo: '#traits-panel' },--}}
{{--            deviceManager: {--}}
{{--                devices: [--}}
{{--                    { name: 'Desktop', width: '' },--}}
{{--                    { name: 'Tablet', width: '768px', widthMedia: '992px' },--}}
{{--                    { name: 'Mobile', width: '320px', widthMedia: '768px' }--}}
{{--                ]--}}
{{--            },--}}
{{--            panels: { defaults: [] }--}}
{{--        });--}}

{{--        // Custom extra blocks--}}
{{--        const bm = editor.BlockManager;--}}
{{--        bm.add('navbar', {--}}
{{--            label: 'Navbar',--}}
{{--            category: 'Layout',--}}
{{--            content: `--}}
{{--                <nav class="navbar" style="padding:10px;background:#f8f9fa;display:flex;justify-content:space-between;align-items:center;">--}}
{{--                    <div class="logo">LOGO</div>--}}
{{--                    <ul style="list-style:none;display:flex;gap:15px;margin:0;">--}}
{{--                        <li><a href="#">Home</a></li>--}}
{{--                        <li><a href="#">About</a></li>--}}
{{--                        <li><a href="#">Contact</a></li>--}}
{{--                    </ul>--}}
{{--                </nav>`--}}
{{--        });--}}

{{--        bm.add('hero', {--}}
{{--            label: 'Hero',--}}
{{--            category: 'Layout',--}}
{{--            content: `--}}
{{--                <section class="hero" style="padding:60px;text-align:center;background:#007bff;color:white;">--}}
{{--                    <h1>Welcome to Our Website</h1>--}}
{{--                    <p>This is a hero section with a call to action</p>--}}
{{--                    <button style="padding:10px 20px;background:white;color:#007bff;border:none;border-radius:4px;">Get Started</button>--}}
{{--                </section>`--}}
{{--        });--}}

{{--        bm.add('button', {--}}
{{--            label: 'Button',--}}
{{--            category: 'Basic',--}}
{{--            content: `<button style="padding:10px 15px;background:#007bff;color:white;border:none;border-radius:4px;">Click Me</button>`--}}
{{--        });--}}

{{--        bm.add('link', {--}}
{{--            label: 'Link',--}}
{{--            category: 'Basic',--}}
{{--            content: `<a href="#" style="color:#007bff;text-decoration:none;">Sample Link</a>`--}}
{{--        });--}}

{{--        // Load saved project--}}
{{--        editor.on('load', () => {--}}
{{--            const html = @json($project->html_content ?? '<h1>Welcome!</h1><p>Start building...</p>');--}}
{{--            const css  = @json($project->css_content ?? '');--}}
{{--            editor.setComponents(html);--}}
{{--            if (css.trim()) editor.setStyle(css);--}}
{{--        });--}}

{{--        setInterval(() => { if (!isPreviewMode) saveProject(true); }, 60000);--}}
{{--    });--}}

{{--    function showPanel(panelName) {--}}
{{--        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));--}}
{{--        event.target.classList.add('active');--}}
{{--        document.querySelectorAll('.panel-content').forEach(p => p.classList.remove('active'));--}}
{{--        document.getElementById(panelName + '-panel').classList.add('active');--}}
{{--    }--}}

{{--    function setDevice(name) {--}}
{{--        editor.setDevice(name);--}}
{{--        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));--}}
{{--        document.querySelector(`[data-device="${name}"]`).classList.add('active');--}}
{{--    }--}}

{{--    function toggleBorders() {--}}
{{--        editor.runCommand('sw-visibility');--}}
{{--        bordersVisible = !bordersVisible;--}}
{{--        document.getElementById('borders-btn').textContent = bordersVisible ? 'Hide Borders' : 'Show Borders';--}}
{{--    }--}}

{{--    function previewMode() {--}}
{{--        const btn = document.getElementById('preview-btn');--}}
{{--        if (!isPreviewMode) {--}}
{{--            editor.runCommand('preview');--}}
{{--            btn.textContent = 'Edit'; btn.style.background = '#dc3545';--}}
{{--            document.querySelector('.editor-sidebar').style.display = 'none';--}}
{{--            isPreviewMode = true;--}}
{{--        } else {--}}
{{--            editor.stopCommand('preview');--}}
{{--            btn.textContent = 'Preview'; btn.style.background = '#007bff';--}}
{{--            document.querySelector('.editor-sidebar').style.display = 'flex';--}}
{{--            isPreviewMode = false;--}}
{{--        }--}}
{{--    }--}}

{{--    function undoAction() { editor.UndoManager.undo(); }--}}
{{--    function redoAction() { editor.UndoManager.redo(); }--}}

{{--    function saveProject(silent=false) {--}}
{{--        const btn = document.getElementById('save-btn');--}}
{{--        console.log('clicked')--}}
{{--        if (!silent) { btn.textContent = '⏳ Saving...'; btn.disabled = true; }--}}
{{--        fetch('/update-project/{{ $project->id }}', {--}}
{{--            method: 'POST',--}}
{{--            headers: {--}}
{{--                'X-CSRF-TOKEN': '{{ csrf_token() }}',--}}
{{--                'Content-Type': 'application/json'--}}
{{--            },--}}
{{--            body: JSON.stringify({--}}
{{--                html_content: editor.getHtml(),--}}
{{--                css_content: editor.getCss()--}}
{{--            })--}}
{{--        })--}}
{{--            .then(r => r.json())--}}
{{--            .then(() => {--}}
{{--                if (!silent) {--}}
{{--                    btn.textContent = '✅ Saved!'; btn.style.background = '#28a745';--}}
{{--                    setTimeout(() => {--}}
{{--                        btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';--}}
{{--                    }, 2000);--}}
{{--                }--}}
{{--            })--}}
{{--            .catch(() => {--}}
{{--                if (!silent) return;--}}
{{--                btn.textContent = '❌ Error'; btn.style.background = '#dc3545';--}}
{{--                setTimeout(() => {--}}
{{--                    btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';--}}
{{--                }, 2000);--}}
{{--            });--}}
{{--    }--}}

{{--    function exitEditor() {--}}
{{--        if (confirm('Exit without saving?')) {--}}
{{--            window.location.href = '{{ route("projects.index") }}';--}}
{{--        }--}}
{{--    }--}}
{{--</script>--}}

@extends('layouts.app')
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body.editor-mode {
        overflow: hidden !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 9999;
        background: #2c2d30;
        display: flex;
        flex-direction: column;
    }

    .editor-container { display: flex; height: 100vh; width: 100vw; }
    .editor-sidebar {
        width: 300px;
        background: #3c4043;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #4a4a4a;
        z-index: 1000;
    }

    .editor-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    .editor-toolbar {
        background: #2c2d30;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #4a4a4a;
        height: 50px;
        flex-shrink: 0;
        flex-wrap: nowrap;
    }
    .toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 8px; }

    .editor-canvas-area { flex: 1; background: #212529; position: relative; overflow: hidden; }
    #gjs { height: 100%; width: 100%; }

    .editor-btn {
        background: #007bff; color: white; border: none; padding: 6px 12px;
        border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 500;
        transition: all 0.2s; white-space: nowrap;
    }
    .editor-btn:hover { background: #0056b3; }
    .editor-btn.secondary { background: #6c757d; }
    .editor-btn.secondary:hover { background: #545b62; }
    .editor-btn.success { background: #28a745; }
    .editor-btn.success:hover { background: #1e7e34; }

    .sidebar-tabs { display: flex; background: #2c2d30; border-bottom: 1px solid #4a4a4a; }
    .sidebar-tab {
        flex: 1; padding: 10px 6px; text-align: center;
        background: #3c4043; color: #ccc; border: none; cursor: pointer;
        font-size: 11px; text-transform: uppercase; border-right: 1px solid #4a4a4a;
    }
    .sidebar-tab.active { background: #007bff; color: white; }
    .sidebar-content { flex: 1; overflow-y: auto; }
    .panel-content { display: none; height: 100%; padding: 12px; }
    .panel-content.active { display: block; }

    .device-buttons { display: flex; gap: 4px; }
    .device-btn { background: #4a4a4a; color: #ccc; border: none; padding: 6px 10px; border-radius: 3px; cursor: pointer; }
    .device-btn.active { background: #007bff; color: white; }

    /* Fix oversized block icons */
    .gjs-block {
        width: auto !important;
        min-height: auto !important;
        padding: 6px !important;
        font-size: 12px !important;
    }
    .gjs-block-label { font-size: 11px !important; }
    .gjs-block svg, .gjs-block i, .gjs-block img {
        max-width: 20px !important;
        max-height: 20px !important;
    }

    .gjs-pn-views-container { display: none !important; }

    .loading {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%); color: #ccc; font-size: 18px;
    }

    /* Image Gallery Modal Styles */
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .image-modal.show {
        display: flex;
    }

    .modal-content {
        background: #3c4043;
        border-radius: 8px;
        width: 90%;
        max-width: 900px;
        height: 80%;
        display: flex;
        flex-direction: column;
        color: white;
    }

    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #4a4a4a;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }

    .close-modal {
        background: none;
        border: none;
        color: #ccc;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-modal:hover {
        color: white;
    }

    .image-source-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #4a4a4a;
    }

    .image-tab {
        background: none;
        border: none;
        color: #ccc;
        padding: 10px 20px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }

    .image-tab.active {
        color: #007bff;
        border-bottom-color: #007bff;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .upload-area {
        border: 2px dashed #4a4a4a;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .upload-area:hover {
        border-color: #007bff;
        background: rgba(0, 123, 255, 0.1);
    }

    .upload-area.dragover {
        border-color: #007bff;
        background: rgba(0, 123, 255, 0.2);
    }

    .search-container {
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 12px;
        background: #2c2d30;
        border: 1px solid #4a4a4a;
        border-radius: 4px;
        color: white;
        font-size: 14px;
    }

    .search-input:focus {
        outline: none;
        border-color: #007bff;
    }

    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .image-item {
        position: relative;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .image-item:hover {
        transform: scale(1.05);
    }

    .image-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .image-item:hover .image-overlay {
        opacity: 1;
    }

    .use-image-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .use-image-btn:hover {
        background: #0056b3;
    }

    .loading-spinner {
        text-align: center;
        padding: 20px;
        color: #ccc;
    }

    .error-message {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 16px;
    }
</style>

@section('content')
    <div class="main-content">
        <div class="editor-container">
            <!-- Sidebar -->
            <div class="editor-sidebar">
                <div class="sidebar-tabs">
                    <button class="sidebar-tab active" onclick="showPanel('blocks')">Blocks</button>
                    <button class="sidebar-tab" onclick="showPanel('layers')">Layers</button>
                    <button class="sidebar-tab" onclick="showPanel('styles')">Styles</button>
                    <button class="sidebar-tab" onclick="showPanel('traits')">Settings</button>
                    <button class="sidebar-tab" onclick="showPanel('images')">Images</button>
                </div>
                <div class="sidebar-content">
                    <div id="blocks-panel" class="panel-content active"></div>
                    <div id="layers-panel" class="panel-content"></div>
                    <div id="styles-panel" class="panel-content"></div>
                    <div id="traits-panel" class="panel-content"></div>
                    <div id="images-panel" class="panel-content">
                        <button class="editor-btn" onclick="openImageModal()" style="width: 100%; margin-bottom: 12px;">
                            <i class="fas fa-images"></i> Add Images
                        </button>
                        <div id="project-images">
                            <!-- Project images will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main area -->
            <div class="editor-main">
                <div class="editor-toolbar">
                    <div class="toolbar-left">
                        <button class="editor-btn secondary" onclick="exitEditor()">← Exit</button>
                        <div class="device-buttons">
                            <button class="device-btn active" onclick="setDevice('Desktop')" data-device="Desktop">💻</button>
                            <button class="device-btn" onclick="setDevice('Tablet')" data-device="Tablet">📱</button>
                            <button class="device-btn" onclick="setDevice('Mobile')" data-device="Mobile">📱</button>
                        </div>
                        <button class="editor-btn" onclick="toggleBorders()" id="borders-btn">Show Borders</button>
                        <button class="editor-btn" onclick="previewMode()" id="preview-btn">Preview</button>
                    </div>
                    <div class="toolbar-right">
                        <button class="editor-btn" onclick="undoAction()">↶ Undo</button>
                        <button class="editor-btn" onclick="redoAction()">↷ Redo</button>
                        <button class="editor-btn success" onclick="saveProject()" id="save-btn">💾 Save</button>
                    </div>
                </div>
                <div class="editor-canvas-area">
                    <div id="gjs"><div class="loading">Loading editor...</div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Images</h3>
                <button class="close-modal" onclick="closeImageModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="image-source-tabs">
                    <button class="image-tab active" onclick="switchImageTab('upload')">Upload</button>
                    <button class="image-tab" onclick="switchImageTab('unsplash')">Unsplash</button>
                </div>

                <!-- Upload Tab -->
                <div id="upload-tab" class="tab-content active">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 48px; margin-bottom: 16px; color: #ccc;"></i>
                        <p>Drag and drop images here or click to select</p>
                        <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
                    </div>
                </div>

                <!-- Unsplash Tab -->
                <div id="unsplash-tab" class="tab-content">
                    <div class="search-container">
                        <input type="text" class="search-input" id="unsplashSearch" placeholder="Search Unsplash images...">
                    </div>
                    <div id="unsplashResults">
                        <p style="text-align: center; color: #ccc;">Enter a search term to find images from Unsplash</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>

<script>
    let editor, isPreviewMode = false, bordersVisible = false;
    let currentImageTab = 'upload';
    let unsplashSearchTimeout;

    // Unsplash configuration - you'll need to add your Unsplash API key
    const UNSPLASH_API_KEY = @json(get_option('unsplash_api_key')); // Replace with your actual API key

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('editor-mode');

        editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            width: '100%',
            storageManager: false,
            plugins: ['gjs-blocks-basic'],
            blockManager: { appendTo: '#blocks-panel' },
            layerManager: { appendTo: '#layers-panel' },
            styleManager: { appendTo: '#styles-panel' },
            traitManager: { appendTo: '#traits-panel' },
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px', widthMedia: '992px' },
                    { name: 'Mobile', width: '320px', widthMedia: '768px' }
                ]
            },
            panels: { defaults: [] }
        });

        // Custom extra blocks
        const bm = editor.BlockManager;

        // Custom image block that uses our modal
        bm.add('custom-image', {
            label: 'Image',
            category: 'Basic',
            content: {
                type: 'image'
            }
        });

        bm.add('navbar', {
            label: 'Navbar',
            category: 'Layout',
            content: `
                <nav class="navbar" style="padding:10px;background:#f8f9fa;display:flex;justify-content:space-between;align-items:center;">
                    <div class="logo">LOGO</div>
                    <ul style="list-style:none;display:flex;gap:15px;margin:0;">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </nav>`
        });

        bm.add('hero', {
            label: 'Hero',
            category: 'Layout',
            content: `
                <section class="hero" style="padding:60px;text-align:center;background:#007bff;color:white;">
                    <h1>Welcome to Our Website</h1>
                    <p>This is a hero section with a call to action</p>
                    <button style="padding:10px 20px;background:white;color:#007bff;border:none;border-radius:4px;">Get Started</button>
                </section>`
        });

        bm.add('button', {
            label: 'Button',
            category: 'Basic',
            content: `<button style="padding:10px 15px;background:#007bff;color:white;border:none;border-radius:4px;">Click Me</button>`
        });

        bm.add('link', {
            label: 'Link',
            category: 'Basic',
            content: `<a href="#" style="color:#007bff;text-decoration:none;">Sample Link</a>`
        });

        // Load saved project
        editor.on('load', () => {
            const html = @json($project->html_content ?? '<h1>Welcome!</h1><p>Start building...</p>');
            const css  = @json($project->css_content ?? '');
            editor.setComponents(html);
            if (css.trim()) editor.setStyle(css);
        });

        // Initialize image functionality
        initializeImageUpload();
        setupUnsplashSearch();

        setInterval(() => { if (!isPreviewMode) saveProject(true); }, 60000);
    });

    function showPanel(panelName) {
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
        document.querySelectorAll('.panel-content').forEach(p => p.classList.remove('active'));
        document.getElementById(panelName + '-panel').classList.add('active');
    }

    function setDevice(name) {
        editor.setDevice(name);
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-device="${name}"]`).classList.add('active');
    }

    function toggleBorders() {
        editor.runCommand('sw-visibility');
        bordersVisible = !bordersVisible;
        document.getElementById('borders-btn').textContent = bordersVisible ? 'Hide Borders' : 'Show Borders';
    }

    function previewMode() {
        const btn = document.getElementById('preview-btn');
        if (!isPreviewMode) {
            editor.runCommand('preview');
            btn.textContent = 'Edit'; btn.style.background = '#dc3545';
            document.querySelector('.editor-sidebar').style.display = 'none';
            isPreviewMode = true;
        } else {
            editor.stopCommand('preview');
            btn.textContent = 'Preview'; btn.style.background = '#007bff';
            document.querySelector('.editor-sidebar').style.display = 'flex';
            isPreviewMode = false;
        }
    }

    function undoAction() { editor.UndoManager.undo(); }
    function redoAction() { editor.UndoManager.redo(); }

    function saveProject(silent=false) {
        const btn = document.getElementById('save-btn');
        if (!silent) { btn.textContent = '⏳ Saving...'; btn.disabled = true; }
        fetch('/update-project/{{ $project->id }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                html_content: editor.getHtml(),
                css_content: editor.getCss()
            })
        })
            .then(r => r.json())
            .then(() => {
                if (!silent) {
                    btn.textContent = '✅ Saved!'; btn.style.background = '#28a745';
                    setTimeout(() => {
                        btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';
                    }, 2000);
                }
            })
            .catch(() => {
                if (!silent) return;
                btn.textContent = '❌ Error'; btn.style.background = '#dc3545';
                setTimeout(() => {
                    btn.textContent = '💾 Save'; btn.disabled = false; btn.style.background = '#28a745';
                }, 2000);
            });
    }

    function exitEditor() {
        if (confirm('Exit without saving?')) {
            window.location.href = '{{ route("projects.index") }}';
        }
    }

    // Image Modal Functions
    function openImageModal() {
        document.getElementById('imageModal').classList.add('show');
        loadProjectImages();
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.remove('show');
    }

    function switchImageTab(tabName) {
        currentImageTab = tabName;

        // Update tab buttons
        document.querySelectorAll('.image-tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(tabName + '-tab').classList.add('active');
    }

    // Image Upload Functions
    function initializeImageUpload() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                uploadImages(files);
            }
        });

        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                uploadImages(files);
            }
        });
    }

    function uploadImages(files) {
        const formData = new FormData();
        files.forEach(file => formData.append('images[]', file));
        formData.append('project_id', '{{ $project->id }}');

        fetch('/upload-images', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadProjectImages();
                    showMessage('Images uploaded successfully!', 'success');
                } else {
                    showMessage('Failed to upload images', 'error');
                }
            })
            .catch(() => {
                showMessage('Error uploading images', 'error');
            });
    }

    // Unsplash Functions
    function setupUnsplashSearch() {
        const searchInput = document.getElementById('unsplashSearch');
        searchInput.addEventListener('input', (e) => {
            clearTimeout(unsplashSearchTimeout);
            const query = e.target.value.trim();

            if (query.length > 2) {
                unsplashSearchTimeout = setTimeout(() => searchUnsplash(query), 500);
            } else {
                document.getElementById('unsplashResults').innerHTML = '<p style="text-align: center; color: #ccc;">Enter a search term to find images from Unsplash</p>';
            }
        });
    }

    function searchUnsplash(query) {
        if (!UNSPLASH_API_KEY || UNSPLASH_API_KEY === 'YOUR_UNSPLASH_API_KEY') {
            document.getElementById('unsplashResults').innerHTML = '<div class="error-message">Please configure your Unsplash API key</div>';
            return;
        }

        document.getElementById('unsplashResults').innerHTML = '<div class="loading-spinner">Searching images...</div>';

        fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=20`, {
            headers: {
                'Authorization': `Client-ID ${UNSPLASH_API_KEY}`
            }
        })
            .then(response => response.json())
            .then(data => {
                displayUnsplashResults(data.results || []);
            })
            .catch(error => {
                document.getElementById('unsplashResults').innerHTML = '<div class="error-message">Failed to search images. Please try again.</div>';
            });
    }

    function displayUnsplashResults(images) {
        const resultsContainer = document.getElementById('unsplashResults');

        if (images.length === 0) {
            resultsContainer.innerHTML = '<p style="text-align: center; color: #ccc;">No images found</p>';
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'images-grid';

        images.forEach(image => {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item';
            imageItem.innerHTML = `
                <img src="${image.urls.small}" alt="${image.alt_description || 'Unsplash image'}">
                <div class="image-overlay">
                    <button class="use-image-btn" onclick="useUnsplashImage('${image.urls.regular}', '${image.id}', '${image.user.name}')">
                        Use Image
                    </button>
                </div>
            `;
            grid.appendChild(imageItem);
        });

        resultsContainer.innerHTML = '';
        resultsContainer.appendChild(grid);
    }

    function useUnsplashImage(imageUrl, imageId, authorName) {
        // Download and upload the image to your server
        fetch('/download-unsplash-image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                image_url: imageUrl,
                image_id: imageId,
                author_name: authorName,
                project_id: '{{ $project->id }}'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the image to the canvas
                    editor.addComponents(`<img src="${data.image_url}" alt="Unsplash image by ${authorName}" style="max-width: 100%; height: auto;">`);

                    loadProjectImages();
                    closeImageModal();
                    showMessage('Image added successfully!', 'success');

                    // Trigger Unsplash download tracking (required by Unsplash API)
                    triggerUnsplashDownload(imageId);
                } else {
                    showMessage('Failed to download image', 'error');
                }
            })
            .catch(() => {
                showMessage('Error downloading image', 'error');
            });
    }

    function triggerUnsplashDownload(imageId) {
        if (UNSPLASH_API_KEY && UNSPLASH_API_KEY !== 'YOUR_UNSPLASH_API_KEY') {
            fetch(`https://api.unsplash.com/photos/${imageId}/download`, {
                headers: {
                    'Authorization': `Client-ID ${UNSPLASH_API_KEY}`
                }
            }).catch(() => {}); // Silent fail for download tracking
        }
    }

    function loadProjectImages() {
        fetch(`/project-images/{{ $project->id }}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('project-images');
                if (data.images && data.images.length > 0) {
                    const grid = document.createElement('div');
                    grid.className = 'images-grid';

                    data.images.forEach(image => {
                        const imageItem = document.createElement('div');
                        imageItem.className = 'image-item';
                        imageItem.innerHTML = `
                            <img src="${image.url}" alt="Project image">
                            <div class="image-overlay">
                                <button class="use-image-btn" onclick="addImageToCanvas('${image.url}')">
                                    Add to Canvas
                                </button>
                            </div>
                        `;
                        grid.appendChild(imageItem);
                    });

                    container.innerHTML = '';
                    container.appendChild(grid);
                } else {
                    container.innerHTML = '<p style="color: #ccc; text-align: center; padding: 20px;">No images uploaded yet</p>';
                }
            });
    }

    function addImageToCanvas(imageUrl) {
        editor.addComponents(`<img src="${imageUrl}" alt="Project image" style="max-width: 100%; height: auto;">`);
        closeImageModal();
        showMessage('Image added to canvas!', 'success');
    }

    function showMessage(message, type) {
        // You can implement a toast notification system here
        // For now, we'll use a simple alert
        if (type === 'success') {
            // You might want to implement a proper toast notification
            console.log('Success:', message);
        } else {
            console.error('Error:', message);
            alert(message);
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', (e) => {
        if (e.target.id === 'imageModal') {
            closeImageModal();
        }
    });
</script>