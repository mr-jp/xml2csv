<?php
/**
 * Youtrack API
 */
class YoutrackApi
{
    protected $configFileName;
    private $authUrl;
    private $serviceId;
    private $serviceSecret;
    private $serviceScope;
    protected $baseUrl;
    private $username;
    private $password;
    protected $token;

    /**
     * Constructor
     * @param String    $configFileName Configuration Filename
     */
    public function __construct(String $configFileName)
    {
        $this->configFileName = $configFileName;
        $this->loadConfig();
        $this->token = $this->getToken();
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

    private function getToken()
    {
        //Headers
        $authCode = base64_encode($this->serviceId.':'.$this->serviceSecret);
        $headers = [
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Basic {$authCode}"
        ];

        //Fields string
        $fieldsString = '';
        $fields = [
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password,
            'scope' => $this->serviceScope,
        ];
        foreach($fields as $key=>$value) { $fieldsString .= $key.'='.$value.'&'; }
        $fieldsString = rtrim($fieldsString, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        $data = json_decode($result);
        return $data->access_token;
    }
}
