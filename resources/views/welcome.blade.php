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
    <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" sizes="16x16 32x32 64x64" href="{{ asset('images/theme/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="196x196" href="{{ asset('images/theme/favicon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/theme/favicon-180.png') }}">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="{{ asset('images/theme/favicon-144.png') }}">
    <meta name="twitter:image:src" content="{{ asset('images/theme/favicon-192.png') }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@coststoexpect" />
    <meta name="twitter:title" content="Costs to Expect API" />
    <meta name="twitter:description" content="The Open Source API for the Costs to Expect service, expense tracking and forecasting" />
    <meta property="og:image" content="{{ asset('images/theme/favicon-192.png') }}" />
    <meta property="og:site_name" content="Costs to Expect API" />
    <meta property="og:type" content="object" />
    <meta property="og:title" content="Costs to Expect API" />
    <meta property="og:url" content="https://api.costs-to-expect.com" />
    <meta property="og:description" content="The Open Source API for the Costs to Expect service, expense tracking and forecasting" />
    <title>Costs to Expect API</title>

    <link href="{{ asset('css/app.css?ver=' . $version) }}" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-64736-10"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-64736-10');
    </script>
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
                        <a href="/"><img src="{{ asset('images/theme/logo-190.png') }}" width="64" height="64" alt="Costs to Expect Logo"
                                                  title="Back to the dashboard"/><span class="d-none">C</span>osts to Expect.com</a>
                    </div>

                    <ul class="menu list-unstyled">
                        <li><strong>Costs to Expect</strong>
                            <ul class="submenu">
                                <li><a class="nav-link  active " href="/v2"><span class="oi oi-key" title="The Costs to Expect API" aria-hidden="true"></span>The API</a></li>
                                <li><a class="nav-link " href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (Github)</a></li>
                                <li><a class="nav-link " href="/v2/changelog"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (API)</a></li>
                                <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website"><span class="oi oi-monitor" title="The Costs to Expect Website" aria-hidden="true"></span>The Website</a></li>
                                <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App"><span class="oi oi-spreadsheet" title="The Costs to Expect App" aria-hidden="true"></span>The App</a></li>
                                <li><a class="nav-link" href="https://blog.costs-to-expect.com" title="The Costs to Expect Blog"><span class="oi oi-copywriting" title="The Costs to Expect Blog" aria-hidden="true"></span>The Blog</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-10 col-lg-9 col-md-9 container-right">
            <nav class="navbar navbar-light d-md-none">
                <a class="navbar-brand" href="/">
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
                                        <li><a class="nav-link  active " href="/v2"><span class="oi oi-key" title="The Costs to Expect API" aria-hidden="true"></span>The API</a></li>
                                        <li><a class="nav-link " href="https://github.com/costs-to-expect/api/blob/master/CHANGELOG.md"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (Github)</a></li>
                                        <li><a class="nav-link " href="/v2/changelog"><span class="oi oi-script" title="Our changelog" aria-hidden="true"></span>Changelog (API)</a></li>
                                        <li><a class="nav-link" href="https://www.costs-to-expect.com" title="The Costs to Expect Website"><span class="oi oi-monitor" title="The Costs to Expect Website" aria-hidden="true"></span>The Website</a></li>
                                        <li><a class="nav-link" href="https://app.costs-to-expect.com" title="The Costs to Expect App"><span class="oi oi-spreadsheet" title="The Costs to Expect App" aria-hidden="true"></span>The App</a></li>
                                        <li><a class="nav-link" href="https://blog.costs-to-expect.com" title="The Costs to Expect Blog"><span class="oi oi-copywriting" title="The Costs to Expect Blog" aria-hidden="true"></span>The Blog</a></li>
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
                            <h1>Our REST API</h1>
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
                        forecasting expenses. We are trying to simplify
                        your budgets. There are three core products in the
                        service; the Open Source REST API, our App and an Open
                        Source website showing the costs to raise our children
                        to adulthood.</p>

                    <p>Our API is the backbone of the service, everything depends
                        on it. Our API is available to anyone who wants to use it.
                        Our focus is expenses, however, that will change as the
                        product matures.</p>

                    <p>
                        <a href="/v2" class="btn btn-primary btn-lg" role="button" aria-pressed="true">Access API</a>
                        <a href="https://github.com/costs-to-expect/api" class="btn btn-primary alter btn-lg" role="button" aria-pressed="true">View our API on Github</a>
                    </p>

                    <hr />

                    <h2>Our Products</h2>

                    <p>There are multiple products within the Costs to Expect
                        service, the major products being our API and App, below
                        is a quick overview of each product.</p>

                    <dl class="row">
                        <dt class="col-sm-4 col-md-4 col-lg-3 col-xl-2">
                            <a href="/v2">
                            <span class="oi oi-key" title="Costs to Expect API" aria-hidden="true"></span>
                            Our API
                            </a>
                        </dt>
                        <dd class="col-sm-8 col-md-8 col-lg-9 col-xl-10">
                            <p>Our <a href="https://github.com/costs-to-expect/api/blob/master/LICENSE">Open Source</a>
                            REST <a href="https://api.costs-to-expect.com/v2">API</a>, available under
                                the MIT license, the API drives the entire service.</p></dd>

                        <dt class="col-sm-4 col-md-4 col-lg-3 col-xl-2">
                            <a href="https://app.costs-to-expect.com">
                            <span class="oi oi-spreadsheet" title="Costs to Expect App" aria-hidden="true"></span>
                            Our App
                            </a>
                        </dt>
                        <dd class="col-sm-8 col-md-8 col-lg-9 col-xl-10"><p>Our <a href="https://app.costs-to-expect.com/">App</a> is the
                            commercial offering for Costs to Expect,
                            we are <a href="https://app.costs-to-expect.com/roadmap">working</a> towards the public alpha, our aim is to make tracking and
                                forecasting expenses as simple as possible.</p></dd>

                        <dt class="col-sm-4 col-md-4 col-lg-3 col-xl-2">
                            <a href="https://www.costs-to-expect.com">
                            <span class="oi oi-monitor" title="Costs to Expect Website" aria-hidden="true"></span>
                            Our Website
                            </a>
                        </dt>
                        <dd class="col-sm-8 col-md-8 col-lg-9 col-xl-10"><p>Our <a href="https://www.costs-to-expect.com">website</a>
                                is a long-term social project. My wife
                                and I are tracking all the expenses to raise our child to adulthood.</p></dd>

                        <dt class="col-sm-4 col-md-4 col-lg-3 col-xl-2">
                            <a href="https://blog.costs-to-expect.com">
                            <span class="oi oi-copywriting" title="Costs to Expect Blog" aria-hidden="true"></span>
                            Our Blog
                            </a>
                        </dt>
                        <dd class="col-sm-8 col-md-8 col-lg-9 col-xl-10"><p>Our blog acts as a central repository to list all updates,
                            explains why we are doing what we are and acts as a place for us to talk about
                            our products and the service.</p></dd>
                    </dl>

                    <hr />

                    <h2>Latest feature release [v2.10.0]</h2>

                    <p>The latest release of the Costs to Expect API is
                        {{ $version }}; we released it on the {{ date('jS M Y', strtotime($date)) }}.</p>

                    <p>The combined changelog below shows all the fixes and improvements we have made to the
                        API since the last feature release.</p>

                    <h3>Added</h3>

                    <ul>
                        <li>We have added a new route, `/resource_types/[id]/resources/[id]/items/[id]/partial-transfer`; A partial transfer allows you to transfer a percentage of the `total` for an item from one resource to another.</li>
                        <li>We have added an `item_transfer` table; the table will log which items were transferred and by whom.</li>
                        <li>We have added a partial transfers collection; the route is `/resource_types/[id]/partial-transfers`.</li>
                        <li>We have added a partial transfers item view; the route is `/resource_types/[id]/partial-transfers/[id]`.</li>
                        <li>We have added a transfers collection; the route is `/resource_types/[id]/transfers`.</li>
                        <li>We have added a transfers item view; the route is `/resource_types/[id]/transfers/[id]`.</li>
                        <li>We have added a delete endpoint for partial transfers.</li>
                    </ul>

                    <h3>Changed</h3>

                    <ul>
                        <li>We have reformatted the validation rules in the configuration files; easier to read and simpler to add additional rules.</li>
                        <li>We have switched the HTTP status code for a "Constraint error" from 500 to 409.</li>
                        <li>We have tweaked the description for the resource field in the `/resource_types/[id]/resources/[id]/items/[id]/transfer` OPTIONS request.</li>
                        <li>We have renamed the third parameter of the route validation methods; we changed the name from `$manage` to `$write`.</li>
                        <li>We have renamed a response helper method; it was not clear from the name that the method is used for updates and delete.</li>
                        <li>We have tweaked our Docker setup to allow a local API and App/Website; the ports have been changed and a network has been created.</li>
                        <li>We have updated all item endpoints to return `updated`; this is the date and time an item was updated, not its category assignments.</li>
                        <li>We have updated item collection and show endpoints; we are going to allow the possibility of items not having categories and subcategories. When you add the `include-categories` and `include-subcategories` parameters to a request, we will not exclude items without category assignments.</li>
                        <li>We have updated the API to the latest release of Laravel 7.</li>
                        <li>We have updated the front end dependencies for the welcome page.</li>
                        <li>We have updated the `item-types` route to show additional information on each tracking method.</li>
                        <li>We have updated all decimal fields to 13,2 rather than 10,2.</li>
                        <li>We have updated all description fields; we have switched all the description fields from varchar(255) to text.</li>
                    </ul>

                    <h3>Fixed</h3>

                    <ul>
                        <li>It is possible to set the quantity for a `simple-item` item as zero.</li>
                        <li>It is possible to clear optional values in a PATCH request.</li>
                        <li>We have corrected a bad link on the landing page.</li>
                        <li>We have corrected a typo on the landing page.</li>
                        <li>We have switched the table we look at to return created at for an item; we should be using the sub table, not the base item table.</li>
                        <li>We have corrected the `/resource-types/` OPTIONS request; `public` is not a required field.</li>
                        <li>We have updated the delete resource type action; we have added additional checks before we attempt to delete, it was possible to remove relationship values which made the resource type inaccessible.</li>
                        <li>We have adjusted the lottery value to reduce session clears.</li>
                        <li>We have updated to v3.5.1 of Jquery, v3.5.0 was bugged.</li>
                    </ul>
                </div>
            </div>

            <div class="row mt-5 mb-2">
                <div class="col-12">
                    <hr />
                    <p class="text-center text-muted">
                        Copyright © <a href="https://www.deanblackborough.com">Dean Blackborough 2018 - {{ date('Y') }}</a><br>
                        <a href="https://app.costs-to-expect.com">Our App</a> |
                        <a href="https://www.costs-to-expect.com/">Our Website</a> |
                        <a href="https://blog.costs-to-expect.com/">Our Blog</a> |
                        <a href="https://status.costs-to-expect.com/">Status</a>
                    </p>
                    <p class="text-center text-muted">Latest release: {{ $version }} ({{ date('jS M Y', strtotime($date)) }})</p>
                    <p class="text-center text-muted">All code maintained by <a href="https://www.deanblackborough.com">Dean Blackborough</a> and released under the MIT license.</p>
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
