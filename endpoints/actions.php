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
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_create() : void {
    if (empty($_REQUEST["title"]) || empty($_REQUEST["isbn"]) || empty($_REQUEST["author"])) {
        http_response_code(400);
    } else {
        $new_id = create_book($_REQUEST["title"], $_REQUEST["isbn"], $_REQUEST["author"]);
        if (is_array($new_id)) {
            echo "SQL Error ".$new_id["errno"]." : ".$new_id["error"];
            debug($new_id["exception"]);
            http_response_code(500);
        } else {
            $url = WEB_PROJECT_ROOT."view.php?id=$new_id";
            header("Location: ".$url, true, 303);
        }
        exit(0);
    }
}

/**
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_edit() : void {
    if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"]) || empty($_REQUEST["title"]) || empty($_REQUEST["isbn"]) || empty($_REQUEST["author"])) {
        http_response_code(400);
    } else {
        $result = edit_book((int) $_REQUEST["id"],$_REQUEST["title"], $_REQUEST["isbn"], $_REQUEST["author"]);
        if (is_array($result)) {
            echo "SQL Error ".$result["errno"]." : ".$result["error"];
            debug($result["exception"]);
            http_response_code(500);
        } else {
            $url = WEB_PROJECT_ROOT."view.php?id=".$_REQUEST["id"];
            header("Location: ".$url, true, 303);
        }
        exit(0);
    }
}

/**
 * @return void
 *
 * @author Marc-Eric Boury
 * @since  2023-02-24
 */
function action_delete() : void {
    if (empty($_REQUEST["id"]) || !is_numeric($_REQUEST["id"])) {
        http_response_code(400);
    } else {
        $result = delete_book((int) $_REQUEST["id"]);
        if (is_array($result)) {
            echo "SQL Error ".$result["errno"]." : ".$result["error"];
            debug($result["exception"]);
            http_response_code(500);
        } else {
            $url = WEB_PROJECT_ROOT;
            header("Location: ".$url, true, 303);
        }
        exit(0);
    }
}

if (!empty($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
        case "create":
            action_create();
            break;
        case "edit":
            action_edit();
            break;
        case "delete":
            action_delete();
            break;
        default:
            echo "Invalid specified action: [".$_REQUEST["action"]."].";
            http_response_code(404);
    }
} else {
    echo "Action not specified.";
    http_response_code(404);
}