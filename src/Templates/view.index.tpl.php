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
                          <a href="{{route('students.show', [$obj->id])}}"
                             class="btn btn-info btn-sm" role="button">
                              Details
                          </a>
                          <a href="{{route('students.edit', [$obj->id])}}"
                             class="btn btn-warning btn-sm" role="button">
                              Update
                          </a>
                          <a href="{{route('students.destroy', [$obj->id])}}"
                             class="btn btn-danger btn-sm" role="button"
                             onclick="return doDelete({!! $obj->id !!})">
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
        <div>
            <a href="{{url('[[route_path]]/create')}}" class="btn btn-primary" role="button">
                Add [[model_singular]]
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