@php
  $now = date('Ymd', current_time('timestamp'));

  $this_season = new WP_Query([
    'post_type' => 'field-season',
    'posts_per_page' => -1,
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'start_date',
        'compare' => '<=',
        'value' => $now,
        'type' => 'NUMERIC'
      ], [
        'key' => 'end_date',
        'compare' => '>=',
        'value' => $now,
        'type' => 'NUMERIC'
      ]
    ]
  ]);
@endphp

@if ($this_season->have_posts())
  @while ($this_season->have_posts())
    @php ($this_season->the_post())
    @php ($banner = get_the_post_thumbnail_url(get_the_id(), 'large'))

    <div class="page-header" style="background-image: url('{{ $banner }}')"></div>

    <div @php(post_class('container current-season ' . get_post_field('post_name')))>
          <p class="h6">{{ date('F j, Y', get_field('start_date')) }} - {{ date('F j, Y', get_field('end_date')) }}</p>
          <h1>It's @php(the_title()) Season!</h1>

          @include('partials.content-single-field-season')
    </div>

  @endwhile
  @php (wp_reset_postdata())
@endif
