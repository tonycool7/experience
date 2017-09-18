<?php

namespace App\Http\Controllers\Sharelogin;

use App\Sharelogin\User_list;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VaultController extends Controller
{
    private static $token = "";
    private $client_token = "";
    private static $serverAddr = "http://127.0.0.1:8200";

    function __construct()
    {
        self::$token = env('VAULT_ROOT_TOKEN');
        $this->middleware('auth');
    }

    public static function sendRequestToVault($token, $url, $request = "GET", $data = []){
        $cSession = curl_init();
        //$token = ($token != "") ? $token : $this->token;
        if($request == "GET"){
            foreach ($data as $key => $value){
                $url .= $url."?".$key."=".$value;
            }
        }
        curl_setopt($cSession,CURLOPT_URL,$url);
        curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, $request);
        curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
        curl_setopt ($cSession, CURLOPT_SSL_VERIFYPEER, FALSE);
        if($request == "POST"){
            $data = json_encode($data);
            curl_setopt($cSession, CURLOPT_POSTFIELDS, $data);
            curl_setopt($cSession, CURLOPT_HTTPHEADER, array(
                'X-Vault-Token: '.self::$token,
                'Content-Type: application/json'
            ));
        }else{
            curl_setopt($cSession, CURLOPT_HTTPHEADER, array('X-Vault-Token: '.self::$token));
        }
        //dd($cSession);
        curl_setopt($cSession,CURLOPT_HEADER, false);
        $result=curl_exec($cSession);
        curl_close($cSession);
        // dd($result);
        return $result;
    }

    public function init(){
        $url = self::$serverAddr."/v1/sys/init";
        dd(json_decode($this->sendRequestToVault(self::$token,$url)));
    }

    public function authWithToken(){

    }

    public function unseal(){

    }

    public static function createClientToken(Request $request, $id){
        //  dd($request->cookie('ShareloginToken'));
        if(self::createUserPolicy($id) == null){
            $policy = User_list::findOrFail($id)->hostname;
            $url = self::$serverAddr.'/v1/auth/token/create';
            $data = ['policies' => $policy, "ttl" => "24h", "renewable" => true];
            $token= json_decode(self::sendRequestToVault(self::$token,$url, "POST", $data))->auth->client_token;
//        return response("Token:".$token)->cookie('X-Sharelogin-Token',$token,60,'/','.sharelog.in')->cookie('X-Sharelogin-User',$policy,60,'/','.sharelog.in');
            return response($token)->cookie('X-Sharelogin-Token',$token,60,'/','.sharelog.in')->cookie('X-Sharelogin-User',$policy,60,'/','.sharelog.in');
        }else{
            return "Policy not created";
        }
    }

    public function listTokenAccessors(){
        $url = self::$serverAddr.'/v1/auth/token/accessors';
        dd(json_decode($this->sendRequestToVault(self::$token,$url, "LIST")));
    }

    public function listPolicy(){
        $url = self::$serverAddr.'/v1/sys/policy';
        dd(json_decode(self::sendRequestToVault(self::$token,$url, "GET")));
    }

    public function readPolicy($policy){
        $url = self::$serverAddr.'/v1/sys/policy/'.$policy;
        dd(json_decode(self::sendRequestToVault(self::$token,$url, "GET")));
    }

    public static function createUserPolicy($id){
        $name = User_list::findOrFail($id)->hostname;
        $url = self::$serverAddr.'/v1/sys/policy/'.$name;
        $rule = ['rules' => "path \"secret/".$name."\" {capabilities = [\"read\", \"create\", \"update\"]}"];
        return json_decode(self::sendRequestToVault(self::$token,$url, "POST", $rule));
        // return json_decode($this->sendRequestToVault("",$url, "POST", $rule)) == null ? "Policy created" : "false";
    }

    public function setSecret($secret, $id){
        $token = \Request::cookie('X-Sharelogin-Token');
        $name = User_list::findOrFail($id)->hostname;
        $url = self::$serverAddr.'/v1/secret/'.$name;
        $data = ['name' => $secret];
//        dd(json_decode($this->sendRequestToVault($token, $url, "POST", $data)));
        return json_decode(self::sendRequestToVault($token,$url, "POST", $data)) == null ? "true" : "false";
    }

    public function getSecret($id,$token){
        $secret = User_list::findOrFail($id)->hostname;
        $url = self::$serverAddr.'/v1/secret/'.$secret;
        dd(self::sendRequestToVault($token,$url));
    }

    public function listSecrets($secret){
        $url = self::$serverAddr.'/v1/secret/';
        dd(json_decode(self::sendRequestToVault("",$url, "LIST")));
    }

}
