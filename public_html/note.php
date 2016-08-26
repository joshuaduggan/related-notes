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
 * The possible modes of this page, determined by the submitted paramaters
 * 
 * SHOW_NOTE:
 *   GET
 *     note=(note id)
 * SHOW_EDITOR:
 *   GET
 *     mode='show_editor'
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
 * SAVE_EDITING: (then SHOW_NOTE)
 *   POST
 *     mode='save_editing'
 *     note=(note id)
 *     nameText=(new text)
 *     descriptionText=(new text)
 */
const SHOW_NOTE = 1;
const SHOW_EDITOR = 2;
const LOGOUT = 3;
const DELETE_NOTE = 4;
const SAVE_EDITING = 5;
$mode = 0;

////////////////////////////////////////////////////////////////////////////////
// figure out what mode we are in then validate the params for that mode....
// ... then code the SAVE_EDITING mode to start things off

if (array_key_exists('mode', $_GET)) {
  switch ($_GET['mode']) {
    case 'show_editor': $mode = SHOW_EDITOR; break;
    case 'logout': $mode = LOGOUT; break;
    case 'delete_note': $mode = DELETE_NOTE; break;
    case 'save_editing': $mode = SAVE_EDITING; break;
    default: $mode = SHOW_NOTE; // should only get here if mode param is wrong
  }
} else $mode = SHOW_NOTE;

// put mode specific init/pre display code here, note that SHOW_NOTE is in a
// seperate switch because some of the modes here revert to it after their
// specific actions are taken.
switch ($mode) {
  case SHOW_EDITOR:
    //...
    break;
  case LOGOUT:
    logoutUser();
    $mode = SHOW_NOTE;
    break;
  case DELETE_NOTE:
    ////////////////////////////////////////////////////////
    $mode = SHOW_NOTE;
    break;
  case SAVE_EDITING:
    ////////////////////////////////////////////////////////
    $mode = SHOW_NOTE;
    break;
}
switch ($mode) {
  case SHOW_NOTE:
    //...
    break;
}

// The page that should be returned to after going to a intermediary page like
// login.
$_SESSION['SlatestNoteView'] = $_SERVER['SCRIPT_NAME'];
if (array_key_exists('note', $_GET))
  $_SESSION['SlatestNoteView'] .= '?note=' . (integer)$_GET['note'];

?><!doctype html>
<html>
<head>
<title>Related Notes - Make Relations</title>
<link rel="stylesheet" href="./note.css" />
</head>
<body>

<?php
if (array_key_exists('note', $_GET)):
  $SnoteId = (integer)$_GET['note'];
  ?>
  <div class="gd-wrap">
  
  <div class='gd-box gd-header'>
  <h2 class='rn-title'>Related Notes - (name of data/site)</h2>
  <p class='rn-user-status'>
    <?php if (authenticateSessionUser()) : ?>
      Logged in as <?php echo htmlspecialchars($_SESSION['userEmail']) ?>
      <a class='rn-link-butt' href='<?php
          echo $_SESSION['SlatestPrimaryPage'] . '&mode=logout'; ?>'>Logout</a>
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
<?php endif; ?>

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
     'SELECT notes.id AS noteId, notes.name AS noteName, rel_types.id AS relType
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
        <?php printShortNote($parRelAssoc['noteId'], $parRelAssoc['noteName']); ?>
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
     'SELECT notes.id AS noteId, notes.name AS noteName, rel_types.id AS relType
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
        $associateAssoc['noteName']); ?>
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
  $res = $db->query(
     'SELECT name, description
        FROM notes
        WHERE id = "' . $SnoteId . '"') or handleIt($db->error);
  $mainAssoc = $res->fetch_assoc();
  ?>
  
  <div class='main-note'>
  <?php printFullNote($SnoteId, $mainAssoc['name'], true,
      $mainAssoc['description']); ?>
  
  <?php
  // get any child relations that this has as well as the name of their nodes
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
             notes.description AS noteDesc, rel_types.name AS relTypeName
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
        $childAssoc['noteDesc']);
  }
  // If any children where echoed we have to end the last relation name div
  ?>
  
  <?php if ($curRelationName != null) : ?></div><?php endif; ?>
  </div>
  
  <?php
}

function printFullNote($Sid, $Xname, $isMain, $Xdescription = null) {
  global $mode;
  $isAuthenticated = authenticateSessionUser();
  ?>
  <?php if ($isMain && $mode == SHOW_EDITOR && $isAuthenticated): ?>
    <form method='post'>
      <input type="hidden" name="mode" value="save_editing" />
      <input type="hidden" name="note" value="<?php echo $Sid; ?>" />
      <input type="text" name="nameText" size="80" value="<?php
          echo htmlspecialchars($Xname); ?>" />
      <textarea name="descriptionText" cols="80" rows="12"><?php
          echo ($Xdescription) ? htmlspecialchars($Xdescription) : '';
          ?></textarea>
      <input type="submit" value="Save" />
      <input type="reset" />
    </form>
    <form onsubmit="return confirm('Really delete this note?');">
      <input type="hidden" name="mode" value="delete_note" />
      <input type="hidden" name="note" value="<?php
        //?????????????????????????????????????????????????????????????// ?>" />
      <input type="hidden" name="note_to_delete" value="<?php echo $Sid; ?>" />
      <input type="submit" value="Delete" />
    </form>
    <form action="#?note=<?php echo $Sid; ?>">
      <input type="submit" value="Cancel" />
    </form>

  <?php else: ?>
    <h3>
      <a href="<?php echo $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid; ?>">
        <?php echo htmlspecialchars($Xname); ?>
      </a>
    </h3>
    <?php if ($isMain && $isAuthenticated): ?>
      <a class='rn-mod-butt rn-link-butt'href='<?php echo
          $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid . '&mode=show_editor';
          ?>'>Edit</a>
    <?php endif; ?>
    <?php if ($Xdescription): ?>
      <p><?php echo htmlspecialchars($Xdescription); ?></p>
    <?php endif; ?>
  <?php endif; ?>
  <?php
}

function printShortNote($Sid, $Xname) {
  ?>
  <div>
    <a href="<?php echo $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid; ?>">
      <?php echo htmlspecialchars($Xname); ?>
    </a>
  </div>
  <?php
}
?>

