@extends('layouts.app')

@section('content')

    <main role="main" class="inner cover">
        <h1 class="cover-heading">Costs to Expect.com</h1>
        <p class="lead">Costs to Expect is a long-term project, my wife and I
            are tracking the expenses to raise our child to adulthood, 18.</p>
        <p class="lead">
            <a href="/v1" class="btn btn-lg btn-primary">Access the API</a>
            <a href="https://github.com/costs-to-expect" class="btn btn-lg btn-secondary">View on GitHub</a>
        </p>

        <p class="mt-4">This Laravel app is the RESTful API for the Costs to Expect service,
            the API will be consumed by the Costs to Expect website and iOS app
            which I'm creating to assist the wife with data input.
        </p>
    </main>

@endsection
