<?php

class FileReader implements InputInterface
{
    private $filename;

    public function __construct(String $filename)
    {
        $this->filename = $filename;
    }

    public function read()
    {
        $resource = fopen($this->filename, "r") or die("Unable to open file!");
        $contents = fread($resource, filesize($this->filename));
        fclose($resource);
        return $contents;
    }
}
