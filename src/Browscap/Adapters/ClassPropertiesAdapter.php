<?php

namespace Browscap\Adapters;

use Browscap\Parser\ParserInterface;

class ClassPropertiesAdapter
{
    protected $parser;

    protected $entityPrototype;

    protected $sourceProperty;

    protected $primaryKey;

    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    public function setEntityPrototype($object)
    {
        $this->entityPrototype = get_class($object);
        return $this;
    }

    public function setSourceProperty($sourceProperty)
    {
        $this->sourceProperty = $sourceProperty;
        return $this;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    public function populateEntities()
    {
        $data = $this->parser->getParsed();

        $entities = array();

        if (!isset($data->{$this->sourceProperty})) {
            $msg = sprintf('Source property "%s" was not found in parsed source "%s"', $this->sourceProperty, $this->parser->getFilename());
            throw new \Exception($msg);
        }

        foreach ($data->{$this->sourceProperty} as $sourceData) {
            $entity = new $this->entityPrototype();

            $props = get_object_vars($entity);

            foreach ($props as $prop => $unused) {
                if (isset($sourceData->{$prop})) {
                    $entity->{$prop} = $sourceData->{$prop};
                }
            }

            if (!isset($entity->{$this->primaryKey})) {
                $msg = sprintf('Primary key "%s" was not foudn in source data "%s"', $this->primaryKey, $this->parser->getFilename());
                throw new \Exception($msg);
            }

            if (isset($entities[$entity->{$this->primaryKey}]))
            {
                $msg = sprintf('Primary key "%s" (value: %s) was duplicated in source data "%s"', $this->primaryKey, $entity->{$this->primaryKey}, $this->parser->getFilename());
                throw new \Exception($msg);
            }

            $entities[$entity->{$this->primaryKey}] = $entity;
        }

        return $entities;
    }
}