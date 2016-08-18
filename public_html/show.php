<!doctype html>

<?php require_once 'common.php'; ?>
<?php $_SESSION['preLoginPage'] = $_SERVER['REQUEST_URI']; ?>

<html>
<head>
<title>Related Notes - Make Relations</title>
<link rel="stylesheet" href="./show.css" />
</head>
<body>

<?php if (array_key_exists('note', $_GET)): // if note value passed ?>
  <?php $SnoteId = (integer)$_GET['note']; ?>
  <div class="gd-wrap">
  
  <div class='gd-box gd-header'>
  <h3 class='rn-title'>Related Notes - (name of data/site)</h3>
  
  <?php ///////////////////////////////////////////////////////////////////// ?>

  <p class='rn-user-status'>Not logged in <a href='./login.php'>[login]</a></p>
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
                AND rel_legs.note <> "' . $SnoteId . '"') or handleIt($db->error); ?>
      <div class='type-parent'>
        <?php echoNote($parRelAssoc['noteId'], $parRelAssoc['noteName']); ?>
      </div>
      
      <?php if ($subRes->num_rows < 1) continue; ?>
      
      <div class='common-parent-siblings'>
        <?php
        while ($siblingAssoc = $subRes->fetch_assoc()) {
          echoNote($siblingAssoc['noteId'], $siblingAssoc['noteName']);
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
    <?php echoNote($associateAssoc['noteId'], $associateAssoc['noteName']); ?>
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
  <?php echoNote($SnoteId, $mainAssoc['name'], $mainAssoc['description']); ?>
  
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
        <h2><?php echo htmlspecialchars($curRelationName); ?></h2>
        
      <?php
    }
    echoNote($childAssoc['noteId'], $childAssoc['noteName'],
        $childAssoc['noteDesc']);
  }
  // If any children where echoed we have to end the last relation name div
  ?>
  
  <?php if ($curRelationName != null) : ?></div><?php endif; ?>
  </div>
  
  <?php
}

function echoNote($Sid, $Xname, $Xdescription = null) {
  ?>
  
  <div>
    <a href="./show.php?note=<?php echo $Sid ?>">
      <?php echo htmlspecialchars($Xname); ?>
    </a>
  </div>
  <?php if ($Xdescription): ?>
    <p><?php echo htmlspecialchars($Xdescription); ?></p>
  <?php endif; ?>
  <?php
}
?>

