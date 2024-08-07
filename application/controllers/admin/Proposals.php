<?php

use app\services\proposals\ProposalsPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Proposals extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposals_model');
        $this->load->model('currencies_model');
        $this->load->helper('bemtevi_api');

    }

    public function index($proposal_id = '')
    {
        $this->list_proposals($proposal_id);
    }

    public function list_proposals($proposal_id = '')
    {
        close_setup_menu();

        if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && get_option('allow_staff_view_estimates_assigned') == 0) {
            access_denied('proposals');
        }

        $isPipeline = $this->session->userdata('proposals_pipeline') == 'true';

        if ($isPipeline && !$this->input->get('status')) {
            $data['title']           = _l('proposals_pipeline');
            $data['bodyclass']       = 'proposals-pipeline';
            $data['switch_pipeline'] = false;
            // Direct access
            if (is_numeric($proposal_id)) {
                $data['proposalid'] = $proposal_id;
            } else {
                $data['proposalid'] = $this->session->flashdata('proposalid');
            }

            $this->load->view('admin/proposals/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['proposal_id']           = $proposal_id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('proposals');
            $data['proposal_statuses']     = $this->proposals_model->get_statuses();
            $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
            $data['years']                 = $this->proposals_model->get_proposals_years();
            $this->load->view('admin/proposals/manage', $data);
        }
    }

    public function table()
    {
        if (
            !has_permission('proposals', '', 'view')
            && !has_permission('proposals', '', 'view_own')
            && get_option('allow_staff_view_proposals_assigned') == 0
        ) {
            ajax_access_denied();
        }

        $this->app->get_table_data('proposals');
    }

    public function proposal_relations($rel_id, $rel_type)
    {
        $this->app->get_table_data('proposals_relations', [
            'rel_id'   => $rel_id,
            'rel_type' => $rel_type,
        ]);
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->proposals_model->delete_attachment($id);
        } else {
            ajax_access_denied();
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('proposals', '', 'delete')) {
            $this->proposals_model->clear_signature($id);
        }

        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function sync_data()
    {
        if (has_permission('proposals', '', 'create') || has_permission('proposals', '', 'edit')) {
            $has_permission_view = has_permission('proposals', '', 'view');

            $this->db->where('rel_id', $this->input->post('rel_id'));
            $this->db->where('rel_type', $this->input->post('rel_type'));

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            $address = trim($this->input->post('address'));
            $address = nl2br($address);
            $this->db->update(db_prefix() . 'proposals', [
                'phone'   => $this->input->post('phone'),
                'zip'     => $this->input->post('zip'),
                'country' => $this->input->post('country'),
                'state'   => $this->input->post('state'),
                'address' => $address,
                'city'    => $this->input->post('city'),
            ]);

            if ($this->db->affected_rows() > 0) {
                echo json_encode([
                    'message' => _l('all_data_synced_successfully'),
                ]);
            } else {
                echo json_encode([
                    'message' => _l('sync_proposals_up_to_date'),
                ]);
            }
        }
    }

    public function proposal($id = '')
    {
        if ($this->input->post()) {
            $proposal_data = $this->input->post();

            if(integracao_logosystem() && $proposal_data['status'] == 1 ){
                if($proposal_data['rel_type'] != "customer"){
                    set_alert('warning', _l('rel_type_customer'));
                    return redirect($_SERVER['HTTP_REFERER']); 
                }
                if($proposal_data['order_type'] == ""){
                    set_alert('warning', _l('required_order_type'));
                    return redirect($_SERVER['HTTP_REFERER']); 
                }
                if($proposal_data['payment_terms'] == ""){
                    set_alert('warning', _l('required_payment_terms'));
                    return redirect($_SERVER['HTTP_REFERER']); 
                }
                $this->db->where('staffid', $proposal_data['assigned']);
                $staff = $this->db->get(db_prefix() . 'staff')->row_array();
                if($staff['idBTV'] == NULL){
                    set_alert('warning', _l('staff_idlogosystem_notfound_assigned').$staff['firstname']." ".$staff['lastname']);
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            

            if (integracao_btv() && $proposal_data['status'] == 1 && $proposal_data['rel_type'] == "customer") {
                //buscar dados do consultor responsavel
                $this->db->where('staffid', $proposal_data['assigned']);
                $staff = $this->db->get(db_prefix() . 'staff')->row_array();
                if($staff['idBTV'] == NULL){
                    set_alert('warning', _l('staff_idBTV_notfound_assigned').$staff['firstname']." ".$staff['lastname']);
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            if ($id == '') {
                if (!has_permission('proposals', '', 'create')) {
                    access_denied('proposals');
                }
                $id = $this->proposals_model->add($proposal_data);
     
                if ($id) {
                    $this->adicionar_suporte_BTV_proposal($proposal_data);
                    $this->adicionar_pedido_logosystem_proposal($proposal_data,$id);
                    set_alert('success', _l('added_successfully', _l('proposal')));
                    if ($this->set_proposal_pipeline_autoload($id)) {
                        redirect(admin_url('proposals'));
                    } else {
                        redirect(admin_url('proposals/list_proposals/' . $id));
                    }
                }
            } else {
                if (!has_permission('proposals', '', 'edit')) {
                    access_denied('proposals');
                }
                $success = $this->proposals_model->update($proposal_data, $id);
                
                if ($success) {
                    $this->adicionar_suporte_BTV_proposal($proposal_data);
                    $this->adicionar_pedido_logosystem_proposal($proposal_data,$id);
                    set_alert('success', _l('updated_successfully', _l('proposal')));
                }
                if ($this->set_proposal_pipeline_autoload($id)) {
                    redirect(admin_url('proposals'));
                } else {
                    redirect(admin_url('proposals/list_proposals/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('proposal_lowercase'));
        } else {
            $data['proposal'] = $this->proposals_model->get($id);

            if (!$data['proposal'] || !user_can_view_proposal($id)) {
                blank_page(_l('proposal_not_found'));
            }

            $data['estimate']    = $data['proposal'];
            $data['is_proposal'] = true;
            $title               = _l('edit', _l('proposal_lowercase'));
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses']      = $this->proposals_model->get_status();
        $data['staff']         = $this->staff_model->get('', ['active' => 1]);
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['payment_terms_types'] = $this->db->get(db_prefix() . 'logosystem_condicao_pagamento')->result_array();
        $data['order_types'] = $this->db->get(db_prefix() . 'logosystem_tipo_pedido')->result_array();

        $data['title'] = $title;
        $this->load->view('admin/proposals/proposal', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/proposals/templates/' . $name, [], true);
    }

    public function send_expiry_reminder($id)
    {
        $canView = user_can_view_proposal($id);
        if (!$canView) {
            access_denied('proposals');
        } else {
            if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && $canView == false) {
                access_denied('proposals');
            }
        }

        $success = $this->proposals_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('proposals/list_proposals/' . $id));
        }
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'proposals', get_acceptance_info_array(true));
        }

        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('proposals'));
        }

        $canView = user_can_view_proposal($id);
        if (!$canView) {
            access_denied('proposals');
        } else {
            if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && $canView == false) {
                access_denied('proposals');
            }
        }

        $proposal = $this->proposals_model->get($id);

        try {
            $pdf = proposal_pdf($proposal);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $proposal_number = format_proposal_number($id);
        $pdf->Output($proposal_number . '.pdf', $type);
    }

    public function get_proposal_data_ajax($id, $to_return = false)
    {
        if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && get_option('allow_staff_view_proposals_assigned') == 0) {
            echo _l('access_denied');
            die;
        }

        $proposal = $this->proposals_model->get($id, [], true);

        if (!$proposal || !user_can_view_proposal($id)) {
            echo _l('proposal_not_found');
            die;
        }

        $this->app_mail_template->set_rel_id($proposal->id);
        $data = prepare_mail_preview_data('proposal_send_to_customer', $proposal->email);

        $merge_fields = [];

        $merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{proposal_items}',
            ],
        ];

        $merge_fields = array_merge($merge_fields, $this->app_merge_fields->get_flat('proposals', 'other', '{email_signature}'));

        $data['proposal_statuses']     = $this->proposals_model->get_statuses();
        $data['members']               = $this->staff_model->get('', ['active' => 1]);
        $data['proposal_merge_fields'] = $merge_fields;
        $data['proposal']              = $proposal;
        $data['totalNotes']            = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'proposal']);
        if ($to_return == false) {
            $this->load->view('admin/proposals/proposals_preview_template', $data);
        } else {
            return $this->load->view('admin/proposals/proposals_preview_template', $data, true);
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_proposal($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'proposal', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_proposal($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'proposal');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function convert_to_estimate($id)
    {
        if (!has_permission('estimates', '', 'create')) {
            access_denied('estimates');
        }
        if ($this->input->post()) {
            $this->load->model('estimates_model');
            $estimate_id = $this->estimates_model->add($this->input->post());
            if ($estimate_id) {
                set_alert('success', _l('proposal_converted_to_estimate_success'));
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'proposals', [
                    'estimate_id' => $estimate_id,
                    'status'      => 3,
                ]);
                log_activity('Proposal Converted to Estimate [EstimateID: ' . $estimate_id . ', ProposalID: ' . $id . ']');

                hooks()->do_action('proposal_converted_to_estimate', ['proposal_id' => $id, 'estimate_id' => $estimate_id]);

                redirect(admin_url('estimates/estimate/' . $estimate_id));
            } else {
                set_alert('danger', _l('proposal_converted_to_estimate_fail'));
            }
            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect(admin_url('proposals'));
            } else {
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }
    }

    public function convert_to_invoice($id)
    {
        if (!has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        if ($this->input->post()) {
            $this->load->model('invoices_model');
            $invoice_id = $this->invoices_model->add($this->input->post());
            if ($invoice_id) {
                set_alert('success', _l('proposal_converted_to_invoice_success'));
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'proposals', [
                    'invoice_id' => $invoice_id,
                    'status'     => 3,
                ]);
                log_activity('Proposal Converted to Invoice [InvoiceID: ' . $invoice_id . ', ProposalID: ' . $id . ']');
                hooks()->do_action('proposal_converted_to_invoice', ['proposal_id' => $id, 'invoice_id' => $invoice_id]);
                redirect(admin_url('invoices/invoice/' . $invoice_id));
            } else {
                set_alert('danger', _l('proposal_converted_to_invoice_fail'));
            }
            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect(admin_url('proposals'));
            } else {
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }
    }

    public function get_invoice_convert_data($id)
    {
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $this->load->model('taxes_model');
        $data['taxes']         = $this->taxes_model->get();
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']          = $this->staff_model->get('', ['active' => 1]);
        $data['proposal']       = $this->proposals_model->get($id);
        $data['billable_tasks'] = [];
        $data['add_items']      = $this->_parse_items($data['proposal']);

        if ($data['proposal']->rel_type == 'lead') {
            $this->db->where('leadid', $data['proposal']->rel_id);
            $data['customer_id'] = $this->db->get(db_prefix() . 'clients')->row()->userid;
        } else {
            $data['customer_id'] = $data['proposal']->rel_id;
            $data['project_id'] = $data['proposal']->project_id;
        }
        $data['custom_fields_rel_transfer'] = [
            'belongs_to' => 'proposal',
            'rel_id'     => $id,
        ];
        $this->load->view('admin/proposals/invoice_convert_template', $data);
    }

    public function get_estimate_convert_data($id)
    {
        $this->load->model('taxes_model');
        $data['taxes']         = $this->taxes_model->get();
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['proposal']  = $this->proposals_model->get($id);
        $data['add_items'] = $this->_parse_items($data['proposal']);

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        if ($data['proposal']->rel_type == 'lead') {
            $this->db->where('leadid', $data['proposal']->rel_id);
            $data['customer_id'] = $this->db->get(db_prefix() . 'clients')->row()->userid;
        } else {
            $data['customer_id'] = $data['proposal']->rel_id;
            $data['project_id'] = $data['proposal']->project_id;
        }

        $data['custom_fields_rel_transfer'] = [
            'belongs_to' => 'proposal',
            'rel_id'     => $id,
        ];

        $this->load->view('admin/proposals/estimate_convert_template', $data);
    }

    private function _parse_items($proposal)
    {
        $items = [];
        foreach ($proposal->items as $item) {
            $taxnames = [];
            $taxes    = get_proposal_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                array_push($taxnames, $tax['taxname']);
            }
            $item['taxname']        = $taxnames;
            $item['parent_item_id'] = $item['id'];
            $item['id']             = 0;
            $items[]                = $item;
        }

        return $items;
    }

    /* Send proposal to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_proposal($id);
        if (!$canView) {
            access_denied('proposals');
        } else {
            if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && $canView == false) {
                access_denied('proposals');
            }
        }

        if ($this->input->post()) {
            try {
                $success = $this->proposals_model->send_proposal_to_email(
                    $id,
                    $this->input->post('attach_pdf'),
                    $this->input->post('cc')
                );
            } catch (Exception $e) {
                $message = $e->getMessage();
                echo $message;
                if (strpos($message, 'Unable to get the size of the image') !== false) {
                    show_pdf_unable_to_get_image_size_error();
                }
                die;
            }

            if ($success) {
                set_alert('success', _l('proposal_sent_to_email_success'));
            } else {
                set_alert('danger', _l('proposal_sent_to_email_fail'));
            }

            if ($this->set_proposal_pipeline_autoload($id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }
    }

    public function copy($id)
    {
        if (!has_permission('proposals', '', 'create')) {
            access_denied('proposals');
        }
        $new_id = $this->proposals_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('proposal_copy_success'));
            $this->set_proposal_pipeline_autoload($new_id);
            redirect(admin_url('proposals/proposal/' . $new_id));
        } else {
            set_alert('success', _l('proposal_copy_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect(admin_url('proposals'));
        } else {
            redirect(admin_url('proposals/list_proposals/' . $id));
        }
    }

    public function mark_action_status($status, $id)
    {
        if (!has_permission('proposals', '', 'edit')) {
            access_denied('proposals');
        }
        $success = $this->proposals_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('proposal_status_changed_success'));
        } else {
            set_alert('danger', _l('proposal_status_changed_fail'));
        }
        if ($this->set_proposal_pipeline_autoload($id)) {
            redirect(admin_url('proposals'));
        } else {
            redirect(admin_url('proposals/list_proposals/' . $id));
        }
    }

    public function delete($id)
    {
        if (!has_permission('proposals', '', 'delete')) {
            access_denied('proposals');
        }
        $response = $this->proposals_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('proposal')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('proposal_lowercase')));
        }
        redirect(admin_url('proposals'));
    }

    public function get_relation_data_values($rel_id, $rel_type)
    {
        echo json_encode($this->proposals_model->get_relation_data_values($rel_id, $rel_type));
    }

    public function add_proposal_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->proposals_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->proposals_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_proposal_comments($id)
    {
        $data['comments'] = $this->proposals_model->get_comments($id);
        $this->load->view('admin/proposals/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'proposal_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->proposals_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function save_proposal_data()
    {
        if (!has_permission('proposals', '', 'edit') && !has_permission('proposals', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }
        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('proposal_id'));
        $this->db->update(db_prefix() . 'proposals', [
            'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('proposal'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    // Pipeline
    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'proposals_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('proposals'));
        }
    }

    public function pipeline_open($id)
    {
        if (has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own') || get_option('allow_staff_view_proposals_assigned') == 1) {
            $data['proposal']      = $this->get_proposal_data_ajax($id, true);
            $data['proposal_data'] = $this->proposals_model->get($id);
            $this->load->view('admin/proposals/pipeline/proposal', $data);
        }
    }

    public function update_pipeline()
    {
        if (has_permission('proposals', '', 'edit')) {
            $this->proposals_model->update_pipeline($this->input->post());
        }
    }

    public function get_pipeline()
    {
        if (has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own') || get_option('allow_staff_view_proposals_assigned') == 1) {
            $data['statuses'] = $this->proposals_model->get_statuses();
            $this->load->view('admin/proposals/pipeline/pipeline', $data);
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $proposals = (new ProposalsPipeline($status))
        ->search($this->input->get('search'))
        ->sortBy(
            $this->input->get('sort_by'),
            $this->input->get('sort')
        )
        ->page($page)->get();

        foreach ($proposals as $proposal) {
            $this->load->view('admin/proposals/pipeline/_kanban_card', [
                'proposal' => $proposal,
                'status'   => $status,
            ]);
        }
    }

    public function set_proposal_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('proposals_pipeline') && $this->session->userdata('proposals_pipeline') == 'true') {
            $this->session->set_flashdata('proposalid', $id);

            return true;
        }

        return false;
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('proposal_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
    // Statuses
    /* View proposals statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Proposals Statuses');
        }
        $data['statuses'] = $this->proposals_model->get_status();
        $data['title']    = 'Proposals statuses';
        $this->load->view('admin/proposals/manage_statuses', $data);
    }


     /* Add or update proposals status */
     public function status()
     {
         if (!is_admin() /*&& get_option('staff_members_create_inline_proposal_status') == '0'*/) {
             access_denied('Proposals Statuses');
         }
         if ($this->input->post()) {
            $data = $this->input->post();
            if($data['id'] != 1){
                if (!$this->input->post('id')) {
                    $inline = isset($data['inline']);
                    if (isset($data['inline'])) {
                        unset($data['inline']);
                    }
                    $id = $this->proposals_model->add_status($data);
                    if (!$inline) {
                        if ($id) {
                            set_alert('success', _l('added_successfully', _l('proposals_status')));
                        }
                    } else {
                        echo json_encode(['success' => $id ? true : false, 'id' => $id]);
                    }
                } else {
                    $id = $data['id'];
                    unset($data['id']);
                    $success = $this->proposals_model->update_status($data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('proposals_status')));
                    }
                }
            }else{
                set_alert('danger', _l('cant_delete_default', _l('proposals_status')));
            }
         }
     }
 
     /* Delete proposals status from databae */
     public function delete_status($id)
     {
         if (!is_admin()) {
             access_denied('Proposals Statuses');
         }
         if (!$id) {
             redirect(admin_url('proposals/statuses'));
         }
         if ($id == 1) {
            redirect(admin_url('proposals/statuses'));
        }
         $response = $this->proposals_model->delete_status($id);
         if (is_array($response) && isset($response['referenced'])) {
             set_alert('warning', _l('is_referenced', _l('proposals_status_lowercase')));
         } elseif ($response == true) {
             set_alert('success', _l('deleted', _l('proposals_status')));
         } else {
             set_alert('warning', _l('problem_deleting', _l('proposals_status_lowercase')));
         }
         redirect(admin_url('proposals/statuses'));
     }
