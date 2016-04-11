<?php
require_once 'common.php';

if (!authenticateSessionUser()) goToLoginAndExit();
// If we're still executing here we know we have an authenticated session user.

// VALIDATE THE $_POST (get stuff is after all this)... {{{
$postProblemMsg = '';
$postAttempted = array_key_exists('action', $_POST)
              && ($_POST['action'] == 'new' || $_POST['action'] == 'edit');
if ($postOk = $postAttempted) {
  $postOk = array_key_exists("nameText", $_POST)
         && strlen($_POST["nameText"]) <= MAX_NAME_LENGTH
         && array_key_exists("descriptionText", $_POST)
         && strlen($_POST["descriptionText"]) <= MAX_DESCRIPTION_LENGTH;
}
if ($postOk) {
  // Prior to displaying the DB contents process the posted note and
  // create or edit it.
  $id = array_key_exists("id", $_POST) ? ((integer)$_POST["id"]) : "";
  $isNew = array_key_exists("action", $_POST) && $_POST["action"] == "new";
  $isCategory = array_key_exists("isCategory", $_POST)
             && $_POST["isCategory"] == "on";
  $seeAlsos = array();
}
// Build an array of "see_also"s
while ($postOk && (list($key, $value) = each($_POST))) {
  if ($value != 'on' || !preg_match('/^[1-9][0-9]*seeAlso$/', $key)) continue;
  $curId = (integer)$key; // PHP ignores non digit trailing string chars.
  // check that the passed seeAlso is a valid id of an active note and not
  // this note and not already in the array.
  $postOk = !in_array($curId, $seeAlsos)
  // if this has a specified id (it's an edit which we test for later) ensure
  // it's not this id because a note can't "see also" itself. If an id was not
  // posted $id will be set to '' which is fine for the purposes of this test.
          && $curId != $id
  // ensure this $curId is a valid note.
          && $db->query("SELECT id FROM notes WHERE id=$curId")->num_rows == 1;
  $seeAlsos[] = $curId;
}
reset($_POST);
// If it isn't new assume it's edit.
if ($postOk && !$isNew) {
  // Check that the note to edit already exists, also confirming that the id
  // is valid.
  $postOk = $db->query("SELECT id FROM notes WHERE id=$id")->num_rows == 1;
}
// Ensure the submitted name isn't a different note already in the db.
if ($postOk) {
  $selStr = $isNew ? 'SELECT id FROM notes WHERE name = ? LIMIT 1'
                   : 'SELECT id FROM notes WHERE name = ? AND id <> ' . $id
                     . ' LIMIT 1';
  $stmt = $db->prepare($selStr);
  $stmt->bind_param('s', $_POST['nameText']);
  $stmt->execute() or handleIt($stmt->error);
  $stmt->store_result();
  $postOk = $stmt->num_rows == 0;
  if (!$postOk) {
    $postProblemMsg .= "The name submitted is already used by another note.\n";
  }
}
// }}} DONE VALIDATING THE $_POST.

if ($postAttempted) {
  // send the new/edit to the db
  if ($postOk) {
    if (!$isNew) deleteNote($id);
    
    $stmt = $db->prepare("INSERT INTO notes (id, is_category, name, description) "
                       . "VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id, $isCategory, $_POST["nameText"],
                      $_POST["descriptionText"]);
    $stmt->execute() or handleIt($stmt->error);
    if ($isNew) $id = $db->insert_id; // up to now if isNew = true, id = ""
    foreach ($seeAlsos as $saId) {
      $db->query("INSERT INTO relationships (note_id) VALUES ($id)")
         or handleIt($db->error);
      $relationshipId = $db->insert_id;
      $db->query("UPDATE relationships SET relationship_id = $relationshipId "
                 . "WHERE id = $relationshipId") or handleIt($db->error);
      $db->query("INSERT INTO relationships (relationship_id, note_id) "
                 . "VALUES ($relationshipId, $saId)") or handleIt($db->error);
    }
    // SUCCESS! go back to the main index and show the results
    redirectAndExit('./?' . ($isCategory ? 'cat=' : 'note=') . $id);
  
  // There was an issue with the posted data.
  } else {
    $postProblemMsg .= "There was a problem processing the submitted data.\n"
                     . "Please check it's validity and try again.\n";
  }
}
// DONE PROCESSING post submission from this creating or editing. If we've reached
// this point their either wasn't a post or the was a recoverable error.

