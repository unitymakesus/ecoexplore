@foreach ($panels as $panel)
<div class="col s12 m4">
  <div class="splat">
    {!! file_get_contents($panel['splat']) !!}
  </div>

  <img data-src="{{ $panel['img-sm'] }}"
       srcset="{{ $panel['img-sm'] }} 1x, {{ $panel['img-lg'] }} 2x"
       alt="{{ $panel['alt'] }}">
</div>
@endforeach
