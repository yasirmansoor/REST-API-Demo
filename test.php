<?php
//use this delete, update or create a new item via CURL


echo '<p>running start</p>';

//generate new security token
//(only valid for an hour, renew at: from index.php?controller=config&get_security_token=companybeta)
$url = new stdClass();
$url->base_url = 'http://192.168.33.22/apidemo/index.php?';
$url->security_token = 'security_token=OhFyJ4cU6zsQrRYKDVX8YsIkEibm-o8PUrqASoZT90c&consumer=companybeta';

//--------------------------------------------------------------------------------------------------------------------
//delete record

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url->base_url . $url->security_token . "controller=customers&format=json&id=100&");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
$output = curl_exec($ch);
echo($output) . PHP_EOL;
curl_close($ch);
*/


//--------------------------------------------------------------------------------------------------------------------
//new record

/*
$data_string = array(   'fullname' => 'India Y',
                        'address' => '16A India Close Y',
                        'test' => 'put something throwaway Y'
                    );

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url->base_url . $url->security_token . 'controller=customers&format=json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
$output = curl_exec($ch);
echo($output) . PHP_EOL;
curl_close($ch);
*/

//--------------------------------------------------------------------------------------------------------------------
//update record

/*
$data_string = array(   'fullname' => 'India Z',
                        'address' => '16A India Close Z',
                        'test' => 'put something throwaway Z'
                    );

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url->base_url . $url->security_token . "controller=customers&format=json&data=105");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
$output = curl_exec($ch);
echo($output) . PHP_EOL;
curl_close($ch);
*/

echo '<p>running end</p>';