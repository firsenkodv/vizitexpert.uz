@foreach(config('selects.data_sity') as $city)
    <li data-option="{{ $city['value'] }}" class="select__item">{{ $city['text'] }}</li>
@endforeach
