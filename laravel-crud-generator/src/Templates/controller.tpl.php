<?php

namespace [[appns]]Http\Controllers;

use Illuminate\Http\Request;

use [[appns]]Http\Requests;
use [[appns]]Http\Controllers\Controller;

use [[appns]][[model_uc]];

use DB;

class [[controller_name]]Controller extends Controller
{
    //
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request)
	{
	    return view('[[view_folder]].index', []);
	}

	public function create(Request $request)
	{
		$model = new [[model_uc]]();

	    return view('[[view_folder]].create', [
			'model' => $model
	    ]);
	}

	public function store(Request $request)
	{
		$[[model_singular]] = new [[model_uc]]();
		$[[model_singular]]->fill($request->all());
		$[[model_singular]]->save();

		return redirect('/[[route_path]]');
	}

	public function show(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);

	    return view('[[view_folder]].show', [
			'model' => $[[model_singular]]
		]);
	}

	public function edit(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);

	    return view('[[view_folder]].edit', [
	        'model' => $[[model_singular]]
	    ]);
	}

	public function update(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);
		$[[model_singular]]->fill($request->all());
	    $[[model_singular]]->save();

	    return redirect('/[[route_path]]');
	}

	public function destroy(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);

		$[[model_singular]]->delete();

		return "OK";
	}

	public function grid(Request $request)
	{
		$len = $_GET['length'];
		$start = $_GET['start'];

		$select = "SELECT *,1,2 ";
		$presql = " FROM [[prefix]][[tablename]] a ";

		if($_GET['search']['value']) {
			$presql .= " WHERE [[first_column_nonid]] LIKE '%".$_GET['search']['value']."%' ";
		}

		$presql .= "  ";

		$sql = $select.$presql." LIMIT ".$start.",".$len;


		$qcount = DB::select("SELECT COUNT(a.id) c".$presql);
		//print_r($qcount);
		$count = $qcount[0]->c;

		$results = DB::select($sql);
		$ret = [];

		foreach ($results as $row) {
			$r = [];

			foreach ($row as $value) {
				$r[] = $value;
			}
			$ret[] = $r;
		}

		$ret['data'] = $ret;
		$ret['recordsTotal'] = $count;
		$ret['iTotalDisplayRecords'] = $count;

		$ret['recordsFiltered'] = count($ret);
		$ret['draw'] = $_GET['draw'];

		echo json_encode($ret);
	}
}