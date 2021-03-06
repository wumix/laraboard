<div id="p-{{ $post->slug }}" class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title clearfix">
            <?php $delete_modal = htmlentities('<p>Are you sure you want to delete this post?</p>' . link_to_route('post.delete', 'Delete', $post->id, ['class' => 'btn btn-danger btn-sm'])); ?>
            @if($post->status != 'Deleted') @can('laraboard::post-delete', $post)<a tabindex="0" class="btn btn-primary btn-xs" role="button" data-placement="right" data-toggle="popover" data-trigger="focus" title="Delete Post?" data-html="true" data-content="<?php echo $delete_modal; ?>"><i class="fa fa-ban"></i> Delete</a>@endcan @endif
            @can('laraboard::reply-edit', $post)<a href="{{ route('post.edit', $post->id ) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Edit</a>@endcan
            @can('laraboard::thread-reply', $post)
                <button id="btn-quote-{{ $post->slug }}" class="btn btn-primary btn-xs"><i class="fa fa-quote-left"></i><span> Quote</span></button>
                <div id="quote-{{ $post->slug }}" class="hidden">{{ '> ' . str_replace("\n", "\n> ", $post->body) }}</div>
                @push('quotes')
                $('#btn-quote-{{ $post->slug }}').click(function(e) {
                    $('#reply-field').data('markdown').replaceSelection($('#quote-{{ $post->slug }}').text());
                });
                @endpush
            @endcan
            <small class="pull-right text-muted">{{ $post->created }}</small>
        </div>
    </div>
    <div class="panel-body thread-row">
        <div class="row">
            <div class="col-sm-2">
                <div class="visible-xs">
                    <div class="text-muted pull-right"><small>Posts: {{ $post->user->post_count }}</small></div>
                </div>

                <a href="{{ url(config('laraboard.user.route') . $post->user->slug) }}">{{ $post->user->display_name }}</a>

                <hr class="visible-xs" />

                <div class="hidden-xs">
                    <div class="text-muted text-left"><small>Posts: {{ $post->user->post_count }}</small></div>
                    <div class="text-muted text-left"><small>Joined: {{ $post->user->created }}</small></div>
                </div>

                @if ($post->user->avatar)
                <img class="img-thumbnail img-responsive avatar" src="{!! asset($post->user->avatar) !!}" alt="" />
                @endif
                @if ($messaging)
                <div class="forum-icons">
                    <a href="{{ route('messages.create', $post->user->display_name) }}"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                </div>
                @endif
            </div>
            <div class="col col-sm-10">
                @if ($post->status != 'Deleted')
                <div class="laraboard-post-body">
                    {!! $post->body_html !!}
                </div>
                @else
                <em class="text-muted">Deleted: {!! $post->deleted !!}</em>
                @endif

                @if ($post->revisionHistory->count() > 0)
                <div id="laraboard-updated-by" class="text-muted">
                    <hr>
                    <small>Edited: @if ($post->updatedByUser){{ $post->updatedByUser->display_name }} at @endif{{ $post->updated }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>