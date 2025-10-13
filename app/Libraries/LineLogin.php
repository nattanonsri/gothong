<?php
namespace App\Libraries;

class LineLogin
{
    private $config;

    public function __construct()
    {
        $this->config = new \Config\LineLogin();
    }
    public function getLink()
    {
        $state = bin2hex(random_bytes(16));
        $session = session();
        $session->set(['state'=> $state]);

        $params = [
            'response_type' => 'code',
            'client_id' => $this->config->clientId,
            'redirect_uri' => $this->config->redirectUrl,
            'scope' => 'profile openid email',
            'state' => $state,
        ];

        $query = http_build_query($params);
        $link = $this->config->authUrl . '?' . $query;

        return $link;
    }


    public function refresh($token)
    {
        if(empty($token)){
            return false;
        }
        $data = [
            "grant_type" => "refresh_token",
            "refresh_token" => $token,
            "client_id" => $this->config->clientId,
            "client_secret" => $this->config->clientSecret
        ];

        // Make sure to use HTTPS for requests to ensure data confidentiality during transit
        $response = $this->sendSecureRequest($this->config->tokenUrl, 'POST', $data);

        // Validate the response and handle any errors or unexpected data
        $responseData = json_decode($response);

        if (!$responseData || !isset($responseData->access_token)) {
            log_message('error', 'LINE API Response: Invalid response from the server');
            return false;
        }

        return $responseData;
    }

    public function token($code, $state)
    {
        session();
//
//        if (session()->get('state') != $state) {
//            return [
//                'code' => 'error',
//                'data' => 'no data'
//            ];
//        }

        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $this->config->redirectUrl,
            "client_id" => $this->config->clientId,
            "client_secret" => $this->config->clientSecret
        ];

        add_log(session()->get('user_id') ?? '','line/token','line/token', $data);

        $response = $this->sendSecureRequest($this->config->tokenUrl, 'POST', $data);
        $responseData = json_decode($response);

        add_log(session()->get('user_id') ?? '','line_login_response','line_login_response', $responseData ?? []);

        if (!$responseData || !isset($responseData->access_token)) {
            log_message('error', 'LINE API Response: ' . print_r($responseData, true));
            return [
                'code' => 'error',
                'data' => 'Invalid response from the server'
            ];
        }

        return [
            'code' => 'success',
            'data' => $responseData
        ];
    }
    private function sendSecureRequest($url, $method, $data = null, $headers = [])
    {
        $request = curl_init();

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2); // Use value 2 to enable SSL verification
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        if ($headers) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($request);

        if ($response === false) {
            $err = curl_error($request);
            log_message('error', 'cURL error:'.print_r($err));
            return false;
        }

        curl_close($request);

        return $response;
    }

    public function profileFormIdToken($token = null)
    {
        if (empty($token->id_token)) {
            return false;
        }
        $payload = explode('.', $token->id_token);

        log_message('alert','profileFormIdToken: '.json_encode($payload));
        $defaultValues = [
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'name' => '',
            'picture' => '',
            'email' => ''
        ];

        if (count($payload) == 3) {
            $decodedPayload = json_decode(base64_decode($payload[1]), true);
            log_message('alert','decodedPayload profileFormIdToken: '.json_encode($decodedPayload));

            // Merge decodedPayload with default values if it's a valid array
            if (is_array($decodedPayload)) {
                $ret = array_merge($defaultValues, $decodedPayload);
            } else {
                $ret = $defaultValues;
            }
            log_message('alert','ret profileFormIdToken: '.json_encode($ret));
        } else {
            $ret = $defaultValues;
        }

        return (object) $ret;
    }


    function profile($token)
    {
        if(empty($token)){
            return false;
        }
        $header = ['Authorization: Bearer ' . $token];
        $response = $this->sendSecureRequest($this->config->profileUrl, 'GET', null, $header);
        return json_decode($response);
    }

    function verify($token)
    {
        if(empty($token)){
            return false;
        }
        $url = $this->config->verifyTokenUrl . '?access_token=' . $token;
        $response = $this->sendSecureRequest($url, NULL, 'GET');
        return $response;
    }

    function revoke($token)
    {
        if(empty($token)){
            return false;
        }
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "access_token" => $token,
            "client_id" => $this->config->clientId,
            "client_secret" => $this->config->clientSecret
        ];
        $response = $this->sendSecureRequest($this->config->revokeUrl, $header, 'POST', $data);
        return $response;
    }

}

