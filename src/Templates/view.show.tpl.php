@extends('[[custom_master]]')

@section('content')



<h2 class="page-header">[[model_uc]]</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        View [[model_uc]]
    </div>

    <div class="panel-body">

        {!! Form::model($model, ['url' => '[[route_path]]', 'class' => 'form-horizontal']) !!}

        [[foreach:columns]]
        
        <div class="form-group">
            {!! Form::label('[[i.name]]', '[[i.display]]:', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                [[if:i.type=='id']]
                    {!! Form::text('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='text']]
                    {!! Form::text('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='number']]
                    {!! Form::number('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='related']]
                    {!! Form::text('[[i.name]]', $model->[[i.relatedName]]->name, ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='date']]
                    {!! Form::date('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='textarea']]
                    {!! Form::textarea('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]
                [[if:i.type=='unknown']]
                    {!! Form::text('[[i.name]]', $model->[[i.name]], ['class' => 'form-control', 'readonly' => 'readonly'])!!}
                [[endif]]

            </div>
        </div>
        
        [[endforeach]]

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/[[route_path]]') }}">
                    <i class="glyphicon glyphicon-chevron-left"></i> Back
                </a>
            </div>
        </div>

        {!! Form::close() !!}

    </div>
</div>







@endsection