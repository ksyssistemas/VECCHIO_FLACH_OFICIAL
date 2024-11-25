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
function integracao_logosystem($client_area = false)
{
    $CI = &get_instance();
    if (get_option('integrado_logosystem') == 1) {
        return true;
    }
    return false;
    
}
function get_integration_variables(){
    $integration_variables = array();
    $integration_variables["baseDados"]["ip"] = get_option('integration_btv_base_dados_ip');
    $integration_variables["baseDados"]["user"] = get_option('integration_btv_base_dados_user');
    $integration_variables["baseDados"]["password"] = get_option('integration_btv_base_dados_password');
    $integration_variables["baseDados"]["db"] = get_option('integration_btv_base_dados_db');
    $integration_variables["baseDados"]["port"] = get_option('integration_btv_base_dados_port');

    $integration_variables["url"] = get_option('integration_btv_url');
    /*$integration_variables["url-prod"] = get_option('integration_btv_url_prod');
    $integration_variables["url-teste"] = get_option('integration_btv_url_teste');
    $integration_variables["url_hd"] = get_option('integration_btv_url_hd');*/

    $integration_variables["url_crm_logosystem"] = get_option('integration_url_crm_logosystem');
    $integration_variables["url_logosystem"] = get_option('integration_url_logosystem');
    $integration_variables["token_logosystem"] = get_option('integration_token_logosystem');

    return $integration_variables;
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

function requestCurlLogosystem($url, $parametros = "", $token, $method = 'GET') {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: bearer $token"
    ));
    if($parametros != ""){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    }
        

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


function adicionar_cliente_btv($data){
    

    //verificar se é CPF ou CNPJ
    if(strlen($data['cpfcnpj']) > 11){
        $data['pessoafisica'] = 0;
    }else{
        $data['pessoafisica'] = 1;
    }

    if(integracao_btv()){

        $conn = get_integration_variables();
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
    

    if(integracao_btv()){


        $conn = get_integration_variables();
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
    


    if(integracao_btv()){


        $conn = get_integration_variables();
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
    


    if(integracao_btv()){


        $conn = get_integration_variables();
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
    

    if(integracao_btv()){


        $conn = get_integration_variables();
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
    
    if(integracao_btv()){


        $conn = get_integration_variables();
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
    
    if(integracao_btv()){

        $conn = get_integration_variables();
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
    
    if(integracao_btv()){

        $conn = get_integration_variables();
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

function adicionar_cliente_logosystem($data){
    

    //verificar se é CPF ou CNPJ
    if(strlen($data['cpf_cnpj']) > 11){
        $data['rg'] = "";
    }else{
        $data['inscricao_estadual'] = "";
    }

    if(integracao_logosystem()){

        $conn = get_integration_variables();
        $param = [
            "funcao"=>"Logosystem.criar_cliente",
                "bd" => [
                "ip" => $conn["baseDados"]["ip"],
                "user" => $conn["baseDados"]["user"],
                "password" => $conn["baseDados"]["password"],
                "db" => $conn["baseDados"]["db"],
                "port" => $conn["baseDados"]["port"]
            ],
            "parametros" => [
                "url" => $conn['url_logosystem'], 
                "nome" => $data['nome'],
                "nome_fantasia" => $data['nome_fantasia'], 
                "cpf_cnpj"=>$data['cpf_cnpj'],
                "rg"=>$data['rg'],
                "inscricao_estadual"=>$data['inscricao_estadual'],
                "email"=>$data['email'],
                "rua"=>$data['rua'],
                "numero"=>$data['numero'],
                "bairro"=>$data['bairro'],
                "cep"=>$data['cep'],
                "cidade_ibge"=>$data['cidade_ibge'],
                "ddd"=>$data['ddd'],
                "fone"=>$data['fone'],
                "contato"=>$data['contato'],
                "data_nascimento"=>$data['data_nascimento'],
                "token"=>$conn['token_logosystem'],
            ]
        ];
        
        $response = requestCurl($conn['url_crm_logosystem'],$param,"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    } 
    return false;
}

function adicionar_pedido_logosystem($data){

    if(integracao_logosystem()){

        $conn = get_integration_variables();
        $param = [
                "codigo_interno" => $data['id_proposta'], 
                "data_emissao" => $data['data_emissao'],
                "cliente_id" => $data['cliente']['cod_logosystem'], 
                "tipo_pedido_id"=>$data['proposta']['order_type'],
                "representante_id"=>$data['cod_usuario_cadastro'],
                "condicao_pagto_id"=>$data['proposta']['payment_terms'],
                "prazo_entrega_inicial"=>$data['proposta']['date'],
                "prazo_entrega_final"=>$data['prazo_entrega_final'],
                "itens"=>$data['items'],
        ];
        $response = requestCurlLogosystem($conn['url_logosystem']."pedidos",json_encode($param),$conn['token_logosystem'],"POST");
        $response = json_decode(json_encode($response),true);
        return $response;
    } 
    return false;
}

function atualizar_produtos_logosystem(){

    if(integracao_logosystem()){

        $conn = get_integration_variables();

        $response = requestCurlLogosystem($conn['url_logosystem']."produtos","",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}

function atualizar_precos_logosystem(){

    if(integracao_logosystem()){

        $conn = get_integration_variables();

        $response = requestCurlLogosystem($conn['url_logosystem']."tabelapreco","",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}

function atualizar_imagens_logosystem($codigo){

    if(integracao_logosystem()){

        $conn = get_integration_variables();

        $response = requestCurlLogosystem($conn['url_logosystem']."produtos/".$codigo."/imagens","",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}

function atualizar_tipo_pedido_logosystem(){

    if(integracao_logosystem()){

        $conn = get_integration_variables();

        $response = requestCurlLogosystem($conn['url_logosystem']."tipopedido?alterado_apos=1970-01-01","",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}

function atualizar_condicao_pagamento_logosystem(){

    if(integracao_logosystem()){

        $conn = get_integration_variables();

        $response = requestCurlLogosystem($conn['url_logosystem']."condpagto?alterado_apos=1970-01-01","",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}

function atualizar_clientes_logosystem($date){

    if(integracao_logosystem()){

        $conn = get_integration_variables();
        
        $response = requestCurlLogosystem($conn['url_logosystem']."clientes?alterado_apos=".$date,"",$conn['token_logosystem'],"GET");
        $response = json_decode($response);

        return $response;

    }
    return false;

}