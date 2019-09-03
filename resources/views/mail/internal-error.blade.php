<!DOCTYPE html>
<html>
<head>
    <title>Costs to Expect API: Internal error</title>
    <style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>
</head>

<body>
    <h2>Captured Costs to Expect API Internal Error</h2>

    <table>
        <tr>
            <td style="padding-right: 50px;">Message:</td>
            <td>{{ $internal_error['message'] }}</td>
        </tr>
        <tr>
            <td>File:</td>
            <td>{{ $internal_error['file'] }}</td>
        </tr>
        <tr>
            <td>Line:</td>
            <td>{{ $internal_error['line'] }}</td>
        </tr>
        <tr>
            <td>Trace:</td>
            <td>{{ $internal_error['trace'] }}</td>
        </tr>
    </table>
</body>
</html>
