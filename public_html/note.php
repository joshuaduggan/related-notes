<?php
require_once 'common.php';

/**
 * GENERAL:
 * 'X' and 'S' prefixes are used on variables that are identified as being
 * exploitable or safe respectively. If there's any possibility of PHP injection
 * or any other attack via passed data the var should be X, it is changed to S
 * if there is some reason that it certainly is not exploitable. A 'S' val may
 * be used for an 'X' function or var because Xs are always sanitized. But
 * never use an X for something that expects an S!
 */
 
/**
 * The possible modes of this page, determined by the submitted parameters
 * 
 * SHOW_NOTE:
 *   GET
 *     [mode='show_note'] (not required, default behavior)
 *     note=(note id)
 * TEXT_EDITOR:
 *   GET
 *     mode='text_editor'
 *     note=(note id)
 * REL_EDITOR:
 *   GET
 *     mode='rel_editor'
 *     note=(note id)
 * LOGOUT: (then SHOW_NOTE)
 *   GET
 *     mode='logout'
 *     note=(note id)
 * DELETE_NOTE: (then SHOW_NOTE)
 *   GET
 *     mode='delete_note'
 *     note=(note id)
 *     note_to_delete=(note id)
 * DELETE_REL: (then REL_EDITOR)
 *   GET
 *     mode='delete_rel'
 *     note=(note id)
 *     rel_core_id=(rel core id)
 * SAVE_REL_EDIT: (then REL_EDITOR)
 *   GET
 *     mode='save_rel_edit'
 *     note=(note id)
 *     new_rel_note=(note id)
 *     new_rel_type=(rel type id)
 *    [is_this_note_parent='true']
 * SAVE_TEXT_EDIT: (then SHOW_NOTE)
 *   POST
 *     mode='save_text_edit'
 *     note=(note id)
 *     nameText=(new text)
 *     descriptionText=(new text)
 * CLONE_EDITOR:
 *   GET
 *     mode='clone_editor'
 *     note=(note id) // the id of the note to be cloned
 * SAVE_CLONE_EDIT: (then SHOW_NOTE)
 *   POST
 *     mode='save_clone_edit'
 *     cloned_note=(note id)
 *     nameText=(new text)
 *     descriptionText=(new text)
 * SAVE_NEW: (then SHOW_NOTE)
 *   POST
 *     mode='save_new'
 *     nameText=(new text)
 *     descriptionText=(new text)
 */
const SHOW_NOTE = 'show_note';
const TEXT_EDITOR = 'text_editor';
const SAVE_TEXT_EDIT = 'save_text_edit';
const CLONE_EDITOR = 'clone_editor';
const SAVE_CLONE_EDIT = 'save_clone_edit';
const REL_EDITOR = 'rel_editor';
const SAVE_REL_EDIT = 'save_rel_edit';
const DELETE_REL = 'delete_rel';
const LOGOUT = 'logout';
const DELETE_NOTE = 'delete_note';
const SAVE_NEW = 'save_new';
// SET ONLY ONCE in the following ifelif/switch statement
$mode = -1;
// set only if this page isn't to display directly but to optionally show a
// message then to forward to itself to display anew.
$nextMode = -1;
$errorMsg = null;

// figure out what mode we are in then validate the params for that mode
if (array_key_exists('mode', $_REQUEST)) {
  switch ($_REQUEST['mode']) {
    case TEXT_EDITOR:
    case SAVE_TEXT_EDIT:
    case CLONE_EDITOR:
    case SAVE_CLONE_EDIT:
    case REL_EDITOR:
    case SAVE_REL_EDIT:
    case DELETE_REL:
    case LOGOUT:
    case DELETE_NOTE:
    case SAVE_NEW:
      $mode = $_REQUEST['mode'];
      break;
    default: // should only get here if mode param is wrong
      $mode = SHOW_NOTE;
  }
} else $mode = SHOW_NOTE;

