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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * @return mysqli
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function get_connection() : mysqli {
    return new mysqli("localhost", DB_USER, DB_PASSWD, DB_NAME, 3306);
}

/**
 * @param string $string
 *
 * @return void
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function validate_string(string $string) : void {
    if (preg_match("/.*([?;`\"'()])+.*/mi", $string, $matches)) {
        $return_val = false;
        throw new Exception(
            "Invalid character found in string: [".$matches[1]."]."
        );
        
    }
}

/**
 * @param int $id
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function get_book(int $id) : array {
    $connection = get_connection();
    $statement = $connection->prepare("SELECT * FROM `".DB_TABLE."` WHERE `id` = ? ;");
    $statement->bind_param("i", $id);
    $statement->execute();
    $result_set = $statement->get_result();
    if ($result_set->num_rows == 0) {
        return [];
    } elseif ($result_set->num_rows > 1) {
        throw new Exception("Oh boy you have a problem, bro!");
    }
    return $result_set->fetch_assoc();
}

/**
 * @param string $title
 * @param string $isbn
 * @param string $author
 *
 * @return int|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function create_book(string $title, string $isbn, string $author) : int|array {
    try {
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
    $connection = get_connection();
    $connection->begin_transaction();
    try {
        $statement = $connection->prepare("INSERT INTO `".DB_TABLE."` (`title`, `isbn`, `author`) VALUES (?, ?, ?);");
        $statement->bind_param("sss", $title, $isbn, $author);
        $statement->execute();
        $new_id = $connection->insert_id;
        $connection->commit();
        return $new_id;
        
    } catch (mysqli_sql_exception $mysqli_ex) {
        $connection->rollback();
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}

/**
 * @param int    $id
 * @param string $title
 * @param string $isbn
 * @param string $author
 *
 * @return bool|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function edit_book(int $id, string $title, string $isbn, string $author) : bool|array {
    try {
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
    $connection = get_connection();
    $statement = $connection->prepare("UPDATE `".DB_TABLE."` SET `title` = ?, `isbn` = ?, `author` = ? WHERE `id` = ?;");
    $statement->bind_param("sssi", $title, $isbn, $author, $id);
    
    try {
        $statement->execute();
        return true;
    } catch (mysqli_sql_exception $mysqli_ex) {
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}

/**
 * @param int $id
 *
 * @return bool|array
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function delete_book(int $id) : bool|array {
    $connection = get_connection();
    $statement = $connection->prepare("DELETE FROM `".DB_TABLE."` WHERE `id` = ? ;");
    $statement->bind_param("i", $id);
    
    try {
        $statement->execute();
        return true;
    } catch (mysqli_sql_exception $mysqli_ex) {
        return [
            "errno" => $connection->errno,
            "error" => $connection->error,
            "exception" => $mysqli_ex
        ];
    }
}