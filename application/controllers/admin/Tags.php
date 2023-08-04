<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tags extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tags_model');
    }

    public function add_tag()
    {
        if (!has_permission('settings', '', 'add')) {
            access_denied('settings');
        }

        if ($this->input->post()) {
            $tag_name = $this->input->post('new_tag');

            $tag_id = $this->tags_model->addTag($tag_name);

            if ($tag_id) {
                set_alert('success', 'Tag adicionada com sucesso');
            } else {
                set_alert('danger', 'Falha ao adicionar a tag');
            }
        }

        redirect(admin_url('settings?group=tags'));
    }
}
