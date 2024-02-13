<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Bemtevi extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('bemtevi_api');
    }

    public function retornar_usuario(){
        $nome = $this->input->get('nome');
        $retornoAPI = retornar_usuarios_pelo_nome_btv($nome);
        echo json_encode($retornoAPI);
    }

}