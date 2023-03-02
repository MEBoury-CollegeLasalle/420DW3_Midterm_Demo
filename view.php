<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo view.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */

// In the view page, I expect my request to contain the ID of a book to, well, display
// so I validate that the request contains such parameter, in my case named "id", and if not
// I terminate the execution with an "400 BAD REQUEST" HTTP status code
if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"])) {
    http_response_code(400);
    exit(0);
}

// then I include my defines file which contains my own path constants like INCLUDES_DIR
// and also include my database.php file since i will need to load the book with the id
// specified in the request from the database to display its data
require_once __DIR__.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";

// with the database functions included, i can now fetch the book using my database get_book function.
// If it returns an empty array, that means that there is no book for the specified ID in the database.
// I thus terminate the execution with a "404 NOT FOUND" status code because, well, the book that the
// requests wants displayed is not found.
try {
    // Get the book from the database
    $book = get_book((int) $_REQUEST["id"]);
    
    if (empty($book)) {
        // function returned an empty array, book not found, terminate with 404 status code
        http_response_code(404);
        exit(0);
    }
    
} catch (Exception $exception) {
    // exception happened, terminate with "500 INTERNAL SERVER ERROR" status code
    http_response_code(500);
    exit(0);
}

// If the script reaches this point (if it has not terminated before) it means that the requested book
// exists and that I was able to retrieve it. I can thus output a webpage with a form to display the book
// and allow its modification and deletion.

?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
    <title>Midterm evaluation demo</title>
    <link rel="stylesheet" href="<?=WEB_PUBLIC_DIR."css.css"?>">
</head>
<body>
<h1>Book details:</h1>
<!--
Here is a book display and edition form
Note that i use my own constants (WEB_ACTIONS_PATH) to create a URL to the action.php file
so that the form, when submitted, sends the request to that php script file.
-->
<form id="editForm" method="post" action="<?=WEB_ACTIONS_PATH?>actions.php" >
    <!--
    The hidden input here adds a parameter to the request sent when the form is submitted.
    That parameter, named "action", is checked in actions.php to determine wich action to execute:
    creating a book, updating an existing one, or deleting an existing one.
    
    In this form, the value of that parameter will be "edit" which will cause my switch-case
    statement in actions.php to trigger a book update.
    -->
    <input type="hidden" id="actionInput" name="action" value="edit">
    <div class="form-input-row">
        <label for="id">ID: </label>
        <!--
        I have made the ID input field readonly since we do not want the users to be able to modify
        the book's ID, since the IDs are handled by the database using Auto-Increment.
        -->
        <input class="form-input" type="number" id="id" name="id" value="<?=$book["id"]?>" readonly>
    </div>
    <div class="form-input-row">
        <label for="title">Title: </label>
        <!--
        The next three inputs are not readonly, so the user is allowed to change the Title, ISBN and author
        of the book.
        -->
        <input class="form-input editable-form-input" type="text" id="title" name="title" maxlength="256" value="<?=$book["title"]?>">
    </div>
    <div class="form-input-row">
        <label for="isbn">ISBN: </label>
        <input class="form-input editable-form-input" type="text" id="isbn" name="isbn" maxlength="256" value="<?=$book["isbn"]?>">
    </div>
    <div class="form-input-row">
        <label for="author">Author: </label>
        <input class="form-input editable-form-input" type="text" id="author" name="author" maxlength="256" value="<?=$book["author"]?>">
    </div>
    <div class="form-input-row">
        <label for="dateCreated">Date Created: </label>
        <!--
        Finally, just as for the ID input, the dateCreated one is made readonly
        as the dateCreated column in my database is set to be handled by the database
        (default value being CURRENT_TIMESTAMP() ); so i also do not want my users to change
        that value.
        -->
        <input class="form-input" type="datetime-local" id="dateCreated" name="dateCreated" value="<?=$book["dateCreated"]?>" readonly>
    </div>
    <div class="form-input-row">
        <!--
        This submit-type input renders as a button that will submit the display/edition form
        and trigger a request to be sent containing parameters with all the form's inputs' values.
        -->
        <input type="submit" value="Save Changes">
    </div>
</form>

<!--
I here have a second form that is used only for deletion.
Since I couldn't use javascript anymore, I couldn't make a single form that would adapt itself
to do both edition or deletion (that needed javascript to change the value of the hidden "action" input).
-->
<form id="deleteForm" method="post" action="<?=WEB_ACTIONS_PATH?>actions.php" >
    <!--
    Note that this form only has hidden inputs (except the submit one) So it will only render as
    a button while still passing parameters and values in the request upon submission.
    
    Also note that in this form, the "action"-named input's value is "delete"; this will cause
    my actions.php switch-case statement to trigger the deletion of the book, and not some other operation.
    -->
    <input type="hidden" name="action" value="delete">
    <!-- Since deletion only requires an ID, i'm not adding any other inputs in this form -->
    <input type="hidden" name="id"  value="<?=$book["id"]?>">
    <div class="form-input-row">
        <input type="submit" value="Delete">
    </div>
</form>
</body>
</html>