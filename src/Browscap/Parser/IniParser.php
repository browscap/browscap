<?php

namespace Browscap\Parser;

/**
 * Class IniParser
 *
 * @package Browscap\Parser
 */
class IniParser implements ParserInterface
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $shouldSort = false;

    /**
     * @var array
     */
    protected $data;

    protected $fileLines;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param  bool $shouldSort
     *
     * @return \Browscap\Parser\IniParser
     */
    public function setShouldSort($shouldSort)
    {
        $this->shouldSort = (bool) $shouldSort;

        return $this;
    }

    public function shouldSort()
    {
        return $this->shouldSort;
    }

    public function getParsed()
    {
        return $this->data;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getLinesFromFile()
    {
        $filename = $this->filename;

        if (!file_exists($filename)) {
            throw new \InvalidArgumentException("File not found: {$filename}");
        }

        return file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function setFileLines(array $fileLines)
    {
        $this->fileLines = $fileLines;
    }

    public function getFileLines()
    {
        if (!$this->fileLines) {
            $fileLines = $this->getLinesFromFile();
        } else {
            $fileLines = $this->fileLines;
        }

        return $fileLines;
    }

    public function parse()
    {
        $fileLines = $this->getFileLines();

        $data = array();

        $currentSection  = '';
        $currentDivision = '';

        for ($line = 0; $line < count($fileLines); $line++) {

            $currentLine       = ($fileLines[$line]);
            $currentLineLength = strlen($currentLine);

            if ($currentLineLength == 0) {
                continue;
            }

            if (substr($currentLine, 0, 40) == ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;') {
                $currentDivision = trim(substr($currentLine, 41));
                continue;
            }

            // We only skip comments that *start* with semicolon
            if ($currentLine[0] == ';') {
                continue;
            }

            if ($currentLine[0] == '[') {
                $currentSection = substr($currentLine, 1, ($currentLineLength - 2));
                continue;
            }

            $bits = explode("=", $currentLine);

            if (count($bits) > 2) {
                throw new \RuntimeException("Too many equals in line: {$currentLine}");
            }

            if (count($bits) < 2) {
                throw new \RuntimeException("Too few equals in line: {$currentLine}");
            }

            $data[$currentSection][$bits[0]] = $bits[1];
            $data[$currentSection]['Division'] = $currentDivision;
        }

        if ($this->shouldSort()) {
            $data = $this->sortArrayAndChildArrays($data);
        }

        $this->data = $data;

        return $data;
    }

    protected function sortArrayAndChildArrays($array)
    {
        ksort($array);

        foreach ($array as $key => $childArray) {
            if (is_array($childArray) && !empty($childArray)) {
                $array[$key] = $this->sortArrayAndChildArrays($childArray);
            }
        }

        return $array;
    }
}
