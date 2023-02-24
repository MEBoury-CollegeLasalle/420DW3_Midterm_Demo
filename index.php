<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo index.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once __DIR__.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."defines.php";

?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
    <title>Midterm evaluation demo</title>
    <link rel="stylesheet" href="<?=WEB_PUBLIC_DIR."css.css"?>">
</head>
<body>
<h1>Book creation form:</h1>
<form method="post" action="<?=WEB_ACTIONS_PATH?>actions.php">
    <input type="hidden" id="actionInput" name="action" value="create">
    <div class="form-input-row">
        <label for="title">Title: </label>
        <input type="text" id="title" name="title" maxlength="256" required>
    </div>
    <div class="form-input-row">
        <label for="isbn">ISBN: </label>
        <input type="text" id="isbn" name="isbn" maxlength="256" required>
    </div>
    <div class="form-input-row">
        <label for="author">Author: </label>
        <input type="text" id="author" name="author" maxlength="256" required>
    </div>
    <div class="form-input-row">
        <input type="submit" id="submit" value="Submit">
    </div>
</form>
</body>
</html>
