<?php
/**
 * Get Issues from Youtrack API
 */
class YoutrackIssuesReader extends YoutrackApi implements InputInterface
{
    private $after;
    private $max;
    private $project;
    private $filter;

    /**
     * Constructor
     * @param String    $configFileName Configuration Filename
     * @param int       $after          Start looking after many issues
     * @param int       $max            Maximum issues to export
     * @param String    $project        Project to export from
     * @param String    $filter        Project filter
     */
    public function __construct(String $configFileName, int $after = 0, int $max = 10, String $project = 'OGS', $filter = "")
    {
        parent::__construct($configFileName);

        $this->after = $after;
        $this->max = $max;
        $this->project = $project;
        $this->filter = $filter;
    }

    public function read()
    {
        //url to read many issues from Youtrack
        $url = $this->baseUrl . "/rest/issue/byproject/{$this->project}?max={$this->max}&after={$this->after}&filter={$this->filter}";

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

        return $result;
    }
}
