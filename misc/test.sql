"SELECT id, name, is_category FROM notes WHERE id IN 
  (SELECT note_id FROM relationships WHERE relationship_id IN
    (SELECT relationship_id FROM relationships WHERE note_id = {$row['id']})
    AND note_id <> {$row['id']})
  ORDER BY is_category, name"