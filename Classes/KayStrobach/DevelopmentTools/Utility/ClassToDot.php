<?php

namespace KayStrobach\DevelopmentTools\Utility;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

class ClassToDot {
	/**
	 * Attributes, which should be hidden
	 * @var array
	 */
	protected $attributesToIgnore = array(
		'Flow_Aop_Proxy_targetMethodsAndGroupedAdvices',
		'Flow_Aop_Proxy_groupedAdviceChains',
		'Flow_Aop_Proxy_methodIsInAdviceMode'
	);
	/**
	 * Types not to be connected
	 *
	 * @var array
	 */
	protected $attributesWithoutConnection = array(
		'boolean',
		'float',
		'string',
		'integer',
		'array',
		'\\DateTime',
	);

	/**
	 * @param $classes
	 * @return string
	 */
	public function makeDotFile($classes) {
		$classesParsed = array();
		$classParser = new ClassParser();
		foreach($classes as $class) {
			$classesParsed[] = $classParser->parseClass($class);
		}
		return $this->makeDotHead($classesParsed);
	}

	/**
	 * @param $classesParsed
	 * @return string
	 */
	protected function makeDotHead($classesParsed) {
		$buffer = 'digraph G {
			graph [
				center=true,
				nodesep=1.2,
				ranksep="1.2 equally",
				sep=6.2,
				splines=spline
			];
			node [
				fontname = "Arial"
				fontsize = 12
				shape = "record"
				sep = "+10
			]
			edge [
				fontname = "Arial"
				fontsize = 8
				dir = "both"
				arrowhead = "open"
				arrowtail = "odot"
				repulsiveforce = 6
				penwidth = 3
			]
		';

		foreach($classesParsed as $class) {
			$buffer .= $this->renderClass($class);
		}

		$buffer .= "\n".'}';

		return $buffer;
	}

	/**
	 * @param $class
	 * @return string
	 */
	protected function renderClass($class) {
		$buffer  = 'n' . md5($class['name']) . ' [label="{<f0>' . addslashes($class['name']) . '|';
		foreach($class['properties'] as $property) {
			if(!$this->isIgnoredProperty($property['name'])) {
				#$buffer .= '<f' .$property['name'] . '>';
				$buffer .= '+ ' . addslashes($property['name']) . ' : ' . addslashes($property['model']) . '\\l';
			}
		}
		$buffer .= '}" color="black", fillcolor="grey95", style="filled" fontcolor="black"];' . "\n";		$i = 1;
		foreach($class['properties'] as $property) {
			if(!$this->isIgnoredProperty($property['name'])) {
				if(!$this->isAttributeTypeWithoutConnection($property['model'])) {
					$buffer .= 'n' .md5($class['name']) . ':f' . $property['name'] . ' -> n' . md5($property['model']) .':f0 [label="*", color="' . $this->getColor($property['model']) . '"]' .";\n";
				}
			}
		}

		return $buffer;
	}

	/**
	 * @param $property
	 * @return bool
	 */
	protected function isIgnoredProperty($property) {
		if(in_array(trim($property), $this->attributesToIgnore)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param $property
	 * @return bool
	 */
	protected function isAttributeTypeWithoutConnection($property) {
		if(in_array(trim($property), $this->attributesWithoutConnection)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * Returns a color based on the nodename, should be a modelname or sth. similar
	 *
	 * Based on
	 * http://stackoverflow.com/questions/14488174/generate-high-contrast-random-color-using-php
	 *
	 * @param string $node
	 * @return string
	 */
	protected function getColor($node = NULL) {
			$colMinAvg =  10;
			$colMaxAvg = 255;
			$colStep   =  30;
			$range = $colMaxAvg - $colMinAvg;
			$factor = $range / 256;
			$offset = $colMinAvg;

			$base_hash = substr(md5($node), 0, 6);
			$b_R = hexdec(substr($base_hash,0,2));
			$b_G = hexdec(substr($base_hash,2,2));
			$b_B = hexdec(substr($base_hash,4,2));

			$f_R = floor((floor($b_R * $factor) + $offset) / $colStep) * $colStep;
			$f_G = floor((floor($b_G * $factor) + $offset) / $colStep) * $colStep;
			$f_B = floor((floor($b_B * $factor) + $offset) / $colStep) * $colStep;

			return sprintf('#%02x%02x%02x', $f_R, $f_G, $f_B);
	}
}