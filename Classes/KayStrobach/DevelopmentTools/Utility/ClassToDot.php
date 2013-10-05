<?php

namespace KayStrobach\DevelopmentTools\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SBS.LaPo".              *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

class ClassToDot {
	protected $attributesToIgnore = array(
		'Flow_Aop_Proxy_targetMethodsAndGroupedAdvices',
		'Flow_Aop_Proxy_groupedAdviceChains',
		'Flow_Aop_Proxy_methodIsInAdviceMode'
	);
	protected $attributesWithoutConnection = array(
		'boolean',
		'float',
		'string',
		'integer',
		'\\DateTime',
	);

	public function makeDotFile($classes) {
		$classesParsed = array();
		$classParser = new ClassParser();
		foreach($classes as $class) {
			$classesParsed[] = $classParser->parseClass($class);
		}
		return $this->makeDotHead($classesParsed);
	}

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
			]
		';

		foreach($classesParsed as $class) {
			$buffer .= $this->renderClass($class);
		}

		$buffer .= "\n".'}';

		return $buffer;
	}
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
					$buffer .= 'n' .md5($class['name']) . ':f' . $property['name'] . ' -> n' . md5($property['model']) .':f0 [label="*"]' .";\n";
				}
			}
		}

		return $buffer;
	}
	protected function isIgnoredProperty($property) {
		if(in_array($property, $this->attributesToIgnore)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	protected function isAttributeTypeWithoutConnection($property) {
		if(in_array($property, $this->attributesWithoutConnection)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	protected function renderNode($node) {

	}
}