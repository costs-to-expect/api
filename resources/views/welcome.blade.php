@extends('layouts.app')

@section('content')

    <main role="main" class="inner cover">
        <h1 class="cover-heading">Costs to Expect API</h1>
        <p class="lead">This <a href="https://github.com/costs-to-expect/api/blob/master/LICENSE">Open Source</a>
            API is the <a href="https://github.com/costs-to-expect/api">API</a> for the
            <a href="https://www.costs-to-expect.com">Costs to Expect</a> service,
            a small part of the service is a long-term personal project, my wife
            and I are tracking the expenses to raise our two children to
            adulthood, 18.</p>
        <p class="lead">
            <a href="/v1" class="btn btn-lg btn-primary mb-1">The API</a>&nbsp;
            <a href="https://www.costs-to-expect.com" class="btn btn-lg btn-primary mb-1">The Website</a>
        </p>
        <p class="lead">
            <a href="https://github.com/costs-to-expect" class="btn btn-lg btn-primary">GitHub</a>
        </p>

        <p><small>Latest release: {{ $version }} ({{ $date }})</small></p>

        <p class="mt-4">This Laravel app is a RESTful API and is part of the
            <a href="https://www.costs-to-expect.com">Costs to Expect</a> service.
        </p>
    </main>

@endsection
