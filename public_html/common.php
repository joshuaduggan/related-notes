<?php
define('LOCAL', true);
define('VERSION', '0.2.1' . (LOCAL ? '-loc' : '-net'));
define('DB_NAME', 'related_notes_0_2');
define('DB_HOST', (LOCAL ? 'localhost' : 'mysql.joshuaduggan.com'));

# Requires a php ini file like this:
# db_username = jsmith
# db_password = secret
define('LOGIN_INI_FILE', '../resources/logins.ini');

$db = null;
{
  $dbLogin = parse_ini_file(LOGIN_INI_FILE);
  $db = new mysqli(
      DB_HOST, $dbLogin['db_username'], $dbLogin['db_password'], DB_NAME);
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
//   preLoginPage (if redirected to login.php or if login.php is a link on this
//                 page this is set)
session_start();

function redirectAndExit($uri) {
  header('Location: ' . $uri);
  exit();
}

function goToLoginAndExit() {
  $_SESSION['preLoginPage'] = $_SERVER['REQUEST_URI'];
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
 * Sets $_SESSION['userEmail'] to the passed userEmail, then determines whether
 * the passed password verifies with the hash in the DB, if this does it sets
 * $_SESSION['dbUserPassHash'] to the passed value. A user is considered "logged
 * in" when php session vars userEmail and dbUserPassHash match a db entry. Any
 * component of Related Notes can (must) check if a user is logged in by calling
 * authenticateSessionUser()
 */
function loginUser($userEmail, $pass) {
  global $db;
  $_SESSION['userEmail'] = $userEmail;
  // first sleep if needed until we're past the available time. Need to continue
  // testing from the DB in case one or more instances are doing the same thing.
  while (true) {
    $stmt = $db->prepare(
        'SELECT id, available_time FROM users WHERE user_email = ? LIMIT 1');
    $stmt->bind_param('s', $userEmail);
    $stmt->execute() or handleIt($stmt->error);
    $res = $stmt->get_result();
    if ($res->num_rows != 1) return false;
    $data = $res->fetch_assoc();
    if ($data['available_time'] <= time()) {
      $db->query('UPDATE users'
              . ' SET available_time = ' . (time() + PASSWORD_TEST_WAIT)
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
  }
}

function logoutUser() {
  $_SESSION['userEmail'] = $_SESSION['dbUserPassHash'] = null;
}

/**
 * Returns the hashed password for the current session user_email if there is one,
 * otherwise null.
 * This function will sleep until it's passed the current available_time in the DB
 */
function getSessionUserPassHash() {
  global $db;
  if (!isset($_SESSION['userEmail'])) return false;
  $stmt = $db->prepare('SELECT user_pass_hash FROM users WHERE user_email = ? LIMIT 1');
  $stmt->bind_param('s', $_SESSION['userEmail']);
  $stmt->execute() or handleIt($stmt->error);
  $res = $stmt->get_result();
  return ($res->num_rows != 1) ? null : $res->fetch_assoc()['user_pass_hash'];
}

/**
 * Delete the relevant records from relations tables and notes table all in
 * one go. NOTE: This will delete an entire relation if any associated note is
 * deleted meaning that future 3+ leg relations would be wiped out inapproriatly
 */
function deleteNote($id) {
  global $db;
  $Sid = (int)$id;
  $db->query(
     'CREATE TEMPORARY TABLE cores_to_delete
        SELECT rel_cores.id
          FROM rel_legs
          JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
            WHERE rel_legs.note = "' . $Sid . '"')
              or handleIt($db->error);
  $db->query(
     'DELETE rel_legs, rel_cores FROM rel_legs
        JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
          WHERE rel_cores.id IN
            (SELECT * FROM cores_to_delete)')
              or handleIt($db->error);
  $db->query(
     'DELETE FROM notes WHERE notes.id = "' . $Sid . '"')
            or handleIt($db->error);
  $db->query(
     'DROP TABLE cores_to_delete')
            or handleIt($db->error);
}

/**
 * RETURNS Nothing unless an error occured in which case a somewhat descriptive
 * message string
 */
function deleteRelation($XrelCoreId) {
  global $db;
  $Sid = (int)$XrelCoreId;
  $db->multi_query('DELETE FROM rel_cores
                      WHERE id = ' . $Sid . ';
                    DELETE FROM rel_legs
                      WHERE rel_core = ' . $Sid) or handleIt($db->error);
}

/**
 * If the relation type is of a one-many structure noteA is the root. Checks to
 * ensure the relation doesn't already exist. Does not allow a relation to
 * relate to itself.
 * RETURNS Nothing unless an error occured in which case a somewhat descriptive
 *    message string.
 */
function relateTheseById($XnoteAId, $XrelTypeId, $XnoteBId) {
  global $db;
  $SnoteAId = (int)$XnoteAId;
  $SrelTypeId = (int)$XrelTypeId;
  $SnoteBId = (int)$XnoteBId;
  
  // Notes may not be directly related to themselves.
  if ($SnoteAId == $SnoteBId) {
    return ' Notes may not be directly related to themselves. ';
  }
  
  // Check that all passed ids are valid.
  $res = $db->query('SELECT id
                     FROM notes
                     WHERE id IN (' . $SnoteAId . ', ' . $SnoteBId . ')
                     LIMIT 2') or handleIt($db->error);
  if ($res->num_rows < 2) return ' Bad id. ';
  $res = $db->query('SELECT structure
                     FROM rel_types
                     WHERE id = ' . $SrelTypeId . '
                     LIMIT 1') or handleIt($db->error);
  if ($res->num_rows < 1) return ' Bad id. ';
  $relTypeStructure = $res->fetch_assoc()['structure'];

  // Check that this relation doesn't already exist. This is done by joining the
  // relation tables together, selecting the rel_core ids and a count of
  // identical ones, where they involve the rel_type and notes that we care
  // about and returning a row where the grouped count of identical
  // rel_legs.rel_core ids are greater than 1 - which can only be the case if 2
  // legs link to the same core, which means the relationship between these
  // notes of this type already exists. (ps; or it could be a relation links to
  // a single note on both ends, which we don't test for here because that
  // shouldn't happen and would be a flaw in the DB)
  $res = $db->query(
     'SELECT rel_legs.rel_core, count(*)
        FROM rel_legs
          JOIN rel_cores
            ON rel_legs.rel_core = rel_cores.id
          JOIN rel_types
            ON rel_cores.rel_type = rel_types.id
        WHERE rel_types.id = ' . $SrelTypeId . '
        AND (rel_legs.note = ' . $SnoteAId . '
          OR rel_legs.note = ' . $SnoteBId . ')
        GROUP BY rel_legs.rel_core
        HAVING count(*) > 1')
      or handleIt($db->error);
  if ($res->num_rows > 0) return ' Relation exists, was not duplicated. ';

  // Create the relation
  $db->query(
      'INSERT INTO rel_cores (rel_type) 
         VALUES (' . $SrelTypeId . ')'
    ) or handleIt($db->error);
  $db->query(
     'INSERT INTO rel_legs (rel_core, note, role)
        VALUES
          (' .
             $db->insert_id . ', ' .
             $SnoteAId . ', ' .
             (($relTypeStructure == 'one-many') ? '"parent"' : 'NULL') .
          '), (' .
             $db->insert_id . ', ' .
             $SnoteBId . ', ' .
             (($relTypeStructure == 'one-many') ? '"child"' : 'NULL') .
          ')'
    ) or handleIt($db->error);
}

/**
 * THIS CAN BE RELIED ON TO die(), unless we're debugging and it's temporarily
 * disabled to see subsequent error reports.
 */
function handleIt($errorMessage) {
  if (LOCAL) {
    echo 'SQL Error message via handleIt: ' . $errorMessage . "\n";
    var_dump(debug_backtrace());
    die();
  } else {
    echo "Oh dear... the server has experienced a problem. Perhaps you could "
       . "try something else?\n";
    die();
  }
}

/**
 * Currently no default note functionality is available, the test db
 * implementation has the concept of a "home" parent note but at the moment
 * that's not a type understood by RN.
 * !!! MUST BE IMPLEMENTED CORRECTLY !!!
 * returns: the id of the latest note added to the DB.
 */
function getDefaultNoteId() {
  global $db;
  $res = $db->query(
      'SELECT MAX(id) AS max_id
       FROM notes') or handleIt($db->error);
  return $res->fetch_assoc()['max_id'];
}
