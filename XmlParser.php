<?php

class XmlParser
{
    private $input;
    private $string;
    private $xml;

    /**
     * Receive an input
     * @param InputInterface $input set and read the input into a string
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
        $this->string = $this->input->read();
    }

    /**
     * Parse the XML and return the SimpleXMLElement object
     * @return SimpleXMLElement XML object
     */
    public function parse()
    {
        $this->xml = simplexml_load_string($this->string);
        return $this->xml;
    }
}
