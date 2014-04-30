@ECHO OFF
SET BIN_TARGET=%~dp0/../sebastianbergmann/phpcov/phpcov.php
php "%BIN_TARGET%" %*
