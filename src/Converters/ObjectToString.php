<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 * @author      Wojtek Wnuk <wojtek@qsolutionsstudio.com>
 */

namespace Nanobots\DataPatchCreator\Converters;

class ObjectToString
{
    /**
     * Update var_export to php5.6 array notation
     *
     * @param string $exportedVariable
     * @return string
     */
    private function _updateArrayNotation(string $exportedVariable): string
    {
        $newNotation = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $exportedVariable);
        $array = preg_split("/\r\n|\n|\r/", $newNotation);
        $array = preg_replace(
            ["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/", '/\bNULL\b/'],
            [null, ']$1', ' => [' , 'null'],
            $array
        );
        return join(PHP_EOL, array_filter(["["] + $array));
    }

    /**
     * Add spaces to exported string, first row without indent
     *
     * @param string $str
     * @param int $spaces
     * @return string
     */
    private function _indent(string $str, int $spaces): string
    {
        $parts = array_filter(explode("\n", $str));
        $firstRow = $parts[0];
        $parts = array_map(function ($part) use ($spaces) {
            return str_repeat(' ', $spaces) . $part;
        }, array_splice($parts, 1));
        return implode("\n", array_merge([$firstRow], $parts));
    }

    /**
     * Converts DataArray to php5.6 array notation with indent
     *
     * @param array $array
     * @param int $spaces
     * @return string
     */
    public function convertDataArrayToString(array $array, int $spaces = 12): string
    {
        return $this->_indent($this->_updateArrayNotation(var_export($array, true)), $spaces);
    }
}
