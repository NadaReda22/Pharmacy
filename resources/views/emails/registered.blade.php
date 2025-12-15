<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmed</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    <p>Thank you for registering in our Pharmacy System.</p>

    <h3>This is your code: {{ $user->id }},</h3>

    @if($user->role === 'vendor')
        <p>Your pharmacy license has been received and will be verified shortly.</p>
    @else
        <p>Welcome to our platform! You can now search and order medicines easily.</p>
    @endif

    <p>Best regards,<br>Pharmacy System Team</p>
</body>
</html>
