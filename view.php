<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo view.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */

if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"])) {
    http_response_code(400);
    exit(0);
}

require_once __DIR__.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";

try {
    $book = get_book((int) $_REQUEST["id"]);
    if (empty($book)) {
        http_response_code(404);
        exit(0);
    }
    
} catch (Exception $exception) {
    http_response_code(500);
    exit(0);
}


?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
    <title>Midterm evaluation demo</title>
    <link rel="stylesheet" href="<?=WEB_PUBLIC_DIR."css.css"?>">
    <script src="<?=WEB_PUBLIC_DIR."jquery-3.6.3.js"?>"></script>
    <script src="<?=WEB_PUBLIC_DIR."js.js"?>"></script>
</head>
<body>
<h1>Book details:</h1>
<form id="viewForm" method="post" action="<?=WEB_ACTIONS_PATH?>actions.php" >
    <input type="hidden" id="actionInput" name="action">
    <div class="form-input-row">
        <label for="id">ID: </label>
        <input class="form-input" type="number" id="id" name="id" value="<?=$book["id"]?>" readonly>
    </div>
    <div class="form-input-row">
        <label for="title">Title: </label>
        <input class="form-input editable-form-input" type="text" id="title" name="title" maxlength="256" value="<?=$book["title"]?>" readonly>
    </div>
    <div class="form-input-row">
        <label for="isbn">ISBN: </label>
        <input class="form-input editable-form-input" type="text" id="isbn" name="isbn" maxlength="256" value="<?=$book["isbn"]?>" readonly>
    </div>
    <div class="form-input-row">
        <label for="author">Author: </label>
        <input class="form-input editable-form-input" type="text" id="author" name="author" maxlength="256" value="<?=$book["author"]?>" readonly>
    </div>
    <div class="form-input-row">
        <label for="dateCreated">Date Created: </label>
        <input class="form-input" type="datetime-local" id="dateCreated" name="dateCreated" value="<?=$book["dateCreated"]?>" readonly>
    </div>
    <div class="form-input-row">
        <button id="enableEditionButton" type="button">Enable Edition</button>
        <button id="disableEditionButton" type="button" hidden>Disable Edition</button>
        <button id="saveChangesButton" type="button" hidden>Save Changes</button>
        <button id="deleteButton" type="button">Delete</button>
    </div>
</form>
</body>
</html>