<?php
/**
 * Get Issue Count from Youtrack given a filter
 */
class YoutrackIssueCount extends YoutrackApi
{
    private $filter;

    /**
     * Constructor
     * @param String    $configFileName Configuration Filename
     * @param String    $filter        Project filter
     */
    public function __construct(String $configFileName, String $filter = "")
    {
        parent::__construct($configFileName);
        $this->filter = $filter;
    }

    public function getCount()
    {
        //url to read many issues from Youtrack
        $url = $this->baseUrl . "/rest/issue/count?filter={$this->filter}";

        //Headers
        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer {$this->token}"
        ];

        //open connection
        $ch = curl_init();

        //set the url, number of GET vars, GET data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        //Format JSONP (json with padding) data
        $data = preg_replace('/.+?({.+}).+/','$1',$result);
        $data = json_decode($data);

        return $data->value;
    }
}
