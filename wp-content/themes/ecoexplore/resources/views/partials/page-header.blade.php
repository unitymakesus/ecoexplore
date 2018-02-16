@if (!is_page('field-season') && !is_page('badges'))

  @if (get_post_type() == 'badge')
    @php
      $season = get_field('badge_season');
      $season_id = $season[0];
    @endphp

    @if (!empty($season_id))
      @php ($banner = get_the_post_thumbnail_url($season_id, 'full'))
    @else
      @php ($banner = get_the_post_thumbnail_url(354, 'full'))
    @endif
  @else
    @php ($banner = get_the_post_thumbnail_url(get_the_id(), 'full'))
  @endif

  <div class="page-header" style="background-image: url('{{ $banner }}')"></div>
@endif
