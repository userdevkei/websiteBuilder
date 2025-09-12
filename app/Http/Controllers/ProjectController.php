<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectMember;
use Validator;
use DataTables;
use Auth;
use DB;
use Notification;
use App\Notifications\ProjectCreated;
use App\Notifications\ProjectUpdated;
use File;
use Session;

class ProjectController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka')));
        
        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                
                if( ! has_feature( 'websites_limit' ) ){
                    if( ! $request->ajax()){
                        return redirect('membership/extend')->with('message', _lang('Sorry, This feature is not available in your current subscription. You can upgrade your package !'));
                    }else{
                        return response()->json(['result'=>'error','message'=>_lang('Sorry, This feature is not available in your current subscription !')]);
                    }
                }

                //If request is create/store
                $route_name = \Request::route()->getName();
                
                if( $route_name == 'projects.create'){
                   if( has_feature_limit( 'websites_limit' ) ){

                    $company_id = company_id();
                    $user_type = Auth::user()->user_type;

                    if($user_type == 'admin') {
                        $projects = Project::select('projects.*')->orderBy("projects.id","desc")->count();
                    } else {
                        $projects = Project::select('projects.*')
    //                                        ->with('members')
                                            ->where('company_id',$company_id)
                                            ->when($user_type, function ($query, $user_type) {
                                                    if($user_type == 'staff'){
                                                    return $query->join('project_members','projects.id','project_members.project_id')
                                                                    ->where('project_members.user_id',Auth::id());
                                                    }
                                                })
                                                ->orderBy("projects.id","desc")->count();
                            if($projects >= Auth::user()->company->websites_limit && Auth::user()->company->websites_limit != 'Unlimited'){
                                if( ! $request->ajax()){
                                    return redirect('membership/extend')->with('message', _lang('Your have already reached your usages limit. You can upgrade your package !'));
                                }else{
                                    return response()->json(['result'=>'error','message'=> _lang('Your have already reached your usages limit. You can upgrade your package !') ]);
                                }
                            }
                        }
                    }

                    
                }
            }
            return $next($request);
        });
    }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

        $data['demo'] = false;

        $user = Auth::getUser();
        $companyMembershipType = $user->company->membership_type;


        if ($companyMembershipType === 'trial' && membership_validity() > now()->toDateString()) {
            $data['demo'] = true;
    }
    
        return view('backend.accounting.project.list', $data);
  }

  public function builder()
  {
        $user = Auth::getUser();
        $companyMembershipType = $user->company->membership_type;

        if ($companyMembershipType === 'trial' && membership_validity() > now()->toDateString()) {
            return redirect('membership/extend')->with('message', _lang('Sorry, this feature is not available in your current subscription. You can upgrade your package!'));
    }

    return view('backend.accounting.project.builder');
  }

    public function get_table_data(Request $request){
        $company_id = company_id();
        $user_type = Auth::user()->user_type;
        
        if($user_type == 'admin') {
            $projects = Project::select('projects.*')->orderBy("projects.id","desc");
        } else {
            $projects = Project::select('projects.*')
            ->where('company_id',$company_id)
            ->when($user_type, function ($query, $user_type) {
                    if($user_type == 'staff'){
                    return $query->join('project_members','projects.id','project_members.project_id')
                                    ->where('project_members.user_id',Auth::id());
                    }
                })
                ->orderBy("projects.id","desc");
        }

        return Datatables::eloquent($projects)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('status')) {
                                $query->whereIn('status', json_decode($request->post('status')));
                            }

                        })
                        ->addColumn('action', function ($project) {
                            return '<form action="'.action('ProjectController@destroy', $project['id']).'" class="text-center" method="post">'
                            .'<a href="'.action('ProjectController@edit', $project['id']).'" data-title="'. _lang('Edit Project Details') .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-notepad"> </i> '._lang('Settings').'</a>&nbsp;'
                            .'<a href="'.action('ProjectController@edit', $project['id']).'" data-title="'. _lang('Update Project') .'" class="btn btn-warning btn-xs"><i class="ti-pencil"></i> '._lang('Edit').'</a>&nbsp;'
                            .csrf_field()
                            .'<input name="_method" type="hidden" value="DELETE">'
                            .'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i> '._lang('Delete').'</button>'
                            .'</form>';
                        })
                        ->setRowId(function ($project) {
                          return "row_".$project->id;
                        })
                        ->rawColumns(['action','members.name','status','name'])
                        ->make(true);
    }

	public function get_project_info( $id = '' ){
  		$project = Project::where("id",$id)
  					      ->where("company_id",company_id())->first();
  		echo json_encode($project);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $data['demo']   =   false;

        $user = Auth::getUser();
        $companyMembershipType = $user->company->membership_type;

        if ($companyMembershipType == 'trial' && membership_validity() > now()->toDateString()) {
            $data['demo']   =   true;
        }


        return \Redirect::to(url('/builder') . '/lara');
        /*
            if(get_option('default_builder') == '' || get_option('default_builder') == 'both'){
                if( ! $request->ajax()){
                    return view('backend.accounting.project.create', $data);
                }else{
                    return view('backend.accounting.project.modal.create', $data);
                }
            }else{
                return \Redirect::to(url('/builder').'/'.get_option('default_builder'));
            }
        */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            }else{
                return redirect()->route('projects.create')
                               ->withErrors($validator)
                               ->withInput();
            }
        }

        DB::beginTransaction();

        $project = new Project();
        $project->name = $request->input('name');
        $project->description = $request->input('description');
        $project->save();

        create_log('projects', $project->id, _lang('Created Project'));


        DB::commit();


        if(! $request->ajax()){
           return redirect()->route('projects.create')->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'), 'data'=>$project, 'table' => '#projects_table']);
        }

   }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $company_id = company_id();
        $user_type = Auth::user()->user_type;
        $data = array();
        
        if(Auth()->user()->user_type == 'admin') {
            $data['project'] = Project::where('projects.id', $id)->first();
        } else {
            $data['project'] = Project::where('projects.id', $id)
            ->where('company_id', $company_id)
            ->when($user_type, function ($query, $user_type) {
                  if($user_type == 'staff'){
                      $query->whereHas('members', function( $q ) use( $user_type ){
                          $q->where('user_id', Auth::id());
                      });
                  }
              })
            ->first();
        }

       
		if(! $data['project']){
			return back()->with('error', _lang('Sorry, Project not found !'));
		}

        if(Auth()->user()->user_type == 'admin') {

            //get Summary data
            $data['hour_completed'] = \App\TimeSheet::where('project_id',$id)
            ->selectRaw("SUM( TIMESTAMPDIFF(SECOND, start_time, end_time) ) as total_seconds")
            ->first();


            $data['invoices'] = \App\Invoice::where('related_to','projects')
            ->where('related_id', $id)
            ->get();

            $data['tasks'] = \App\Task::where('project_id',$id)->get();

            $data['timesheets'] = \App\TimeSheet::where('project_id',$id)
            ->orderBy('id','desc')
            ->get();

            $data['project_milestones']  = \App\ProjectMilestone::where('project_id',$id)
                        ->orderBy('id','desc')
                        ->get();


            $data['projectfiles'] = \App\ProjectFile::where('related_id', $id)
            ->where('related_to', 'projects')
            ->orderBy('id','desc')
            ->get();

            $data['notes'] = \App\Note::where('related_id', $id)
            ->where('related_to', 'projects')
            ->orderBy('id','desc')
            ->get();

        } else {

            //get Summary data
            $data['hour_completed'] = \App\TimeSheet::where('project_id',$id)
            ->selectRaw("SUM( TIMESTAMPDIFF(SECOND, start_time, end_time) ) as total_seconds")
            ->where('company_id', $company_id)
            ->first();


            $data['invoices'] = \App\Invoice::where('related_to','projects')
            ->where('related_id', $id)
            ->where('company_id', $company_id)
            ->get();

            $data['expenses'] = \App\Transaction::where("transactions.company_id",$company_id)
            ->where("project_id",$id)
            ->orderBy("transactions.id","desc")
            ->get();

            $data['tasks'] = \App\Task::where('project_id',$id)
            ->where('company_id', $company_id)
            ->when($user_type, function ($query, $user_type) {
            if($user_type == 'staff'){
            return $query->where('assigned_user_id',Auth::id());
            }
            })
            ->get();

            $data['timesheets'] = \App\TimeSheet::where('project_id',$id)
            ->where('company_id', $company_id)
            ->orderBy('id','desc')
            ->get();

            $data['project_milestones']  = \App\ProjectMilestone::where('project_id',$id)
                        ->where('company_id',$company_id)
                        ->orderBy('id','desc')
                        ->get();


            $data['projectfiles'] = \App\ProjectFile::where('related_id', $id)
            ->where('related_to', 'projects')
            ->where('company_id', $company_id)
            ->orderBy('id','desc')
            ->get();

            $data['notes'] = \App\Note::where('related_id', $id)
            ->where('related_to', 'projects')
            ->where('company_id', $company_id)
            ->orderBy('id','desc')
            ->get();

        }

        

        return view('backend.accounting.project.view', $data);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $user = Auth::user();
        $project = null;

        if ($user->user_type === 'admin') {
            $project = Project::find($id);
        } else {
            $project = Project::where('id', $id)->where('company_id', company_id())->first();
        }

        if (!$project) {
            return redirect()->back()->withErrors(['error' => 'Project not found.']);
        }

        $projectfile = \App\ProjectFile::where('related_to', 'projects')->where('related_id', $id)->first();

        $data = [
            'project' => $project,
            'id' => $project->id,
            'company_id' => $project->company_id,
            'demo' => false,
            'projectfile' => $projectfile ? str_replace(public_path() . "/uploads/project_files/", asset('public/uploads/project_files') . '/', $projectfile->file) : null,
        ];

        if ($user->company->membership_type === 'trial' && membership_validity() > date('Y-m-d')) {
            $data['demo'] = true;
        }

        if (!$request->ajax()) {
            define('SUPRA_BASE_PATH', base_path('public/backend/assets/builder'));
            define('SUPRA_BASE_URL', asset('public/backend/assets/builder'));

            $Viewbuilder = new \App\Utilities\Builder\Html;
            $data['groups'] = $Viewbuilder->groups;

            return view('backend.accounting.project.editlara', $data);
            /*
                if($project->status == 'lara'){



                    define('SUPRA_BASE_PATH', base_path('public/backend/assets/builder'));
                    define('SUPRA_BASE_URL', asset('public/backend/assets/builder'));


                    $Viewbuilder = new \App\Utilities\Builder\Html;

                    $data['groups'] =   $Viewbuilder->groups;

                    $project = Project::where('id',$id)
                                    ->where('company_id',company_id())
                                    ->first();
                    $projectfile = \App\ProjectFile::where('related_to','projects')->where('related_id',$id)->first();;
                    
                    $data['project']        =   $project;
                    $data['projectfile']    =   str_replace(public_path()."/uploads/project_files/",asset('public/uploads/project_files').'/',$projectfile->file);
                    $data['id']             =   $project->id;

                    define('SUPRA', 1);

                    return view('backend.accounting.project.editlara',$data);
                }else{
                    return view('backend.accounting.project.edit',$data);
                }
            */
            } else {
            return view('backend.accounting.project.modal.edit', $data);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if(env('DEMO_MODE') == true  && !empty($request->input('sub_domain'))){
            if($request->ajax()) {
                return response()->json(['result' => 'error', 'action' => 'update', 'message' => _lang('DEMO MODE NOT ALLOWED')]);
            }else{
                return redirect()->back()->with('error', _lang('DEMO MODE NOT ALLOWED'));
            }
        }

        if(env('DEMO_MODE') == true  && !empty($request->input('custom_domain'))){
            if($request->ajax()) {
                return response()->json(['result' => 'error', 'action' => 'update', 'message' => _lang('DEMO MODE NOT ALLOWED')]);
            }else{
                return redirect()->back()->with('error', _lang('DEMO MODE NOT ALLOWED'));
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sub_domain'           => 'nullable|sometimes|unique:projects,sub_domain,' . $id,
            'custom_domain'     => 'nullable|sometimes|unique:projects,custom_domain,' . $id
        ]);




    DB::beginTransaction();    
    $domain_main = '.'.getAppDomain();

    $company_id = $request->company_id;
    Session::put('company_id', $company_id);
    $project = Project::where('id',$id)->first();

            if( $request->sub_domain && Project::where('sub_domain', $request->sub_domain.$domain_main)->first()){
                return response()->json(['result'=>'error','message'=>_lang('This subdomain is already exist')]);
                return redirect()->back();
            } elseif ($request->custom_domain  && Project::where('custom_domain', $request->custom_domain)->first()) {
                return response()->json(['result'=>'error','message'=>_lang('This custom domain is already exist')]);
                return redirect()->back();
            }

           
        $project->name = $request->input('name');
        $project->description = $request->input('description');

        
        if (isset($request->sub_domain)) {
            # code...
            if (strpos($domain_main, $request->sub_domain)) {
                return back()->with('error', __('Subdomain must have ').$domain_main);
            }
        }
        
        if(isset($request->sub_domain) && $request->sub_domain != ''){
            if(str_contains($request->sub_domain, getAppDomain())) {
                $project->sub_domain = $request->sub_domain;
                $project->custom_domain = Null;
            } else {
                $project->sub_domain = $request->sub_domain . $domain_main;
                $project->custom_domain = Null;
            }
        } else {
                $project->sub_domain = Null;
                $project->custom_domain = $request->custom_domain;
        }


        $project->save();

        create_log('projects', $project->id, _lang('Updated Project'));

        DB::commit();



    if(! $request->ajax()){
           return redirect()->route('projects.index')->with('success', _lang('Updated Sucessfully'));
        }else{
       return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'), 'data'=>$project, 'table' => '#projects_table']);
    }

    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_project_member(Request $request, $member_id)
    {
        DB::beginTransaction();

        $query = ProjectMember::join('projects', 'projects.id', '=', 'project_members.project_id')
            ->where('project_members.user_id', $member_id);

        if (Auth::user()->user_type !== 'admin') {
            $query->where('company_id', company_id());
        }

        $project_member = $query->select('project_members.*')->first();

        create_log('projects', $project_member->project_id, _lang('Removed') . ' ' . $project_member->user->name . ' ' . _lang('from Project'));

        $project_member->delete();
        DB::commit();

        if (!$request->ajax()) {
            return back()->with('success', _lang('Removed Successfully'));
        }

        return response()->json([
            'result' => 'success',
            'action' => 'delete',
            'message' => _lang('Member Removed'),
            'id' => $member_id,
            'table' => '#project_members_table'
        ]);
    }


    /* Get Logs Data*/
    public function get_logs_data($project_id)
    {
            $logsQuery = \App\ActivityLog::with('created_by')
            ->select('activity_logs.*')
            ->where('related_to', 'projects')
            ->where('related_id', $project_id)
            ->orderBy('activity_logs.id', 'desc');

        if (Auth()->user()->user_type !== 'admin') {
            $companyId = company_id();
            $logsQuery->where('activity_logs.company_id', $companyId);
        }

        $logs = $logsQuery->get();

        return response()->json($logs);
    }


    /**
     * Store File to Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    {

        $max_size = get_option('file_manager_max_upload_size',2) * 1024;
        $supported_file_types = get_option('file_manager_file_type_supported','png,jpg,jpeg');

        $validator = Validator::make($request->all(), [
            'related_id' => 'required',
            'file' => "required|file|max:$max_size|mimes:$supported_file_types",
        ]);

        if ($validator->fails()) {
            if($request->ajax()){
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return back()->withErrors($validator)
                             ->withInput();
            }
        }

        $file_path = '';
        if($request->hasfile('file'))
        {
            $file = $request->file('file');
            $file_path = time().$file->getClientOriginalName();
            $file->move(public_path()."/uploads/project_files/", $file_path);
        }

        $projectfile = new \App\ProjectFile();
        $projectfile->related_to = 'projects';
        $projectfile->related_id = $request->input('related_id');
        $projectfile->file = $file_path;
        $projectfile->user_id = Auth::id();

        $projectfile->company_id = company_id();

        $projectfile->save();

        create_log('projects', $projectfile->related_id, _lang('Uploaded File'));

        //Prefix output
        $projectfile->file = '<a href="'. url('projects/download_file/'.$projectfile->file) .'">'.$projectfile->file .'</a>';
        $projectfile->user_id = '<a href="'. action('StaffController@show', $projectfile->user->id) .'" data-title="'. _lang('View Staf Information') .'"class="ajax-modal-2">'. $projectfile->user->name .'</a>';
        $projectfile->remove = '<a class="ajax-get-remove" href="'. url('projects/delete_file/'.$projectfile->id) .'">'. _lang('Remove') .'</a>';

        if(! $request->ajax()){
           return back()->with('success', _lang('File Uploaded Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('File Uploaded Sucessfully'),'data'=>$projectfile, 'table' => '#files_table']);
        }

   }

   /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_file(Request $request, $id)
    {
        if(Auth::user()->user_type == 'admin'){
            $projectfile = \App\ProjectFile::where($id)
                                           ->where('company_id',$company_id());
            unlink(public_path('uploads/project_files/'.$projectfile->file));
            $projectfile->delete();

            create_log('projects', $id, _lang('File Removed'));
        }

        if(Auth::user()->user_type != 'admin'){
            $projectfile = \App\ProjectFile::where('id',$id)
                                           ->where('user_id',Auth::id())
                                           ->first();
            if(!$projectfile){
                if(! $request->ajax()){
                   return back()->with('error',_lang('Sorry only admin or creator can remove this file !'));
                }else{
                   return response()->json(['result'=>'error','message'=>_lang('Sorry only admin or creator can remove this file !')]);
                }

            }
            unlink(public_path('uploads/project_files/'.$projectfile->file));
            $projectfile->delete();

            create_log('projects', $id, _lang('File Removed'));
        }

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Removed Sucessfully'),'id'=>$id, 'table' => '#files_table']);
        }

    }

    public function download_file(Request $request, $file){
        $file = 'public/uploads/project_files/'.$file;
        return response()->download($file);
    }

    /**
     * Store note.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_note(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'related_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('notes.create')
                                 ->withErrors($validator)
                                 ->withInput();
            }
        }

        $note = new \App\Note();
        $note->related_to ='projects';
        $note->related_id = $request->input('related_id');
        $note->note = $request->input('note');
        $note->user_id = Auth::id();
        if(Auth()->user()->user_type != 'admin') { 
            $note->company_id = company_id();
        }
        $note->save();

        create_log('projects', $note->related_id, _lang('Added Note'));

        //Prefix Output
        $note->created = '<small>'.$note->user->name.'('.$note->created_at.')<br>'.$note->note.'</small>';
        $note->action = '<a href="'. url('projects/delete_note/'.$note->id) .'" class="ajax-get-remove"><i class="far fa-trash-alt text-danger"></i></a>';

        if(! $request->ajax()){
           return back()->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$note, 'table' => '#notes_table']);
        }

   }

   /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_note(Request $request, $id)
    {
        if(Auth::user()->user_type == 'admin'){
            $note = \App\Note::where('id', $id)
                             ->where('company_id', company_id());
            $note->delete();
            create_log('projects', $id, _lang('Removed Note'));
        }

        if(Auth::user()->user_type != 'admin'){
            $note = \App\Note::where('id',$id)
                             ->where('user_id',Auth::id())
                             ->first();
            if(!$note){
                if(! $request->ajax()){
                   return back()->with('error',_lang('Sorry only admin or creator can remove this file !'));
                }else{
                   return response()->json(['result'=>'error','message'=>_lang('Sorry only admin or creator can remove this file !')]);
                }

            }
            $note->delete();
            create_log('projects', $id, _lang('Removed Note'));
        }

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Removed Sucessfully'),'id'=>$id, 'table' => '#notes_table']);
        }

    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
          $objects = scandir($dir);
          foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
              if (filetype($dir."/".$object) == "dir")
                    $this->rrmdir($dir."/".$object);
              else unlink   ($dir."/".$object);
            }
          }
          reset($objects);
          rmdir($dir);
        }
       }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        DB::beginTransaction();

        try {
        $company_id = company_id();


            $projectQuery = Project::where('id', $id);

            if (Auth()->user()->user_type !== 'admin') {
                $projectQuery->where('company_id', $company_id);
            }

            $project = $projectQuery->first();

            // Ensure the project exists before attempting to delete
            if (!$project) {
                return redirect()->route('projects.index')->with('error', _lang('Project not found.'));
        }

            $project->delete(); // Delete the project

            // Remove associated temporary files
            $this->rrmdir(public_path('tmp/' . Auth::user()->id . '/' . $id));

            // Log the deletion action
        create_log('projects', $id, _lang('File Removed'));

        DB::commit();

            return redirect()->route('projects.index')->with('success', _lang('Deleted Successfully'));
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('projects.index')->with('error', _lang('An error occurred while deleting the project.'));
        }
    }

    public function deleteProject($id)
    {
        Project::find($id)->delete();
        return redirect()->route('projects.index')->with('success', _lang('Deleted Successfully'));
    }

    public function imgRemove(Request $request){
        $path = $request->path;
        $name = $request->name;
        $id = $request->userId;

        $imgPath = public_path('backend/assets/builder/images/gallery/uploaded/'.$id.'/'.$name.'.jpg');
    
        if(File::exists($imgPath)) {
            File::delete($imgPath);
        }
   
        return response()->json(["status" => "ok"]);
    }

    public function videoRemove(Request $request){
        $path = $request->path;
        $videoName = $request->videoName;
        $id = $request->userId;

        $videoPath = public_path('backend/assets/builder/'.$path);

        if(File::exists($videoPath)) {
            File::delete($videoPath);
        }
   
        return response()->json(["status" => "ok"]);
    }
}