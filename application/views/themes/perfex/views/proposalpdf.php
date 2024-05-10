<?php

defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();
/*
    TIPO DE PROPOSTA MAQUINAS
*/
if($proposal->proposal_type != "Sutil Cargo"){
    //página 1
$html = '<img src="'. base_url('/uploads/company/logo_sutil.jpg'). '">';
$html .= '<br> <h1 align="center" style="font-size:70px;">' . $proposal->subject . '</h1>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->addPage();
//página 2
$html = '<br><br><br> <p>A Sutil Máquinas atua na área de equipamentos industriais voltados para a indústria de
processamento de biomassa, classificação e processamento de resíduos urbanos e industriais,
secagem em geral e específica para alimentos e materiais pastosos, fabricas de fertilizantes.
Possui mais de 30 anos de experiência em projeto, desenvolvimento, construção e implantação de
equipamentos industriais e soluções turn key, que somam mais de 300 projetos implantados nos
principais players dos setores em que atua.</p>
<p>São nossos clientes efetivos:</p>
    <ul>
        <li> Klabin / Ambitec Projeto Puma I e II - Ortigueira - PR;</li>
        <li> Mec Prec (pertencente ao grupo Klabin) - Telêmaco Borba;</li>
        <li> Klabin Otacílio Costa - SC;</li>
        <li> Irmãos Benecke - Timbó - SC;</li>
        <li> Grupo Ambipar - SP;</li>
        <li> Grupo Ecomark - SP;</li>
        <li> Grupo Planfértil;</li>
        <li> Faber Castell;</li>
        <li> Grupo Consita;</li>
        <li> Grupo Ferticel;</li>
        <li> Grupo Cetric;</li>
        <li> Multicolor Têxtil;</li>
        <li> Timac Fertilizantes;</li>
        <li> Grupo Bauminas;</li>
        <li> Incal Conterma;</li>
        <li> Grupo Palmali;</li>
        <li> Teka Têxtil;</li>
        <li> Usina Flex Porto Seguro;</li>
        <li> Raizem / Barra Bonita / Piracicaba - SP;</li>
        <li> Lafarje Cimentos;</li>
        <li> JBS/Eldorado Papel e Celulose</li>
        <li> Votorantim unidades Itapeva SP /Rio Branco do Sul/ </li>
    </ul>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->addPage();
//página 3 em diante
//informações da empresa
$this->SetFont($this->get_font_name(), '', 10);
$texto_empresa = get_option('invoice_company_name').'<br>';
$texto_empresa .= get_option('invoice_company_address').'<br>';
$texto_empresa .= 'BAIRRO: INTERIOR - CEP:'.get_option('invoice_company_postal_code').' - '.get_option('invoice_company_city'). ' / '. get_option('company_state').' - BRASIL<br>';
$texto_empresa .= get_option('invoice_company_phonenumber').'<br>';
$texto_empresa .= '<a>orcamento@sutilmaquinas.com</a><br>';
$texto_empresa .= 'CNPJ: '.get_option('company_vat').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE: 257043497<br>';

$this->MultiCell(55, 5, '<img style="width:240px;" src="'. base_url('/uploads/company/logo_sutil.jpg'). '">', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(130, 32, $texto_empresa, 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
//informações da proposta e cliente
$rel_data = get_relation_data($proposal->rel_type, $proposal->rel_id);
$rel_val  = get_relation_values($rel_data, $proposal->rel_type);
$menu_proposta_cliente = 'Situação:<br>Cliente:<br>CNPJ:<br>Endereço:<br>Cidade:<br>Contato:<br>E-mail:<br>Tipo de Frete:<br>Proposta:';

$texto_proposta_cliente = strtoupper(format_proposal_status($proposal->status)).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Data Emissão: </b>'._d($proposal->date).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo Proposta:</b> '.$proposal->proposal_type.'<br>';
$texto_proposta_cliente .= $proposal->rel_id.' - '. $rel_val['name'].'<br>';
$texto_proposta_cliente .= $rel_data->vat.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE: '. $rel_data->rg_ie.'<br>';
$texto_proposta_cliente .= $proposal->address.'<br>';
$texto_proposta_cliente .= $proposal->city.' / '. $proposal->state.'<br>';
$texto_proposta_cliente .= $proposal->proposal_to.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fone: '. $proposal->phone.'<br>';
$texto_proposta_cliente .= '<a>'.$proposal->email.'</a><br>';
$texto_proposta_cliente .= $proposal->shipping_type.'<br>';
$texto_proposta_cliente .= $proposal->id. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rev.: 0<br>';
$this->MultiCell(60, 40, $menu_proposta_cliente, 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(130, 40, $texto_proposta_cliente, 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->SetFont($this->get_font_name(), '', 12);
$this->MultiCell(190, 5, '<b>Itens da Proposta</b>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);

/*
$y = $pdf->getY();

$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);

$pdf->ln(4);
// Get Y position for the separation
$y = $pdf->getY();

$proposal_info = '<div style="color:#424242;">';
    $proposal_info .= format_organization_info();
$proposal_info .= '</div>';

$pdf->writeHTMLCell(($swap == '0' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $proposal_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);

// Proposal to
$client_details = '<b>' . _l('proposal_to') . '</b>';
$client_details .= '<div style="color:#424242;">';
    $client_details .= format_proposal_info($proposal, 'pdf');
$client_details .= '</div>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 7, '', ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);

$pdf->ln(6);

$proposal_date = _l('proposal_date') . ': ' . _d($proposal->date);
$open_till     = '';

if (!empty($proposal->open_till)) {
    $open_till = _l('proposal_open_till') . ': ' . _d($proposal->open_till) . '<br />';
}


$project = '';
if ($proposal->project_id != '' && get_option('show_project_on_proposal') == 1) {
    $project .= _l('project') . ': ' . get_project_name_by_id($proposal->project_id) . '<br />';
}

$qty_heading = _l('estimate_table_quantity_heading', '', false);

if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l($this->type . '_table_hours_heading', '', false);
} elseif ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading', '', false) . '/' . _l('estimate_table_hours_heading', '', false);
}
*/
// The items table
$items = get_items_table_data($proposal, 'proposal', 'pdf')
        ->set_headings('estimate');

//$items_html = $items->table();
$this->SetFont($this->get_font_name(), '', 11);
$quantos_items = count($proposal->items);
$contador_items = 0;
foreach ($proposal->items as $item){
    $contador_items++;
    if($item['item_image'] != null){
        $url_img_item = '/uploads/proposals/item_'.$item['original_id'].'/'.$item['item_image'];
    }else{
        $url_img_item = '/uploads/company/logo_sutil.jpg';
    }
    $this->MultiCell(90, 10, '<img src="'. base_url($url_img_item). '">', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(100, 10, '<b> Quantidade &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Valor Unit. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Valor Total </b', 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(98, 10, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(30, 10, number_format($item['qty'],0,",","."), 0, 'C', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(32, 10, $proposal->symbol.number_format($item['rate'],2,",","."), 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(30, 10, $proposal->symbol.number_format($item['qty']*$item['rate'],2,",","."), 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(98, 70, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(190, 60, '<h4>'.$item['description'].'</h4><p>'.$item['long_description'].'</p>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
    if($contador_items < $quantos_items){
        $pdf->addPage();
    }
}                                                           
$items_html .= '<br/><br/><br/><br/>';
$items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';

$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
</tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
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

//penultima pagina
$this->SetFont($this->get_font_name(), '', 12);
$pdf->addPage();
$html = '<p>Obrigações da Compradora:</p>
    <ul>
        <li>Frete por conta e risco da compradora.</li>
        <li>Muncks de elevações e carregamento de itens diversos.</li>
        <li>Conferência dos projetos e fabricação dos equipamentos.</li>
        <li>Fornecimento de dados técnicos do processo e validação dos documentos.</li>
        <li>Obras civis (bases) de acordo com a solicitação da Sutil (projeto).</li>
        <li>Área com segurança para armazenar materiais, de máquinas e de montagens.</li>
        <li>Disponibilidade de pontos de energias elétricas disponíveis para montagem.</li>
        <li>Pontos com energia com tensões de qualidade para montagens.</li>
        <li>Disponibilidade de aprovação e liberação de montagens para mínimo 8 horas diárias.</li>
        <li>Colaboradores competentes para treinamento no ato do comissionamento e montagem e treinamento de manutenção.</li>
        <li>Assinatura nas declarações de retiradas e de recebimento de material.</li>
        <li>Assinatura e aprovação nos projetos enviados pela Sutil.</li>
        <li>Aprovação do Layout do processo.</li>
        <li>Despesas de translado estádia e alimentação por conta do comprador.</li>
        <li>Içamento de carregamento, descargas e montagem por conta do cliente.</li>
    </ul>
    <p>Obrigações do Vendedor:</p>
    <ul>
        <li>Garantia mecânica de 6 meses e motores elétricos 12 meses.</li>
        <li>Garantia contra quebras mecânicas em peças (eixos, moto redutores, pistas de rodagens, chapas de cilindro, eixo de centrífugas, revestimento de rodas).</li>
        <li>Itens considerados mau uso e sem garantia (Componentes elétricos expostos a pó, desalinhamentos de rodas com quebras de eixos ou danos ao poliuretano, sobrecarga na centrífuga ou no cilindro da peneira, sobrecarga e queimas em motores elétricos, acoplamento do sistema em local inapropriado).</li>
        <li>Garantia de qualidade dos produtos acabados.</li>
        <li>Fornecer suporte técnico e treinamento ao comprador quando contratado.</li>
        <li>Layout de montagem e especificações de cargas estáticas e dinâmicas para bases civis.</li>
        <li>Nota: Layout ilustrativo anexado a esta proposta. Layout específico e detalhamento do projeto somente após aceitação da proposta.</li>
        <li>Além disso, outras obrigações incluem:</li>
        <li>Itens considerados mau uso e sem garantia (Componentes elétricos expostos a pó, desalinhamentos de eixos de tração com quebras de eixos ou danos à esteira, sobrecarga nos componentes do processo, desalinhamentos de pistas de suporte, sobrecarga no piso móvel, desalinhamentos de rodas guias ou rodas de tração).</li>
        <li>Garantia de qualidade dos produtos acabados.</li>
        <li>Fornecer suporte técnico e treinamento ao comprador, quando contratado.</li>
        <li>Despesas de translado, estadia e alimentação por conta do Vendedor.</li>
        <li>Içamento de carregamento, descargas e montagem por conta do comprador.</li>
    </ul>
    <p>OBS:<br>Dias expressos em dias uteis</p>
    ';

$pdf->writeHTML($html, true, false, true, false, '');
//ultima pagina
setlocale(LC_TIME, 'pt_BR.utf8');
$this->SetFont($this->get_font_name(), '', 10);
$pdf->addPage();
$database = date_create($proposal->date);
$datadehoje = date_create($proposal->open_till);
$resultado_data_contrato = date_diff($database, $datadehoje);
$data_contrato = strftime('%d de %B de %Y', strtotime(date("Y-m-d")));
$this->MultiCell(190, 19, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(50, 30, '<b>Previsão Entrega:<br><br>Validade da Proposta:<br><br>Condição de Pagamento:<br></b>', 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(50, 30, (($proposal->delivery_forecast)?$proposal->delivery_forecast:'<br>').'<br><br>'._d($resultado_data_contrato->days).' dias<br><br>'.strtoupper($proposal->payment_terms), 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 8, ucwords(strtolower(get_option('invoice_company_city'))).',  '.$data_contrato.'', 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 50, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(5, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, '<hr>', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(20, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(5, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, get_option('invoice_company_name').'<br>'.preg_replace("/[^0-9]/", "", get_option('company_vat')), 0, 'C', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(20, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, $proposal->rel_id.' - '. strtoupper($rel_val['name']).'<br>'.$rel_data->vat, 0, 'C', 0, 1, '', '', true, 0, true, true, 40);



/*
    TIPO DE PROPOSTA CARGO
*/


}
else {
    //página 1
$html = '<img src="'. base_url('/uploads/company/logo_sutil_cargo.jpg'). '">';
$html .= '<br> <h1 align="center" style="font-size:70px;">' . $proposal->subject . '</h1>';
$html .= '<img src="'. base_url('/uploads/company/caminhao_sutil_cargo.jpg'). '">';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->addPage();
//página 2
$html = '<br><br><br> <p>A Sutil Máquinas atua na área de equipamentos industrias voltados para a indústria de
processamento de biomassa, classificação, e processamento de resíduos urbanos e industriais,
secagem em geral. Possui mais de 30 anos de experiência em projetos, desenvolvimento,
construção e implantação de equipamentos industrias e soluções turn key, que somam mais de
300 projetos implantados no principais players dos setores que atua.</p>
<p>O conhecimento e a excelência fabril são nossa marca registrada. Isto também esta presente na
Sutil Cargo, Quando você escolhe a Sutil Cargo, você está escolhendo anos de expertise e
inovação, proporcionando eficiência, segurança e qualidade em cada piso móvel.</p>
<p>Instalável em qualquer tipo de caminhão. </p>
<span>Benefícios:</span>
    <ul>
        <li>Tara Baixa;</li>
        <li>Ideal para volumes elevados;</li>
        <li>Segurança;</li>
        <li>Agilidade na Descarga;</li>
        <li>Alta Performance;</li>
        <li>Compacta em até 30% cargas volumosas;</li>
        <li>Elimina os riscos de tombamento na descarga;</li>
        <li>Baixo custo de manutenção;</li>
        <li>Facilidade no carregamento;</li>
        <li>Dispensa o uso de mão de obra e outros equipamentos para a descarga;</li>
        <li>Pode operar em locais com tetos baixos, considerando que não necessita de espaço livre acima do caminhão para realizar a descarga.</li>
    </ul>
<span>Aplicações:</span>
    <ul>
        <li>Serragem;</li>
        <li>Cavaco;</li>
        <li>Maravalha;</li>
        <li>Soja/milho (Cereais diversos).</li>
    </ul>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->addPage();
//página 3 em diante
//informações da empresa
$this->SetFont($this->get_font_name(), '', 10);
$texto_empresa = get_option('invoice_company_name').'<br>';
$texto_empresa .= get_option('invoice_company_address').'<br>';
$texto_empresa .= 'BAIRRO: INTERIOR - CEP:'.get_option('invoice_company_postal_code').' - '.get_option('invoice_company_city'). ' / '. get_option('company_state').' - BRASIL<br>';
$texto_empresa .= get_option('invoice_company_phonenumber').'<br>';
$texto_empresa .= '<a>orcamento@sutilmaquinas.com</a><br>';
$texto_empresa .= 'CNPJ: '.get_option('company_vat').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE: 257043497<br>';

$this->MultiCell(55, 5, '<img style="width:240px;" src="'. base_url('/uploads/company/logo_sutil.jpg'). '">', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(130, 32, $texto_empresa, 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
//informações da proposta e cliente
$rel_data = get_relation_data($proposal->rel_type, $proposal->rel_id);
$rel_val  = get_relation_values($rel_data, $proposal->rel_type);
$menu_proposta_cliente = 'Situação:<br>Cliente:<br>CNPJ:<br>Endereço:<br>Cidade:<br>Contato:<br>E-mail:<br>Tipo de Frete:<br>Proposta:';

$texto_proposta_cliente = strtoupper(format_proposal_status($proposal->status)).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Data Emissão: </b>'._d($proposal->date).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo Proposta:</b> '.$proposal->proposal_type.'<br>';
$texto_proposta_cliente .= $proposal->rel_id.' - '. $rel_val['name'].'<br>';
$texto_proposta_cliente .= $rel_data->vat.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE: '. $rel_data->rg_ie.'<br>';
$texto_proposta_cliente .= $proposal->address.'<br>';
$texto_proposta_cliente .= $proposal->city.' / '. $proposal->state.'<br>';
$texto_proposta_cliente .= $proposal->proposal_to.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fone: '. $proposal->phone.'<br>';
$texto_proposta_cliente .= '<a>'.$proposal->email.'</a><br>';
$texto_proposta_cliente .= $proposal->shipping_type.'<br>';
$texto_proposta_cliente .= $proposal->id. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rev.: 0<br>';
$this->MultiCell(60, 40, $menu_proposta_cliente, 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(130, 40, $texto_proposta_cliente, 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->SetFont($this->get_font_name(), '', 12);
$this->MultiCell(190, 5, '<b>Itens da Proposta</b>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);

/*
$y = $pdf->getY();

$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);

$pdf->ln(4);
// Get Y position for the separation
$y = $pdf->getY();

$proposal_info = '<div style="color:#424242;">';
    $proposal_info .= format_organization_info();
$proposal_info .= '</div>';

$pdf->writeHTMLCell(($swap == '0' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $proposal_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);

// Proposal to
$client_details = '<b>' . _l('proposal_to') . '</b>';
$client_details .= '<div style="color:#424242;">';
    $client_details .= format_proposal_info($proposal, 'pdf');
$client_details .= '</div>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 7, '', ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);

$pdf->ln(6);

$proposal_date = _l('proposal_date') . ': ' . _d($proposal->date);
$open_till     = '';

if (!empty($proposal->open_till)) {
    $open_till = _l('proposal_open_till') . ': ' . _d($proposal->open_till) . '<br />';
}


$project = '';
if ($proposal->project_id != '' && get_option('show_project_on_proposal') == 1) {
    $project .= _l('project') . ': ' . get_project_name_by_id($proposal->project_id) . '<br />';
}

$qty_heading = _l('estimate_table_quantity_heading', '', false);

if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l($this->type . '_table_hours_heading', '', false);
} elseif ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading', '', false) . '/' . _l('estimate_table_hours_heading', '', false);
}
*/
// The items table
$items = get_items_table_data($proposal, 'proposal', 'pdf')
        ->set_headings('estimate');

//$items_html = $items->table();
$this->SetFont($this->get_font_name(), '', 11);
$quantos_items = count($proposal->items);
$contador_items = 0;
foreach ($proposal->items as $item){
    $contador_items++;
    if($item['item_image'] != null){
        $url_img_item = '/uploads/proposals/item_'.$item['original_id'].'/'.$item['item_image'];
    }else{
        $url_img_item = '/uploads/company/logo_sutil.jpg';
    }
    $this->MultiCell(90, 10, '<img src="'. base_url($url_img_item). '">', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(100, 10, '<b> Quantidade &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Valor Unit. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Valor Total </b', 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(98, 10, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(30, 10, number_format($item['qty'],0,",","."), 0, 'C', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(32, 10, $proposal->symbol.number_format($item['rate'],2,",","."), 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
    $this->MultiCell(30, 10, $proposal->symbol.number_format($item['qty']*$item['rate'],2,",","."), 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(98, 70, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
    $this->MultiCell(190, 60, '<h4>'.$item['description'].'</h4><p>'.$item['long_description'].'</p>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
    if($contador_items < $quantos_items){
        $pdf->addPage();
    }
}                                                           
$items_html .= '<br/><br/><br/><br/>';
$items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';

$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
</tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
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

//penultima pagina
$this->SetFont($this->get_font_name(), '', 12);
$pdf->addPage();
$html = '<p>CONDIÇÕES DE VENDA:</p>
    <ul>
        <li>Condição de pagamento: '.$proposal->payment_terms;
    $html .= '</li>
    </ul>
    <p>ITENS INCLUSOS NA PROPOSTA:</p>
    <ul>
        <li>Trava de Porta;</li>
        <li>Rodas de Ferro.</li>
    </ul>
    <p>ITENS OPCIONAIS A PROPOSTA:</p>
    <ul>
        <li>Kit Hidráulico Cavalo Mecânico (não incluso a parametrização de cambio) R$ 18.000,00;</li>
        <li>13 Pneus R$ 26.800,00 ( para modelo 3 Eixos);</li>
        <li>18 Pneus R$ 37.100,00 (para modelo 4 Eixos);</li>
        <li>Acabamento Lateral em ACM. Implemento fica em torno de 850 KG mais leve. Acréscimo de R$ 15.000,00;</li>
        <li>Personalização com corte a Laser com logo do cliente (valor a orçar);</li>
        <li>Lona de Cobertura Fácil R$ 4.500,00;</li>
        <li>Sistema de Limpeza de Fundo R$ 18.000,00;</li>
        <li>Controle remoto para o piso móvel R$ 19.000,00</li>
    </ul>
    <p>OBRIGAÇÕES DA COMPRADORA:</p>
    <ul>
        <li>Frete por conta e risco da compradora;</li>
        <li>Pneus por conta do Comprador.</li>
    </ul>
    <p>OBRIGAÇÕES DA VENDEDORA:</p>
    <ul>
        <li>Prazo de entrega 30 a 60 dias uteis;</li>
        <li>Implemento homologado junto ao Inmetro e Denatran;</li>
        <li>Piso Móvel Marca Hallco.</li>
    </ul>
    <p>IMPOSTOS E TAXAS:</p>
    <ul>
        <li> Todos os impostos e taxas incidentes sobre a venda deste equipamento estão inclusos no preço e qualquer alteração
        que possa ocorrer até a data do efetivo faturamento, poderá ser repassada aos preços. Imposto conforme legislação
        vigente na data do faturamento.</li>
    </ul>
    <p>GARANTIA:</p>
    <ul>
        <li>Seis meses para estrutura e sistema piso móvel;</li>
        <li>Componentes de terceiros serão cobertos de acordo com as garantias concedidas e nos prazos determinados por seus fabricantes;</li>
        <li>Não estão cobertos por garantia: lubrificantes, vedações, calços deslizantes, buchas e itens de desgaste normal. Ainda ficam excluídos os defeitos provocados por mau uso, inobservância das normas técnicas de segurança, negligencia e não certificação do operador;</li>
        <li>Toda assistência técnica realizada fora de nossas fábricas, as despesas de transporte, alimentação e hospedagem do técnico, são por conta do comprador. No período de garantia, não será cobra mão de obra e peças de reposição que estejam em garantia.</li>
    </ul>
    ';

$pdf->writeHTML($html, true, false, true, false, '');
//ultima pagina
setlocale(LC_TIME, 'pt_BR.utf8');
$this->SetFont($this->get_font_name(), '', 10);
$pdf->addPage();
$database = date_create($proposal->date);
$datadehoje = date_create($proposal->open_till);
$resultado_data_contrato = date_diff($database, $datadehoje);
$data_contrato = strftime('%d de %B de %Y', strtotime(date("Y-m-d")));
$this->MultiCell(190, 19, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(50, 30, '<b>Previsão Entrega:<br><br>Validade da Proposta:<br><br>Condição de Pagamento:<br></b>', 0, 'R', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(50, 30, (($proposal->delivery_forecast)?$proposal->delivery_forecast:'<br>').'<br><br>'._d($resultado_data_contrato->days).' dias<br><br>'.strtoupper($proposal->payment_terms), 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 8, ucwords(strtolower(get_option('invoice_company_city'))).',  '.$data_contrato.'', 0, 'R', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(190, 50, '', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(5, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, '<hr>', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(20, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, '<hr>', 0, 'L', 0, 1, '', '', true, 0, true, true, 40);
$this->MultiCell(5, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, get_option('invoice_company_name').'<br>'.preg_replace("/[^0-9]/", "", get_option('company_vat')), 0, 'C', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(20, 1, '', 0, 'L', 0, 0, '', '', true, 0, true, true, 40);
$this->MultiCell(80, 1, $proposal->rel_id.' - '. strtoupper($rel_val['name']).'<br>'.$rel_data->vat, 0, 'C', 0, 1, '', '', true, 0, true, true, 40);

}
