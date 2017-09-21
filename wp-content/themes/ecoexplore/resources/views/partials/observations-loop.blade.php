@php($observations = App\get_observations())

@if (!empty($observations))
	@foreach($observations as $o)
		<div class="observation card horizontal">
			<a href="{{ $o->uri }}" target="_blank" rel="noopener" class="mega-link" aria-hidden="true"></a>
			<div class="card-image">
				<img src="{{ $o->photos[0]->square_url }}" alt="" />
			</div>

			<div class="card-stacked">
				<div class="card-content">
					<h3><a href="{{ $o->uri }}" target="_blank" rel="noopener">{{ $o->species_guess }}</a></h3>
          <p>spotted by _____</p>
					<ul>
						<li class="where"><i class="material-icons" aria-label="Where">location_on</i> {{ $o->place_guess }}</li>
						<li class="when"><i class="material-icons" aria-label="When">access_time</i> {{ date("M j, Y", strtotime($o->created_at)) }}</li>
					</ul>
				</div>
			</div>
		</div>
	@endforeach
@endif
