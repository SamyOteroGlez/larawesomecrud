@extends('[[custom_master]]')

@section('content')

<h2 class="page-header">[[model_uc]]</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        Create [[model_uc]]
    </div>

    <div class="panel-body">

        {!! Form::model($model, ['url' => '[[route_path]]', 'class' => 'form-horizontal']) !!}

        {{ csrf_field() }}

        @include('[[model_plural]]._form')

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-plus"></i> Save
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