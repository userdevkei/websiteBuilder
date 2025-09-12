<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tax;
use Validator;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxs = Tax::where("company_id",company_id())
            ->latest("id")->get();
        return view('backend.accounting.tax.list',compact('taxs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $viewPath = $request->ajax() ? 'backend.accounting.tax.modal.create' : 'backend.accounting.tax.create';
        return view($viewPath);
		}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
        $validator = $this->validateRequest($request);
		
		if ($validator->fails()) {
            return $this->handleValidationError($request, $validator);
			}			

        $tax = $this->createOrUpdateTax(new Tax(), $request);

        return $this->handleResponse($request, 'store', $tax, _lang('Saved Sucessfully'));
		}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $tax = Tax::where('id', $id)->where('company_id', company_id())->firstOrFail();
        $viewPath = $request->ajax() ? 'backend.accounting.tax.modal.view' : 'backend.accounting.tax.view';
        
        return view($viewPath, compact('tax','id'));
		} 
        

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $tax = Tax::where('id', $id)
            ->where('company_id', company_id())
            ->firstOrFail();

        $viewPath = $request->ajax() ? 'backend.accounting.tax.modal.edit' : 'backend.accounting.tax.edit';

        return view($viewPath, compact('tax','id'));
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
        $validator = $this->validateRequest($request);
		
		if ($validator->fails()) {
            return $this->handleValidationError($request, $validator, $id);
			}			
		
        $tax = Tax::where("id", $id)->where("company_id", company_id())->first();
        $tax = $this->createOrUpdateTax($tax, $request);

        return $this->handleResponse($request, 'update', $tax, _lang('Updated Sucessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tax = Tax::where('id', $id)
            ->where('company_id', company_id())
            ->firstOrFail();

        $tax->delete();

        return redirect('taxs')->with('success', _lang('Information has been deleted successfully'));
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'tax_name' => 'required|max:30',
            'rate' => 'required|numeric',
            'type' => 'required'
        ]);
    }

    private function handleValidationError(Request $request, $validator, $id = null)
    {
        return $request->ajax()
            ? response()->json(['result' => 'error', 'message' => $validator->errors()->all()])
            : redirect($id ? route('taxs.edit', $id) : 'taxs/create')->withErrors($validator)->withInput();
    }

    private function createOrUpdateTax($tax, Request $request)
    {
        $tax->tax_name = $request->input('tax_name');
        $tax->rate = $request->input('rate');
        $tax->type = $request->input('type');
        $tax->company_id = company_id();

        $tax->save();

        $tax->rate = $tax->type === "percent"
            ? currency() . " " . decimalPlace($tax->rate) . "%"
            : currency() . " " . decimalPlace($tax->rate);

        return $tax;
    }

    private function handleResponse(Request $request, $action, $tax, $message)
    {
        return $request->ajax()
            ? response()->json(['result' => 'success', 'action' => $action, 'message' => $message, 'data' => $tax])
            : redirect('taxs')->with('success', $message);
    }


}
