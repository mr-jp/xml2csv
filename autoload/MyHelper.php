<?php

class MyHelper
{
    public static function fPrint($e)
    {
        echo "<pre>";
        print_r($e);
        echo "</pre>";
    }

    /**
     * Call API with CURL
     * @param  string $url     URL to call
     * @param  string $method  POST or GET
     * @param  array  $headers Array headers
     * @param  array  $fields  Array of fields
     * @return object          Object returned by CURL
     */
    public static function curlCall(string $url, string $method = "POST", array $headers, array $fields = [])
    {
        //Fields string
        if (!empty($fields)) {
            $fieldsString = '';
            foreach($fields as $key=>$value) { $fieldsString .= $key.'='.$value.'&'; }
            $fieldsString = rtrim($fieldsString, '&');
        }

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        //set options
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);

            if (!empty($fields)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $result;
    }

    /**
     * Read File as String
     * @param  string $filename Filename
     * @return string           Contents of the file
     */
    public static function readFileAsString(string $filename)
    {
        $resource = fopen($filename, "r") or die("Unable to open file!");
        $contents = fread($resource, filesize($filename));
        fclose($resource);
        return $contents;
    }

    /**
     * Parse String as simplexml object
     * @param  string $input String to parse
     * @return object        SimpleXmlObject
     */
    public static function parseXml(string $input)
    {
        return simplexml_load_string($input);
    }
}
