<?php
/**
 * xmlToArray: Converts simple XML data to an associative array
 *
 * This is a simple class which converts a string of XML data into a PHP associative array.
 * As a simple implementation, it does not care about schema and ignores XML properties.
 *
 * PHP version 5
 *
 * Copyright (c) 2010 Dave Hensley
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 * 
 * @author Dave Hensley
 * @copyright Dave Hensley 2010
 * @link http://dave.ag/xml-to-array/
 * @package xmlToArray
 * @todo Add more methods for array manipulation after array conversion, plus XML export
 * @version 0.1
 */

/**
 * xmlToArray class
 *
 * Provides methods for converting an XML string to a threaded associative array
 *
 * @package xmlToArray
 */
  class xmlToArray {

/**
 * The array which is built from the data supplied in the XML string
 *
 * @access public
 * @var array
 */
    public $array = array();

/**
 * Collapses single array elements
 *
 * This method scans an array for collapsable elements. An element is collapsable if it
 * contains an indexed array holding only a single element. For example, if a Person only
 * has one BirthDate, $array['People']['Person'][4]['BirthDate'][0] becomes
 * $array['People']['Person'][4]['BirthDate']. This should be called recursively so that
 * all levels of the array will be collapsed (as is done by the _worker() method).
 *
 * @access private
 * @param array $array The array which is to be collapsed 
 * @return array The collapsed array
 */
    private function _collapse ($array) {
      foreach ($array as $tag => $data) {
        if (is_array($data) && (count($data) == 1)) {
          $array[$tag] = $data[0];
        }
      }
      return $array;
    }

/**
 * Worker method
 *
 * This method recursively walks the output of xml_parse_into_struct(), creating a
 * threaded associative array.
 *
 * @access private
 * @param array $a_values A "values" array created by xml_parse_into_struct()
 * @param boolean $collapse True if the array should be collapsed
 * @return array The resulting multi-level array
 */
    private function _worker (&$a_values, $collapse) {
      while ($element = array_shift($a_values)) {
        switch ($element['type']) {
          case 'open':
            $array[$element['tag']][] = $this->_worker($a_values, $collapse);
            break;
          case 'complete':
            if (isset($element['value'])) {
              $array[$element['tag']][] = $element['value'];
            }
            break;
          case 'close':
            return $collapse ? $this->_collapse($array) : $array;
        }
      }
      return $collapse ? $this->_collapse($array) : $array;
    }

/**
 * Constructor
 *
 * The constructor takes the input XML string and builds an associative array out of it
 *
 * @access public
 * @param string $xml_string The input string in XML format
 * @param boolean $collapse True if the array should be collapsed
 */
    public function __construct ($xml_string, $collapse = true) {
      xml_parse_into_struct($xml_parser = xml_parser_create(), $xml_string, $a_values);
      xml_parser_free($xml_parser);
      $this->array = $this->_worker($a_values, $collapse);
    }
  }
?>
