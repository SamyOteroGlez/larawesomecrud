@extends('[[custom_master]]')

@section('content')

<h2 class="page-header">[[model_uc]]</h2>

<div class="btn-group" role="group">
    <a href="{{url('[[route_path]]/')}}" class="btn btn-primary btn-sm" role="button">
        <i class="glyphicon glyphicon-th-list"></i>
        List of {{ ucfirst('[[model_plural]]') }}
    </a>
</div>
<div class="clearfix"><div/>
<br/>

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
                    <i class="glyphicon glyphicon-ok"></i>
                    Save
                </button>
            </div>
        </div>

        {!! Form::close() !!}

    </div>
</div>

@endsection