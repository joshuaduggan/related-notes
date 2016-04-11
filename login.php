<?php
require_once 'common.php';

$postOk = true;
$loginPosted = isset($_POST['userEmail']);

$message = "";

// If this should respond to the input of this form.
if ($loginPosted) {
  $postOk = filter_var($_POST['userEmail'], FILTER_VALIDATE_EMAIL)
         && isset($_POST['userPass'])
         && strlen($_POST['userPass']) <= MAX_USER_PASS_LENGTH
         && strlen($_POST['userPass']) > MIN_USER_PASS_LENGTH;
  if ($postOk) {
    $_SESSION['userEmail'] = $_POST['userEmail'];
    if (authenticateUser($_POST['userPass'])) {
      setcookie('userEmailSuccessful', $_POST['userEmail'], time() + THIRTY_DAYS_TIME);
      redirectAndExit($_SESSION['redirectedFrom']);
    } else {
      $message .= "Unable to log in, are email and password correct?";
    }
  } else {
    $message .= "There was an error with the submitted data.";
  }
}

// Display a new form.
?>
<html><head><title>Related Notes - Login</title>
<style type="text/css">
form {
  display:inline;
}
</style>
</head><body>
<form method="post">
<label for="userEmail">User Email: </label>
<input type="text" name="userEmail" <?php
if (isset($_COOKIE['userEmailSuccessful'])) {
  echo 'value="' . htmlspecialchars($_COOKIE['userEmailSuccessful']) . '" ';
} else {
  echo 'autofocus ';
}
?>/><br />
<label for="userPass">User Password: </label>
<input type="password" name="userPass" <?php
if (isset($_COOKIE['userEmailSuccessful'])) echo 'autofocus ';
?>/><br />
<input type="submit" value="Login" /></form>
<form action="./"><input type="submit" value="Cancel" /></form>
<div id="messageBlock"><?php echo $message; ?></div>
</body></html>
