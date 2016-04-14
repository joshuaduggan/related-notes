<?php
// Generates the password hash for the db for test users
echo password_hash('', PASSWORD_DEFAULT) . "\n";
?>