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
                        <a href="https://postman.costs-to-expect.com" class="btn btn-primary alter btn-lg" role="button" aria-pressed="true">Documentation</a>
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

                    <h2>Latest feature release [v2.11.0]</h2>

                    <p>The latest release of the Costs to Expect API is
                        {{ $version }}; we released it on the {{ date('jS M Y', strtotime($date)) }}.</p>

                    <p>The combined changelog below shows all the fixes and improvements we have made to the
                        API since the last feature release.</p>

                    <h3>Added</h3>

                    <ul>
                        <li>We have added search to the categories summary endpoint.</li>
                        <li>We have added search to the subcategories summary endpoint.</li>
                        <li>We have added search to the resources summary endpoint.</li>
                        <li>We have added search to the resource-types summary endpoint.</li>
                        <li>We have updated the transfers collection; you can filter the results by an `item` ID.</li>
                        <li>We have updated the partial transfers collection; you can filter the results by an `item` ID.</li>
                        <li>We have added additional filtering options for collections. We have added `total` and `actualised_total` range filtering for the `allocated-expense` item type and `total` range filtering for the `simple-expense` item type.</li>
                        <li>We have added an application cache for collections; we include the ETag header however we are not yet returning a 304.</li>
                    </ul>

                    <h3>Changed</h3>

                    <ul>
                        <li>We have added the documentation URI to the README and API; the documentation for the API is work in progress; it is almost complete.</li>
                        <li>We have added a version query parameter to the CSS include.</li>
                        <li>We have removed a unique index on the `item_transfer` table; the index was limiting the ability to transfer an item multiple times.</li>
                        <li>We have altered the format of the category and subcategory object for item relationships; the returned object is now a closer match to a category object.</li>
                        <li>We have updated the URIs for item category and subcategory assignments; the URI was singular for a collection.</li>
                        <li>We have updated the item delete endpoint; we did not return a conflict error before removing relationships.</li>
                        <li>We have tweaked the `type` for expense fields; we highlight that the supplied value should be a decimal string.</li>
                        <li>We have moved our route validators; the route validators sit inside the `App\Request` namespace.</li>
                        <li>We have moved our `Header` utility class; the `Header` class sits inside the `App\Response` namespace.</li>
                        <li>We have moved our `RoutePermission` utility class; the `Permission` class sits inside the `App\Request` namespace.</li>
                        <li>We have moved and simplified our `Parameter` classes; the `Parameter` classes sit inside the `App\Request` namespace.</li>
                        <li>We have moved and renamed the `RequestUtility` class; the `BodyValidation` class sits in the `App\Request` namespace.</li>
                        <li>We have split calls to clear public and private cache keys.</li>
                        <li>We only clear public cache keys when modifying a public resource type.</li>
                        <li>We have updated the web.config; the web.config file was referring to PHP7.3.</li>
                        <li>Added removed class type hints now that we are running PHP7.4.</li>
                    </ul>

                    <h3>Fixed</h3>

                    <ul>
                        <li>The `name` field now displays as a required field in the items collection OPTIONS request.</li>
                        <li>The `filterable` array shows in the OPTIONS response for resource type items.</li>
                        <li>We have updated the namespace in a model.</li>
                        <li>We have corrected the case of a model name.</li>
                        <li>We have added the migrations for the original item-types, missing from the import.</li>
                        <li>We have added a database migration to update the `friendly_name` and `examples` data for `item-types`.</li>
                        <li>We have updated the delete item request; we delete the transfer log entries for the item.</li>
                        <li>We have updated two `item-type` summary endpoints to show they are sortable by `name`.</li>
                    </ul>

                    <h3>Removed</h3>

                    <ul>
                        <li>We have removed the category and subcategory item assigned URIs and replaced them with correctly named URIs for collections.</li>
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
