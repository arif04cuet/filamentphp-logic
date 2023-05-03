@if (filled($brand = config('filament.brand')))
<div @class([ 'filament-brand text-xl font-bold tracking-tight' , 'dark:text-white'=> config('filament.dark_mode'),
    ])>

    <img src="{{asset('images/logo.png')}}" alt="Logic">
</div>
@endif
<style>
    .filament-body {
        background-image: url('/images/bg2.webp');
        /* background-repeat: repeat-x; */
        opacity: .8;
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>