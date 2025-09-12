<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use App\Company;
use App\Role;
use App\Package;
use App\AccessControl;
use Validator;
use Hash;
use Image;
use DB;

class UserController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_type = 'user')
    {
		if (!in_array($user_type, ['user', 'admin'])) {
		   abort(404);
		}
		$title = $user_type == 'user' ? _lang('User List') : _lang('Admin List');
        $users = User::where("user_type",$user_type)
                     ->orderBy("id","desc")->get();
        return view('backend.user.list',compact('users','title'));
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		$view = $request->ajax() ? 'backend.user.modal.create' : 'backend.user.create';

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
		$package = Package::findOrFail($request->package_id);

		$validator = Validator::make($request->all(), $this->getValidationRules($package->type));

		if ($validator->fails()) {
			if ($request->ajax()) {
				return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
			} else {
				return redirect('users/create')->withErrors($validator)->withInput();
			}
		}

		DB::beginTransaction();

		// Create Company
		$company = $this->createCompany($request, $package);

		// Create Role
		$role = $this->createRole($company->id);

		// Create User 
		$user = $this->createUser($request, $role->id, $company->id);

		DB::commit();


		if (!$request->ajax()) {
			return redirect('users')->with('success', _lang('Saved Sucessfully'));
		} else {
			return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Sucessfully'), 'data' => $user]);
		}
	}

	private function getValidationRules($packageType)
	{
		$rules = [
				'business_name' => 'required|max:191',
				'name' => 'required|max:191',
				'email' => 'required|email|unique:users|max:191',
				'password' => 'required|max:20|min:6|confirmed',
				'status' => 'required',
				'package_id' => 'required',
				'profile_picture' => 'nullable|image|max:5120',
		];
			
		if ($packageType !== 'free') {
			$rules['membership_type'] = 'required';
			$rules['package_type'] = 'required';
		}

		return $rules;
			}

	private function createCompany(Request $request, Package $package)
	{
		$company = new Company();
		$company->business_name = $request->business_name;
		$company->status = $request->status;
		$company->package_id = $request->package_id;

		if ($package->type == 'free') {
		$company->package_type = 'Free';
		$company->membership_type = 'Free';
		$company->valid_to = '3022-06-20';
		$company->websites_limit = $package->websites_limit;
		$company->recurring_transaction = 'No';
		$company->online_payment = 'No';
		} else {
		$company->package_type = $request->package_type;
		$company->membership_type = $request->membership_type;
			$company->valid_to = $company->package_type == 'monthly'
				? now()->addMonth()->format('Y-m-d')
				: now()->addYear()->format('Y-m-d');
			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
			$company->online_payment = unserialize($package->online_payment)[$company->package_type];
		}

		$company->save();
		return $company;
		}

	private function createRole($companyId)
	{
        $role = new Role();
        $role->name = 'manager';
		$role->description = 'For managing the websites';
		$role->company_id = $companyId;
        $role->save();

		$permission = new AccessControl();
		$permission->role_id = $role->id;
		$permission->permission = 'websites.create';
		$permission->save();

		return $role;
	}

	private function createUser(Request $request, $roleId, $companyId)
	{
        $user = new User();
	    $user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->email_verified_at = now();
		$user->password = Hash::make($request->password);
		$user->user_type = 'user';
		$user->role_id = $roleId;
		$user->status = $request->input('status');
	    $user->profile_picture = 'default.png';
		$user->company_id = $companyId;

		if ($request->hasFile('profile_picture')) {
           $image = $request->file('profile_picture');
			$file_name = "profile_" . time() . '.' . $image->getClientOriginalExtension();
			Image::make($image)->crop(300, 300)->save(base_path('public/uploads/profile/') . $file_name);
		   $user->profile_picture = $file_name;
		}

        $user->save();
		return $user;
		}
        

	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(Request $request, $id)
    {
		$user = User::findOrFail($id);
		$view = $request->ajax() ? 'backend.user.modal.view' : 'backend.user.view';

		return view($view, compact('user', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
		$user = User::findOrFail($id);
		$view = $request->ajax() ? 'backend.user.modal.edit' : 'backend.user.edit';

		return view($view, compact('user', 'id'));
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
		// Check for demo mode restriction
		if (env('DEMO_MODE') && User::find($id)->email === "demo@larabuilder.com") {
			$errorMessage = _lang('DEMO MODE NOT ALLOWED');
			return $request->ajax()
				? response()->json(['result' => 'error', 'action' => 'update', 'message' => $errorMessage])
				: redirect()->back()->with('error', $errorMessage);
            }

		$user = User::findOrFail($id);
		$package = Package::findOrFail($request->package_id);

		// Validation rules based on package type
		$commonRules = [
				'business_name' => 'required|max:191',
				'name' => 'required|max:191',
			'email' => ['required', Rule::unique('users')->ignore($id)],
				'password' => 'nullable|max:20|min:6|confirmed',
				'status' => 'required',
				'package_id' => 'required',
				'profile_picture' => 'nullable|image|max:5120',
		];

		$extraRules = ($package->type !== 'free')
			? ['membership_type' => 'required', 'package_type' => 'required']
			: [];

		$validator = Validator::make($request->all(), array_merge($commonRules, $extraRules));
			
		// Handle validation failure
			if ($validator->fails()) {
			$errorMessages = $validator->errors()->all();
			return $request->ajax()
				? response()->json(['result' => 'error', 'message' => $errorMessages])
				: redirect()->route('users.edit', $id)
								->withErrors($validator)
								->withInput();
			}

			DB::beginTransaction();

		try {
			// Update Company Details
			$company = Company::findOrFail($user->company_id);
			$previousPackage = $company->package_id;

			$company->business_name = $request->business_name;
			$company->status = $request->status;
			$company->package_id = $request->package_id;

			// Handle package type specific updates
			if ($package->type === 'free') {
			$company->package_type = 'Free';
			$company->membership_type = 'Free';

				if ($previousPackage !== $request->package_id) {
					$this->applyFreePackageDetails($company);
				}
			} else {
				$company->package_type = $request->package_type;
				$company->membership_type = $request->membership_type;

				$this->applyPaidPackageDetails($company);
			}

			$company->save();

			// Update User Details
			$user->name = $request->name;
			$user->email = $request->email;

			if ($request->password) {
				$user->password = Hash::make($request->password);
			}

			// Handle profile picture upload
			if ($request->hasFile('profile_picture')) {
				$this->uploadProfilePicture($request->file('profile_picture'), $user);
			}

			$user->status = $request->status;
			$user->save();

			DB::commit();

			// Success Response
			return $request->ajax()
				? response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $user])
				: redirect('users')->with('success', _lang('Updated Successfully'));
		} catch (\Exception $e) {
			DB::rollBack();
			return $request->ajax()
				? response()->json(['result' => 'error', 'message' => $e->getMessage()])
				: redirect()->back()->with('error', $e->getMessage());
			}			
		}

	/**
	 * Apply details for free package
	 *
	 * @param Company $company
	 */
	private function applyFreePackageDetails(Company $company)
	{
		$company->valid_to = '3022-06-20';
		$company->websites_limit = $company->package->websites_limit;
		$company->recurring_transaction = 'NO';
		$company->online_payment = 'NO';
	}

	/**
	 * Apply details for paid package
	 *
	 * @param Company $company
	 */
	private function applyPaidPackageDetails(Company $company)
	{
		$company->valid_to = ($company->package_type === 'monthly')
			? now()->addMonth()->format('Y-m-d')
			: now()->addYear()->format('Y-m-d');
			
			$company->websites_limit = unserialize($company->package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($company->package->recurring_transaction)[$company->package_type];
			$company->online_payment = unserialize($company->package->online_payment)[$company->package_type];
	}

	/**
	 * Handle profile picture upload and cropping
	 *
	 * @param UploadedFile $image
	 * @param User $user
	 */
	private function uploadProfilePicture($image, User $user)
	{
		$fileName = "profile_" . time() . '.' . $image->getClientOriginalExtension();
		Image::make($image)->crop(300, 300)->save(public_path('uploads/profile/') . $fileName);
		$user->profile_picture = $fileName;

        $user->save();		

		DB::commit();
		

		if(! $request->ajax()){
           return redirect('users')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$user]);
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
        if(env('DEMO_MODE') == true  && User::find($id)->email == "demo@larabuilder.com"){
            return redirect('users')->with('error', _lang('DEMO MODE NOT ALLOWED'));
        }
        DB::beginTransaction();
		
		try {
			$user = User::findOrFail($id);

			$company = Company::findOrFail($user->company_id);
        $company->delete();
	    
			User::where('company_id', $user->company_id)->delete();
		
		DB::commit();
		
			return redirect('users')->with('success', _lang('Removed Successfully'));
		} catch (\Exception $e) {
			DB::rollBack();

			return redirect('users')->with('error', _lang('Something went wrong. Please try again.'));
		}
	}
}
