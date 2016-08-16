<?php
// Generates the password hash for the db for test users
echo password_hash('secret', PASSWORD_DEFAULT) . "\n";
?>