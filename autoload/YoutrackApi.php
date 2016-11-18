<?php
/**
 * Youtrack API
 */
class YoutrackApi
{
    private static $instance;
    protected $configFileName;
    private $authUrl;
    private $serviceId;
    private $serviceSecret;
    private $serviceScope;
    protected $baseUrl;
    private $username;
    private $password;
    protected $token;
    protected $refreshToken;
    protected $tokenExpiry = 0;

    /**
     * Singleton Pattern
     * @return object Instance of this object
     */
    public static function getInstance(string $configFileName)
    {
        if (static::$instance === null) {
            static::$instance = new static($configFileName);
        }
        return static::$instance;
    }
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Constructor
     * @param string    $configFileName Configuration Filename
     */
    protected function __construct(string $configFileName)
    {
        $this->configFileName = $configFileName;
        $this->loadConfig();
        $this->getOrRefreshToken();
    }

    private function loadConfig()
    {
        $this->configFileName = $this->configFileName;
        $resource = fopen($this->configFileName, 'r');
        $contents = fread($resource, filesize($this->configFileName));
        $contentsJSON = json_decode($contents);
        fclose($resource);

        foreach ($contentsJSON as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get a new access token or fresh an old one
     */
    private function getOrRefreshToken()
    {
        if ($this->tokenExpiry == 0) {
            //Get Token
            $url = $this->tokenUrl;
            $authCode = base64_encode($this->serviceId.':'.$this->serviceSecret);
            $headers = [
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic {$authCode}"
            ];
            $fields = [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
                'scope' => $this->serviceScope,
                'access_type' => 'offline'
            ];
            $result = MyHelper::curlCall($url, 'POST', $headers, $fields);
            $data = json_decode($result);
            $this->token = $data->access_token;
            $this->tokenExpiry = time() + $data->expires_in;
            $this->refreshToken = $data->refresh_token;

        } elseif (time() > $this->tokenExpiry) {
            //refresh token
            $url = $this->tokenUrl;
            $authCode = base64_encode($this->serviceId.':'.$this->serviceSecret);
            $headers = [
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic {$authCode}"
            ];
            $fields = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
                'scope' => $this->serviceScope,
            ];
            $result = MyHelper::curlCall($url, 'POST', $headers, $fields);
            $data = json_decode($result);
            $this->token = $data->access_token;
            $this->tokenExpiry = time() + $data->expires_in;
        }
    }

    /**
     * Get count of issues
     * @param  string $filter Filter to apply
     * @return int         Number of issues found
     */
    public function getIssueCount(string $filter = "")
    {
        //Refresh token if needed
        $this->getOrRefreshToken();

        //Call API
        $url = $this->baseUrl . "/rest/issue/count?filter={$filter}";
        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer {$this->token}"
        ];
        $result = MyHelper::curlCall($url, 'GET', $headers);

        //Format JSONP (json with padding) data
        $data = preg_replace('/.+?({.+}).+/','$1',$result);
        $data = json_decode($data);

        return $data->value;
    }

    /**
     * Constructor
     * @param int       $after          Start looking after many issues
     * @param int       $max            Maximum issues to export
     * @param string    $project        Project to export from
     * @param string    $filter        Project filter
     */
    public function getIssues(int $after = 0, int $max = 10, string $project = 'OGS', $filter = "")
    {
        //Refresh token if needed
        $this->getOrRefreshToken();

        //Call API
        $url = $this->baseUrl . "/rest/issue/byproject/{$project}?max={$max}&after={$after}&filter={$filter}";
        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer {$this->token}"
        ];
        $result = MyHelper::curlCall($url, 'GET', $headers);

        return $result;
    }
}
