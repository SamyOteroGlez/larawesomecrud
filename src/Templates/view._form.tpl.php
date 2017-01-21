
[[foreach:columns]]
    <div class="form-group {{ $errors->has('[[i.name]]') ? 'has-error' : ''}}">
        {!! Form::label('[[i.name]]', '[[i.display]]:', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
        [[if:i.type=='id']]
            {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
        [[endif]]
        [[if:i.type=='text']]
            {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control']) !!}
        [[endif]]
        [[if:i.type=='number']]
            {!! Form::number('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control']) !!}
        [[endif]]
        [[if:i.type=='related']]
            {!! Form::select('[[i.name]]', $model->[[i.relatedName]]List, null, ['placeholder' => 'Please select:', 'class' => 'form-control']) !!}
        [[endif]]
        [[if:i.type=='date']]
            {!! Form::date('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control']) !!}
        [[endif]]
        [[if:i.type=='textarea']]
            {!! Form::textarea('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control']) !!}
        [[endif]]
        [[if:i.type=='unknown']]
            {!! Form::text('[[i.name]]', null, ['id' => '[[i.name]]', 'class' => 'form-control']) !!}
        [[endif]]
            @if($errors->has('[[i.name]]'))
            {!! Form::label('error-[[i.name]]', $errors->first('[[i.name]]'), ['class' => 'control-label has-error']) !!}
            @endif
        </div>
    </div>
[[endforeach]]