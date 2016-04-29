<?php
require_once 'common.php';

/**
 * If the relation type is of a one-many structure noteA is the root.
 * RETURNS A somewhat descriptive message string if an error was encountered
 */
function relateThese($XnoteAName, $XrelationTypeName, $XnoteBName) {

  global $db;
  
  // Check that the notes both exist and get their ids
  $stmt = $db->prepare(
      'SELECT id ' .
      '  FROM notes ' .
      '  WHERE name = ? ' .
      '  LIMIT 1');
  $noteNames = [$XnoteAName, $XnoteBName];
  $noteIds = [];
  for ($i = 0 ; $i < 2 ; $i++) {
    $stmt->bind_param('s', $noteNames[$i]);
    $stmt->execute() or handleIt($stmt->error);
    $res = $stmt->get_result();
    if ($res->num_rows < 1) {
      return 'One or more of the passed names does not exist.';
    }
    $data = $res->fetch_assoc();
    $noteIds[$i] = $data['id'];
  }
  
  // Check that the rel_type.name exists get it's id and structure
  $stmt = $db->prepare(
      'SELECT id, structure ' .
      '  FROM rel_types ' .
      '  WHERE name = ? ' .
      '  LIMIT 1');
  $stmt->bind_param('s', $XrelationTypeName);
  $stmt->execute() or handleIt($stmt->error);
  $res = $stmt->get_result();
  if ($res->num_rows < 1) {
    return 'One or more of the passed names does not exist.';
  }
  $data = $res->fetch_assoc();
  $relationTypeId = $data['id'];
  $relationTypeStructure = $data['structure'];
  
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
      'SELECT rel_legs.rel_core, count(*) ' .
      '  FROM rel_legs ' .
      '    JOIN rel_cores ' .
      '      ON rel_legs.rel_core = rel_cores.id ' .
      '    JOIN rel_types ' .
      '      ON rel_cores.rel_type = rel_types.id ' .
      '  WHERE rel_types.id = ' . $relationTypeId .
      '  AND (rel_legs.note = ' . $noteIds[0] .
      '    OR rel_legs.note = ' . $noteIds[1] . ') ' .
      '  GROUP BY rel_legs.rel_core ' .
      '  HAVING count(*) > 1'
    ) or handleIt($db->error);
  if ($res->num_rows > 0) {
    return 'This relation already exists, and was not re-created.';
  }

  // Create the relation
  $db->query(
      'INSERT INTO rel_cores (rel_type) ' .
      '  VALUES (' . $relationTypeId . ')'
    ) or handleIt($db->error);
  $db->query(
      'INSERT INTO rel_legs (rel_core, note, role) ' .
      '  VALUES ' .
      '    (' .
             $db->insert_id . ', ' .
             $noteIds[0] . ', ' .
             (($relationTypeStructure == 'one-many') ? '"root"' : 'NULL') .
      '    ), (' .
             $db->insert_id . ', ' .
             $noteIds[1] . ', ' .
      '      NULL);'
    ) or handleIt($db->error);
}

?>
<!doctype html>
<html>
<head>
<title>Related Notes - Make Relations</title>
</head>
<body>
<?php
echo relateThese('Azure', 'Category', 'Lync');
?>
<p>end</p>
</body>
</html>