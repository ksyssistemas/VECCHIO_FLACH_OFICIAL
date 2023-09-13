<?php
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\RequestException;
defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');

/*--------------------------------------------------------------*/
function integracao_btv($client_area = false)
{
    $CI = &get_instance();
    if (get_option('integrado_btv') == 1) {
        return true;
    }
    return false;
    
}
function integracao_hd($client_area = false)
{
    $CI = &get_instance();
    if (get_option('integrado_hd') == 1) {
        return true;
    }
    return false;
}

function requestCurl($url, $parametros, $method = 'POST') {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cert/file/cacert.pem');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parametros));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $retorno = json_decode(curl_exec($ch));

    curl_close($ch);
    return $retorno;
}

function adicionar_cliente_btv($data){
    $file = (APPPATH."configConnectionBTV.json");

    //verificar se é CPF ou CNPJ
    if(strlen($data['cpfcnpj']) > 11){
        $data['pessoafisica'] = 0;
    }else{
        $data['pessoafisica'] = 1;
    }

    if(file_exists($file)){

        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Cliente.criar_cliente",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "nome" => $data['nome'], 
                "cpfcnpj"=>$data['cpfcnpj'],
                "pessoafisica"=>$data['pessoafisica'],
                "inscricaoestadual"=>$data['inscricaoestadual'],
                "razaosocial"=>$data['razaosocial'],
                "rg"=>$data['rg'] 
            ]
        ];
        
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    } 
    return false;
}