if (array_key_exists("action", $_GET) && $_GET["action"] == 'delete') {
  if (array_key_exists("id", $_GET)) deleteNote((integer)$_GET['id']);
  redirectAndExit('./');
}

$isNew = !array_key_exists('id', $_GET);
$Sid = -1;

$UTnameText = '';
$UTdescriptionText = '';
$UTisCategory = false;

if (!$isNew) {
  $Sid = (integer)$_GET['id'];
  $stmt = $db->prepare(
      'SELECT name,description,is_category FROM notes WHERE id = ?');
  $stmt->bind_param('i', $Sid);
  $stmt->execute() or handleIt($stmt->error);
  $stmt->store_result();
  $stmt->bind_result($UTnameText, $UTdescriptionText, $UTisCategory);
  $stmt->fetch();
  // if the requested note to edit isn't in the DB treat this as a "new".
  $isNew = $stmt->num_rows == 0;
}

function buildSeeAlsoCheckboxes($title, $categoriesOnly) {
  global $db;
  global $Sid;
  global $isNew;
  $seeAlsoIds = $isNew ? [] : getSeeAlsoIds($Sid);
  echo "<br /><div>$title<br />\n";
  $res = $db->query('SELECT id,name FROM notes WHERE is_category = '
                  . $categoriesOnly) or handleIt($db->error);
  $firstSA = true;
  while ($curRow = $res->fetch_assoc()) {
    $ScurName = htmlspecialchars($curRow['name']);
    $curId = $curRow['id'];
    if ($curId == $Sid) continue;
    if (!$firstSA) echo ", ";
    else $firstSA = false;
    echo "<label for='{$curId}seeAlso'>$ScurName</label><input type='checkbox'"
       . " name='{$curId}seeAlso' "
       . (in_array($curId, $seeAlsoIds) ? "checked />" : "/>");
  }
  echo "</div><br />\n";
}
?>
<!doctype html>
<html>
<head>
<title>Related Notes - <?php echo $isNew ? 'New' : 'Edit'; ?></title>
<style type="text/css">
form {
  display:inline;
}
</style>
</head>
<body>
<h3>Related Notes - <?php echo $isNew ? 'New' : 'Edit'; ?></h3>
<div id="dataProblemMsg">
<?php if ($postProblemMsg != '') echo "<pre>$postProblemMsg</pre>"; ?></div>
<form method="post">
<?php if (!$isNew): ?>
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="id" value="<?php echo $Sid ?>" />
<?php else: ?>
<input type="hidden" name="action" value="new" />
<?php endif; ?>
<label for="nameText">Name</label><br />
<input type="text" name="nameText" size="80" value="<?php echo htmlspecialchars($UTnameText); ?>" /><br />
<?php buildSeeAlsoCheckboxes("Categories", 1); ?>
<label for="descriptionText">Description</label><br />
<textarea name="descriptionText" cols="80" rows="12"><?php echo htmlspecialchars($UTdescriptionText); ?></textarea><br />
<label for="isCategory">Is Category</label>
<input type="checkbox" name="isCategory" <?php if ($UTisCategory) echo 'checked '; ?>/><br />
<?php buildSeeAlsoCheckboxes("See Also;", 0); ?>
<input type="submit" value="Save" />
<input type="reset" />
</form>
<?php if (!$isNew): ?>
<form method="get" onsubmit="return confirm('Are you sure you want to delete this note?');">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="<?php echo $Sid; ?>" />
<input type="submit" value="Delete" />
</form>
<?php endif; ?>
<form action="./#<?php echo $Sid; ?>note">
<input type="submit" value="Cancel" />
</form>
<?php echoFooter(); ?>
</body>
</html>