$SnoteId = (array_key_exists('note', $_REQUEST))
             ? (integer)$_REQUEST['note']
             : getDefaultNoteId();

// Put mode specific init/pre display code here.
switch ($mode) {
  case LOGOUT:
    logoutUser();
    $nextMode = SHOW_NOTE;
    break;
  case DELETE_NOTE:
    ////////////////////////////// !!! incomplete !!! //////////////////////////
    $nextMode = SHOW_NOTE;
    break;
  case SAVE_REL_EDIT:
  case DELETE_REL:
    if (authenticateSessionUser()) {
      $errorMsg = ($mode == SAVE_REL_EDIT) ?
          saveGettedRelToDb() :
          deleteRelFromDb();
    } else $errorMsg = ' You are not logged in, change not saved! ';
    $nextMode = ($errorMsg) ? SHOW_NOTE : REL_EDITOR;
    break;
  case SAVE_CLONE_EDIT:
  case SAVE_TEXT_EDIT:
  case SAVE_NEW:
    if (authenticateSessionUser()) {
      $errorMsg = savePostedNoteToDb($mode);
      if (!$errorMsg && $mode == SAVE_CLONE_EDIT) {
        // clone the relations
        ////////////////////////////////////////////////////////////////////////
      }
      $nextMode = ($errorMsg) ? TEXT_EDITOR : SHOW_NOTE;
    } else {
      $errorMsg = ' You are not logged in, edit not saved! ';
      $nextMode = SHOW_NOTE;
    }
    break;
}

// The page that should be returned to after going to a intermediary page like
// login.
$_SESSION['SlatestNoteView'] = $_SERVER['SCRIPT_NAME'] . '?note=' . $SnoteId;

$nextUri = null;
if ($nextMode == SHOW_NOTE ||
    $nextMode == TEXT_EDITOR ||
    $nextMode == CLONE_EDITOR ||
    $nextMode == REL_EDITOR) {
  $nextUri = $_SESSION['SlatestNoteView'];
  if ($nextMode == TEXT_EDITOR) $nextUri .= '&mode=' . TEXT_EDITOR;
  if ($nextMode == CLONE_EDITOR) $nextUri .= '&mode=' . CLONE_EDITOR;
  if ($nextMode == REL_EDITOR) $nextUri .= '&mode=' . REL_EDITOR;
  if (!$errorMsg) redirectAndExit($nextUri);
  // else see body below...
}

?><!doctype html>
<html>
<head>
<title>Related Notes - Make Relations</title>
<link rel="stylesheet" href="./note.css" />
</head>
<body>
<div class="gd-wrap">
<?php if (!$errorMsg): // no error show this page! ?>

  <div class='gd-box gd-header'>
    <h2 class='rn-title'>Related Notes - (name of data/site)</h2>
    <p class='rn-user-status'>
      <?php if (authenticateSessionUser()) : ?>
        Logged in as <?php echo htmlspecialchars($_SESSION['userEmail']) ?>
        <a class='rn-link-butt' href='<?php
            echo $_SESSION['SlatestNoteView'] . '&mode=logout'; ?>'>Logout</a>
      <?php else : ?>
        Not logged in <a class='rn-link-butt' href='./login.php'>Login</a>
      <?php endif; ?>
    </p>
  </div>

  <div class="gd-box gd-main">
    <?php showNote($SnoteId); ?>
  </div>

  <div class="gd-box gd-sidebar">
    <?php showParentAndSiblings($SnoteId); ?>
    <?php showAssociates($SnoteId); ?>
  </div>
  
<?php else: // show the error and provide continue to this again ?>

  <p><?php echo $errorMsg; ?></p>
  <p><a href='<?php echo $nextUri; ?>'>Continue</a></p>
  
<?php endif; ?>
</div>
</body>
</html>

<?php
/**
 * Shows just names of sibling notes of this.
 */
