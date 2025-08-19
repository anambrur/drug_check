@extends('layouts.frontend.master2')

@section('content')
    <section class="my-5">
        <div class="container pt-5">
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="text-center">{{ $clearing_house->title }}</h2>
                    <h5 class="text-center">@php echo html_entity_decode($clearing_house->short_description); @endphp</h5>
                    <p class="text-center">
                        @php echo html_entity_decode($clearing_house->description); @endphp
                    </p>

                    <h5 class="text-center mt-5">Learning Center Section:</h5>

                    <!-- For Drivers Section -->
                    <h2>📘 For Drivers</h2>
                    <p>If you're a CDL driver, you must be registered in the FMCSA Clearinghouse to stay compliant with
                        federal regulations. This section helps you understand how to register, respond to employer queries,
                        and
                        view your drug and alcohol testing history.
                    </p>

                    @if ($clearing_house->driver_pdf)
                        <div class="mt-3">
                            <h5>Downloadable Resources:</h5>
                            <ul class="list-group">
                                @foreach (json_decode($clearing_house->driver_pdf) as $pdf)
                                    <li class="list-group-item">
                                        <a href="{{ asset('uploads/pdf/driver_pdf/' . $pdf) }}" target="_blank"
                                            class="text-primary">
                                            <i class="fas fa-file-pdf mr-2"></i>
                                            @php
                                                $filenameWithoutTimestamp = substr($pdf, strpos($pdf, '-') + 1);
                                                $cleanName = pathinfo($filenameWithoutTimestamp, PATHINFO_FILENAME);
                                                $formattedName = ucwords(str_replace('-', ' ', $cleanName));
                                            @endphp
                                            {{ $formattedName }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-muted">No driver resources available at this time.</p>
                    @endif

                    <!-- For Employers Section -->
                    <h2 class="mt-5">🧑‍💼 For Employers</h2>
                    <p>As an employer of CDL drivers, you're required to register with the FMCSA Clearinghouse, run annual
                        queries, report violations, and stay compliant with DOT drug and alcohol testing rules.
                    </p>

                    @if ($clearing_house->employer_pdf)
                        <div class="mt-3">
                            <h5>Downloadable Resources:</h5>
                            <ul class="list-group">
                                @foreach (json_decode($clearing_house->employer_pdf) as $pdf)
                                    <li class="list-group-item">
                                        <a href="{{ asset('uploads/pdf/employer_pdf/' . $pdf) }}" target="_blank"
                                            class="text-primary">
                                            <i class="fas fa-file-pdf mr-2"></i>@php
                                                $filenameWithoutTimestamp = substr($pdf, strpos($pdf, '-') + 1);
                                                $cleanName = pathinfo($filenameWithoutTimestamp, PATHINFO_FILENAME);
                                                $formattedName = ucwords(str_replace('-', ' ', $cleanName));
                                            @endphp
                                            {{ $formattedName }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-muted">No employer resources available at this time.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
