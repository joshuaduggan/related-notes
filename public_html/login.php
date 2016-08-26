<?php
require_once 'common.php';

$message = '';

// If this should respond to the input of this form.
if (isset($_POST['userEmail'])) {
  if (filter_var($_POST['userEmail'], FILTER_VALIDATE_EMAIL)
         && isset($_POST['userPass'])
         && strlen($_POST['userPass']) <= MAX_USER_PASS_LENGTH
         && strlen($_POST['userPass']) > MIN_USER_PASS_LENGTH) {
    loginUser($_POST['userEmail'], $_POST['userPass']); // attempt login
    if (authenticateSessionUser()) { // check if logged in
      setcookie('userEmailSuccessful', $_POST['userEmail'],
          time() + THIRTY_DAYS_TIME);
      redirectAndExit($_SESSION['SlatestNoteView']);
    } else {
      $message .= ' Unable to log in, are email and password correct? ';
    }
  } else {
    $message .= ' There is an issue with the submitted data. ';
  }
}

?><!doctype html>
<html><head>
<title>Related Notes - Login</title>
<style type="text/css">
form {
  display:inline;
}
</style>
</head>
<body>
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
  <input type="submit" value="Login" />
</form>
<form method='post' action="<?php echo $_SESSION['SlatestNoteView']; ?>">
  <input type="submit" value="Cancel" />
</form>
<div id="messageBlock">
  <?php echo $message; ?>
</div>
</body>
</html>