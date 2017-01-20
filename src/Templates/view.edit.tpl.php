@extends('[[custom_master]]')

@section('content')


<h2 class="page-header">[[model_uc]]</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        Update [[model_uc]]
    </div>

    <div class="panel-body">

        {!! Form::model($model, ['url' => '[[route_path]]/' . $model->id , 'class' => 'form-horizontal']) !!}

        {{ csrf_field() }}

        <input type="hidden" name="_method" value="PATCH">

        @include('[[model_plural]]._form')

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-plus"></i> Update
                </button>
                <a class="btn btn-default" href="{{ url('/[[route_path]]') }}">
                    <i class="glyphicon glyphicon-chevron-left"></i> Back
                </a>
            </div>
        </div>

        {!! Form::close() !!}

    </div>
</div>

@endsection