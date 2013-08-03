<?php

namespace Browscap\Parser;

class JsonParser implements ParserInterface
{

    /**
     * @var string
     */
    protected $filename;

    protected $decoded;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function parse()
    {
        $fileContents = file_get_contents($this->getFilename());

        // @todo Replace with Zend\Json or Symfony equiv
        $this->decoded = json_decode($fileContents);
    }

    public function getParsed()
    {
        if (empty($this->decoded))
        {
            $this->parse();
        }

        return $this->decoded;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
