INSERT INTO rel_cores (rel_type)
  VALUES ('2');
INSERT INTO rel_legs (rel_core, note, role)
  VALUES
	(LAST_INSERT_ID(), '27', 'root'),
	(LAST_INSERT_ID(), '28', NULL);

	
INSERT INTO rel_cores (rel_type)
  VALUES (' . $ray[$XrelationTypeName] . ')

INSERT INTO rel_legs, (rel_core, note, role)
  VALUES (' . 
    $db->last_insert_id . ', ' . 
    $ray[$XnoteAName] . ', ' ,
    ($relationTypeStructure == 'one-many' ? 'root' : 'NULL') . ');
INSERT INTO rel_legs, (rel_core, note)
  VALUES (' . 
    $db->last_insert_id . ', ' . 
    $ray[$XnoteBName] . ');

SELECT rel_legs.rel_core, count(*)
  FROM rel_legs
    JOIN rel_cores
      ON rel_legs.rel_core = rel_cores.id
    JOIN rel_types
      ON rel_cores.rel_type = rel_types.id
  WHERE rel_types.id = ?
  AND (rel_legs.note = ? OR rel_legs.note = ?)
  GROUP BY rel_legs.rel_core
  HAVING count(*) > 1

SELECT rel_legs.rel_core, count(*)
  FROM rel_legs
    JOIN rel_cores
      ON rel_legs.rel_core = rel_cores.id
    JOIN rel_types
      ON rel_cores.rel_type = rel_types.id
  WHERE rel_types.id = 2
  AND (rel_legs.note = 43 OR rel_legs.note = 15)
  GROUP BY rel_legs.rel_core
  HAVING count(*) > 1



SELECT note 
  FROM rel_legs
  WHERE rel_core IN
    (SELECT rel_core
      FROM rel_legs
      WHERE note = '')
  AND note <> '' 

UPDATE rel_cores 
INNER JOIN rel_legs ON rel_legs.rel_core = rel_cores.id
INNER JOIN notes ON notes.id = rel_legs.note
SET rel_cores.rel_type = 2
WHERE notes.is_category = 1

SELECT *
FROM notes
INNER JOIN rel_legs ON notes.id = rel_legs.note
INNER JOIN rel_cores ON rel_legs.rel_core = rel_cores.id
WHERE notes.is_category = 1

UPDATE rel_legs
INNER JOIN notes ON rel_legs.note = notes.id
SET rel_legs.role = 'root'
WHERE notes.is_category = 1

UPDATE rel_cores
SET rel_type = 1
WHERE rel_type = 0

SELECT *
FROM notes
JOIN rel_legs ON notes.id = rel_legs.note
WHERE rel_legs.role = 'root'
GROUP BY notes.id

DELETE rel_legs, rel_cores, notes
  FROM rel_legs JOIN rel_cores
    ON rel_legs.rel_core = rel_cores.id
  JOIN notes
    ON rel_legs.note = notes.id
  WHERE notes.id = "43"

SELECT *
  FROM rel_legs JOIN rel_cores
    ON rel_legs.rel_core = rel_cores.id
  JOIN notes
    ON rel_legs.note = notes.id
  WHERE notes.id = "43"
  
  