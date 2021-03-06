<?php
define('LOCAL', true);
define('VERSION', '0.1.10' . (LOCAL ? '-loc' : '-net'));
define('NET_DB_ADDRESS', 'mysql.joshuaduggan.com');

# Requires a php ini file like this:
# sql_db_username = jsmith
# sql_db_password = secret
define('LOGIN_INI_FILE', '../resources/logins.ini');

$db = null;
{
  $dbLogin = parse_ini_file(LOGIN_INI_FILE);
  $db = new mysqli(
      (LOCAL ? 'localhost' : NET_DB_ADDRESS),
      $dbLogin['sql_db_username'],
      $dbLogin['sql_db_password'],
      'relatednotes');
  unset($dbLogin);
}
if ($db->connect_error) handleIt($db->connect_error);

// 255 is the SQL VARCHAR max (db currently set to 101)
define('MAX_NAME_LENGTH', 100);
// 65535 is the SQL TEXT max
define('MAX_DESCRIPTION_LENGTH', 65000);
define('MAX_USER_PASS_LENGTH', 60);
define('MIN_USER_PASS_LENGTH', 1);
define('THIRTY_DAYS_TIME', 60 * 60 * 24 * 30);
define('PASSWORD_TEST_WAIT', 5);

// Session vars:
//   userEmail
//   dbUserPassHash (the pass hash that was in the db when user logged in)
//   redirectedFrom (if redirected to login.php or another intermediary page)
session_start();

function redirectAndExit($uri) {
  header('Location: ' . $uri);
  exit();
}

function goToLoginAndExit() {
  $_SESSION['redirectedFrom'] = $_SERVER['REQUEST_URI'];
  redirectAndExit('login.php');
}

/**
 * Checks whether the current _SESSION['dbUserPassHash'] which is set
 * during the initial verification still matches what's in the DB. This
 * ensures the user hasn't been deleted or the password updated since
 * initial login.
 */
function authenticateSessionUser() {
  return isset($_SESSION['dbUserPassHash'])
      && $_SESSION['dbUserPassHash'] == getSessionUserPassHash();
}

/**
 * Determines whether the passed password verifies with the hash in the DB.
 */
function authenticateUser($pass) {
  global $db;
  // first sleep if needed until we're past the available time. Need to continue
  // testing from the DB in case one or more instances are doing the same thing.
  while (true) {
    $stmt = $db->prepare(
        'SELECT id, availableTime FROM users WHERE userEmail = ? LIMIT 1');
    $stmt->bind_param('s', $_SESSION['userEmail']);
    $stmt->execute() or handleIt($stmt->error);
    $res = $stmt->get_result();
    if ($res->num_rows != 1) return false;
    $data = $res->fetch_assoc();
    if ($data['availableTime'] <= time()) {
      $db->query('UPDATE users'
              . ' SET availableTime = ' . (time() + PASSWORD_TEST_WAIT)
              . ' WHERE id = ' . $data['id']) or handleIt($db->error);
      break;
    }
    sleep(PASSWORD_TEST_WAIT);
  }
  // authenticate the user.
  $dbUserPassHash = getSessionUserPassHash();
  if (!$dbUserPassHash) return false;
  // can't use password_verify because dreamhost is currently on PHP 5.4
  // if (password_verify($pass, $dbUserPassHash)) {
  if (crypt($pass, $dbUserPassHash) === $dbUserPassHash) {
    $_SESSION['dbUserPassHash'] = $dbUserPassHash;
    return true;
  } else {
    return false;
  }
}

/**
 * Returns the hashed password for the current session userEmail if there is one,
 * otherwise null.
 * This function will sleep until it's passed the current availableTime in the DB
 */
function getSessionUserPassHash() {
  global $db;
  if (!isset($_SESSION['userEmail'])) return false;
  $stmt = $db->prepare('SELECT userPassHash FROM users WHERE userEmail = ? LIMIT 1');
  $stmt->bind_param('s', $_SESSION['userEmail']);
  $stmt->execute() or handleIt($stmt->error);
  $res = $stmt->get_result();
  return ($res->num_rows != 1) ? null : $res->fetch_assoc()['userPassHash'];
}

function deleteNote($id) {
  global $db;
  $db->query("DELETE FROM notes WHERE id = " . ((integer)$id))
    or handleIt($db->error);
  // need to create a temp table to delete while selecting from the same table
  $db->query(
      "DELETE FROM relationships WHERE relationship_id IN ("
      . "SELECT relationship_id FROM ("
        . "SELECT * from relationships WHERE note_id = " . ((integer)$id)
      . ") AS temp"
    . ")")
    or handleIt($db->error);
}

// Build an array of this note's relations, used to check the appropriate
// categories and see alsos.
function getSeeAlsoIds($id) {
  global $db;
  $seeAlsoIds = [];
  $res = $db->query(
      "SELECT note_id FROM relationships WHERE relationship_id IN ("
    . "SELECT relationship_id FROM relationships WHERE note_id = "
    . ((integer)$id) . ") AND note_id <> " . ((integer)$id))
    or handleIt($db->error);
  while ($curRow = $res->fetch_row()) $seeAlsoIds[] = $curRow[0];
  return $seeAlsoIds;
}

function echoStartOfDoc($title) {
  echo <<< EOT
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
EOT;
  echo "<title>$title</title>\n";
  echo <<< EOT
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="main.css" />
<link rel="stylesheet" href="relatednotes.css" />
</head>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js">
</script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<body>
<div id='main' class='container'>
EOT;
}

function echoHeader() {
  echo <<< EOT
<header>
<figure class="jd-icon-light jd-icon-light-header"></figure>
<div>
<h1><a href="./">Related Notes</a></h1>
<h3>Note app used to track web dev tech</h3>
</div>
</header>
EOT;
}

function echoFooter() {
  echo "<footer><p>Related Notes " . VERSION . " created by "
     . "<a href='http://joshuaduggan.com/'>Joshua Duggan</a></p>\n"
     . "<figure class='jd-icon-light jd-icon-light-footer'></figure></footer>\n";
}

function echoEndOfDoc() {
  echo <<< EOT
<script  src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="../bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
</div>
</body>
</html>
EOT;
}

/**
 * THIS CAN BE RELIED ON TO die(), unless we're debugging and it's temporarily
 * disabled to see subsequent error reports.
 */
function handleIt($errorMessage) {
  if (LOCAL) {
    echo $errorMessage . "\n";
    die();
  } else {
    echo "Oh dear... the server has experienced a problem. Perhaps you could "
       . "try something else?\n";
    die();
  }
}
