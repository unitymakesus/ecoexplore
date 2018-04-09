@php ($class = '')
@if (!is_page('user'))
  @php($class = 'container')
@endif

<div @php(post_class($class))>
  @php(the_content())
  {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
</div>

@if (is_page('prizes'))
  @include('partials.prizes-loop')
@endif

@if (is_page('field-season'))
  @include('partials.field-season-loop')
@endif

@if (is_page('badges'))
  @include('partials.badges-loop')
@endif

@if (is_page('science-mentors'))
  @include('partials.scientists-loop')
@endif
