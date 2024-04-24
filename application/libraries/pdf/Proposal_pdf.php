<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Proposal_pdf extends App_pdf
{
    protected $proposal;

    private $proposal_number;

    public function Footer()
    {
        // Position from bottom
        $this->SetY(-18);

        $this->SetFont($this->get_font_name(), '', $this->get_font_size());

        hooks()->do_action('pdf_footer', ['pdf_instance' => $this, 'type' => $this->type()]);

            $this->SetFont($this->get_font_name(), '', 9);
            $this->SetTextColor(0, 0, 0);
            $numero_pagina = $this->PageNo();
            $quantas_paginas_tem = $this->getAliasNbPages();
            $texto_pagina = "Página $numero_pagina de $quantas_paginas_tem";
            $this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
            $this->MultiCell(63, 5, '', 0, 'L', 0, 0, '', '', true);
            $this->MultiCell(63, 5, '<a href="www.sutilmaquinas.com.br"><b>www.sutilmaquinas.com.br</b></a>', 0, 'C', 0, 0, '', '', true, 0, true, true, 40);
            $this->MultiCell(71, 5,  $texto_pagina, 0, 'R', 0, 1, '', '', true);
            $this->MultiCell(63, 5, 'Proposta: '.$this->proposal->id. '                      Rev.: 0', 0, 'L', 0, 0, '' ,'', true);
            $this->MultiCell(63, 5, '+ 55 ( 49 ) 33200144', 0, 'C', 0, 0, '', '', true);
            $this->MultiCell(63, 5, _d($this->proposal->date), 0, 'R', 0, 0, '', '', true);
    }
    public function __construct($proposal, $tag = '')
    {
        if ($proposal->rel_id != null && $proposal->rel_type == 'customer') {
            $this->load_language($proposal->rel_id);
        } else if ($proposal->rel_id != null && $proposal->rel_type == 'lead') {
            $CI = &get_instance();

            $CI->db->select('default_language')->where('id', $proposal->rel_id);
            $language = $CI->db->get('leads')->row()->default_language;

            load_pdf_language($language);
        }

        $proposal                = hooks()->apply_filters('proposal_html_pdf_data', $proposal);
        $GLOBALS['proposal_pdf'] = $proposal;

        parent::__construct();

        $this->tag      = $tag;
        $this->proposal = $proposal;

        $this->proposal_number = format_proposal_number($this->proposal->id);

        $this->SetTitle($this->proposal_number);
        $this->SetDisplayMode('default', 'OneColumn');

        # Don't remove these lines - important for the PDF layout
        $this->proposal->content = $this->fix_editor_html($this->proposal->content);
    }

    public function prepare()
    {
        $number_word_lang_rel_id = 'unknown';

        if ($this->proposal->rel_type == 'customer') {
            $number_word_lang_rel_id = $this->proposal->rel_id;
        }

        $this->with_number_to_word($number_word_lang_rel_id);

        $total = '';
        if ($this->proposal->total != 0) {
            $total = app_format_money($this->proposal->total, get_currency($this->proposal->currency));
            $total = _l('proposal_total') . ': ' . $total;
        }

        $this->set_view_vars([
            'number'       => $this->proposal_number,
            'proposal'     => $this->proposal,
            'total'        => $total,
            'proposal_url' => site_url('proposal/' . $this->proposal->id . '/' . $this->proposal->hash),
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'proposal';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_proposalpdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/proposalpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
