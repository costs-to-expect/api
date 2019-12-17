@extends('layouts.app')

@section('content')

    <main role="main" class="inner cover">
        <h1 class="cover-heading text-center">Costs to Expect API</h1>
        @if ($maintenance === true)
        <div class="alert alert-info mt-4" role="alert">
            The Costs to Expect API is down for maintenance, we should be back online soon.
        </div>
        @endif

        <p class="lead mt-4">
            Costs to Expect is a service which primarily focuses on the tracking
            and forecasting of expenses; it grew from an offhand comment I made
            to my wife before we had our first child,
            <em>"There is no way the average child costs £250,000 to raise!"</em>
        </p>

        <p>
            Medium-term the service will not be limited to expenses, the project
            grew from a personal expenses tracking project, so it makes sense to
            focus initially on expenses.
        </p>

        <p>
            There are three parts to the service, two Open Source and one soon to
            be commercial product.
        </p>

        <ul class="mb-5 mt-5">
            <li>The API is the backbone of the service; it is
                <a href="https://github.com/costs-to-expect/api/blob/master/LICENSE">Open Source</a>
                and available under the MIT license.</li>
            <li>The <a href="https://www.costs-to-expect.com">Website</a> is a
                long-term personal project; my wife and I
                are tracking the expenses to raise our two
                children to adulthood, 18. The Website is
                <a href="https://github.com/costs-to-expect/website/blob/master/LICENSE">Open Source</a>
                and available under the MIT license.</li>
            <li>The <a href="https://www.costs-to-expect.com">website</a> is a
                long-term personal project; my wife and I are tracking the expenses
                to raise our two children to adulthood, 18. The Website is Open
                Source and available under the MIT license.</li>
            <li>The <a href="https://app.costs-to-expect.com">App</a> is the
                beginnings of our commercial product; we are hoping to
                have an alpha ready for the end of the year with betas in early
                2020.</li>
        </ul>

        <p class="text-center mt-3">
            <a href="/v2" class="btn btn-md btn-primary">Access the API</a>
            <a href="https://www.costs-to-expect.com" class="btn btn-md btn-primary">The Website</a>
        </p>
        <p class="text-center">
            <a href="https://github.com/costs-to-expect" class="btn btn-md btn-primary">The API on GitHub</a>
            <a href="https://app.costs-to-expect.com" class="btn btn-dm btn-primary">The app</a>
        </p>

        <p class="text-center"><small>Latest release: {{ $version }} ({{ $date }})</small></p>

    </main>

@endsection
