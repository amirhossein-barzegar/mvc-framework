
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    This is index view
    <br/>
    <form action="/register" method="post">
        <input type="hidden" name="csrf_field" value="<?php echo $_SESSION['csrf_field'] = md5(uniqid(mt_rand(), true)); ?>">
        <input type="text" name="name" value="<?php echo $_SESSION['requests']['name'] ?? '' ?>">
        <input type="text" name="lastname" value="<?php echo $_SESSION['requests']['lastname'] ?? '' ?>">
        <button>submit form</button>
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
    <a href="<?php echo '/register';?>">Register</a>


    <form method="POST" action="/user/1234234/amirhossein">
        <input type="hidden" name="csrf_field" value="<?php echo $_SESSION['csrf_field'] = md5(uniqid(mt_rand(), true)); ?>">
        <button>Post User</button>
    </form>
</body>
</html>