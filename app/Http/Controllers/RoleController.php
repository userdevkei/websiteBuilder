<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use Validator;

class RoleController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone','Asia/Dhaka'));
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyId = company_id();
        $roles = Role::Where("company_id",$companyId)->orderBy("id","desc")->get();
        
        return view('backend.accounting.staff.role.list',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $view = $request->ajax() ? 'backend.accounting.staff.role.modal.create'  : 'backend.accounting.staff.role.create';
        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateRole($request);
    
        if ($validator->fails()) {
            return $this->handleValidationFailure($request, $validator, 'roles.create');
        }
    
        $role = $this->createOrUpdateRole(new Role(), $request);
    
        if (!$request->ajax()) {
            return redirect()->route('roles.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json([
                'result'=>'success',
                'action'=>'store',
                'message'=>_lang('Saved Successfully'),
                'data'=>$role,
                'table' => '#roles_table'
            ]);
        }
    }    
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $role = Role::findOrFail($id);

        $view = $request->ajax()  ? 'backend.accounting.staff.role.modal.view'  : 'backend.accounting.staff.role.view';
        return view($view,compact('role','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $role = Role::findOrFail($id);
        $view = $request->ajax()  ? 'backend.accounting.staff.role.modal.edit'  : 'backend.accounting.staff.role.edit';
    
        return view($view, compact('role', 'id'));
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
        $validator = $this->validateRole($request);
    
        if ($validator->fails()) {
            return $this->handleValidationFailure($request, $validator, 'roles.edit', $id);
        }
    
        $role = Role::find($id);
        
        if (!$role) {
            return $this->handleRoleNotFound($request);
        }
    
        $role = $this->createOrUpdateRole($role, $request);
    
        if (!$request->ajax()) {
            return redirect()->route('roles.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json([
                'result'=>'success',
                'action'=>'update',
                'message'=>_lang('Updated Successfully'),
                'data'=>$role,
                'table' => '#roles_table'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $companyId = company_id();
        $role = Role::where('id', $id)->where('company_id', $companyId)->first();
    
        if ($role) {
        $role->delete();
            return redirect()->route('roles.index')->with('success', _lang('Deleted Successfully'));
        }
    
        return redirect()->route('roles.index')->with('error', _lang('Role not found.'));
    }

    private function validateRole(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'description' => '',
        ]);
    }

    private function handleValidationFailure(Request $request, $validator, $route, $id = null)
    {
        if ($request->ajax()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        } else {
            return redirect()->route($route, $id)
                            ->withErrors($validator)
                            ->withInput();
        }
    }

    private function handleRoleNotFound(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(['result' => 'error', 'message' => [_lang('Role not found.')]]);
        } else {
            return redirect()->route('roles.index')->with('error', _lang('Role not found.'));
        }
    }

    private function createOrUpdateRole(Role $role, Request $request)
    {
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->company_id = company_id();
        
        $role->save();

        return $role;
    }

}