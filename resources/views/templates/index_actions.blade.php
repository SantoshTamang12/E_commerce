@if(!isset($hideShow))
<a href="{{route($route.'show',$id??$item->id)}}"
   class="btn btn-sm btn-clean btn-icon btn-hover-primary"><i
        class="fa fa-eye"></i></a>
@endif
@if(!isset($showModal))
    <a data-id="{{$id}}" href="{{route($route.'edit',$id??$item->id)}}"
       class="btn btn-sm btn-clean btn-icon btn-hover-info btn-edit"><i
            class="fa fa-pencil-alt"></i></a>
@else
    <a data-id="{{$id}}" href="{{route($route.'edit',$id??$item->id)}}" id="modal-edit" data-toggle="modal"
       data-target="#edit-modal" data-whatever="@mdo"
       class="btn btn-sm btn-clean btn-icon btn-hover-info btn-edit"><i
            class="fa fa-pencil-alt"></i></a>
@endif

@if(!isset($hideDelete))
<form class="d-inline" action="{{ route($route.'destroy',$id??$item->id) }}"
      method="POST" >
    @csrf
    @method('DELETE')
    <button data-id="{{ $id }}" class="btn-delete btn btn-sm btn-clean btn-icon btn-hover-danger"><i
            class="fa fa-trash"></i></button>
</form>
@endif

@foreach($actions??[] as $action)
    {!! $action !!}
@endforeach