/* Adicionar suporte no BTV com itens de proposta */
public function adicionar_suporte_BTV_proposal($proposal_data) {
    if(integracao_btv() && $proposal_data['status'] == 1 && $proposal_data['rel_type'] == "customer"){
       
        //pegar codigo do usuario associado a proposta
        $this->db->where('staffid', $proposal_data['assigned']);
        $staff = $this->db->get(db_prefix() . 'staff')->row_array();
       
        //pegar os dados do cliente da proposta
        $this->db->where('userid', $proposal_data['rel_id']);
        $costumer = $this->db->get(db_prefix() . 'clients')->row_array();
        $problema = "";


        foreach($proposal_data['items'] as $item){
            $problema .= "<b>"._l('invoice_table_item_heading'). ":</b> ". $item['description'] . " ";
            $problema .= "<b>"._l('invoice_items_list_description'). ":</b> ". $item['long_description'] . " ";
            $problema .= "<b>"._l('invoice_table_quantity_heading'). ":</b> ". $item['qty'] . " ";
            $problema .= "<b>"._l('invoice_table_rate_heading'). ":</b> ". $item['unit'] ." ". $item['rate'] . " ";
            $problema .= "<b>"._l('invoice_table_amount_heading'). ":</b> ". $item['unit'] . ($item['rate']*$item['qty']);
            $problema .= "<br>";
        }
        $problema .= "<b>"._l('invoice_subtotal'). ":</b> ". $proposal_data['subtotal'] ."<br>";
        $problema .= "<b>"._l('invoice_discount'). ":</b> ". $proposal_data['discount_total'] ."<br>";
        $problema .= "<b>"._l('invoice_adjustment'). ":</b> ". $proposal_data['adjustment'] ."<br>";
        $problema .= "<b>"._l('invoice_total'). ":</b> ". $proposal_data['total'];


        $dados = [
            "cod_usuario"=> $staff['idBTV'],
            "cod_situacao"=> "2",
            "cod_classe"=> "1",
            "cod_categoria"=> "1",
            "cod_cliente"=> $costumer['codbtv'],
            "cod_usuario_cadastro"=> $staff['idBTV'],
            "problema"=> $problema,
            "titulo"=> $proposal_data['subject']
        ];


        criar_suporte_btv($dados);
    }
 }

 /* Adicionar pedido na Logosystem com itens de proposta */
