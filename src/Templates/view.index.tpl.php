@extends('[[custom_master]]')

@section('content')

<h2 class="page-header">{{ ucfirst('[[model_plural]]') }}</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        List of {{ ucfirst('[[model_plural]]') }}
    </div>

    <div class="panel-body">
        @if(!$model->isEmpty())
        <div class="">
            <table class="table table-striped" id="tbl-datatable">
              <thead>
                <tr>
                [[foreach:columns]]
                    <th>[[i.display]]</th>
                [[endforeach]]
                    <th style="width:200px"></th>
                </tr>
              </thead>
              <tbody>
              @foreach($model as $obj)
                <tr>
                    [[foreach:columns]]
                    <td>{{ $obj->[[i.name]] }}</td>
                    [[endforeach]]
                    <td>
                        <div class="btn-group" role="group">
                          <a href="{{route('[[route_path]].show', [$obj->id])}}"
                             class="btn btn-info btn-sm" role="button">
                              <i class="glyphicon glyphicon-zoom-in"></i>
                              Details
                          </a>
                          <a href="{{route('[[route_path]].edit', [$obj->id])}}"
                             class="btn btn-warning btn-sm" role="button">
                              <i class="glyphicon glyphicon-pencil"></i>
                              Update
                          </a>
                          <a href="{{route('[[route_path]].destroy', [$obj->id])}}"
                             class="btn btn-danger btn-sm" role="button"
                             onclick="return doDelete({!! $obj->id !!})">
                              <i class="glyphicon glyphicon-remove"></i>
                              Delete
                          </a>
                        </div>
                    </td>
                </tr>
              @endforeach
              </tbody>
            </table>
            <div>
                {!! $model->render() !!}
            </div>
        </div>
        @else
            No {{ ucfirst('[[model_plural]]') }} found.
        @endif
        <br/>
        <div>
            <a href="{{url('[[route_path]]/create')}}" class="btn btn-primary btn-sm" role="button">
                <i class="glyphicon glyphicon-plus"></i>
                Create {{ ucfirst('[[model_uc]]') }}
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')

    <script type="text/javascript">
        var theGrid = null;
        $(document).ready(function(){
            table = $('#tbl-datatable').DataTable({
                "processing": true,
                "ordering": true,
                "responsive": true,
                "paging": false
            });
        });

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