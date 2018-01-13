@foreach ($panels as $panel)
<div class="col s12 m4">
  <div class="mini-splat">
    {{ App\svg_image($panel['splat']) }}
  </div>

  <div class="panel-inner">
    <div class="panel-img">
      <img src="{{ $panel['img-sm'] }}"
           srcset="{{ $panel['img-sm'] }} 1x, {{ $panel['img-lg'] }} 2x"
           alt="{{ $panel['alt'] }}">
    </div>
  </div>
</div>
@endforeach
