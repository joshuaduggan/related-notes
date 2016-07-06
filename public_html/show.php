<?php
require_once 'common.php';

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
  echo "<div class='sibling-notes'>\n";
  while ($parRelAssoc = $res->fetch_assoc()) {
    $subRes = $db->query(
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
              AND rel_legs.note <> "' . $SnoteId . '"') or handleIt($db->error);
    echo "<div class='type-parent'>\n";
    echoNote($parRelAssoc['noteId'], $parRelAssoc['noteName']);
    echo "</div>\n";
    
    if ($subRes->num_rows < 1) continue;
    echo "<div class='common-parent-siblings'>\n";
    while ($siblingAssoc = $subRes->fetch_assoc()) {
      echoNote($siblingAssoc['noteId'], $siblingAssoc['noteName']);
    }
    echo "</div>\n";
  }
  echo "</div>\n";
}

function showAssociates($SnoteId) {
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
              AND rel_types.structure = "one-one"
            ORDER BY rel_types.id)
          AND rel_types.structure = "one-many"
          AND rel_legs.role = "parent"') or handleIt($db->error);
        
  // For each of this' parents show all their child notes who's relation to that
  // parent is of the same type as this'.
  echo "<div class='sibling-notes'>\n";

}

/**
 * Shows just names of parent notes of this.
 */
function showParentNames($SnoteId) {
  global $db;

  // get any parent relations that this has as well as the name of their nodes
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
             rel_types.name AS relTypeName
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
  
  echo "<div class='parent-notes'>\n";
  
  $curRelationName = null;
  while ($childAssoc = $res->fetch_assoc()) {
    if ($childAssoc['relTypeName'] != $curRelationName) {
      if ($curRelationName != null) echo "</div>\n";
      $curRelationName = $childAssoc['relTypeName'];
      echo "<div class='type-parent'>\n";
      echo '<h2>' . htmlspecialchars($curRelationName) . "</h2>\n";
    }
    echoNote($childAssoc['noteId'], $childAssoc['noteName']);
  }
  // If any children where echoed we have to end the last relation name div
  if ($curRelationName != null) echo "</div>\n";
    
  echo "</div>\n";
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
  
  echo "<div class='main-note'>\n";
  echoNote($SnoteId, $mainAssoc['name'], $mainAssoc['description']);
  
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
      if ($curRelationName != null) echo "</div>\n";
      $curRelationName = $childAssoc['relTypeName'];
      echo "<div class='child-notes'>\n";
      echo '<h2>' . htmlspecialchars($curRelationName) . "</h2>\n";
    }
    echoNote($childAssoc['noteId'], $childAssoc['noteName'],
        $childAssoc['noteDesc']);
  }
  // If any children where echoed we have to end the last relation name div
  if ($curRelationName != null) echo "</div>\n";
    
  echo "</div>\n";
}

function echoNote($Sid, $Xname, $Xdescription = null) {
  echo '<h3><a href="./show.php?note=' . $Sid . '">' . htmlspecialchars($Xname) . "</a></h3>\n";
  if ($Xdescription) echo '<p>' . htmlspecialchars($Xdescription) . "</p>\n";
}
?>
<!doctype html>
<html>
<head>
<title>Related Notes - Make Relations</title>
<link rel="stylesheet" href="./show.css" />
</head>
<body>

<?php
// find note value if passed
if (array_key_exists('note', $_GET)) {
  $SnoteId = (integer)$_GET['note'];
  //showParentNames($SnoteId);
  showNote($SnoteId);
  showParentAndSiblings($SnoteId);
  showAssociates($SnoteId);
}
?>
</body>
</html>