function showParentAndSiblings($SnoteId) {
  global $db;
  
  // Get the parent nodes of this along with each relation type
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
            rel_types.id AS relType, rel_cores.id AS relCoreId
        FROM rel_legs
        JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
        JOIN rel_types ON rel_cores.rel_type = rel_types.id
        JOIN notes ON rel_legs.note = notes.id
        WHERE rel_cores.id IN
          (SELECT rel_cores.id
            FROM rel_legs
            JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
            JOIN rel_types ON rel_cores.rel_type = rel_types.id
            WHERE rel_legs.note = "' . $SnoteId . '"
              AND rel_types.structure = "one-many"
              AND rel_legs.role = "child"
            ORDER BY rel_types.id)
          AND rel_types.structure = "one-many"
          AND rel_legs.role = "parent"') or handleIt($db->error);
        
  // For each of this' parents show all their child notes who's relation to that
  // parent is of the same type as this'.
  ?>
  
  <div class='sibling-notes'>
    <?php while ($parRelAssoc = $res->fetch_assoc()) : ?>
      <?php $subRes = $db->query(
           'SELECT notes.id AS noteId, notes.name AS noteName
              FROM rel_legs
              JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
              JOIN rel_types ON rel_cores.rel_type = rel_types.id
              JOIN notes ON rel_legs.note = notes.id
              WHERE rel_cores.id IN
                (SELECT rel_cores.id
                  FROM rel_legs
                  JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
                  JOIN rel_types ON rel_cores.rel_type = rel_types.id
                  WHERE rel_legs.note = "' . $parRelAssoc['noteId'] . '"
                    AND rel_types.id = "' . $parRelAssoc['relType'] . '"
                    AND rel_legs.role = "parent"
                  ORDER BY rel_types.id)
                AND rel_types.structure = "one-many"
                AND rel_legs.role = "child"
                AND rel_legs.note <> "' . $SnoteId . '"')
                  or handleIt($db->error); ?>
      <div class='type-parent'>
        <?php printShortNote($parRelAssoc['noteId'], $parRelAssoc['noteName'],
                $parRelAssoc['relCoreId']); ?>
      </div>
      
      <?php if ($subRes->num_rows < 1) continue; ?>
      
      <div class='common-parent-siblings'>
        <?php
        while ($siblingAssoc = $subRes->fetch_assoc()) {
          printShortNote($siblingAssoc['noteId'], $siblingAssoc['noteName']);
        }
        ?>
      </div>
    <?php endwhile; ?>
  </div>
  
  <?php
}

function showAssociates($SnoteId) {
  global $db;
  
  // Get the associate nodes (any one-one relation) of this along with each
  // relation type
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
            rel_types.id AS relType, rel_cores.id AS relCoreId
        FROM rel_legs
        JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
        JOIN rel_types ON rel_cores.rel_type = rel_types.id
        JOIN notes ON rel_legs.note = notes.id
        WHERE rel_cores.id IN
          (SELECT rel_cores.id
            FROM rel_legs
            JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
            JOIN rel_types ON rel_cores.rel_type = rel_types.id
            WHERE rel_legs.note = "' . $SnoteId . '"
              AND rel_types.structure = "one-one"
            ORDER BY rel_types.id)
          AND notes.id <> "' . $SnoteId . '"')
              or handleIt($db->error);
  ?>
  
  <div class='associate-notes'>
  <?php while ($associateAssoc = $res->fetch_assoc()) : ?>
    <?php printShortNote($associateAssoc['noteId'],
        $associateAssoc['noteName'], $associateAssoc['relCoreId']); ?>
  <?php endwhile; ?>
  </div>
  
  <?php
}

/**
 * Shows the passed note id as well as any child notes, which are grouped by
 * relation to this note.
 */
function showNote($SnoteId) {
  global $db;
  global $mode;
  ?>
  <div class='main-note'>
  <?php
  if ($mode == CLONE_EDITOR) {
    printFullNote($SnoteId, '', true, '');
  } else {
    $mainAssoc = $db->query(
       'SELECT name, description
          FROM notes
          WHERE id = "' . $SnoteId . '"')->fetch_assoc()
              or handleIt($db->error);
    printFullNote($SnoteId, $mainAssoc['name'], true,
        $mainAssoc['description']);
  }
  
  // get any child relations that this has as well as the name of their nodes
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
             notes.description AS noteDesc, rel_types.name AS relTypeName,
             rel_cores.id AS relCoreId
        FROM rel_legs
        JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
        JOIN rel_types ON rel_cores.rel_type = rel_types.id
        JOIN notes ON rel_legs.note = notes.id
        WHERE rel_cores.id IN
          (SELECT rel_cores.id
            FROM rel_legs
            JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
            JOIN rel_types ON rel_cores.rel_type = rel_types.id
            WHERE rel_legs.note = "' . $SnoteId . '"
              AND rel_types.structure = "one-many"
              AND rel_legs.role = "parent"
            ORDER BY rel_types.id)
          AND rel_types.structure = "one-many"
          AND rel_legs.role = "child"') or handleIt($db->error);

  $curRelationName = null;
  while ($childAssoc = $res->fetch_assoc()) {
    if ($childAssoc['relTypeName'] != $curRelationName) {
      ?>
      
      <?php if ($curRelationName != null) : ?></div><?php endif; ?>
      <?php $curRelationName = $childAssoc['relTypeName']; ?>
      <div class='child-notes'>
        <h3><?php echo htmlspecialchars($curRelationName); ?></h3>
        
      <?php
    }
    printFullNote($childAssoc['noteId'], $childAssoc['noteName'], false,
        $childAssoc['noteDesc'], $childAssoc['relCoreId']);
  }
  // If any children where echoed we have to end the last relation name div
  ?>
  
  <?php if ($curRelationName != null) : ?></div><?php endif; ?>
  </div>
  
  <?php
}

function printFullNote($Sid, $Xname, $isMain, $Xdescription = null,
            $SrelCoreId = null) {
  global $mode;
  global $db;
  $isAuthenticated = authenticateSessionUser();
  ?>
  <?php if ($isMain && $isAuthenticated &&
            ($mode == TEXT_EDITOR || $mode == CLONE_EDITOR)): ?>
    <?php $isC = $mode == CLONE_EDITOR; ?>
    <form method='post' action='<?php
          // this is required because with the change to post old get params
          // arn't stripped if the action is left empty or referring to self.
          echo $_SERVER['SCRIPT_NAME']; ?>'>
      <input type="hidden" name="mode" value="<?php
          echo $isC ? SAVE_CLONE_EDIT : SAVE_TEXT_EDIT; ?>" />
      <input type="hidden" name="note" value="<?php echo $Sid; ?>" />
      <input type="text" name="name" size="80" value="<?php
          echo ($Xname) ? htmlspecialchars($Xname) : ''; ?>" />
      <textarea name="description" cols="80" rows="12"><?php
          echo ($Xdescription) ? htmlspecialchars($Xdescription) : '';
          ?></textarea>
      <br />
      <input type="submit" />
      <input type="reset" />
    </form>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
      <input type="hidden" name="note" value="<?php echo $Sid; ?>" />
      <input type="submit" value="Cancel" />
    </form>
    <?php if (!$isC): ?>
      <form onsubmit="return confirm('Really delete this note?');">
        <input type="hidden" name="mode" value="<?php echo DELETE_NOTE; ?>" />
        <input type="hidden" name="note" value="<?php //?????????????????// ?>" />
        <input type="hidden" name="note_to_delete" value="<?php echo $Sid; ?>" />
        <input type="submit" value="Delete" />
      </form>
    <?php endif; ?>

  <?php else: ?>
    <h3>
      <a href="<?php echo $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid; ?>">
        <?php echo htmlspecialchars($Xname); ?>
      </a>
      <?php if ($isAuthenticated && !$isMain && $mode == REL_EDITOR) : ?>
        <?php printRelDeleteForm($SrelCoreId); ?>
      <?php endif; ?>
    </h3>
    <?php if ($isAuthenticated && $isMain && $mode != REL_EDITOR): ?>
      <a class='rn-mod-butt rn-link-butt'href='<?php echo
          $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid . '&mode=' . TEXT_EDITOR;
          ?>'>Edit</a>
      <a class='rn-mod-butt rn-link-butt'href='<?php echo
          $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid . '&mode=' . REL_EDITOR;
          ?>'>Relate</a>
      <a class='rn-mod-butt rn-link-butt'href='<?php echo
          $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid . '&mode=' . CLONE_EDITOR;
          ?>'>Clone</a>
    <?php endif; ?>
    <?php if ($Xdescription): ?>
      <p><?php echo htmlspecialchars($Xdescription); ?></p>
    <?php endif; ?>

    <?php if ($isMain && $mode == REL_EDITOR && $isAuthenticated): ?>
      <div>
      <form>
        <input type="hidden" name="mode" value="<?php echo SAVE_REL_EDIT; ?>" />
        <input type="hidden" name="note" value="<?php echo $Sid; ?>" />
        <label for="rel_note">Note to relate to this:</label>
        <select name="new_rel_note">
          <?php $res = $db->query('SELECT name, id
                             FROM notes
                             WHERE id <> "' . $Sid . '"
                             ORDER BY name') or handleIt($db->error); ?>
          <option></option>
          <?php while ($row = $res->fetch_assoc()) : ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo htmlspecialchars($row['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
        <br />
        <label name="new_rel_type">Type of relation:</label>
        <select name="new_rel_type">
          <?php $res = $db->query('SELECT name, id
                             FROM rel_types
                             ORDER BY name') or handleIt($db->error); ?>
          <option></option>
          <?php while ($row = $res->fetch_assoc()) : ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo htmlspecialchars($row['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
        <br />
        <input type="checkbox" name="is_this_note_parent" value="true" />
        <label for="is_edited_note_parent">
          The parent of this relationship is
          <?php echo htmlspecialchars($Xname); ?>.
        </label>
        <br />
        <input type="submit" />
        <input type="reset" />
      </form>
      <form>
        <input type="hidden" name="note" value="<?php echo $Sid; ?>" />
        <input type="submit" value="Cancel" />
      </form>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php
}

function printShortNote($Sid, $Xname, $SrelCoreId = null) {
  global $mode;
  $isAuthenticated = authenticateSessionUser();
  ?>
  <div>
    <a href="<?php echo $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid; ?>">
      <?php echo htmlspecialchars($Xname); ?>
    </a>
    <?php if ($isAuthenticated && $mode == REL_EDITOR && $SrelCoreId) : ?>
      <?php printRelDeleteForm($SrelCoreId); ?>
    <?php endif; ?>
  </div>
  <?php
}

function printRelDeleteForm($SrelCoreId) {
  global $SnoteId;
  ?>
  <form>
    <input type="hidden" name="mode" value="<?php echo DELETE_REL; ?>" />
    <input type="hidden" name="note" value="<?php echo $SnoteId; ?>" />
    <input type="hidden" name="rel_core_id" value="<?php
        echo $SrelCoreId; ?>" />
    <input type="submit" value="X" />
  </form>
  <?php
}

/**
 * Requires $_GET with the following:
 *     new_rel_note=(note id)
 *     new_rel_type=(rel type id)
 *     is_this_note_parent='true' || ''
 * Returns: String problem message if somethings wrong with input data and
 * nothing was written
 */
function saveGettedRelToDb() {
  global $SnoteId;
  if (!array_key_exists('new_rel_type', $_GET) ||
      !array_key_exists('new_rel_note', $_GET)) {
    return ' Bad form data. ';
  }
  if ($_GET['is_this_note_parent'] == 'true') {
    return relateTheseById(
        $SnoteId, $_GET['new_rel_type'], $_GET['new_rel_note']);
  } else {
    return relateTheseById(
        $_GET['new_rel_note'], $_GET['new_rel_type'], $SnoteId);
  }
}

/**
 * Requires $_GET with the following:
 *     rel_core_id=(rel core id)
 * Returns: String problem message if somethings wrong with input data and
 * nothing was written
 */
function deleteRelFromDb() {
  if (!array_key_exists('rel_core_id', $_GET)) return ' Bad form data. ';
  return deleteRelation($_GET['rel_core_id']);
}

/**
 * mode must be SAVE_TEXT_EDIT or SAVE_NEW
 * if mode is SAVE_TEXT_EDIT post must contain: 
 *     note=(id), name=(text), description=(text)
 * or if mode is SAVE_NEW post must contain: 
 *     name=(text), description=(text)
 *
 * Returns: String problem message if somethings wrong with input data and
 * nothing was written
 */
function savePostedNoteToDb($mode) {
  global $db;
  $SLnoteId = -1; // local version
  
  // VALIDATE THE $_POST {{{
  $postProblemMsg = null;
  $postOk = array_key_exists('name', $_POST)
         && strlen($_POST['name']) <= MAX_NAME_LENGTH
         && array_key_exists('description', $_POST)
         && strlen($_POST['description']) <= MAX_DESCRIPTION_LENGTH;
  if ($postOk && $mode == SAVE_TEXT_EDIT) {
    if (array_key_exists('note', $_POST)) {
      $SLnoteId = (integer)$_POST['note'];
      // Check that the note to edit already exists, also confirming that the id
      // is valid.
      $postOk = $db->query("SELECT id FROM notes WHERE id = '$SLnoteId'")
                  ->num_rows == 1;
    } else $postOk = false;
  }
  // Ensure the submitted name isn't a different note already in the db.
  if ($postOk) {
    $selStr = ($mode == SAVE_NEW)
        ? 'SELECT id FROM notes WHERE name = ? LIMIT 1'
        : "SELECT id FROM notes WHERE name = ? AND id <> '$SLnoteId' LIMIT 1";
    $stmt = $db->prepare($selStr);
    $stmt->bind_param('s', $_POST['name']);
    $stmt->execute() or handleIt($stmt->error);
    $stmt->store_result();
    $postOk = $stmt->num_rows == 0;
    if (!$postOk) {
      $postProblemMsg .= ' The name submitted is already used by another note. ';
    }
  }
  // }}} DONE VALIDATING THE $_POST.

  // send the new/edit to the db
  if ($postOk) {
    $stmt;
    if ($mode == SAVE_TEXT_EDIT) {
      $stmt = $db->prepare('UPDATE notes '
                         . 'SET name = ?, description = ? '
                         . 'WHERE id = ?');
      $stmt->bind_param('ssi', $_POST['name'], $_POST['description'], $SLnoteId);
    } else {
      $stmt = $db->prepare('INSERT INTO notes (name, description) '
                         . 'VALUES (?, ?)');
      $stmt->bind_param('ss', $_POST['name'], $_POST['description']);
    }
    $stmt->execute() or handleIt($stmt->error);
  
  // There was an issue with the posted data.
  } else {
    $postProblemMsg .= ' There was a problem processing the submitted data. Please check it\'s validity and try again. ';
    return $postProblemMsg;
  }
}

/**
 * Currently no default note functionality is available, the test db
 * implementation has the concept of a "home" parent note but at the moment
 * that's not a type understood by RN.
 * returns: the id of the latest note added to the DB.
 */
function getDefaultNoteId() {
  global $db;
  $res = $db->query(
      'SELECT MAX(id) AS max_id
       FROM notes') or handleIt($db->error);
  return $res->fetch_assoc()['max_id'];
}
?>
