<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Api_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_table($name, $id)
    {
        switch ($name) {
            case 'projects':
                $this->load->model('Projects_model');
                return $this->Projects_model->get($id);
                break;
            case 'tasks':
                $this->load->model('Tasks_model');
                return $this->Tasks_model->get($id);
                break;
            case 'staffs':
                $this->load->model('Staff_model');
                return $this->Staff_model->get($id);
                break;
            case 'tickets':
                $this->load->model('Tickets_model');
                return $this->Tickets_model->get($id);
                break;
            case 'leads':
                $this->load->model('Leads_model');
                return $this->Leads_model->get($id);
                break;
            case 'clients':
                $this->load->model('Clients_model');
                return $this->Clients_model->get($id);
                break;
            case 'contracts':
                $this->load->model('Contracts_model');
                return $this->Contracts_model->get($id);
                break;
            case 'invoices':
                $this->load->model('Invoices_model');
                return $this->Invoices_model->get($id);
                break;
            case 'estimates':
                $this->load->model('Estimates_model');
                return $this->Estimates_model->get($id);
                break;
            case 'departments':
                $this->load->model('Departments_model');
                return $this->Departments_model->get($id);
                break;
            case 'payments':
                $this->load->model('Payments_model');
                return $this->Payments_model->get($id);
                break;
            case 'roles':
                $this->load->model('Roles_model');
                return $this->Roles_model->get($id);
                break;
            case 'proposals':
                $this->load->model('Proposals_model');
                return $this->Proposals_model->get($id);
                break;
            case 'knowledge':
                $this->load->model('Knowledge_base_model');
                return $this->Knowledge_base_model->get($id);
                break;
            case 'goals':
                $this->load->model('Goals_model');
                return $this->Goals_model->get($id);
                break;
            case 'currencies':
                $this->load->model('Currencies_model');
                return $this->Currencies_model->get($id);
                break;
            case 'annex':
                $this->load->model('Annex_model');
                return $this->Annex_model->get($id);
                break;
            case 'contacts':
                $this->load->model('Clients_model');
                return $this->clients_model->get_contact($id);
                break;
            case 'all_contacts':
                $this->load->model('Clients_model');
                return $this->clients_model->get_contacts($id);
                break;
            case 'invoices':
                $this->load->model('invoices_model');
                return $this->invoices_model->get($id);
                break;
            case 'invoice_items':
                $this->load->model('invoice_items_model');
                return $this->invoice_items_model->get($id);
                break;
            case 'milestones':
                return $this->get_milestones_api($id);
                break;
            default:
                return '';
                break;
        }
    }

    public function value($value)
    {
        if($value){
            return $value;
        }else{
            return '';
        }
    }

    public function search($type, $key)
    {
        return $this->get_relation_data_api($type,$key);
    }

    public function login($username, $password)
    {
        $this->db->where('email', $username);
        $user = $this->db->get('tblstaff')->row();
        var_dump($user->password);
        var_dump(md5($password));
        if(count($user) > 0)
        {
            $user_pass = $user->password;
            if(md5($password) === $user_pass)
            {
                return $user;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public function _search_tickets($q, $limit = 0, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'tickets',
            'search_heading' => _l('support_tickets'),
        ];

        if (is_staff_member() || (!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1) || $api == true) {
            $is_admin = is_admin();

            $where = '';
            if (!$is_admin && get_option('staff_access_only_assigned_departments') == 1 && $api == false) {
                $this->load->model('departments_model');
                $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $departments_ids      = [];
                if (count($staff_deparments_ids) == 0) {
                    $departments = $this->departments_model->get();
                    foreach ($departments as $department) {
                        array_push($departments_ids, $department['departmentid']);
                    }
                } else {
                    $departments_ids = $staff_deparments_ids;
                }
                if (count($departments_ids) > 0) {
                    $where = 'department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")';
                }
            }

            $this->db->select();
            $this->db->from('tbltickets');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department');
            $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
            $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');


            if (!_startsWith($q, '#')) {
                $this->db->where('(
                    ticketid LIKE "' . $q . '%"
                    OR subject LIKE "%' . $q . '%"
                    OR message LIKE "%' . $q . '%"
                    OR tblcontacts.email LIKE "%' . $q . '%"
                    OR CONCAT(firstname, \' \', lastname) LIKE "%' . $q . '%"
                    OR company LIKE "%' . $q . '%"
                    OR vat LIKE "%' . $q . '%"
                    OR tblcontacts.phonenumber LIKE "%' . $q . '%"
                    OR tblclients.phonenumber LIKE "%' . $q . '%"
                    OR city LIKE "%' . $q . '%"
                    OR state LIKE "%' . $q . '%"
                    OR address LIKE "%' . $q . '%"
                    OR tbldepartments.name LIKE "%' . $q . '%"
                    )');

                if ($where != '') {
                    $this->db->where($where);
                }

            } else {
                $this->db->where('ticketid IN
                    (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                    (SELECT id FROM tbltags WHERE name="' . strafter($q, '#') . '")
                    AND tbltags_in.rel_type=\'ticket\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                    ');
            }

            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('ticketid', 'DESC');
            $result['result'] = $this->db->get()->result_array();
        }

        return $result;
    }

     public function _search_leads($q, $limit = 0, $where = [], $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'leads',
            'search_heading' => _l('leads'),
        ];

        $has_permission_view = has_permission('leads', '', 'view');
        if (is_staff_member() || $api == true) {
            // Leads
            $this->db->select();
            $this->db->from('tblleads');

            if (!$has_permission_view && $api == false) {
                $this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public=1)');
            }

            if (!_startsWith($q, '#')) {
                $this->db->where('(name LIKE "%' . $q . '%"
                    OR title LIKE "%' . $q . '%"
                    OR company LIKE "%' . $q . '%"
                    OR zip LIKE "%' . $q . '%"
                    OR city LIKE "%' . $q . '%"
                    OR state LIKE "%' . $q . '%"
                    OR address LIKE "%' . $q . '%"
                    OR email LIKE "%' . $q . '%"
                    OR phonenumber LIKE "%' . $q . '%"
                    )');
            } else {
                $this->db->where('id IN
                    (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                    (SELECT id FROM tbltags WHERE name="' . strafter($q, '#') . '")
                    AND tbltags_in.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                    ');
            }


            $this->db->where('client_id < 1');

            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('name', 'ASC');
            $result['result'] = $this->db->get()->result_array();
        }

        return $result;
    }

    public function _search_invoices($q, $limit = 0, $where = [], $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'invoices',
            'search_heading' => _l('invoices'),
        ];
        $has_permission_view_invoices     = has_permission('invoices', '', 'view');
        $has_permission_view_invoices_own = has_permission('invoices', '', 'view_own');

        if ($has_permission_view_invoices || $has_permission_view_invoices_own || get_option('allow_staff_view_invoices_assigned') == '1' || $api == true) {
            if (is_numeric($q)) {
                $q = trim($q);
                $q = ltrim($q, '0');
            } elseif (startsWith($q, get_option('invoice_prefix'))) {
                $q = strafter($q, get_option('invoice_prefix'));
                $q = trim($q);
                $q = ltrim($q, '0');
            }
            $invoice_fields    = prefixed_table_fields_array(db_prefix() . 'invoices');
            $clients_fields    = prefixed_table_fields_array(db_prefix() . 'clients');
            // Invoices
            $this->db->select(implode(',', $invoice_fields) . ',' . implode(',', $clients_fields) . ',' . db_prefix() . 'invoices.id as invoiceid,' . get_sql_select_client_company());
            $this->db->from(db_prefix() . 'invoices');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid', 'left');
            $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency');
            $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');

            if (!startsWith($q, '#')) {
                $this->db->where('(
                ' . db_prefix() . 'invoices.number LIKE "' . $this->db->escape_like_str($q) . '"
                OR
                ' . db_prefix() . 'clients.company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.clientnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.adminnote LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                CONCAT(firstname,\' \',lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'invoices.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.billing_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_street LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR
                ' . db_prefix() . 'clients.shipping_zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
            } else {
                $this->db->where(db_prefix() . 'invoices.id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'invoice\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }


            $this->db->order_by('number,YEAR(date)', 'desc');
            if ($limit != 0) {
                $this->db->limit($limit);
            }

            $result['result'] = $this->db->get()->result_array();
            // echo $this->db->last_query();
        }

        return $result;
    }

    public function _search_projects($q, $limit = 0, $where = false, $rel_type = null, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'projects',
            'search_heading' => _l('projects'),
        ];

        $projects = has_permission('projects', '', 'view');
        // Projects
        $this->db->select('tblprojects.*');
        $this->db->from('tblprojects');
        if(isset($rel_type) && $rel_type=="lead"){
            $this->db->join('tblleads', 'tblleads.id = tblprojects.clientid');
        } else {
            $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid','LEFT'); 
            $this->db->join('tblleads', 'tblleads.id = tblprojects.clientid','LEFT');    
        }
        
        if (!$projects && $api == false) {
            $this->db->where('tblprojects.id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')');
        }
        if ($where != false) {
            $this->db->where($where);
        }
        if (!_startsWith($q, '#')) {
            $this->db->where('(tblleads.company LIKE "%' . $q . '%"
                OR tblprojects.description LIKE "%' . $q . '%"
                OR tblprojects.name LIKE "%' . $q . '%"
                
                OR tblleads.phonenumber LIKE "%' . $q . '%"
                OR tblleads.city LIKE "%' . $q . '%"
                OR tblleads.zip LIKE "%' . $q . '%"
                OR tblleads.state LIKE "%' . $q . '%"
                OR tblleads.zip LIKE "%' . $q . '%"
                OR tblleads.address LIKE "%' . $q . '%"
                )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                (SELECT id FROM tbltags WHERE name="' . strafter($q, '#') . '")
                AND tbltags_in.rel_type=\'project\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by(db_prefix() . 'projects.name', 'ASC');
        $result['result'] = $this->db->get()->result_array();

        return $result;
    }

    public function _search_staff($q, $limit = 0, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'staff',
            'search_heading' => _l('staff_members'),
        ];

        if (has_permission('staff', '', 'view') || $api == true) {
            // Staff
            $this->db->select();
            $this->db->from(db_prefix() . 'staff');
            $this->db->like('firstname', $q);
            $this->db->or_like('lastname', $q);
            $this->db->or_like("CONCAT(firstname, ' ', lastname)", $q, false);
            $this->db->or_like('facebook', $q);
            $this->db->or_like('linkedin', $q);
            $this->db->or_like('phonenumber', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('skype', $q);

            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('firstname', 'ASC');
            $result['result'] = $this->db->get()->result_array();
        }

        return $result;
    }

    public function _search_tasks($q, $limit = 0, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'tasks',
            'search_heading' => _l('tasks'),
        ];

        if (has_permission('tasks', '', 'view') || $api == true) {
            // task
            $this->db->select();
            $this->db->from(db_prefix() . 'tasks');
            $this->db->like('name', $q);
            $this->db->or_like('id', $q);

            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('name', 'ASC');
            $result['result'] = $this->db->get()->result_array();
        }

        return $result;
    }
     public function get_user($id = '')
    {
        $this->db->select('*');
        if($id != '')
        {
             $this->db->where('id', $id);
        }
        return $this->db->get(db_prefix() . 'user_api')->result_array();
    }

     public function add_user($data)
    {
        $payload = [
            'user' => $data['user'],
            'name' => $data['name'],
            'password' => $data['password']
        ];
        // Load Authorization Library or Load in autoload config file
        $this->load->library('Authorization_Token');
        // generate a token
        $data['token'] = $this->authorization_token->generateToken($payload);
        $today = date('Y-m-d H:i:s');
                
        $data['expiration_date'] = to_sql_date($data['expiration_date'],true);
        $data['password'] = app_hash_password($data['password']);
       $this->db->insert(db_prefix() . 'user_api', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New User Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }
     public function update_user($data, $id)
    {        
        if($data['password'] == '')
        {
            unset($data['password']);
        }else{
             $data['password'] = app_hash_password($data['password']);
        }
        $data['expiration_date'] = to_sql_date($data['expiration_date'],true);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'user_api', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Ticket User Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }
    public function delete_user($id)
    {

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'user_api');
        if ($this->db->affected_rows() > 0) {
            log_activity('User Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    public function check_token($token)
    {

        $this->db->where('token', $token);
        $user = $this->db->get(db_prefix() . 'user_api')->row();
        if(isset($user)){
            return true;
        }
        return false;
    }

    public function user_api_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $lead_id = $this->input->post('lead_id');
                $abbreviated_name = strtoupper($this->input->post('abbreviated_name'));
                if ($lead_id != '') {
                    $this->db->where('id', $lead_id);
                    $_current_email = $this->db->get('tblleads')->row();
                    if ($_current_email->abbreviated_name == $abbreviated_name) {
                        echo json_encode(true);
                        die();
                    }
                }
                $result_lead = true;
                $result_client = true;
                $client_id = $this->input->post('client_id');
                $this->db->where('abbreviated_name', $abbreviated_name);
                if($client_id!= ''){
                    $arr_id = array();
                    $arr_id[] = $client_id;
                    $this->db->where_not_in('client_id', $arr_id);
                }

                $total_rows = $this->db->count_all_results('tblleads');
                
                if ($total_rows > 0) {
                    $result_lead = false;
                } else {
                    $result_lead = true;
                }
                $this->db->where('abbreviated_name', $abbreviated_name);
                if($client_id!= ''){
                    $arr_id = array();
                    $arr_id[] = $client_id;
                    $this->db->where_not_in('userid', $arr_id);
                }
                $total_rows = $this->db->count_all_results('tblclients');
                if ($total_rows > 0) {
                    $result_client = false;
                } else {
                    $result_client = true;
                }
                if($result_lead && $result_client){
                    echo json_encode(true);
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }

    public function get_relation_data_api($type, $search = '')
    {
        $q  = '';
        if($search != ''){
            $q = $search;
            $q = trim($q);
        }
        $data = [];
        if ($type == 'customer' || $type == 'customers') {
            $where_clients = 'tblclients.active=1';
            if ($q) {
                $where_clients .= ' AND (company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%"
                OR tblclients.phonenumber LIKE "%'.$q.'%"
                )';
            }
            // if ($q) {
            //     $where_clients .= ' AND (company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%")';
            // }
            $this->load->model('clients_model');
            $data = $this->clients_model->get('', $where_clients);
            // echo $this->db->last_query();
        } 
         elseif ($type == "contacts") {
            $where_clients = 'tblclients.active=1';
            if ($q) {
                $where_clients .= ' AND (company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR CONCAT(firstname, " ", lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\' OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1';
            }
            $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid', 'left');

            $this->load->model('clients_model');
            $data = $this->clients_model->get_contacts('', $where_clients);
         }
         elseif ($type == 'ticket') {
                $search = $this->_search_tickets($q, 0, true);
                $data   = $search['result'];
        } elseif ($type == 'lead' || $type == 'leads') {
                $search = $this->_search_leads($q, 0, [
                    'junk' => 0,
                    ], true);
                $data = $search['result'];
        } elseif ($type == 'invoice' || $type == 'invoices') {
                $search = $this->_search_invoices($q, 0, [], true);
                $data = $search['result'];
        } elseif ($type == 'invoice_items') {
                $this->load->model('invoice_items_model');
                $data = $this->invoice_items_model->search($q);
                
        } elseif ($type == 'project') {
            
                $where_projects = '';
                if ($this->input->post('customer_id')) {
                    $where_projects .= '(clientid=' . $this->input->post('customer_id').' or clientid in (select id from tblleads where client_id='.$this->input->post('customer_id').') )';
                }
                if ($this->input->post('rel_type')) {
                    $where_projects .= ' and rel_type="' . $this->input->post('rel_type').'" ' ;
                }
                $search = $this->_search_projects($q, 0, $where_projects,$this->input->post('rel_type'), true);
                
                
                $data   = $search['result'];
            
        } elseif ($type == 'staff') {
                $search = $this->_search_staff($q,0,true);
                $data   = $search['result'];
            
        } elseif ($type == 'tasks') {
            $search = $this->_search_tasks($q,0,true);
            $data   = $search['result'];
        } elseif ($type == 'milestones') {
            $where_milestones = '';
            if ($q) {
                $where_milestones .= '(name LIKE "%' . $q . '%" OR id LIKE "%' . $q . '%")';
            }
            $data = $this->get_milestones_api('', $where_milestones);
        }
        return $data;
    }
    public function get_milestones_api($id = '', $where = [])
    {
        $this->db->select('*, (SELECT COUNT(id) FROM '.db_prefix().'tasks WHERE milestone='.db_prefix().'milestones.id) as total_tasks, (SELECT COUNT(id) FROM '.db_prefix().'tasks WHERE rel_type="project" and milestone='.db_prefix().'milestones.id AND status=5) as total_finished_tasks');
        if($id != ''){
            $this->db->where('id', $id);
        }
         if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('milestone_order', 'ASC');
        $milestones = $this->db->get(db_prefix() . 'milestones')->result_array();


        return $milestones;
    }
}
