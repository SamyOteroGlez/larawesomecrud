@extends('[[custom_master]]')

@section('content')

<div class="btn-group" role="group">
    <a href="{{url('[[route_path]]/')}}" class="btn btn-primary btn-sm" role="button">
        <i class="material-icons left">list</i>
        List of {{ ucfirst('[[model_plural]]') }}
    </a>
    <a href="{{url('[[route_path]]/create')}}" class="btn btn-primary btn-sm" role="button">
        <i class="material-icons left">add</i>
        Create {{ ucfirst('[[model_uc]]') }}
    </a>
    <a href="{{route('[[route_path]].edit', [$[[model_singular]]->id])}}" class="btn btn-warning btn-sm" role="button">
        <i class="material-icons left">mode_edit</i>
        Update {{ ucfirst('[[model_uc]]') }}
    </a>
    <a href="{{route('[[route_path]].destroy', [$[[model_singular]]->id])}}" class="btn btn-danger btn-sm" role="button"
       onclick="return doDelete({!! $[[model_singular]]->id !!})">
        <i class="material-icons left">delete</i>
        Delete {{ ucfirst('[[model_uc]]') }}
    </a>
</div>
<div class="clearfix"></div>

<div class="form-panel">
    <h3>View {{ ucfirst('[[model_singular]]') }}</h3>
    {!! Form::model($[[model_singular]], ['url' => '[[route_path]]']) !!}

    [[foreach:columns]]
    <div class="row">
        <div class="input-field col s12">
            [[if:i.type=='id']]
            {!! Form::text('[[i.name]]', $[[model_singular]]->[[i.name]], [ 'readonly' => 'readonly'])!!}
            [[endif]]

            [[if:i.type=='text']]
            {!! Form::text('[[i.name]]', $[[model_singular]]->[[i.name]], [ 'readonly' => 'readonly'])!!}
            [[endif]]

            [[if:i.type=='number']]
            {!! Form::number('[[i.name]]', $[[model_singular]]->[[i.name]], [ 'readonly' => 'readonly'])!!}
            [[endif]]

            [[if:i.type=='related']]
            {!! Form::text('[[i.name]]', $[[model_singular]]->[[i.displayMethodName]]->[[i.displayRelatedField]], [
            'readonly' => 'readonly'])!!}

            [[endif]]

            [[if:i.type=='date']]
            {!! Form::date('[[i.name]]', $[[model_singular]]->[[i.name]], [ 'readonly' => 'readonly'])!!}
            [[endif]]

            [[if:i.type=='textarea']]
            {!! Form::textarea('[[i.name]]', $[[model_singular]]->[[i.name]], ['class' => 'materialize-textarea',
            'readonly' => 'readonly'])!!}
            [[endif]]

            [[if:i.type=='unknown']]
            {!! Form::text('[[i.name]]', $[[model_singular]]->[[i.name]], [ 'readonly' => 'readonly'])!!}
            [[endif]]

            {!! Form::label('[[i.name]]', '[[i.display]]:',['class'=>'active'] ) !!}
        </div>
    </div>
    [[endforeach]]

    {!! Form::close() !!}

</div>
@endsection
@section('scripts')

<script type="text/javascript">
  function doDelete(id) {
    if (confirm('Do you really want to delete this record?')) {
      $.ajax({
        url: '{{ url(' / [[route_path]]') }}/' +id,
        type: 'DELETE',
        success: function () {
          window.location.reload();
        },
        error: function () {
          alert('Woops! Something went wrong. Internal error.');
        }
      });
    }
    return false;
  }
</script>

@endsection