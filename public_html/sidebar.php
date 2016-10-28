<?php
/**
 * Contains code exclusive to the display of the sideboard on the note page.
 * Required by note.php
 */

function showSidebar() {
  global $SnoteId;
  ?>
  <div class="gd-box gd-sidebar">
    <?php showParentAndSiblings($SnoteId); ?>
    <?php showAssociates($SnoteId); ?>
    <?php showAll($SnoteId); ?>
  </div>
  <?php
}  

/**
 * Shows just names of sibling notes of this.
 */
function showParentAndSiblings($SnoteId) {
  global $db;
  global $XnoteName;
  
  // Get the parent nodes of this along with each relation type
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName,
            rel_types.id AS relType, rel_cores.id AS relCoreId,
            rel_types.purpose AS relPurpose
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
  
  <div>
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
                AND notes.id <> "' . $SnoteId . '"
              ORDER BY notes.name')
                  or handleIt($db->error); ?>
      <div class='type-parent'>
        <?php printSidebarNote($parRelAssoc['noteId'], $parRelAssoc['noteName'],
                $parRelAssoc['relCoreId']); ?>
      </div>

      
      <div class='common-parent-siblings'>
        
        <div class="explainer">
          <span class="about-note"><?php echo htmlspecialchars($XnoteName); ?></span>
          -
          <?php echo htmlspecialchars($parRelAssoc['relPurpose']); ?>
          <?php echo htmlspecialchars($parRelAssoc['noteName']); ?>

          <?php if ($subRes->num_rows < 1) : ?>
            </div></div>
            <?php continue; ?>
          <?php endif; ?>
          
          as are the following
        </div>
        
        <?php
        while ($siblingAssoc = $subRes->fetch_assoc()) {
          printSidebarNote($siblingAssoc['noteId'], $siblingAssoc['noteName']);
        }
        ?>
      </div>
    <?php endwhile; ?>
  </div>
  
  <?php
}

function showAssociates($SnoteId) {
  global $db;
  global $XnoteName;
  
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
              AND rel_types.structure = "one-one")
          AND notes.id <> "' . $SnoteId . '"
          ORDER BY notes.name')
              or handleIt($db->error);
  if ($res->num_rows < 1) return;
  ?>
  
  <div class='associate-notes'>
  <div class="explainer">
    <span class="about-note"><?php echo htmlspecialchars($XnoteName); ?></span>
    - is associated with the following
  </div>
  <?php while ($associateAssoc = $res->fetch_assoc()) : ?>
    <?php printSidebarNote($associateAssoc['noteId'],
        $associateAssoc['noteName'], $associateAssoc['relCoreId']); ?>
  <?php endwhile; ?>
  </div>
  
  <?php
}

function showAll() {
  global $db;
  
  $res = $db->query(
     'SELECT notes.id AS noteId, notes.name AS noteName
        FROM notes
        ORDER BY noteName')
              or handleIt($db->error);
  ?>
  
  <div class='all-notes'>
  <?php while ($allAssoc = $res->fetch_assoc()) : ?>
    <?php printSidebarNote($allAssoc['noteId'], $allAssoc['noteName']); ?>
  <?php endwhile; ?>
  </div>
  
  <?php
}

function printSidebarNote($Sid, $Xname, $SrelCoreId = null) {
  global $mode;
  ?>
  <div>
    <a href="<?php echo $_SERVER['SCRIPT_NAME'] . '?note=' . $Sid; ?>"><?php echo htmlspecialchars($Xname); ?></a>
    <?php if ($mode == REL_EDITOR && $SrelCoreId
            && authenticateSessionUser()) : ?>
      <?php printRelDeleteForm($SrelCoreId); ?>
    <?php endif; ?>
  </div>
  <?php
}

