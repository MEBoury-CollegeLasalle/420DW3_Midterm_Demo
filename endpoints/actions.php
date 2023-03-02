<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo actions.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";
require_once INCLUDES_DIR."debug_functions.php";

/**
 * This funtion is called from the switch-case statement at the end of this file.
 * It causes validation checks on the contents of the received request and creates
 * a new book in the database using the parameter values received in the request.
 *
 * It also tailors the PHP response to make the client's browser redirect to the view
 * page and display the newly created book.
 *
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_create() : void {
    
    // validate that all the necessary request parameter values are present
    if (empty($_REQUEST["title"]) || empty($_REQUEST["isbn"]) || empty($_REQUEST["author"])) {
        // If any of the required values are missing, set the response's HTTP
        // status code to "400 BAD REQUEST".
        http_response_code(400);
        
    } else {
        // all the required parameter values are present, proceed with book creation in the DB
        
        // Create the book using the function defined in database.php
        // That function returns the auto-increment created ID of the book or an array on failure.
        $new_id = create_book($_REQUEST["title"], $_REQUEST["isbn"], $_REQUEST["author"]);
        
        if (is_array($new_id)) {
            // If $new_id is an array, then the book creation failed. Output an error message
            echo "SQL Error ".$new_id["errno"]." : ".$new_id["error"];
            debug($new_id["exception"]);
            
            // And set the HTTP response code to "500 INTERNAL SERVER ERROR".
            http_response_code(500);
            
        } else {
            // $new_id is not an array, the book creation is successful and we have its ID
            
            // create a URL to the "view.php" web page, and manually add a request parameter
            // to that url specifying the id of the created book (the "?id=$new_id" part).
            $url = WEB_PROJECT_ROOT."view.php?id=$new_id";
            
            /*
             * Add a "Location: " header to the response with the created URL as the header value.
             * Also set the HTTP response code to "303 SEE OTHER"; this response code, when combined
             * with a "Location" header will cause the client's browser to redirect the page
             * to the specified URL.
             * In this case, it will redirect to the "view.php" page, passing along with it the ID of the
             * newly created book so it can be displayed.
             */
            header("Location: ".$url, true, 303);
        }
    }
    // terminate the program, send the response.
    exit(0);
}

/**
 * This funtion is called from the switch-case statement at the end of this file.
 * It causes validation checks on the contents of the received request and modifies
 * an existing book in the database using the parameter values received in the request.
 *
 * It also tailors the PHP response to make the client's browser redirect to the view
 * page and display the updated book.
 *
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_edit() : void {
    
    // validate that all the necessary request parameter values are present
    if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"])
        || empty($_REQUEST["title"]) || empty($_REQUEST["isbn"])
        || empty($_REQUEST["author"])) {
        // If any of the required values are missing or that the "id" is not numeric,
        // set the response's HTTP status code to "400 BAD REQUEST".
        http_response_code(400);
        
    } else {
        // all the required parameter values are present, proceed with book update in the DB
    
        // Update the book using the function defined in database.php
        // That function returns true if the book update was successful or an array on failure.
        $result = edit_book((int) $_REQUEST["id"],$_REQUEST["title"], $_REQUEST["isbn"], $_REQUEST["author"]);
        
        if (is_array($result)) {
            // $result is an array, an error happened, echo an error message and the exception
            echo "SQL Error ".$result["errno"]." : ".$result["error"];
            debug($result["exception"]);
            
            // And set the HTTP response code to "500 INTERNAL SERVER ERROR".
            http_response_code(500);
            
        } else {
            // $result is not an array, book modification was successful
    
            // create a URL to the "view.php" web page, and manually add a request parameter
            // to that url specifying the id of the modified book (the "?id=$_REQUEST["id"]" part).
            $url = WEB_PROJECT_ROOT."view.php?id=".$_REQUEST["id"];
    
            /*
             * Add a "Location: " header to the response with the created URL as the header value.
             * Also set the HTTP response code to "303 SEE OTHER"; this response code, when combined
             * with a "Location" header will cause the client's browser to redirect the page
             * to the specified URL.
             * In this case, it will redirect to the "view.php" page, passing along with it the ID of the
             * updated book so it can be displayed.
             */
            header("Location: ".$url, true, 303);
        }
    }
    
    // terminate the program, send the response.
    exit(0);
}

/**
 * This funtion is called from the switch-case statement at the end of this file.
 * It causes validation checks on the contents of the received request and deletes
 * an existing book in the database using the book's id parameter value received in the request.
 *
 * It also tailors the PHP response to make the client's browser redirect to the index
 * page and display the book creation form again.
 *
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_delete() : void {
    
    // validate that all the necessary request parameter values (id only) are present (and numeric)
    if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"])) {
        // If any of the id value is missing or not numeric,
        // set the response's HTTP status code to "400 BAD REQUEST".
        http_response_code(400);
        
    } else {
        // ID value is OK, proceed with deletion
        $result = delete_book((int) $_REQUEST["id"]);
    
        // Update the book using the function defined in database.php
        // That function returns true if the book deletion was successful or an array on failure.
        if (is_array($result)) {
            // $result is an array, an error happened, echo an error message and the exception
            echo "SQL Error ".$result["errno"]." : ".$result["error"];
            debug($result["exception"]);
    
            // And set the HTTP response code to "500 INTERNAL SERVER ERROR".
            http_response_code(500);
            
        } else {
            // $result is not an array, book modification was successful
    
            // Create a URL to the project root directory (where my index.php file is located in).
            $url = WEB_PROJECT_ROOT;
            
            /*
             * Add a "Location: " header to the response with the created URL as the header value.
             * Also set the HTTP response code to "303 SEE OTHER"; this response code, when combined
             * with a "Location" header will cause the client's browser to redirect the page
             * to the specified URL.
             *
             * In this case, it will redirect to the project root directory.
             *
             * When no file is specified at the end of a certain URL path, web servers automatically
             * search for an index file in the directory specified in the URL path.
             * So a URL pointing to <host>/420DW3_Midterm_Demo/ will search for and load
             * any index.html or index.php file in the 420DW3_Midterm_Demo/ directory in the host server's
             * document root (htdocs).
             *
             * I could also have specified the file directly, it would also work:
             * $url = WEB_PROJECT_ROOT."index.php";
             */
            header("Location: ".$url, true, 303);
        }
    }
    
    // terminate the program, send the response.
    exit(0);
}


// Switch-case statement that handles which action to do depending on the value
// of the "action" parameter in the request.
// First, check that that parameter exists and is not empty.
if (!empty($_REQUEST["action"])) {
    // action parameter is not empty, proceed with switch-case statement
    
    switch ($_REQUEST["action"]) {
        case "create":
            // value of the "action" parameter is "create": call book creation function
            action_create();
            break;
        case "edit":
            // value of the "action" parameter is "edit": call book update function
            action_edit();
            break;
        case "delete":
            // value of the "action" parameter is "delete": call book deletion function
            action_delete();
            break;
        default:
            // action parameter is present but not recognized: display a message and set the
            // HTTP response code to "400 BAD REQUEST" before terminating execution.
            echo "Invalid specified action: [".$_REQUEST["action"]."].";
            http_response_code(400);
            exit(0);
    }
} else {
    // action parameter is empty (or not present): display a message and set the
    // HTTP response code to "400 BAD REQUEST" before terminating execution.
    echo "\"action\" request parameter not specified.";
    http_response_code(400);
    exit(0);
}