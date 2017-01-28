@extends('[[custom_master]]')

@section('content')

<h2 class="page-header">[[model_uc]]</h2>

<div class="btn-group" role="group">
    <a href="{{url('[[route_path]]/')}}" class="btn btn-primary btn-sm" role="button">
        <i class="glyphicon glyphicon-th-list"></i>
        List of {{ ucfirst('[[model_plural]]') }}
    </a>
    <a href="{{url('[[route_path]]/create')}}" class="btn btn-primary btn-sm" role="button">
        <i class="glyphicon glyphicon-plus"></i>
        Create {{ ucfirst('[[model_uc]]') }}
    </a>
    <a href="{{route('[[route_path]].edit', [$model->id])}}" class="btn btn-warning btn-sm" role="button">
        <i class="glyphicon glyphicon-pencil"></i>
        Update {{ ucfirst('[[model_uc]]') }}
    </a>
    <a href="{{route('[[route_path]].destroy', [$model->id])}}" class="btn btn-danger btn-sm" role="button"
       onclick="return doDelete({!! $model->id !!})">
        <i class="glyphicon glyphicon-remove"></i>
        Delete {{ ucfirst('[[model_uc]]') }}
    </a>
</div>
<div class="clearfix"><div/>
<br/>

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

        {!! Form::close() !!}

    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    function doDelete(id) {
        if(confirm('Do you really want to delete this record?')) {
            $.ajax({
                url: '{{ url('/[[route_path]]') }}/' + id,
                type: 'DELETE',
                success: function() {
                    window.location.reload();
                },
                error: function() {
                    alert('Woops! Something went wrong. Internal error.');
                }
            });
        }
        return false;
    }
</script>

@endsection