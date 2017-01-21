<?php

namespace [[appns]]Http\Controllers;

use Illuminate\Http\Request;

use [[appns]]Http\Requests\[[formrequest]];
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

    public function index(Request $request)
	{
		$[[model_singular]] = $this->model->paginate();

	    return view('[[view_folder]].index', [
			'model' => $[[model_singular]]
		]);
	}

	public function create([[formrequest]] $request)
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

	public function show(Request $request, $id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);

	    return view('[[view_folder]].show', [
			'model' => $[[model_singular]]
		]);
	}

	public function edit([[formrequest]] $request, $id)
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

	public function destroy([[formrequest]] $request, $id)
	{
		$[[model_singular]] = $this->model->findOrFail($id);
		$[[model_singular]]->delete();

//		return redirect()->route('[[model_plural]].index', [])
//			->withSuccess('[[model_uc]] deleted!');
	}

	public function grid(Request $request)
	{
		if($request->ajax()) {
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
}