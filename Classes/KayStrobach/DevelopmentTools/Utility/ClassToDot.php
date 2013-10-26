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
	 * contains the nodes which are already rendered, is aggregated during run
	 * @var array
	 */
	protected $nodes = array();

	/**
	 * edges to be drawn
	 * @var array
	 */
	protected $edges = array();

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
				shape = "plaintext"
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
		foreach($this->edges as $edge) {
			$buffer .= $this->renderEdge($edge);
		}

		$buffer .= "\n".'}';

		return $buffer;
	}

	function renderEdge($edge) {
		$buffer  = '';
		if(!in_array($edge['destinationClassName'], $this->nodes)) {
			$buffer .= $this->renderFallbackNode($edge['destinationClassName']);
		}
		if($edge['type'] === 'use') {
			$buffer .= 'n' .md5($edge['sourceClassName']) . ':f' . md5($edge['sourcePropertyName']) . ' -> n' . md5($edge['destinationClassName']) .':f0 [label="-", color="' . $this->getColor(sha1($edge['destinationClassName'])) . '"]' .";\n";
		} elseif($edge['type'] === 'extends') {
			$buffer .= 'n' .md5($edge['sourceClassName']) . ':f' . md5($edge['sourcePropertyName']) . ' -> n' . md5($edge['destinationClassName']) .':f0 [label="extends", color="grey75", arrowhead="empty"]' .";\n";
		} elseif($edge['type'] === 'implements') {

		}
		return $buffer;
	}

	function renderFallbackNode($classname) {
		return 'n' . md5($classname) . '[label=<<table CELLPADDING="0" CELLSPACING="0" BGCOLOR="grey95"><tr><td port="f0">' . addslashes($classname) . '</td></tr></table>> ];' . "\n";
	}

	/**
	 * @param $class
	 * @return string
	 */
	protected function renderClass($class) {
		$this->nodes[] = $class['name'];
		$buffer  = 'n' . md5($class['name']) . ' [label=<<table CELLPADDING="0" CELLSPACING="0" BGCOLOR="grey95"><tr><td port="f0"><b>' . addslashes($class['name']) . '</b></td></tr><tr><td><table BORDER="0">';
		foreach($class['properties'] as $property) {
			if(!$this->isIgnoredProperty($property['name'])) {
				#$buffer .= '<f' .$property['name'] . '>';
				$buffer .= '<tr><td  ALIGN="LEFT" port="f' . md5($property['name']) . '">+' . addslashes($property['name']) . ' : ' . addslashes($property['model']) . '</td></tr>';
			}
		}
		$buffer .= '</table></td></tr></table>>];' . "\n";		$i = 1;
		foreach($class['properties'] as $property) {
			if(!$this->isIgnoredProperty($property['name'])) {
				if(!$this->isAttributeTypeWithoutConnection($property['model'])) {
					$this->edges[] = array(
						'type'                 => 'use',
						'sourceClassName'      => $class['name'],
						'sourcePropertyName'   => $property['name'],
						'destinationClassName' => $property['model'],
					);
				}
			}
		}

		if($class['parent']) {
			$this->edges[] = array(
				'type'                 => 'extends',
				'sourceClassName'      => $class['name'],
				'sourcePropertyName'   => '',
				'destinationClassName' => $class['parent'],
			);
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