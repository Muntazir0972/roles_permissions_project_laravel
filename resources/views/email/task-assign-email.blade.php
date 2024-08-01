<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Assign Email</title>
</head>
<body>
    <h1>Hello {{ $mailData['task']->user->name }},</h1>
    <p>You have been assigned a new task:</p>
    <p><strong>Title:</strong> {{ $mailData['task']->title }}</p>

    @if (!empty($mailData['task']->description))
    <p><strong>Description:</strong> {{ $mailData['task']->description }}</p>
    @endif

    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($mailData['task']->due_date)->format('d M, Y') }}</p>
    <p>Please log in to your account to view and manage the task.</p>
</body>
</html>