public function adicionar_pedido_logosystem_proposal($proposal_data, $proposal_id) {
    if(integracao_logosystem() && $proposal_data['status'] == 1 && $proposal_data['rel_type'] == "customer"){
        
        //pegar codigo do usuario associado a proposta
        $this->db->where('staffid', $proposal_data['assigned']);
        $staff = $this->db->get(db_prefix() . 'staff')->row_array();
       
        //pegar os dados do cliente da propostaa
        $this->db->where('userid', $proposal_data['rel_id']);
        $costumer = $this->db->get(db_prefix() . 'clients')->row_array();
        $problema = "";

        $itemsProposal = array();
        $contadorItem = 0;
        foreach($proposal_data['items'] as $item){
            //pegar os dados do item
            $this->db->where('id', $item['original_id']);
            $itemOriginal = $this->db->get(db_prefix() . 'items')->row_array();
            
            $itemsProposal[$contadorItem]['produto_id'] = $itemOriginal['codigo_logosystem'];
            $itemsProposal[$contadorItem]['preco_unitario'] = $item['rate'];
            $itemsProposal[$contadorItem]['total_quantidade'] = $item['qty'];

            $contadorItem++;
        }
        foreach($proposal_data['newitems'] as $item){
            //pegar os dados dos itens novos
            $this->db->where('id', $item['original_id']);
            $itemOriginal = $this->db->get(db_prefix() . 'items')->row_array();
            
            $itemsProposal[$contadorItem]['produto_id'] = $itemOriginal['codigo_logosystem'];
            $itemsProposal[$contadorItem]['preco_unitario'] = $item['rate'];
            $itemsProposal[$contadorItem]['total_quantidade'] = $item['qty'];

            $contadorItem++;
        }

        $dados = [
            "data_emissao"=> date('d/m/Y'),
            "cliente"=> $costumer,
            "cod_usuario_cadastro"=> $staff['idBTV'],
            "id_proposta"=> $proposal_id,
            "proposta"=> $proposal_data,
            "prazo_entrega_final" => $proposal_data['open_till'] =! ""?$proposal_data['open_till']:$proposal_data['date'],
            "items"=>$itemsProposal,
        ];

        $add_pedido_logosystem = json_decode(adicionar_pedido_logosystem($dados));

        //vinculuar a proposta com o id retornado do logosystem
        if($add_pedido_logosystem->codigo){
            $this->db->where('id', $proposal_id);
            $this->db->update(db_prefix() . 'proposals', ['codigo_logosystem' => $add_pedido_logosystem->codigo]);
        }
    }
 }

}
