
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
</head>
<body>
    This is index view
    <br/>
    <form action="/user/1/amirhossein" method="post">
        <input type="hidden" name="csrf_field" value="<?php echo $_SESSION['csrf_field'] = md5(uniqid(mt_rand(), true)); ?>">
        <label>
            <input type="text" name="name" value="<?php echo $_SESSION['requests']['name'] ?? '' ?>">
        </label>
        <label>
            <input type="text" name="lastname" value="<?php echo $_SESSION['requests']['lastname'] ?? '' ?>">
        </label>
        <button type="submit">submit form</button>
        <?php if (isset($_SESSION['errors']['name'])): ?>
            <?php if ($_SESSION['errors']['name']): ?>
                <p style="color:red"><?php echo $_SESSION['errors']['name']; ?></p>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['errors']['lastname'])): ?>
            <?php if ($_SESSION['errors']['lastname']): ?>
                <p style="color:red"><?php echo $_SESSION['errors']['lastname']; ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </form>
</body>
</html>