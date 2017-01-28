<?php

namespace [[appns]]Http\Controllers;

use Illuminate\Http\Request;

[[if:false !== formrequest]]
use [[appns]]Http\Requests\[[formrequest]];
[[endif]]
use [[appns]]Http\Controllers\Controller;

use [[appns]][[model_uc]];

use DB;

class [[controller_name]]Controller extends Controller
{
    private $model;

    public function __construct([[model_uc]] $model)
    {
        //$this->middleware('auth');
		$this->model = $model;
    }

    public function index()
	{
		$[[model_singular]] = $this->model->paginate();

	    return view('[[view_folder]].index', [
			'model' => $[[model_singular]]
		]);
	}

	public function create()
	{
		$[[model_singular]] = $this->model->newInstance();

	    return view('[[view_folder]].create', [
			'model' => $[[model_singular]]
	    ]);
	}

	public function store([[formrequest]] $request)
	{
		$[[model_singular]] = $this->model->newInstance();
		$[[model_singular]]->fill($request->all());
		$[[model_singular]]->save();

		return redirect()->route('[[model_plural]].index', [])
			->withSuccess('[[model_uc]] created!');
	}

	public function show($id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);

	    return view('[[view_folder]].show', [
			'model' => $[[model_singular]]
		]);
	}

	public function edit($id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);

	    return view('[[view_folder]].edit', [
	        'model' => $[[model_singular]]
	    ]);
	}

	public function update([[formrequest]] $request, $id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);
		$[[model_singular]]->fill($request->all());
	    $[[model_singular]]->save();

	    return redirect()->route('[[model_plural]].index', [])
			->withSuccess('[[model_uc]] updated!');
	}

	public function destroy($id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);
		$[[model_singular]]->delete();

//		return redirect()->route('[[model_plural]].index', [])
//			->withSuccess('[[model_uc]] deleted!');
	}
}
