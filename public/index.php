<?php
function validateInput($input): string
{
    $input = trim($input);
    $input = htmlspecialchars($input);
    return stripslashes($input);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $userFirstname = validateInput($_POST['firstname']);
    $userLastname = validateInput($_POST['lastname']);
    $userAge = validateInput($_POST['age']);
    if (empty($userFirstname)) {
        $errors[] = 'Invalid firstname';
    }
    if (empty($userLastname)) {
        $errors[] = 'Invalid lastname';
    }
    if (empty($userAge) || $userAge > 100) {
        $errors[] = 'Invalid age';
    }
    $uploadDir = 'uploads/';
//    basename($_FILES['profilePicture']['name']);
    $extension = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
    $uploadFile = $uploadDir . uniqid() . '.' . $extension;

    $authorisedExtensions = ['jpg', 'jpeg', 'png'];
    $maxFileSize = 1000000;

    if (!in_array($extension, $authorisedExtensions)) {
        $errors[] = 'File must be a jpg, jpeg, or png picture';
    }

    if (file_exists($_FILES['profilePicture']['tmp_name'])
        && filesize($_FILES['profilePicture']['tmp_name']) > $maxFileSize) {
        $errors[] = 'Your file is too big, it must weight less than 1 Mo';
    }

    if (empty($errors)) {
        move_uploaded_file($_FILES['profilePicture']['tmp_name'], $uploadFile);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php foreach ($errors as $error): ?>
    <li><?= $error ?></li>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
    <label for="firstname">Enter your firstname</label>
    <input type="text" name="firstname" id="firstname">
    <br>
    <label for="lastname">Enter your lastname</label>
    <input type="text" name="lastname" id="lastname">
    <br>
    <label for="age">Enter your age</label>
    <input type="number" name="age" id="age">
    <br>
    <label for="imageUpload">Upload your profile picture</label>
    <input type="file" name="profilePicture" id="imageUpload">
    <br>
    <button name="send">Send</button>
</form>
<?php if ($_SERVER['REQUEST_METHOD'] === "POST"): ?>

<h1>Your profile</h1>
<div class="profile">
    <img src="<?php $img = (isset($uploadFile)) ? $uploadFile : '';
    echo $img ?>" alt="">
    <h2><?= $userFirstname ?></h2>
    <h2><?= $userLastname ?></h2>
    <h3><?= $userAge ?></h3>

    <?php endif; ?>
</div>
</body>
</html>