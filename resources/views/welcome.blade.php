@extends('layouts.app')

@section('content')

    <main role="main" class="inner cover">
        <h1 class="cover-heading text-center">Costs to Expect API</h1>
        @if ($maintenance === true)
        <div class="alert alert-info" role="alert">
            The Costs to Expect API is down for maintenance, we should be back online soon.
        </div>
        @endif

        <p class="lead mb-5">
            Costs to Expect is a service that primarily focuses on providing tools
            for tracking and forecasting expenses. Long-term the service will not be
            limited to expenses; initially, however, expenses are the primary focus.
            There are three parts to the service, the Open Source
            <a href="https://github.com/costs-to-expect/api">API</a>, the
            app and the <a href="https://www.costs-to-expect.com">website</a>.
        </p>

        <ul class="mb-5">
            <li>The API is the backbone of the service; it is
                <a href="https://github.com/costs-to-expect/api/blob/master/LICENSE">Open Source</a>
                and available under the MIT license.</li>
            <li>The <a href="https://www.costs-to-expect.com">website</a> is a
                long-term personal project; my wife and I are tracking the expenses
                to raise our two children to adulthood, 18.</li>
            <li>The app is going to be the public side of the service; we are
                hoping to have a version ready by the end of the year.</li>
        </ul>

        <p class="text-center mt-3">
            <a href="/v2" class="btn btn-md btn-primary">Access the API</a>
            <a href="https://www.costs-to-expect.com" class="btn btn-md btn-primary">The Website</a>
        </p>
        <p class="text-center">
            <a href="https://github.com/costs-to-expect" class="btn btn-md btn-primary">The API on GitHub</a>
            <a href="#" class="btn btn-dm btn-primary disabled">The app</a>
        </p>

        <p class="text-center"><small>Latest release: {{ $version }} ({{ $date }})</small></p>

    </main>

@endsection
