@php
  $season = get_field('season_mentor');
  $season_id = $season[0];
@endphp

@if ($format !== 'horizontal')
  @php ($activator = 'activator')
  @php ($waves = 'waves-effect waves-block waves-light')
@endif

<div class="card {{ $format }} {{ get_post_field('post_name', $season_id) }} scientist">
  <div class="card-image {{ $waves }}">
    @include('partials.lazy-image', [
      'src'   => get_the_post_thumbnail_url(get_the_id(), 'event-square'),
      'alt' => '',
      'class' => $activator
    ])
  </div>

  <div class="card-content" aria-label="hidden">
    <span class="card-title {{ $activator }}">
      {{ the_title() }}, {{ get_field('affiliation') }}

      @if ($format !== 'horizontal')
        <i class="material-icons right">info_outline</i>
      @endif
    </span>

    <p>{{ get_the_title($season_id) }} Science Mentor</p>

    @if ($format == 'horizontal')
      <p>{{ the_content() }}</p>
    @endif
  </div>

  @if ($format !== 'horizontal')
    <div class="card-reveal">
      <span class="card-title">{{ the_title() }}, {{ get_field('affiliation') }} <i class="material-icons right">close</i></span>
      <p>{{ get_the_title($season_id) }} Science Mentor</p>
      <p>{{ the_content() }}</p>
    </div>
  @endif
</div>
