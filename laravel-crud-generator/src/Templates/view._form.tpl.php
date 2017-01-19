

[[foreach:columns]]
    [[if:i.type=='id']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-6">
            <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}" readonly="readonly">
        </div>
    </div>
    [[endif]]
    [[if:i.type=='text']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-6">
            <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}">
        </div>
    </div>
    [[endif]]
    [[if:i.type=='number']]
        <div class="form-group">
            <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
            <div class="col-sm-2">
                <input type="number" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}">
            </div>
        </div>
    [[endif]]
    [[if:i.type=='related']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-6">
            {!!
                Form::select('[[i.name]]', $model->[[i.relatedName]]List, null, ['placeholder' => 'Please select:', 'class' => 'form-control'])
            !!}
        </div>
    </div>
    [[endif]]
    [[if:i.type=='date']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-3">
            <input type="date" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}">
        </div>
    </div>
    [[endif]]
    [[if:i.type=='textarea']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-6">
            <textarea name="[[i.name]]" id="[[i.name]]" class="form-control" rows="6" cols="50"
                      value="{{$model['[[i.name]]'] or ''}}">
            </textarea>
        </div>
    </div>
    [[endif]]
    [[if:i.type=='unknown']]
    <div class="form-group">
        <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
        <div class="col-sm-6">
            <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}">
        </div>
    </div>
    [[endif]]
[[endforeach]]