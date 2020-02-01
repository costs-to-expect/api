
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <meta name="description" content="The Costs to Expect API. Open source REST API focused on budgeting and forecasting">
    <meta name="author" content="Dean Blackborough">
    <meta name="copyright" content="Dean Blackborough 2018-{{ date('Y') }}">
    <link href="{{ asset('node_modules/open-iconic/font/css/open-iconic-bootstrap.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" sizes="16x16 32x32 64x64" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="196x196" href="{{ asset('images/theme/favicon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/theme/favicon-180.png') }}">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="{{ asset('images/theme/favicon-144.png') }}">
    <title>Costs to Expect API</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 preview text-center">
            <p>Our <a href="https://app.costs-to-expect.com/">App</a> is in the alpha stage, the beta is coming soon.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-lg-3 col-md-3 container-left d-none d-sm-none d-md-block">
            <div class="row">
                <div class="col-12">
                    <div class="logo">
                        <a href="/dashboard"><img src="https://app.costs-to-expect.com/images/theme/logo-190.png" width="64" height="64" alt="Costs to Expect Logo"
                                                  title="Back to the dashboard"/><span class="d-none">C</span>osts to Expect.com</a>
                    </div>

                    <ul class="menu list-unstyled">
                        <li><strong>Costs to Expect</strong>
                            <ul class="submenu">
                                <li><a class="nav-link  active " href="/v2"><span class="oi oi-shield" title="Our privacy policy" aria-hidden="true"></span>The API</a></li>
                                <li><a class="nav-link " href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (Github)</a></li>
                                <li><a class="nav-link " href="/v2/changelog"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (API)</a></li>
                                <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website"><span class="oi oi-monitor" title="The Costs to Expect Website" aria-hidden="true"></span>The Website</a></li>
                                <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App"><span class="oi oi-monitor" title="The Costs to Expect App" aria-hidden="true"></span>The App</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-10 col-lg-9 col-md-9 container-right">
            <nav class="navbar navbar-light d-md-none">
                <a class="navbar-brand" href="/dashboard">
                    <img src="https://app.costs-to-expect.com/images/theme/logo-100.png" width="32" height="32" alt="Costs to Expect Logo"
                         title="Back to the dashboard"/><span class="d-none">C</span>osts to Expect.com
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#toggleNavbar" aria-controls="navbarTogglerApp"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse mt-1" id="toggleNavbar">
                    <div class="row">
                        <div class="col-12">
                            <ul class="menu list-unstyled">
                                <li><strong>Costs to Expect</strong>
                                    <ul class="submenu">
                                        <li><a class="nav-link  active " href="/v2"><span class="oi oi-shield" title="Our privacy policy" aria-hidden="true"></span>The API</a></li>
                                        <li><a class="nav-link " href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (Github)</a></li>
                                        <li><a class="nav-link " href="/v2/changelog"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (API)</a></li>
                                        <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website"><span class="oi oi-monitor" title="The Costs to Expect Website" aria-hidden="true"></span>The Website</a></li>
                                        <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App"><span class="oi oi-monitor" title="The Costs to Expect App" aria-hidden="true"></span>The App</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="row page-intro">
                <div class="col-12">
                    <div class="intro">
                        <div class="icon">
                            <img src="https://app.costs-to-expect.com/images/theme/header-icon-dashboard.png" width="50" height="50" alt="Icon" title="Our privacy policy: Costs to Expect.com" />
                        </div>
                        <div class="welcome">
                            Welcome to Costs to Expect
                        </div>
                        <div class="title">
                            <h1>The Costs to Expect API</h1>
                        </div>

                        <hr />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    @if ($maintenance === true)
                        <div class="alert alert-info mt-2" role="alert">
                            The Costs to Expect API is down for maintenance, we
                            should be back online soon. Please check our
                            <a href="https://status.costs-to-expect.com">status</a>
                            page for more information.
                        </div>

                    @endif

                    <h2>Overview</h2>

                    <p>Costs to Expect is a service focused on tracking and
                        forecasting expenses. There are three parts to the
                        service; the Open Source REST API, our App and an Open
                        Source website showing the costs to raise our children
                        to adulthood.</p>

                    <p>The API is the backbone to our service and is available
                        to anyone who wants it. Our API focuses on expenses;
                        however, that will change as the product matures.</p>

                    <p>
                        <a href="/v2" class="btn btn-primary btn-lg" role="button" aria-pressed="true">Access the API</a>
                        <a href="https://github.com/costs-to-expect/api" class="btn btn-primary alter btn-lg" role="button" aria-pressed="true">View the API on Github</a>
                    </p>

                    <hr />

                    <h2>Costs to Expect</h2>

                    <p>There are three parts to the Costs to Expect service;
                        two are Open Source, the third is Closed Sourced and
                        our commercial product.</p>

                    <ul>
                        <li>The Costs to Expect API: Our REST API is Open Source and available under the MIT license.</li>
                        <li>The Costs to Expect App: Our <a href="https://app.costs-to-expect.com">App</a> is our commercial offering. Our App makes tracking and forecasting expenses and costs simple as well as acting as a friendly interface to the API.</li>
                        <li>The Costs to Expect Website: Our <a href="https://www.costs-to-expect.com">Website</a> is a long term social experiment. My wife and I are tracking the expenses to raise our children to the age of 18.</li>
                    </ul>

                    <hr />

                    <h2>Latest release [v2.07.0]</h2>

                    <p>The latest release of the Costs to Expect API is
                        {{ $version }}; we released it on the {{ date('jS M Y', strtotime($date)) }}.
                        Review our changelog(s) to see the history of all our
                        releases.</p>

                    <h3>Added</h3>

                    <ul>
                        <li>We have added a GET 'auth/check' endpoint; faster check for the Costs to Expect App.</li>
                    </ul>

                    <h3>Changed</h3>

                    <ul>
                        <li>We have updated the dependencies for the API.</li>
                        <li>We have enabled URL compression.</li>
                        <li>We now return the user id on sign-in, saves a second request for the Costs to Expect App.</li>
                        <li>We have updated the README, adding links to the App `readme` and `changelog`.</li>
                        <li>We have tweaked two middleware classes to improve performance slightly.</li>
                    </ul>

                    <h3>Fixed</h3>

                    <ul>
                        <li>The HTTP verb was incorrect for the 'auth/user' endpoint.</li>
                    </ul>
                </div>
            </div>

            <div class="row mt-5 mb-2">
                <div class="col-12">
                    <hr />
                    <p class="text-center text-muted">
                        Copyright © <a href="https://www.deanblackborough.com">Dean Blackborough 2018 - {{ date('Y') }}</a><br>
                        <a href="https://app.costs-to-expect.com">The App</a> |
                        <a href="https://status.costs-to-expect.com/">Status</a> |
                        Latest release: {{ $version }} ({{ date('jS M Y', strtotime($date)) }})
                    </p>
                    <p class="text-center text-muted">All code maintained by <a href="https://www.deanblackborough.com">Dean Blackborough</a> and licensed under MIT.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('node_modules/jquery/dist/jquery.js') }}" defer></script>
<script src="{{ asset('node_modules/popper.js/dist/umd/popper.js') }}" defer></script>
<script src="{{ asset('node_modules/bootstrap/dist/js/bootstrap.js') }}" defer></script>
</body>
</html>
