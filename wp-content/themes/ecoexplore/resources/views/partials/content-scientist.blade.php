@php
  $season = get_field('season_mentor');
  $season_id = $season[0];
@endphp

<div class="card">
  <div class="card-image waves-effect waves-block waves-light">
    @include('partials.lazy-image', [
      'src'   => get_the_post_thumbnail_url(get_the_id(), 'thumbnail'),
      'alt' => '',
      'class' => 'activator'
    ])
  </div>

  <div class="card-content">
    <span class="card-title activator">{{ the_title() }}, {{ get_field('affiliation') }} <i class="material-icons right">info_outline</i></span>
    <p>{{ get_the_title($season_id) }} Science Mentor</p>
  </div>

  <div class="card-reveal">
    <span class="card-title">{{ the_title() }}, {{ get_field('affiliation') }} <i class="material-icons right">close</i></span>
    <p>{{ get_the_title($season_id) }} Science Mentor</p>
    <p>{{ the_content() }}</p>
  </div>
</div>
