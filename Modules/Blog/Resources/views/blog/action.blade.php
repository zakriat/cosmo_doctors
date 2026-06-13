
<?php
    $auth_user= authSession();
?>
{{-- {{ html()->form('DELETE', route('backend.blog.destroy', $blog->id))->attribute('data--submit', 'blog'.$blog->id)->open()}} --}}
<div class="d-flex justify-content-end align-items-center">
    @if(!$blog->trashed()) 
        @if($auth_user->can('blog edit'))
        <a class="btn text-primary p-0 fs-5" href="{{ route('backend.blog.create',['id' => $blog->id]) }}" title="{{ __('messages.edit') }} "data-bs-toggle="tooltip"><i class="ph ph-pencil-simple-line"></i></a>
        @endif 

        @if($auth_user->can('blog delete'))
        <a href="{{ route('backend.blog.destroy', $blog->id) }}" 
               id="delete-blog-{{ $blog->id }}" class="btn text-danger p-0 fs-5" 
               data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip"
data-confirm="{{ __('messages.are_you_sure?', ['form' => e($blog->title) ?? __('Unknown'), 'module' => __('messages.blogs')]) }}"><i class="ph ph-trash"></i>
        </a>
         @endif 
    @endif
     @if(auth()->user()->hasAnyRole(['admin']) && $blog->trashed()) 
        <a class="btn text-primary p-0 fs-5 restore-tax"
            data-confirm-message="{{ __('messages.are_you_sure_restore') }}"
            data-success-message="{{ __('messages.restore_form') }}"
            href="{{ route('backend.blog.action',['id' => $blog->id, 'type' => 'restore']) }}" title="{{ __('messages.restore') }} "data-bs-toggle="tooltip">
            <i class="ph ph-arrow-clockwise align-middle"></i>
        </a>

        <a class="btn text-primary p-0 fs-5 restore-tax"
            data-confirm-message="{{ __('messages.are_you_sure_delete_permanently') }}"
            data-success-message="{{ __('messages.permanently_deleted') }}"
            href="{{ route('backend.blog.action',['id' => $blog->id, 'type' => 'forcedelete']) }}" title="{{ __('messages.permanent_delete') }} "data-bs-toggle="tooltip">
            <i class="ph ph-trash align-middle"></i>
        </a>
    @endif 
</div>
{{ html()->form()->close() }}