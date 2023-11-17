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
                "rg"=>$data['rg'],
                "consultor_responsavel"=>$data['consultor_responsavel']
            ]
        ];
        
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    } 
    return false;
}

function adicionar_celular_btv($data){
    $file = (APPPATH."configConnectionBTV.json");


    if(file_exists($file)){


        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Cliente.adicionar_celular",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "cod_cliente"=>$data["cod_cliente"],
                "numero"=>$data["numero"]
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    }
    return false;
}

function adicionar_endereco_btv($data){
    $file = (APPPATH."configConnectionBTV.json");


    if(file_exists($file)){


        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Cliente.adicionar_endereco_cliente",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "cod_cliente"=>$data["cod_cliente"],
                "cod_estado"=>$data["cod_estado"],
                "cod_cidade"=>$data["cod_cidade"],
                "bairro"=>$data["bairro"],
                "rua"=>$data["rua"],
                "cep"=>$data["cep"],
                "complemento"=>"",
                "caixa_postal"=>"",
                "cod_predio"=>"",
                "codcep"=>"",
                "obs"=>"",
                "latitude"=>"",
                "longitude"=>"",
                "tipoendereco"=>"",
                "numeroEndereco"=>"",
                "numeroApto"=>"",
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    }
    return false;
}


function retornar_codigo_estado_btv($data){
    $file = (APPPATH."configConnectionBTV.json");


    if(file_exists($file)){


        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Endereco.retornar_estado_por_nome",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "nome_estado"=>$data['estado']
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);


        foreach( $response['lista_estados'] as $estado){
            if($data['estado'] == $estado['cod_estado'] || $data['estado'] == $estado['nome']){
                return $estado['cod_cidade'];//deveria ser cod_estado mas na API está assim
            }
        }
    }
    return false;
}


function retornar_codigo_cidade_btv($data){
    $file = (APPPATH."configConnectionBTV.json");


    if(file_exists($file)){


        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Endereco.retornar_cidade_por_nome",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "nome_cidade"=>$data['cidade']
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        foreach( $response['lista_cidades'] as $cidade){
            if($data['cidade'] == $cidade['nome'] && $data['cod_estado'] == $cidade['cod_estado']){
                return $cidade['cod_cidade'];
            }
        }
    }
    return false;
}

function criar_suporte_btv($data){
    $file = (APPPATH."configConnectionBTV.json");




    if(file_exists($file)){




        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Suporte.criar_suporte",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "cod_usuario"=> $data['cod_usuario'],
                "cod_situacao"=> $data['cod_situacao'],
                "cod_classe"=> $data['cod_classe'],
                "cod_categoria"=> $data['cod_categoria'],
                "cod_cliente"=> $data['cod_cliente'],
                "cod_usuario_cadastro"=> $data['cod_usuario_cadastro'],
                "problema"=> $data['problema'],
                "titulo"=> $data['titulo']
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    }
    return false;
}

function retornar_usuarios_pelo_nome_btv($data){
    $file = (APPPATH."configConnectionBTV.json");




    if(file_exists($file)){




        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Usuario.retornar_usuarios_pelo_nome",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "nome" => $data
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    }
    return false;
}


function adicionar_email_btv($data){
    $file = (APPPATH."configConnectionBTV.json");


    if(file_exists($file)){


        $conn = json_decode(file_get_contents($file),true);
        $param = [
            "funcao"=>"Cliente.adicionar_email",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "cod_cliente"=>$data["cod_cliente"],
                "email"=>$data["email"]
            ]
        ];
       
        $response = requestCurl($conn['url'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    }
    return false;
}
