<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo index.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */


/*
 * loading my defines.php file relative to the directory containing the current PHP file (__DIR__
 * magic constant). Here, __DIR__ will have the value of the absoilute path to the
 * 420DW3_Midterm_Demo directory of my project (root) since its in that directory
 * that the present index.php file is.
 *
 * DIRECTORY_SEPARATOR is a PHP constant that takes the value the correct path directory separator character
 * depending on the operating system the PHP script is executed in. '\' in windows, '/' in UNIX-based systems.
 * This allows to define paths that work regardless of the execution environment.
 *
 * So here, when I execute this in my computer:
 *
 * __DIR__ = "C:\xampp\htdocs\420DW3_Midterm_Demo" (the directory containing the current file on my machine)
 * DIRECTORY_SEPARATOR = "\" (the Windows path separator)
 * "includes" = "includes" (standard string)
 * DIRECTORY_SEPARATOR = "\" (the Windows path separator)
 * "defines.php" = "defines.php" (standard string)
 *
 * Resulting, when concatenated, in the following absolute path string:
 * "C:\xampp\htdocs\420DW3_Midterm_Demo\includes\defines.php"
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
<!--
Here is a book creation form
Note that i use my own constants (WEB_ACTIONS_PATH) to create a URL to the action.php file
so that the form, when submitted, sends the request to that php script file.
-->
<form method="post" action="<?=WEB_ACTIONS_PATH?>actions.php">
    <!--
    The hidden input here adds a parameter to the request sent when the form is submitted.
    That parameter, named "action", is checked in actions.php to determine wich action to execute:
    creating a book, updating an existing one, or deleting an existing one.
    
    In this form, the value of that parameter will be "create" which will cause my switch-case
    statement in actions.php to trigger a book creation.
    -->
    <input type="hidden" id="actionInput" name="action" value="create">
    <div class="form-input-row">
        <label for="title">Title: </label>
        <!--
        The three following inputs inputs are marked as "required", so the user is not allowed
        to submit the form when any of them are empty.
        
        Note that the parameters added to the request when the form is submitted use the input's "name"
        attribute value as the parameter name. PHP will parse those HTTP parameters and put them into the
        $_REQUEST array superglobal variable.
        
        Therefore, for this input named "title", there will be an entry in $_REQUEST with "title" as its key
        and the input's value as its value:
        $_REQUEST["title"] will contain whatever the user has typed in the "title"-named input field
        in the form.
        -->
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
        <!--
        This submit-type input renders as a button that will submit the book creation form
        and trigger a request to be sent containing parameters with all the form's inputs' values.
        -->
        <input type="submit" id="submit" value="Submit">
    </div>
</form>
</body>
</html>
