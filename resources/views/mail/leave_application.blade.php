<!DOCTYPE html>
<html>

<head>
    <title>Leave Application</title>
</head>

<body>
    <h1>Leave Application Notification</h1>
    <p>A new leave application has been submitted by {{ $application->employee->name }}.</p>
    <p>Details:</p>
    <ul>
        <li>Type: {{ $application->leaveType->title }}</li>
        <li>Start Date: {{ $application->date_from }}</li>
        <li>End Date: {{ $application->date_to }}</li>
        <li>Reason: {{ $application->remarks }}</li>
    </ul>
</body>

</html>
