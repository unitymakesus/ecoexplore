<div class="container scientists">
  <div class="row block-grid up-s1 up-m2 up-l4">
    @php
      $scientists = new WP_Query([
        'post_type' => 'scientist',
        'posts_per_page' => -1,
      ]);
    @endphp

    @if ($scientists->have_posts())
      @while ($scientists->have_posts())
        @php ($scientists->the_post())
        
        <div class="col">
          @include('partials.content-scientist')
        </div>
      @endwhile
    @endif
  </div>
</div>
