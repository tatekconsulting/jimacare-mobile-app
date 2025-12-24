<?php

namespace App\Http\Controllers\Admin;

use App\Events\JobPostedEvent;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Day;
use App\Models\Language;
use App\Models\TimeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContractController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$contracts = Contract::all();
		return view('admin.pages.contract.index', compact('contracts') );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
    public function show($id)
    {
            return redirect()->route('dashboard.contract.edit', ['contract' => $id]);
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Contract $contract)
{
        $role           = $contract->role ?? $contract->type ?? null;
        $types          = $role ? $role->types : collect([]);
        $languages      = Language::all();
        $experiences    = $role ? $role->experiences : collect([]);
        $educations     = $role ? $role->educations : collect([]);
        $interests      = $role ? $role->interests : collect([]);
        $skills         = $role ? $role->skills : collect([]);
        $days           = Day::all();
        $time_types     = TimeType::all();

        return view('admin.pages.contract.edit', compact('contract', 'role', 'types', 'languages', 'experiences', 'educations', 'interests', 'skills', 'days', 'time_types'));
}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Contract $contract)
	{
		$role = $contract->role ?? $contract->type ?? null;

		$valid = [
			'title'         => 'bail|required|string',
			'start_type'    => 'bail|required|string|in:immediately,not-sure,specific-date',
			'start_date'    => 'bail|sometimes|date',
			'end_type'      => 'bail|required|string|in:fixed-period,on-going',
			'end_date'      => 'bail|sometimes|date',
			'start_time'    => 'bail|required|date_format:H:i',
			'end_time'      => 'bail|required|date_format:H:i',
			'desc'          => 'bail|required|string|min:4|max:255',
		];

		if(($role->id ?? 0) == 3){
			$valid = array_merge($valid, [
				'type'  => 'bail|required|array',
				'type.*' => 'bail|required|numeric|exists:types,id',
			]);
		}elseif(($role->id ?? 0) == 4){
			$valid = array_merge($valid, [
				'drive'         => 'bail|required|string|in:yes,no'
			]);
		}elseif(($role->id ?? 0) == 5){
			$valid = array_merge($valid, [
				'how_often'     => 'bail|required|string|in:Daily,Twice a week,Weekly,Every other week,Once a month,One time clean,Other',
				'beds'          => 'bail|required|string|in:0 bedrooms,1 bedroom,2 bedrooms,3 bedrooms,4 bedrooms,5+ bedrooms,Studio',
				'baths'         => 'bail|required|string|in:1 bathroom,1 bathroom + 1 additional toilet,2 bathrooms,2 bathrooms + 1 additional toilet,3 bathrooms,4+ bathrooms',
				'rooms'         => 'bail|required|string|in:0,1,2,3,4+',
				'cleaning_type' => 'bail|required|string|in:Standard cleaning,Deep cleaning,Move-out cleaning',
			]);
		}

        if(in_array(($role->id ?? 0), [3,5])){
                $valid = array_merge($valid, [
                        'day'  => 'bail|nullable|array',
                        'day.*' => 'bail|nullable|numeric|exists:days,id'
                ]);
        }elseif(($role->id ?? 0) == 4){
                $valid = array_merge($valid, [
                        'availability'     => 'bail|nullable|array',
                        'availability.*'   => 'bail|nullable|array',
                        'availability.*.*' => 'bail|nullable|numeric|exists:time_types,id',
        
                        'interest'   => 'bail|nullable|array',
                        'interest.*' => 'bail|nullable|numeric|exists:interests,id',
                ]);
        }
        

            $valid = array_merge($valid, [
                    'language'   => 'bail|nullable|array',
                    'language.*' => 'bail|nullable|numeric|exists:languages,id',
                    'experience'       => 'bail|nullable|array',
                    'experience.*'     => 'bail|nullable|numeric|exists:experiences,id',
            ]);

		$request->validate($valid);

		$data = [
			'title'         => $request->title,
			'start_type'    => $request->start_type,
			'start_date'    => $request->start_date ?? null,
			'end_type'      => $request->end_type,
			'end_date'      => $request->end_date ?? null,
			'start_time'    => $request->start_time,
			'end_time'      => $request->end_time,
			'desc'          => $request->desc,
		];

		if(($role->id ?? 0) == 3){
			$data = array_merge($data, [
				'drive'         => ($request->drive == 'yes') ? true : false,
			]);
		} elseif(($role->id ?? 0) == 5){
			$data = array_merge($data, [
				'how_often'     => $request->how_often,
				'beds'          => $request->beds,
				'baths'         => $request->baths,
				'rooms'         => $request->rooms,
				'cleaning_type' => $request->cleaning_type,
			]);
		}

		$contract->update($data);

		if(($role->id ?? 0) == 3){
			$contract->types()->sync($request->type);
		}

        if( in_array(($role->id ?? 0), [3,5]) && $request->day ){
                $contract->days()->sync($request->day);
		}elseif(($role->id ?? 0) == 4){
			if ($request->has('availability')) {
				$contract->time_availables()->delete();
				foreach ($request->availability as $day => $avails) {
					foreach ($avails as $time){
						$contract->time_availables()->create([
							'day_id'        => $day,
							'type_id'       => $time
						]);
					}
				}
			}

			if ($request->interest) {
                 $contract->interests()->sync($request->interest);
			    
			}
		}

        if ($request->language) {
            $contract->languages()->sync($request->language);
        }
        if ($request->experience) {
            $contract->experiences()->sync($request->experience);
        }

		session()->flash('notice', 'Contract has been updated!');
		return redirect()->route('dashboard.contract.edit', ['contract' => $contract->id ]);
	}

	public function status(Request $request, Contract $contract)
	{
		$current_status = $contract->status;
		$contract->status = $request->status;
		$contract->save();
		if ($current_status == 'pending' && $request->status == 'active') {
			event(new JobPostedEvent($contract, 'service_provider'));
		}
		return "success";
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Contract $contract)
	{
		$contract->delete();
		session()->flash('notice', 'Contract has been removed!');
		return redirect()->route('dashboard.contract.index');
	}
}
