<?php

defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();
//informações da empresa
$this->SetFont($this->get_font_name(), '', 10);
/*
$texto_empresa = get_option('invoice_company_name').'<br>';
$texto_empresa .= get_option('invoice_company_address').'<br>';
$texto_empresa .= 'BAIRRO: INTERIOR - CEP:'.get_option('invoice_company_postal_code').' - '.get_option('invoice_company_city'). ' / '. get_option('company_state').' - BRASIL<br>';
$texto_empresa .= get_option('invoice_company_phonenumber').'<br>';
$texto_empresa .= '<a>orcamento@sutilmaquinas.com</a><br>';
$texto_empresa .= 'CNPJ: '.get_option('company_vat').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE: 257043497<br>';
*/
$texto_empresa = '<br><br><strong>FLACH CARREGADORES</strong>'.'<br>';
$texto_empresa .= 'Pedido N°'.$proposal->id.'<br>';

$this->MultiCell(55, 21, '<img style="width:240px;" src="'. base_url('/uploads/company/logo_flach.png'). '">', 1, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(135, 21, $texto_empresa, 1, 'C', 0, 1, '', '', true, 0, true, true, 40, 'M');
$this->MultiCell(190, 5, '<strong>Representada:</strong> FLACH CARREGADORES / FLACH CARREGADORES', 1, 'L', 0, 1, '', '', true, 0, true, true, 40);
//$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
//informações da proposta e cliente
$rel_data = get_relation_data($proposal->rel_type, $proposal->rel_id);
$rel_val  = get_relation_values($rel_data, $proposal->rel_type);
//$menu_proposta_cliente = 'Situação:<br>Cliente:<br>:<br>Endereço:<br>Cidade:<br>Contato:<br>E-mail:<br>Tipo de Frete:<br>Proposta:';

//$texto_proposta_cliente = '<b>Situação:</b> '.strtoupper(format_proposal_status($proposal->status)).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Data Emissão: </b>'._d($proposal->date).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo Proposta:</b> '.$proposal->proposal_type.'<br>';
$texto_proposta_cliente = '<b>Cliente:</b> '.$proposal->rel_id.' - '. $rel_val['name'].'<br>';
$texto_proposta_cliente .= '<b>CNPJ:</b> '.$rel_data->vat.'<br>';
$texto_proposta_cliente .= '<b>Endereço:</b> '.$proposal->address.'<br>';
$texto_proposta_cliente .= '<b>Bairro:</b> '.$proposal->proposal_to.'<br>';
$texto_proposta_cliente .= '<b>Cidade:</b> '.$proposal->city.'<br>';
$texto_proposta_cliente .= '<b>Telefone:</b>'.$proposal->phone.'<br>';

$texto_proposta_cliente2 = '<b>Nome Fantasia:</b> ' .$rel_data->fantasy_name.'<br>';
$texto_proposta_cliente2.= '<b>Inscrição Estadual:</b> '.$rel_data->rg_ie.'<br>';
$texto_proposta_cliente2 .= '<br>';
$texto_proposta_cliente2 .= '<b>CEP:</b> '.$proposal->zip.'<br>';
$texto_proposta_cliente2 .= '<b>Estado:</b> '.$proposal->state.'<br>';
$texto_proposta_cliente2 .= '<b>E-mail:</b>'.$proposal->email.'<br>';
//$this->MultiCell(30, 40, $menu_proposta_cliente, 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(95, 30, $texto_proposta_cliente, 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(95, 30, $texto_proposta_cliente2, 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->SetFont($this->get_font_name(), '', 12);
$this->MultiCell(190, 5, '<b>Itens da Proposta</b>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);

// The items table
$items = get_items_table_data($proposal, 'proposal', 'pdf')
        ->set_headings('estimate');
$this->SetFont($this->get_font_name(), '', 11);
$this->MultiCell(30, 10, '<b> Código </b', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(50, 10, '<b> Descrição </b', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(20, 10, '<b> Qtdade </b', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(28, 10, '<b> Valor Unit. </b', 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(32, 10, '<b> Desconto </b', 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(33, 10, '<b> Valor Total </b', 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
//$items_html = $items->table();
$this->SetFont($this->get_font_name(), '', 10);
$quantos_items = count($proposal->items);
$contador_items = 0;
$this->SetFillColor(200, 255, 127);
$subtotal = 0;
foreach ($proposal->items as $item){
    $contador_items++;
    $y = $pdf->getY();
    if($y >= 255){//para não bugar um produto no final da pagina e ir corretamente para a proxima pagina
        $pdf->addPage();
    }
    if($contador_items % 2 == 0){
    //if(true){
        $pdf->SetFillColor(200, 200, 200);
    }else{
        $pdf->SetFillColor(255, 255, 255);
    }

    $url_img_item = null;
    if($item['format_image'] != null){
        $url_img_item = 'data:image/'.$item['format_image'].';base64, '.$item['item_image'];
    }else if($item['alternative_path_image'] == 1){
        $url_img_item = base_url("").'uploads/proposals/itemable_'.$item['id'].'/'.rawurlencode($item['item_image']);
    }else if ($item['item_image'] != null){
        $url_img_item = base_url("").'/uploads/proposals/item_'.$item['original_id'].'/'.rawurlencode($item['item_image']);
    }else{
        $url_img_item = base_url("").'/uploads/company/logo_flach.png';
    }
    
    //calcular altura do fundo da imagem e da descrição dinamicamente
    $altura_imagem_descricao = 0;
    if((strlen($item['long_description'])/10) > 30){
        $altura_imagem_descricao = (strlen($item['long_description'])/9);
    }else{
        $altura_imagem_descricao = 30;
    }
    //unir os 3 descontos do item (se possuir)
    $descontoItem = "";
    $amount = $item['qty']*$item['rate'];
    if($item['item_discount_percent'] && $item['item_discount_percent'] != 0){
        $descontoItem .= app_format_number($item['item_discount_percent'], true)."%";
        $amount = $amount - (($amount * $item['item_discount_percent']) / 100);
    }
    if($item['item_discount_percent2'] && $item['item_discount_percent2'] != 0){
        $descontoItem .= " + ".app_format_number($item['item_discount_percent2'], true)."%";
        $amount = $amount - (($amount * $item['item_discount_percent2']) / 100);
    }
    if($item['item_discount_percent3'] && $item['item_discount_percent3'] != 0){
        $descontoItem .= " + ".app_format_number($item['item_discount_percent3'], true)."%";
        $amount = $amount - (($amount * $item['item_discount_percent3']) / 100);
    }
    $subtotal += $amount;

    $pdf->MultiCell(30, 10, '<b>'.$item['original_id'].'</b>', 0, 'L', 1, 0, '', '', true, 0, true, true, 40);
    $pdf->MultiCell(50, 10, '<h4>'.$item['description'].'</h4>', 0, 'L', 1, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(20, 10, '<b>'.number_format($item['qty'],0,",",".").'</b>', 0, 'C', 1, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(28, 10, '<b>'.$proposal->symbol.number_format($item['rate'],2,",",".").'</b>', 0, 'R', 1, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(32, 10, '<b>'.($descontoItem).'</b>', 0, 'R', 1, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(33, 10, '<b>'.$proposal->symbol.number_format($amount,2,",",".").'</b>', 0, 'R', 1, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(30, $altura_imagem_descricao, '<img src="'. $url_img_item. '">', 0, 'L', 1, 0, '', '', true, 0, true, true, 40);
    //$this->MultiCell(98, 10, '', 1, 'L', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(163, $altura_imagem_descricao, '<p>'.$item['long_description'].'</p>', 0, 'L', 1, 1, '', '', true, 0, true, true, 40);

    $this->MultiCell(15, 20, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
    if($contador_items < $quantos_items){
        //$pdf->addPage();
    }
}
$proposalTotal = $subtotal;

$pdf->SetFillColor(255, 255, 255);                                     
$items_html .= '<br/><br/><br/><br/>';
$items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';

$items_html .= '
<tr>
    <td align="right" width="80%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="20%">' . app_format_money($subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
        <td align="right" width="80%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        if($proposal->discount_percent && $proposal->discount_percent != 0){
            $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
            $proposalTotal = $proposalTotal - (($proposalTotal * $proposal->discount_percent) / 100);
        }
        if($proposal->discount_percent2 && $proposal->discount_percent2 != 0){
            $items_html .= ' + (' . app_format_number($proposal->discount_percent2, true) . '%)';
            $proposalTotal = $proposalTotal - (($proposalTotal * $proposal->discount_percent2) / 100);
        }
        if($proposal->discount_percent3 && $proposal->discount_percent3 != 0){
            $items_html .= ' + (' . app_format_number($proposal->discount_percent3, true) . '%)';
            $proposalTotal = $proposalTotal - (($proposalTotal * $proposal->discount_percent3) / 100);
        }
        $items_html .= '</strong>';
        $items_html .= '</td>';
        $items_html .= '<td align="right" width="20%">-' . app_format_money($subtotal-$proposalTotal, $proposal->currency_name) . '</td>
        </tr>';
    }else{
        $proposalTotal = $proposalTotal - $proposal->discount_total;
        $items_html .= '</strong>';
        $items_html .= '</td>';
        $items_html .= '<td align="right" width="20%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
        </tr>';
    }
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="80%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="20%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="80%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="20%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
</tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="80%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="20%">' . app_format_money($proposalTotal, $proposal->currency_name) . '</td>
</tr>';
$items_html .= '</table>';

if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><br />';
    $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
}

//$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);

// Get the proposals css
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = $items_html;

$pdf->writeHTML($html, true, false, true, false, '');