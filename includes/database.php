<?php
declare(strict_types=1);

/*
 * 420DW3_Midterm_Demo database.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-24
 * (c) Copyright 2023 Marc-Eric Boury 
 */

// DATABASE CONSTANTS
/**
 * Constant for the database user username
 */
const DB_USER = "root";
/**
 * Constant for the database user username
 */
const DB_PASSWD = "";
/**
 * Constant for the database name itself
 */
const DB_NAME = "420dw3_midterm_demo";
/**
 * Constant for the database table name
 */
const DB_TABLE = "books";


// Set the Mysqli driver-level option to report errors and throw exceptions when they happen.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


/**
 * Creates and returns a new {@see mysqli} class instance.
 *
 * @return mysqli
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function get_connection() : mysqli {
    // create a new connection mysqli object and return it.
    return new mysqli("localhost", DB_USER, DB_PASSWD, DB_NAME, 3306);
}

/**
 * Validates a string against characters that are considered unacceptable.
 * Will throw an exception if the passed string parameter contains any of the following
 * characters:
 * <ul>
 * <li>?</li>
 * <li>;</li>
 * <li>`</li>
 * <li>'</li>
 * <li>"</li>
 * <li>(</li>
 * <li>)</li>
 * </ul>
 *
 * @param string $string
 *
 * @return void
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function validate_string(string $string) : void {
    // throw an exception if the string to validate contains any of the following illegal characters:
    // "?", ";", "`", both single and double quotes characters, "(" and ")".
    if (preg_match("/.*([?;`\"'()])+.*/mi", $string, $matches)) {
        throw new Exception(
            "Invalid character found in string: [".$matches[1]."]."
        );
    }
}

/**
 * Retrieves a book from the database based on an identifier number.
 * Returns an associative array representing the book or an empty array
 * if no book was found for the passed identifier.
 *
 * @param int $id The book's identifier number
 *
 * @return array An array representing the book
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function get_book(int $id) : array {
    try {
        // open a connection
        $connection = get_connection();
        
        // prepare a parameterized statement (I use a constant for the table name to lower the risk of typos)
        $statement = $connection->prepare("SELECT * FROM `".DB_TABLE."` WHERE `id` = ? ;");
    
        // Bind the received ID value as an integer parameter to the statement. This gives a
        // value to replace the ? placeholder with.
        $statement->bind_param("i", $id);
    
        // Execute the statement with the bound parameters
        $statement->execute();
    
        // obtain the result set of the execution.
        $result_set = $statement->get_result();
    
        if ($result_set->num_rows == 0) {
            // if the result set has no results (rows) in it, return an empty array.
            return [];
        } elseif ($result_set->num_rows > 1) {
            // if the result set has more than one result (rows) in it, throw an error:
            // the statement has an id condition based on the primary key column. This
            // should never happen.
            throw new Exception("Multiple results found for primary-key conditional statement!");
        }
    
        // return the first (and only) result, fetched as an associative array.
        return $result_set->fetch_assoc();
        
    } catch (Exception $exception) {
        // this time i will not return an array describing the exception if something went wrong
        // since this function also returns an array if everithing went well.
        // I'll throw a wrapper exception instead
        throw new Exception("Failed to retrive book id # [$id] from the database.", 0, $exception);
    }
}

/**
 * Inserts a new book in the database based on relevant data.
 * Returns the newly inserted book's ID if successful or an array describing the error if not.
 *
 * @param string $title  The book's title
 * @param string $isbn   The book's ISBN number
 * @param string $author The book's author
 *
 * @return int|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function create_book(string $title, string $isbn, string $author) : int|array {
    try {
        // validate the inputs against bad characters in a try-catch to handle the exceptions
        // that are thrown when incalid
        validate_string($title);
        validate_string($isbn);
        validate_string($author);
    } catch (Exception $excep) {
        // handle the exceptions. I'm spoofing the mysqli-type of error system with
        // an array but fill it with my own exception message.
        return [
            "errno" => 0,
            "error" => $excep->getMessage(),
            "exception" => $excep
        ];
    }
    
    // open a connection to the DB
    $connection = get_connection();
    
    // begin a transaction on the opened connection. This transaction is a bit pointless
    // since it wraps around a single statement, but its a showcase of how it works
    $connection->begin_transaction();
    
    try {
        // prepare the insert statement
        $statement = $connection->prepare("INSERT INTO `".DB_TABLE."` (`title`, `isbn`, `author`) VALUES (?, ?, ?);");
        
        // bind the validated parameters as values for the statement
        $statement->bind_param("sss", $title, $isbn, $author);
        
        // execute the statement
        $statement->execute();
        
        // retrieve the inserted ID
        $new_id = $connection->insert_id;
        
        // commit the transaction: all changes will now be saved in the database
        $connection->commit();
        
        // return the retrieved ID if all went well (no exceptions thrown)
        return $new_id;
        
    } catch (mysqli_sql_exception $mysqli_ex) {
        // rollback the transaction if an exception happens
        $connection->rollback();
        
        // return an error array
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}

/**
 * Updates a book in the database based on its id number.
 * Returns <code>true</code> on success or an array describing the error
 * on failure.
 *
 * @param int    $id     The book's ID number
 * @param string $title  The new title of the book
 * @param string $isbn   The new ISBN of the book
 * @param string $author The new Author of the book
 *
 * @return bool|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function edit_book(int $id, string $title, string $isbn, string $author) : bool|array {
    try {
        // Validate the string inputs against incalid characters
        validate_string($title);
        validate_string($isbn);
        validate_string($author);
    } catch (Exception $excep) {
        return [
            "errno" => 0,
            "error" => $excep->getMessage(),
            "exception" => $excep
        ];
    }
    
    // open the connection
    $connection = get_connection();
    try {
        // prepare the update statement.
        $statement =
            $connection->prepare("UPDATE `".DB_TABLE."` SET `title` = ?, `isbn` = ?, `author` = ? WHERE `id` = ?;");
        
        // bind the parameter values to the statement
        $statement->bind_param("sssi", $title, $isbn, $author, $id);
        
        // execute the statement.
        $statement->execute();
    
        // return true if all went well (no exceptions thrown)
        return true;
        
    } catch (mysqli_sql_exception $mysqli_ex) {
        // In an error happened, return an array that describes the error.
        // I could also re-throw the exception or a new exception wrapping around the caught one.
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}

/**
 * Deletes a book from the database based on its identifier number.
 * Returns <code>true</code> on success or an array describing the error if
 * one happens.
 *
 * @param int $id The identifier of the book to delete.
 *
 * @return bool|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function delete_book(int $id) : bool|array {
    
    // open a connection to the DB
    $connection = get_connection();
    
    try {
        // Prepare the delete statement
        $statement = $connection->prepare("DELETE FROM `".DB_TABLE."` WHERE `id` = ? ;");
        
        // Binds the parameter to the statement
        $statement->bind_param("i", $id);
        
        // Execute the statemnent
        $statement->execute();
        
        // return true if all went well (no exceptions thrown)
        return true;
        
    } catch (mysqli_sql_exception $mysqli_ex) {
        // Return an array describing the error if an database exception happened.
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}