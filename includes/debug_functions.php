<?php

/*
 * exercises1 debug_functions.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-01-05
 * (c) Copyright 2023 Marc-Eric Boury 
 */

declare(strict_types=1);

/**
 * Instructs {@see debug()} to echo its results to the output.
 */
const DEBUG_OUTPUT_ECHO = 1;
/**
 * Instructs {@see debug()} to echo its results to the output AND terminate the application.
 */
const DEBUG_OUTPUT_ECHO_AND_DIE = 2;
/**
 * Instructs {@see debug()} to return its results instead of adding to the output.
 */
const DEBUG_OUTPUT_RETURN = 3;

/**
 * Basic debug helper function. Generates an HTML table string for whatever value is provided in <code>$input</code>.
 * The table will contain the type data of the <code>$input</code> value, and its value(s). For container-types values
 * (arrays, objects...), the function is recursive and will display each element or property of the container-type.
 *
 * By default, the string is echoed before the function returns it.
 *
 * @param mixed $input        The value to debug
 * @param int   $outputMethod OPTIONAL: Whether to echo the generated HTML table string before returning it or not.
 *                            Defaults to <code>DEBUG_OUTPUT_ECHO</code>
 *
 * @return string|null
 *
 * @author Marc-Eric Boury
 * @since  2023-01-05
 */
function debug(mixed $input, int $outputMethod = DEBUG_OUTPUT_ECHO) : ?string {
    // begin output of a table HTML element for the $input
    $return_value = "<table style='border: 1px solid black; border-collapse: collapse;'>";
    
    // get the type of the passed $input
    $input_type = gettype($input);
    
    // handle the different types
    switch ($input_type) {
        case "boolean":
            // boolean value: in a table row, display the type in a table cell and "true" or "false"
            // in another table cell depending on the value
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>".($input ? "true" : "false")."</td></tr>";
            break;
        case "integer":
        case "double":
            // numeric value: in a table row, display the type in a table cell and the value itself
            // in another table cell.
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            break;
        case "string":
            // string value: in a table row, display the type in a table cell and the value itself,
            // wrapped in a <pre> block in another table cell (the <pre> block will keep the string's
            // extra whitespaces).
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'><pre>\"$input\"</pre></td></tr>";
            break;
        case "NULL":
            // null value: in a table row, display "null" in a table cell
            $return_value .= "<tr><td style='border: 1px solid black;'>null</td></tr>";
            break;
        case "array":
            // array value: In a table row, display the type in a table cell. In a second cell,
            // start a new table.
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'><table style='border: 1px solid black; border-collapse: collapse;'>";
            
            // for each array element...
            foreach ($input as $key => $value) {
                $key_name = $key;
                
                // if the array element's key is not numeric, add double quotes to its value...
                if (!is_numeric($key)) {
                    $key_name = "\"$key\"";
                }
                
                // add a table row to the nested table, with the array key in a cell...
                $return_value .= "<tr><td style='border: 1px solid black;'>$key_name</td>";
                
                // and add the result of a recursive call to this very function (so another nested table)
                // using the array element's value as the input to a second cell in the row created just
                // before. This will make the function nest tables for every array element and display
                // their values whatever type they are.
                $return_value .= "<td style='border: 1px solid black;'>".debug($value, DEBUG_OUTPUT_RETURN)."</td>";
            }
            // close the nested table, cell and row.
            $return_value .= "</table></td></tr>";
            break;
        case "object":
            // object value...
            try {
                // use Reflection to obtain the ReflectionClass of the object
                $reflection_class = new ReflectionClass($input);
                
                // create a table row and add a first cell containing the object's short class name
                // obtaine through reflection
                $return_value .= "<tr><td style='border: 1px solid black;'>".$reflection_class->getShortName()."</td>";
                
                // create a second table cell in the row created just before...
                $return_value .= "<td style='border: 1px solid black;'>";
                // In that cell, create a nested table...
                $return_value .= "<table style='border: 1px solid black; border-collapse: collapse;'>";
                
                // Get the list of all the properties defined in the object's class, obtained
                // through reflection.
                $properties = $reflection_class->getProperties();
                
                // for each defined property...
                foreach ($properties as $property) {
                    
                    // create a table row in the nested table and add the quoted property name in a first
                    // table cell...
                    $return_value .= "<tr><td style='border: 1px solid black;'>\"".$property->getName()."\"</td>";
                    
                    // and add the result of a recursive call to this very function (so another nested table)
                    // using the property's value as the input to a second cell in the row created just
                    // before. This will make the function nest tables for every object property and display
                    // their values whatever type they are.
                    $return_value .= "<td style='border: 1px solid black;'>".debug($property->getValue($input), DEBUG_OUTPUT_RETURN)."</td>";
                }
                
                // close the nested table, cell and row.
                $return_value .= "</table></td></tr>";
            } catch (ReflectionException $refl_ex) {
                // If a reflection exception happens, create a single table row
                // instead of a row containing a nested table and display the exception
                // message in a cell in that row.
                $return_value .= "<tr><td style='border: 1px solid black;'>ReflectionException thrown: ".
                              $refl_ex->getMessage()."</td></tr>";
            }
            break;
        case "resource":
        case "resource (closed)":
        case "unknown type":
        default:
            // resource, unknown and default unsupported types cannot be displayed correctly...
            try {
                // attempt to create a table row, with the type in a first table cell and the
                // value in a second table cell. This will fail if the $input value cannot be converted
                // to a string (if it doesnt't implement a __ToString() method)
                $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            } catch (Exception $exception) {
                // If the $input value cannot be converted to a string, create a table row
                // containing a single cell itself displaying a message that the type
                // is unstringifyable.
                $return_value .= "<tr><td style='border: 1px solid black;'>unstringifyable $input_type</td></tr>";
            }
            break;
    }
    // close the table started at the very beginning of this function
    $return_value .= "</table>";
    
    // manage the output depending on the function'S received arguments
    switch ($outputMethod) {
        case DEBUG_OUTPUT_RETURN:
            // return the HTML; mainly used in the recursive calls of this function
            return $return_value;
        case DEBUG_OUTPUT_ECHO_AND_DIE:
            // echo the HTML, then terminate execution of the program.
            echo $return_value;
            die(0);
        case DEBUG_OUTPUT_ECHO:
        default:
            // Default behaviour: echo the HTML, then return null.
            echo $return_value;
            return null;
    }
}

/**
 * Returns an array containing all the received request HTTP headers
 *
 * @return array
 *
 * @author Marc-Eric Boury
 * @since  2/2/2023
 */
function get_request_headers() : array {
    // create an output array
    $headers = array();
    
    // For each element in the $_SERVER superglobal variable...
    foreach($_SERVER as $key => $value) {
        if (!str_starts_with($key, 'HTTP_')) {
            // If the element's key does not start with "HTTP_", skip it
            continue;
        }
        // Otherwise, obtain (and format as valid for requests and responses) the header name and the
        // header value from the $_SERVER superglobal.
        // Create a new entry in out output array with the formatted header name as key and header
        // value as value.
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        
        // Uee the formatted key name as a new entry key and the value from the $_SERVER
        $headers[$header] = $value;
    }
    // return the output array.
    return $headers;
}