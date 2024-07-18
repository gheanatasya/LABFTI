<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
</head>

<body>
    <h1>Welcome to Laboratorium Fakultas Teknologi Informasi UKDW, {{ $dataEmail['nama'] }}!</h1>
    <p>Thank you for registering on our website.</p>

    <p>Here is link to get you started:</p>
    <ul>
        <li><a href="http://localhost:8000/">Visit our website</a></li>
    </ul>

    {{-- @if (isset($dataEmail['verification_link']))
        <p>To activate your account, please click on the following link:</p>
        <a href="{{ $dataEmail['verification_link'] }}">Verify your email</a>
    @endif --}}

    <p>Thank you,</p>
    <p>Laboratorium Fakultas Teknologi Informasi UKDW</p>
</body>

</html>
