<?php
require_once 'common.php';

echoStartOfDoc("Related Notes");

echo "<div class='row'>\n";
echoHeader();
echo "</div>\n";
echo "<div class='row'>\n";

// find all_notes value if passed
$allNotes = array_key_exists('all_notes', $_GET);

// find cat value if passed
$catId = array_key_exists('cat', $_GET) ? (integer)$_GET['cat'] : -1;

// find note value if passed
$noteId = array_key_exists('note', $_GET) ? (integer)$_GET['note'] : -1;

$catsRes;
$notesRes;
// If we should display one cat with all it's notes and other cats
if ($catId > 1) {
  // get current cat at top of otherwise ordered result
  $catsRes = $db->query(
            "(SELECT * FROM notes WHERE is_category = 1 AND id = $catId) "
          . "UNION ALL (SELECT * FROM (SELECT * FROM notes "
          . "WHERE is_category = 1 AND id <> $catId ORDER BY name) tmp)")
            or handleIt($db->error);
// If we should display all cats with no notes (default) or If we should display
// all notes with cats at the bottom (in the case that $allNotes is true.
} else {
  $catsRes = $db->query(
            "SELECT * FROM notes WHERE is_category = 1 ORDER BY name")
            or handleIt($db->error);

  // if there was a note getted show that instead of the cats (but we still need
  // them for the nav sidebar.
  if ($noteId > 1) {
    $notesRes = $db->query(
            "SELECT * FROM notes WHERE is_category <> 1 AND id = $noteId");
  }
}

// retrieve the SA notes of the specified cat if one was passed
if ($catId > 1) {
  $notesRes = $db->query(
              "SELECT * FROM notes WHERE id IN "
            . "(SELECT note_id FROM relationships WHERE relationship_id IN "
            . "(SELECT relationship_id FROM relationships WHERE note_id = "
            . $catId . ") AND note_id <> " . $catId . ") AND is_category = 0 "
            . "ORDER BY name")
          or handleIt($db->error);
} else if ($allNotes) {
  $notesRes = $db->query(
              "SELECT * FROM notes WHERE is_category <> 1 ORDER BY name")
            or handleIt($db->error);
}

// output the main content div
echo "<div class='col-sm-9'>";

// if all_notes was getted
if ($allNotes) {
  echoNoteSections($notesRes);

// if a single note was getted
} else if ($noteId > 1) {
  echoNoteSections($notesRes);
  
// if nothing was getted show all cats, otherwise show the first cat
} else {
  echoNoteSections($catsRes);
}

echo "</div>\n";

// build nav
echo "<ul class='nav col-sm-3 jd-navcol'>\n";
echo "<li class='jd-edit-nav'><a href='edit.php'>Create New Note</a></li>\n";
if ($allNotes) {
  echo "<li><a href='#'>All Notes</a><ul class='nav'>\n";
  echoNavLis($notesRes);
  echo "</ul></li>\n";
  echoNavLis($catsRes);
} else {
  echoNavLis($catsRes);
  echo "<li><a href='./?all_notes#'>All Notes</a></li>\n";
}
echo "</ul>\n";
echo "</div>\n";
echo "<div class='row'>\n";
echoFooter();
echo "</div>\n";
echoEndOfDoc();

function echoNoteSections($res) {
  if (!$res) return;
  global $catId;
  global $notesRes;
  global $db;
  while ($row = $res->fetch_assoc()) {
    echo "<section id='{$row['id']}note' class='"
       . ($row['is_category'] ? "jd-cat" : "jd-note") . "'>\n";
    echo "<div class='jd-h-block'>"
       . "<a href='edit.php?id={$row['id']}'>Edit</a>"
       . "<h3>" . htmlspecialchars($row['name']) . "</h3>"
       . "</div>\n";
    $description = htmlspecialchars($row['description']);
    // support [url]myurl.com[/url]
    $description = preg_replace('/\[url\](.*?)\[\/url\]/i',
                                '<a href="$1">$1</a>', $description);
    $description = str_replace("\n", "</p>\n<p>", $description);
    echo "<p>" . $description . "</p>\n";
    echo "<p>";
    $saRes = $db->query(
        "SELECT id, name, is_category FROM notes WHERE id IN "
        . "(SELECT note_id FROM relationships WHERE relationship_id IN "
        . "(SELECT relationship_id FROM relationships "
        . "WHERE note_id = {$row['id']}) AND note_id <> {$row['id']}) "
        . "ORDER BY is_category DESC, name");
    while ($saRow = $saRes->fetch_assoc()) {
      $nType = ($saRow['is_category']) ? 'cat' : 'note';
      echo "<a href='./?$nType={$saRow['id']}' class='jd-$nType-link'>"
        . htmlspecialchars($saRow['name']) . "</a>\n";
    }
    echo "</p>\n";
    // if this is the currently specified cat (in the get) show it's SA notes.
    // then break the loop so we don't show any other cats (which I found
    // confuses things).
    if ($row['id'] == $catId) {
      echoNoteSections($notesRes);
      echo "</section>\n";
      break;
    }
    echo "</section>\n";
  }
  // reset the pointer so the result set can be re-iterated.
  $res->data_seek(0);
}

function echoNavLis($res) {
  if (!$res) return;
  global $catId;
  global $notesRes;
  while ($row = $res->fetch_assoc()) {
    if ($row['id'] == $catId) {
      echo "<li><a href='#'>" . htmlspecialchars($row['name']) . "</a>";
      echo "<ul class='nav'>\n";
      echoNavLis($notesRes);
      echo "</ul>\n";
    } else {
      echo "<li><a href='"
         . (($row['is_category']) ? "./?cat={$row['id']}" : "#{$row['id']}note")
         . "'>" . htmlspecialchars($row['name']) . "</a>";
    }
    echo "</li>\n";
  }
}
