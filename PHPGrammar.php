<?php

/**
 * PHP Grammar - this file contains classes that can be used to generate W3C XML Grammar files.
 *
 * @copyright 2010 Mark J. Headd (http://www.voiceingov.org)
 * @package PHPGrammar 
 * @author Mark Headd
 */
class Grammar {
	
	// XML Writer instance used to build grammar.
	private $xml;
	
	// Denotes if the grammar is a standalone file or used inline in another document.
	private $standalone;

	/**
	 * Class constructor.
	 *
	 * @param bool $standalone
	 * @param string $language
	 * @param string $mode
	 * @param string $root
	 * @return void
	 */
	public function __construct($standalone, $language="en", $mode="voice", $root=NULL, $indent=true) {
		$this->xml = new XMLWriter();
		$this->xml->openMemory();
		if($standalone) {
			$this->standalone = $standalone;
			$this->xml->startDocument('1.0', "ISO-8859-1"); 
		} 
		$this->xml->setIndent($indent);
		$this->xml->startElement("grammar");
		$this->xml->writeAttribute("xmlns", "http://www.w3.org/2001/06/grammar");
		$this->xml->writeAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
		$this->xml->writeAttribute("xsi:schemaLocation", "http://www.w3.org/2001/06/grammar http://www.w3.org/TR/speech-grammar/grammar.xsd");
		$this->xml->writeAttribute("version", "1.0");
		$this->xml->writeAttribute("xml:lang", $language);
		$this->xml->writeAttribute("mode", $mode);
		if(isset($root)) {
					$this->xml->writeAttribute("root", $root);
		}
	}
	
	/**
	 * Write out an example phrase that will cause a grammar match.
	 *
	 * @param string $example
	 * @return void
	 */
	public function example($example) {
		$this->xml->startElement("example");
		$this->xml->text($example);
		$this->xml->endElement();
	}
	
	/**
	 * Write out a grammar item without any nested elements.
	 *
	 * @param string $item
	 * @param string $repeat
	 * @param string $repeatprob
	 * @param string $weight
	 * @return void
	 */
	public function item($item, $repeat=NULL, $repeatprob=NULL, $weight=NULL) {
		$this->xml->startElement("item");
		if(isset($repeat)){
			$this->xml->writeAttribute("repeat", $repeat);
		}
		if(isset($repeatprob)){
			$this->xml->writeAttribute("repeat-prob", $repeatprob);
		}
		if(isset($weight)){
			$this->xml->writeAttribute("weight", $weight);
		}
		$this->xml->text($item);
		$this->xml->endElement();
	}
	
	/**
	 * Write the start tage for a grammar item that has nested elements.
	 *
	 * @param string $repeat
	 * @param string $repeatprob
	 * @param string $weight
	 * @return void
	 */
	public function startItem($item, $repeat=NULL, $repeatprob=NULL, $weight=NULL) {
		$this->xml->startElement("item");
		if(isset($repeat)){
			$this->xml->writeAttribute("repeat", $repeat);
		}
		if(isset($repeatprob)){
			$this->xml->writeAttribute("repeat-prob", $repeatprob);
		}
		if(isset($weight)){
			$this->xml->writeAttribute("weight", $weight);
		}
		$this->xml->text($item);
	}
	
	/**
	 * Start a one-of block of grammar items.
	 *
	 * @return void
	 */
	public function startOneOf() {
		$this->xml->startElement("one-of");
	}
	
	/**
	 * Write out a grammar rule reference.
	 *
	 * @param string $uri
	 * @param SpecialRule $special
	 * @return void
	 */
	public function ruleRef($uri, SpecialRule $special) {
		$this->xml->startElement("ruleref");
		if(isset($uri)) {
			$this->xml->writeAttribute("uri", $uri);
		} else {
			$this->xml->writeAttribute("special", $special);
		}
		$this->xml->endElement();
	}
	
	/**
	 * Start a new grammar rule.
	 *
	 * @param string $id
	 * @param GrammarScope $scope
	 * @return void
	 */
	public function startRule($id, $scope=NULL) {
		$this->xml->startElement("rule");
		$this->xml->writeAttribute("id", $id);
		if(isset($scope)) {
			$this->xml->writeAttribute("scope", $scope);
		} else {
			$this->xml->writeAttribute("scope", GrammarScope::$private);
		}		
	}
	
	/**
	 * Write out a tag for semantic interpretation of a grammar match.
	 *
	 * @param string $tag
	 * @return void
	 */
	public function tag($tag, $cdata=false) {
		$this->xml->startElement("tag");
		if($cdata) {
			$this->xml->writeCData($tag);
		} else {
			$this->xml->text($tag);
		}		
		$this->xml->endElement();
	}
	
	/**
	 * Write out a grammar token.
	 *
	 * @param string $token
	 * @return void
	 */
	public function token($token) {
		$this->xml->startElement("token");
		$this->xml->text($token);
		$this->xml->endElement();
	}
	
	/**
	 * Generic element end tag.
	 *
	 * @return void
	 */
	public function endElement() {
		$this->xml->endElement();
	}

	/**
	 * Write out text in a grammar file (careful now...)
	 *
	 * @param string $text
	 */
	public function writeText($text) {
		$this->xml->text($text);
	}
	
	/**
	 * Write out the grammar to be consumed.
	 *
	 * @return string;
	 */
	public function writeGrammar() {
		$this->xml->endElement();
		if($this->standalone) {
			$this->xml->endDocument();
			header('Content-type: application/grammar+xml');
		}
		echo $this->xml->flush();
	}	
}

// Utility class for special grammar rules.
class SpecialRule {
	public static $null = "NULL";
	public static $void = "VOID";
	public static $garbage = "GARBAGE";	
}

// Utility class for grammar scope.
class GrammarScope {
	public static $public = "public";
	public static $private = "private";
}

?>
