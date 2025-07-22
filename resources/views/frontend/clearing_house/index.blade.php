@extends('layouts.frontend.master2')

@section('content')
    <section class="my-5">
        <div class="container pt-5">
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="text-center">{{ $clearing_house->title }}</h2>
                    <h5 class="text-center"> @php echo html_entity_decode($clearing_house->short_description); @endphp</h5>
                    <p class="text-center">
                        @php echo html_entity_decode($clearing_house->description); @endphp
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
