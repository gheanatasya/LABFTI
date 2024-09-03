<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <p>Berikut link reset password anda:</p>
    <button type="button" class="btn btn-primary">
        <a href="{{ $dataEmail['reset_password_link'] }}">Reset Password</a>
    </button>
    
    <br>
    <p>Tertanda,</p>
    <p>Laboratorium Fakultas Teknologi Informasi UKDW</p>
</body>

</html>
