<?php namespace DCarbone\Helpers;

/**
 * Class TableMapper
 * @package DCarbone\Helpers
 */
class TableMapper
{
    /** @var array */
    protected $rowCellMap = array();

    /** @var array */
    protected $rowGroups = array();

    /** @var \DOMDocument */
    protected $dom;

    /** @var \DOMXPath */
    protected $xpath;

    /** @var \DOMElement */
    protected $table;

    /**
     * @param \DOMElement $table
     */
    public function __construct(\DOMElement $table)
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->xpath = new \DOMXPath($this->dom);
        $imported = $this->dom->importNode($table->cloneNode(true), true);
        $this->dom->appendChild($imported);
        $this->table = $imported;
    }

    /**
     * @param callable $rowParseCallback
     * @param callable $cellParseCallback
     * @return void
     */
    public function createMap(\Closure $rowParseCallback = null, \Closure $cellParseCallback = null)
    {
        // First, dos ome basic cleanup
        $this->cleanupTable();

        // Then, get all rows and parse!
        $trs = $this->table->getElementsByTagName('tr');
        if ($trs instanceof \DOMNodeList && $trs->length > 0)
        {
            for ($tri = 0, $rowGroupi = 0; $tri < $trs->length; $rowGroupi++)
            {
                /** @var \DOMElement $tr */
                $tr = $trs->item($tri);

                $this->rowCellMap[$rowGroupi] = array();
                $this->rowGroups[$rowGroupi] = array();

                $rowSpan = 1;
                foreach($tr->childNodes as $child)
                {
                    /** @var $child \DOMElement */
                    if ($child->hasAttribute('rowspan') && (int)$child->getAttribute('rowspan') > $rowSpan)
                        $rowSpan = (int)$child->getAttribute('rowspan');
                }

                $this->parseRowGroup($this->rowCellMap[$rowGroupi], $this->rowGroups[$rowGroupi], $rowSpan, $tr, $rowParseCallback, $cellParseCallback);

                $tri += $rowSpan;
            }
        }
    }

    /**
     * @return void
     */
    protected function cleanupTable()
    {
        // First, remove all empty text nodes
        $texts = $this->xpath->query('//text()', $this->table);
        if ($texts instanceof \DOMNodeList && $texts->length > 0)
        {
            for ($texti = 0; $texti < $texts->length; )
            {
                /** @var $text \DOMText */
                $text = $texts->item($texti);
                $parent = $text->parentNode;

                if (trim($text->nodeValue) === '' && $parent !== null)
                    $parent->removeChild($text);
                else
                    $texti++;
            }
        }
    }

    /**
     * @param array $cellMap
     * @param array $rowGroup
     * @param int $rowSpan
     * @param \DOMElement $firstTR
     * @param callable $rowParseCallback
     * @param callable $cellParseCallback
     * @return void
     */
    protected function parseRowGroup(array &$cellMap, array &$rowGroup, $rowSpan, \DOMElement $firstTR, \Closure $rowParseCallback = null, \Closure $cellParseCallback = null)
    {
        if ($rowParseCallback instanceof \Closure)
            $rowParseCallback($firstTR);

        $rowGroup[] = $firstTR;

        // $firstTR is always a row which does not have cells that extend from previous rows
        for ($childi = 0; $childi < $firstTR->childNodes->length; $childi++)
        {
            /** @var \DOMElement $child */
            $child = $firstTR->childNodes->item($childi);

            if ($cellParseCallback !==  null)
                $cellParseCallback($child, $firstTR);

            // First, get the rowspan value of this child (if there is one)
            $childRowSpan = $child->hasAttribute('rowspan') ? (int)$child->getAttribute('rowspan') : 1;

            // If the child extends over multiple columns...
            if ($child->hasAttribute('colspan'))
            {
                // ...and is the only child of this <tr>
                if ($firstTR->childNodes->length === 1)
                {
                    for ($csi = 0; $csi < (int)$child->getAttribute('colspan'); $csi++)
                    {
                        $cellMap[0][$childi + $csi] = (string)($childRowSpan - 1).':'.$childi;
                    }
                }
                // ...and is NOT the only child of this <tr>
                else
                {
                    for ($csi = 0; $csi <= (int)$child->getAttribute('colspan'); $csi++)
                    {
                        $cellMap[0][$childi + $csi] = (string)($childRowSpan - 1).':'.$childi;
                    }
                }
            }
            // ...else
            else
            {
                $cellMap[0][$childi] = (string)($childRowSpan - 1).':'.$childi;
            }
        }

        $tr = $firstTR->nextSibling;
        for ($tri = 1; $tri < $rowSpan && $tr !== null; $tri++)
        {
            // Call the closure, if there is one.
            if ($rowParseCallback instanceof \Closure)
                $rowParseCallback($tr);

            // Add this row to the rows array
            $rowGroup[] = $tr;

            // Get any citation keys present in this row
//            $citationKeys = $citationKeys + $this->getRowCitationKeys($tr, $legendNodeArray);

            // Define this row's area in $rowCells
            $cellMap[$tri] = array();

            // Loop through and build the rowCell array.
            for($celli = 0, $tdi = 0; $celli < count($cellMap[0]); $celli++)
            {
                $previous = $cellMap[$tri - 1][$celli];
                $exp = explode(':', $previous);
                $prevRowSpan = (int)$exp[0];
                $prevTDI = (int)$exp[1];

                // If the previous row in position $celli defined a <td> that expands to/beyond this row
                if ($prevRowSpan >= $tri)
                {
                    $cellMap[$tri][$celli] = "-1:{$prevTDI}";
                }
                // If 0, then use <td> present in the current row
                else if ($prevRowSpan === 0)
                {
                    $child = $tr->childNodes->item($tdi);
                    $currRowSpan = ($child->hasAttribute('rowspan') ? (int)$child->getAttribute('rowspan') : 1);

                    if ($currRowSpan === 1)
                        $cellMap[$tri][$celli] = '0:'.$tdi;
                    else
                        $cellMap[$tri][$celli] = (($currRowSpan - 1) + $tri).':'.$tdi;

                    $tdi++;
                }
                // If this value is negative it means that the previous row's <td> was defined by a row further up
                else if ($prevRowSpan < 0)
                {
                    $sourceRowIdx = $prevRowSpan + ($tri - 1);
                    $sourceExp = explode(':', $cellMap[$sourceRowIdx][$celli]);
                    $sourceRowSpan = (int)$sourceExp[0];
                    $sourceTDI = (int)$sourceExp[1];

                    if ($sourceRowSpan >= $tri)
                    {
                        $cellMap[$tri][$celli] = ($sourceRowIdx - $tri).':'.$sourceTDI;
                    }
                    else if ($sourceRowSpan < $tri)
                    {
                        $child = $tr->childNodes->item($tdi);
                        $currRowSpan = ($child->hasAttribute('rowspan') ? (int)$child->getAttribute('rowspan') : 1);

                        if ($currRowSpan === 1)
                            $cellMap[$tri][$celli] = '0:'.$tdi;
                        else
                            $cellMap[$tri][$celli] = (($currRowSpan - 1) + $tri).':'.$tdi;

                        $tdi++;
                    }
                }
            }
            $tr = $tr->nextSibling;
        }
    }

    /**
     * @param int $groupNum
     * @param int $rowNum
     * @param string $cellNum
     * @return mixed
     */
    public function getCell($groupNum, $rowNum, $cellNum)
    {
        $exp = explode(':', $cellNum);
        $rowOffset = (int)$exp[0];
        $tdOffset = (int)$exp[1];
        if ($rowOffset >= 0)
            return $this->rowGroups[$groupNum][$rowNum]->childNodes->item($tdOffset);

        return $this->rowGroups[$groupNum][$rowNum + $rowOffset]->childNodes->item($tdOffset);
    }

    /**
     * @param int $groupNum
     * @param int $rowNum
     * @return \DOMElement
     */
    public function getTR($groupNum, $rowNum)
    {
        return $this->rowGroups[$groupNum][$rowNum];
    }

    /**
     * @return array
     */
    public function getRowCellMap()
    {
        return $this->rowCellMap;
    }

    /**
     * @return array
     */
    public function getRowGroups()
    {
        return $this->rowGroups;
    }

    /**
     * @return \DOMNode
     */
    public function getTableNode()
    {
        return $this->table->cloneNode(true);
    }

    /**
     * @param \DOMDocument $dom
     * @return \DOMNode
     */
    public function appendTableToDom(\DOMDocument $dom)
    {
        $table = $dom->importNode($this->table->cloneNode(true), true);
        $dom->appendChild($table);
        return $table;
    }

    /**
     * @param \DOMNode $node
     * @return \DOMNode|null
     */
    public function appendTableToNode(\DOMNode $node)
    {
        if ($node->ownerDocument === null)
            return null;

        if ($node->ownerDocument === $this->dom)
            return null;

        $table = $node->ownerDocument->importNode($this->table->cloneNode(true), true);
        return $node->appendChild($table);
    }

    /**
     * This is a debug method that will echo out a table with the reformed structure
     *
     * @return void
     */
    public function printParsedTableDebug()
    {
        echo '<h2>Cell Map</h2>';
        echo '<pre>';
        var_dump($this->rowCellMap);
        echo '</pre>';

        echo '<h2>Table Representation</h2>';

        echo '<table>';

        foreach($this->rowCellMap as $groupi=>$groupDef)
        {
            $length = count($groupDef[0]);

            echo sprintf('<tr><th colspan="%s">Group %s</th></tr>',
                $length,
                $groupi
            );

            foreach($groupDef as $rowi=>$cellMap)
            {
                echo '<tr>';

                for ($celli = 0; $celli < count($cellMap); $celli++)
                {
                    $cell = $cellMap[$celli];
                    $exp = explode(':', $cell);
                    $rowOffset = (int)$exp[0];
                    $tdOffset = (int)$exp[1];

                    $nodeValue = null;
                    if ($rowOffset >= 0)
                        $nodeValue = $this->rowGroups[$groupi][$rowi]->childNodes->item($tdOffset)->nodeValue;
                    else
                        $nodeValue = $this->rowGroups[$groupi][$rowi + $rowOffset]->childNodes->item($tdOffset)->nodeValue;

                    echo "<td>{$nodeValue}</td>";
                }
                echo '</tr>';
            }
        }

        echo '</table>';
    }

    /**
     * @throws \BadMethodCallException
     * @return void
     */
    public function printParsedTable()
    {
        throw new \BadMethodCallException('\DCarbone\Helpers\TableMapper::printParseTable is not yet implemented');

//        foreach($this->rowCellMap as $groupi=>$groupDef)
//        {
//            $length = count($groupDef[0]);
//
//            echo sprintf('<tr><th colspan="%s">Group %s</th></tr>',
//                $length,
//                $groupi
//            );
//
//            foreach($groupDef as $rowi=>$cellMap)
//            {
//                echo '<tr>';
//
//                for ($celli = 0, $tdi = 0; $celli < count($cellMap); $celli++)
//                {
//                    $cell = $cellMap[$celli];
//                    $exp = explode(':', $cell);
//                    $rowOffset = (int)$exp[0];
//                    $tdOffset = (int)$exp[1];
//                    if ($rowOffset >= 0)
//                    {
//                        echo '<td>';
//                        echo $this->rowGroups[$groupi][$rowi]->childNodes->item($tdOffset)->nodeValue;
//                        echo '</td>';
//                        $tdi++;
//                    }
//                    else
//                    {
//                        echo '<td>';
//                        echo $this->rowGroups[$groupi][$rowi + $rowOffset]->childNodes->item($tdOffset)->nodeValue;
//                        echo '</td>';
//                    }
//                }
//                echo '</tr>';
//            }
//        }
    }
}