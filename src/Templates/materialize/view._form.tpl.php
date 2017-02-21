<!-- <div class="form-group {{ $errors->has('[[i.name]]') ? 'has-error' : ''}}"> -->


[[foreach:columns]]
<div class="row">
    <div class="input-field col s12">
        [[if:i.type=='id']]
        {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]', 'readonly' => 'readonly']) !!}
        [[endif]]
        [[if:i.type=='text']]
        {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]' ]) !!}
        [[endif]]
        [[if:i.type=='number']]
        {!! Form::number('[[i.name]]', null, ['id' => '[[i.name]]' ]) !!}
        [[endif]]
        [[if:i.type=='related']]
        {!! Form::label('[[i.name]]', '[[i.display]]:' ,['class'=>'active']) !!}
        {!! Form::select('[[i.name]]', $[[model_singular]]->[[i.relatedName]]List ) !!}
        @section('scripts')

        @parent
        <script type="text/javascript">
          $(document).ready(function () {
            $('#[[i.name]]').material_select();
            //TODO Add select to the correct one
          });
        </script>
        @endsection
        [[endif]]
        [[if:i.type=='date']]
        {!! Form::date('[[i.name]]', null, ['id' => '[[i.name]]', 'class'=>'datepicker' ]) !!}
        [[endif]]

        [[if:i.type=='datetime']]
        {!! Form::date('[[i.name]]', null, ['id' => '[[i.name]]' ]) !!}
        [[endif]]

        [[if:i.type=='textarea']]
        {!! Form::textarea('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'materialize-textarea' ]) !!}
        [[endif]]
        [[if:i.type=='unknown']]
        {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]' ]) !!}
        [[endif]]

        [[if:i.type!='related']]
        {!! Form::label('[[i.name]]', '[[i.display]]:') !!}
        [[endif]]


        @if($errors->has('[[i.name]]'))
        {!! Form::label('error-[[i.name]]', $errors->first('[[i.name]]'), ['class' => 'control-label has-error']) !!}
        @endif
    </div>
</div>
[[endforeach]]
