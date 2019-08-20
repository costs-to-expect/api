<!DOCTYPE html>
<html>
<head>
    <title>Costs to Expect API: Request error</title>
    <style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>
</head>

<body>
    <h2>Captured Costs to Expect API Request Error</h2>

    <table>
        <tr>
            <td style="padding-right: 50px;">Method</td>
            <td>{{ $request_error['method'] }}</td>
        </tr>
        <tr>
            <td>Expected Status Code:</td>
            <td>{{ $request_error['expected_status_code'] }}</td>
        </tr>
        <tr>
            <td>Returned Status Code:</td>
            <td>{{ $request_error['returned_status_code'] }}</td>
        </tr>
        <tr>
            <td>Requested URI:</td>
            <td>{{ $request_error['request_uri'] }}</td>
        </tr>
        <tr>
            <td>Source:</td>
            <td>{{ $request_error['source'] }}</td>
        </tr>
        <tr>
            <td>Referer (if set):</td>
            <td>{{ $request_error['referer'] }}</td>
        </tr>
        <tr>
            <td>Debug information (if set):</td>
            <td>{{ $request_error['debug'] }}</td>
        </tr>
    </table>
</body>
</html>
