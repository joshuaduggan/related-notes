<?php
require_once 'common.php';

function showNote($SnoteId, $isPrimary = false) {
  global $db;
  $res = $db->query(
      'SELECT name, description ' .
        'FROM notes ' .
        'WHERE id = ' . $SnoteId) or handleIt($db->error);
  $data = $res->fetch_assoc();
  echo "<div>\n";
  echo '<h3>' . htmlspecialchars($data['name']) . "</h3>\n";
  echo '<p>' . htmlspecialchars($data['description']) . "</p>\n";
  
  if ($isPrimary) {
    //////////////////////////////////
	// is this note a root to one-many relationships?
	
  }
  
  echo "</div>\n";
}
?>
<!doctype html>
<html>
<head>
<title>Related Notes - Make Relations</title>
</head>
<body>

<?php
// find note value if passed
if (array_key_exists('note', $_GET)) {
  $SnoteId = (integer)$_GET['note'];
  showNote($SnoteId, true);
}
?>

<p>--- end of shownote.php ---</p>
</body>
</html>
