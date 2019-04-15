@extends('layouts.app')

@section('content')

    <main role="main" class="inner cover">
        <h1 class="cover-heading">Costs to Expect API</h1>
        <p class="lead"><a href="https://www.costs-to-expect.com">Costs to Expect</a>
            is a long-term project, my wife and I
            are tracking the expenses to raise our child to adulthood, 18.</p>
        <p class="lead">
            <a href="/v1" class="btn btn-lg btn-primary mb-1">The API</a>
            <a href="https://www.costs-to-expect.com" class="btn btn-lg btn-primary mb-1">Website</a>
        </p>
        <p class="lead">
            <a href="https://github.com/costs-to-expect" class="btn btn-lg btn-secondary">View on GitHub</a>
        </p>

        <p><small>Latest release: {{ $version }} ({{ $date }})</small></p>

        <p class="mt-4">This Laravel app is the RESTful API for the
            <a href="https://www.costs-to-expect.com">Costs to Expect</a> service,
            the API will be used by the <a href="https://www.costs-to-expect.com">Costs to Expect</a> website
            and companion iOS app which I'm creating to assist my wife with data input.
        </p>
    </main>

@endsection
