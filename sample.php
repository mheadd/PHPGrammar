<?php

/**
 * PHP Grammar sample file.
 *
 * @copyright 2010 Mark J. Headd (http://www.voiceingov.org)
 * @package PHPGrammar 
 * @author Mark Headd
 */

// Inlcude the PHPGrammar class file.
require("PHPGrammar.php");

// An array to hold names and phone numbers of employees (you could easily get this from any data source).
$employees = Array();
$employees[1] = Array("fname" => "Amanda", "lname" => "Hugankiss", "phone" => "7148596547");
$employees[2] = Array("fname" => "Hugh", "lname" => "Jazz", "phone" => "7148745213");
$employees[3] = Array("fname" => "Joe", "lname" => "Schmo", "phone" => "7845987456");
$employees[4] = Array("fname" => "John", "lname" => "Public", "phone" => "4785412364");

// Create a new grammar instance.
$grammar = new Grammar(false, "en-US", "voice", "menu");

// Create a rule for the grammar.
$grammar->startRule("menu", GrammarScope::$public);
$grammar->startOneOf();

// Iterate over the employees array.
for($i=1; $i<count($employees); $i++) {
	
	$grammar->startItem();
	$grammar->item($employees[$i]["fname"], "0-1");
	$grammar->writeText($employees[$i]["lname"]);
	$grammar->tag($employees[$i]["phone"]);
	$grammar->endElement();
}

$grammar->endElement(); // End element for one-of.
$grammar->endElement(); // End element for rule.

// Write the grammar out for applications to consume.
$grammar->writeGrammar();

?>