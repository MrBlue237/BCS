
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>
</head>

<body>
<form class="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"></form>
    <label>
        <select> <option name="student">student</option>
            <option name="employer">employer</option>
        </select>
    </label>


<?php $role = "student";
if ($role == "student")
?>

<form class="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label><input type="text" name="username" placeholder="Full Name" required></label>
    <label><input type="text" name="username" placeholder="Email" required></label>
    <label><input type="text" name="username" placeholder="Phone Number" required></label>
    <label><input type="text" name="username" placeholder="Postal Address" required></label>
    <label><input type="text" name="username" placeholder="CV file" required></label>

</form>
<?php elseif ($role == "employer")?>
<?php endif;?>

</body>
</html>

