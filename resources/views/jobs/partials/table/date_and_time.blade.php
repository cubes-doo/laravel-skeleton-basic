@if($datum instanceof \DateTime)
    {{$datum->format('d.m.Y / H:i:s')}}
@else
    {{-- Kada je datum NULL strtotime() vraca datum pocetka 'Unix' epohe --}}
    @if( ! is_null($datum) ) 
        {{date('d.m.Y / H:i:s', strtotime($datum))}}
    @endif 
@endif