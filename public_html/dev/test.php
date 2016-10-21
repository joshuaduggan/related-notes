<?php
require_once 'common.php';

$res = $db->multi_query('
CREATE TEMPORARY TABLE cores_to_delete
  SELECT rel_cores.id
    FROM rel_legs
    JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
    JOIN rel_types ON rel_cores.rel_type = rel_types.id
    WHERE rel_legs.note = 61;
DELETE rel_legs, rel_cores FROM rel_legs
  JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
  JOIN notes ON rel_legs.note = notes.id
  WHERE rel_cores.id IN (SELECT * FROM cores_to_delete);
DELETE FROM notes WHERE notes.id = 61;
') or handleIt($db->error);
?>


