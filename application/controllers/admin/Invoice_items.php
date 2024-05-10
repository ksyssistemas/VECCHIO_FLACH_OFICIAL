<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->helper('bemtevi_api');
    }

    public function atualizar_produtos($atualizar_todos_items = false){
        $ultima_atualizacao_produtos = get_option('last_updated_items');
        if(integracao_logosystem()){
            $produtos = atualizar_produtos_logosystem();
            foreach($produtos as $produto){
                $ultima_alteracao_produto = strtotime($produto->ultima_alteracao);
                foreach($produto->variantes as $variante){
                    foreach($variante->tamanhos as $tamanho){
                        $this->db->where('codigo_logosystem', $produto->codigo);
                        $this->db->where('cor_logosystem', $variante->cor_nome);
                        $this->db->where('tamanho_logosystem', $tamanho->tamanho);
                        $item = $this->db->get(db_prefix() . 'items')->row_array();
            
                        $data = array();
                        if(is_null($item)){
                            $data['codigo_logosystem'] = $produto->codigo;
                            $data['description'] = $produto->descricao;
                            $data['shortened_description'] = $produto->descricao_reduzida;
                            $data['short_description'] = $produto->descricao_curta;
                            $data['long_description'] = $produto->descricao_longa;
                            $data['rate'] = "0";
                            $data['unit'] = $produto->unidade_medida;
                            $data['comments'] = $produto->observacoes;
                            $data['cor_logosystem'] = $variante->cor_nome;
                            $data['tamanho_logosystem'] = $tamanho->tamanho;
                            $data['qtd_estoque_logosystem'] = $tamanho->qtde_estoque;
                            $data['active'] = $produto->ativo;
                            $imagens = atualizar_imagens_logosystem($produto->codigo);
                            foreach($imagens as $imagem){
                                if(isset($imagem)){
                                    $data['item_image'] = $imagem->imagem;
                                    $data['format_image'] = $imagem->formato;
                                }
                            }
                            $id      = $this->invoice_items_model->add($data);
                        }else{
                            if($ultima_alteracao_produto > $ultima_atualizacao_produtos || $atualizar_todos_items){
                                $data['codigo_logosystem'] = $produto->codigo;
                                $data['description'] = $produto->descricao;
                                $data['shortened_description'] = $produto->descricao_reduzida;
                                $data['short_description'] = $produto->descricao_curta;
                                $data['long_description'] = $produto->descricao_longa;
                                $data['rate'] = "0";
                                $data['unit'] = $produto->unidade_medida;
                                $data['comments'] = $produto->observacoes;
                                $data['cor_logosystem'] = $variante->cor_nome;
                                $data['tamanho_logosystem'] = $tamanho->tamanho;
                                $data['qtd_estoque_logosystem'] = $tamanho->qtde_estoque;
                                $data['active'] = $produto->ativo;
                                $imagens = atualizar_imagens_logosystem($produto->codigo);
                                foreach($imagens as $imagem){
                                    if(isset($imagem)){
                                        $data['item_image'] = $imagem->imagem;
                                        $data['format_image'] = $imagem->formato;
                                    }
                                }
                            }else{
                                $data['qtd_estoque_logosystem'] = $tamanho->qtde_estoque;
                            }
                            $data['itemid'] = $item['id'];
                            $success = $this->invoice_items_model->edit($data);
                        }
                    }
                }
            }

            $tabelaspreco = atualizar_precos_logosystem($atualizar_todos_items = false);
            foreach($tabelaspreco as $tabelapreco){
                $ultima_alteracao_tabela = strtotime($tabelapreco->ultima_alteracao);
                if($ultima_alteracao_tabela > $ultima_atualizacao_produtos || $atualizar_todos_items){
                    foreach($tabelapreco->produtos as $produto){
                        foreach($produto->variantes as $variante){
                            foreach($variante->grade as $grade){
                                $this->db->where('codigo_logosystem', $produto->codigo);
                                $this->db->where('cor_logosystem', $variante->cor_nome);
                                $this->db->where('tamanho_logosystem', $grade->tamanho);
                                $item = $this->db->get(db_prefix() . 'items')->row_array();
                                $data = array();        
                                if(! is_null($item)){
                                    $data['itemid'] = $item['id'];
                                    $data['rate'] = $grade->preco;
                                    $success = $this->invoice_items_model->edit($data);
                                }
                            }
                        }
                    }
                }
            }

        }
        update_option("last_updated_items", strtotime("-1 day"));
    }


    /* List all available items */
    public function index()
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Invoice Items');
        }

        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('invoice_items');
        $this->load->view('admin/invoice_items/manage', $data);
    }

    public function table()
    {
        if (!has_permission('items', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('invoice_items');
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission('items', '', 'view')) {
            //QUANDO TEM INTEGRAÇÃO NÃO PODE ADICIONAR OU EDITAR MANUAL
            if ($this->input->post() && !integracao_logosystem()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission('items', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id      = $this->invoice_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        handle_proposal_item_image_upload($id);
                        $success = true;
                        $message = _l('added_successfully', _l('sales_item'));
                        set_alert('success', $message);
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->invoice_items_model->get($id),
                    ]);
                } else {
                    if (!has_permission('items', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';        
                    if (handle_proposal_item_image_upload($data['itemid']) || $success) {
                        $message = _l('updated_successfully', _l('sales_item'));
                        set_alert('success', $message);
                    }else{
                        $success = false;
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    public function import()
    {
        if (!has_permission('items', '', 'create')) {
            access_denied('Items Import');
        }
        if(integracao_logosystem()){
            access_denied('Não é possível importar com integração Logosystem');
        }
        $this->load->library('import/import_items', [], 'import');

        $this->import->setDatabaseFields($this->db->list_fields(db_prefix() . 'items'))
            ->setCustomFields(get_custom_fields('items'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if (
            $this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['title'] = _l('import');
        $this->load->view('admin/invoice_items/import', $data);
    }

    public function add_group()
    {
        if ($this->input->post() && has_permission('items', '', 'create')) {
            $this->invoice_items_model->add_group($this->input->post());
            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }

    public function update_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit')) {
            $this->invoice_items_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }

    public function delete_group($id)
    {
        if (has_permission('items', '', 'delete')) {
            if ($this->invoice_items_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('invoice_items?groups_modal=true'));
    }

    /* Delete item*/
    public function delete($id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('Invoice Items');
        }
        if(integracao_logosystem()){
            access_denied('Não é possível deletar com integração Logosystem');
        }
        if (!$id) {
            redirect(admin_url('invoice_items'));
        }

        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        redirect(admin_url('invoice_items'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_items');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $has_permission_delete = has_permission('items', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->invoice_items_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }

    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->invoice_items_model->get($id);
            $item->long_description   = nl2br($item->long_description);
            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
            $item->custom_fields      = [];

            $cf = get_custom_fields('items');

            foreach ($cf as $custom_field) {
                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
                if ($custom_field['type'] == 'textarea') {
                    $val = clear_textarea_breaks($val);
                }
                $custom_field['value'] = $val;
                $item->custom_fields[] = $custom_field;
            }

            echo json_encode($item);
        }
    }

    /* Copy Item */
    public function copy($id)
    {
        if (!has_permission('items', '', 'create')) {
            access_denied('Create Item');
        }
        if(integracao_logosystem()){
            access_denied('Não é possível copiar com integração Logosystem');
        }
        $data = (array) $this->invoice_items_model->get($id);

        $id = $this->invoice_items_model->copy($data);

        if ($id) {
            set_alert('success', _l('item_copy_success'));
            return redirect(admin_url('invoice_items?id=' . $id));
        }

        set_alert('warning', _l('item_copy_fail'));
        return redirect(admin_url('invoice_items'));
    }

    public function delete_proposal_item_image($item_id)
    {
        if($this->invoice_items_model->delete_proposal_item_image($item_id)){
            set_alert('success', _l('invoice_item_deleted'));
        }else{
            set_alert('warning', _l('invoice_item_deleted_not_deleted'));
        }
        return redirect(admin_url('invoice_items'));
    }
}
