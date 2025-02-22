<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="init_lead(); return false;"
                        class="btn btn-primary mright5 pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new_lead'); ?>
                    </a>
                    <?php if (is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1') { ?>
                    <a href="<?php echo admin_url('leads/import'); ?>"
                        class="btn btn-primary pull-left display-block hidden-xs">
                        <i class="fa-solid fa-upload tw-mr-1"></i>
                        <?php echo _l('import_leads'); ?>
                    </a>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-5 ">
                            <a href="#" class="btn btn-default btn-with-tooltip" data-toggle="tooltip"
                                data-title="<?php echo _l('leads_summary'); ?>" data-placement="top"
                                onclick="slideToggle('.leads-overview'); return false;"><i
                                    class="fa fa-bar-chart"></i></a>
                            <a href="<?php echo admin_url('leads/switch_kanban/' . $switch_kanban); ?>"
                                class="btn btn-default mleft5 hidden-xs" data-toggle="tooltip" data-placement="top"
                                data-title="<?php echo $switch_kanban == 1 ? _l('leads_switch_to_kanban') : _l('switch_to_list_view'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                <i class="fa-solid fa-grip-vertical"></i>
                                <?php } else { ?>
                                <i class="fa-solid fa-table-list"></i>
                                <?php }; ?>
                            </a>
                        </div>
                        <div class="col-sm-4 col-xs-12 pull-right leads-search">
                            <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                            <div data-toggle="tooltip" data-placement="top"
                                data-title="<?php echo _l('search_by_tags'); ?>">
                                <?php echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'leads_kanban();', 'placeholder' => _l('leads_search')], [], 'no-margin') ?>
                            </div>
                            <?php } ?>
                            <?php echo form_hidden('sort_type'); ?>
                            <?php echo form_hidden('sort', (get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="hide leads-overview tw-mt-2 sm:tw-mt-4 tw-mb-4 sm:tw-mb-0">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                            <?php echo _l('leads_summary'); ?>
                        </h4>
                        <div class="tw-flex tw-flex-wrap tw-flex-col lg:tw-flex-row tw-w-full tw-gap-3 lg:tw-gap-6">
                            <?php
                           foreach ($summary as $status) { ?>
                            <div
                                class="lg:tw-border-r lg:tw-border-solid lg:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center last:tw-border-r-0">
                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php
                                          if (isset($status['percent'])) {
                                              echo '<span data-toggle="tooltip" data-title="' . $status['total'] . '">' . $status['percent'] . '%</span>';
                                          } else {
                                              // Is regular status
                                              echo $status['total'];
                                          }
                                       ?>
                                </span>
                                <span style="color:<?php echo $status['color']; ?>"
                                    class="<?php echo isset($status['junk']) || isset($status['lost']) ? 'text-danger' : ''; ?>">
                                    <?php echo $status['name']; ?>
                                </span>
                            </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <div class="<?php echo $isKanBan ? '' : 'panel_s' ; ?>">
                    <div class="<?php echo $isKanBan ? '' : 'panel-body' ; ?>">
                        <div class="tab-content">
                            <?php
                        if ($isKanBan) { ?>
                            <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="kanban-leads-sort">
                                    <span class="bold">
                                        <?php echo _l('leads_sort_by'); ?>:
                                    </span>
                                    <a href="#" onclick="leads_kanban_sort('dateadded'); return false"
                                        class="dateadded">
                                        <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                        } ?>
                                        <?php echo _l('leads_sort_by_datecreated'); ?>
                                    </a>
                                    |
                                    <a href="#" onclick="leads_kanban_sort('leadorder');return false;"
                                        class="leadorder">
                                        <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                        } ?>
                                        <?php echo _l('leads_sort_by_kanban_order'); ?>
                                    </a>
                                    |
                                    <a href="#" onclick="leads_kanban_sort('lastcontact');return false;"
                                        class="lastcontact">
                                        <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                        } ?>
                                        <?php echo _l('leads_sort_by_lastcontact'); ?>
                                    </a>
                                </div>
                                <div class="row">
                                    <div class="container-fluid leads-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="row" id="leads-table">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="bold">
                                                <?php echo _l('filter_by'); ?>
                                            </p>
                                        </div>
                                        <?php if (has_permission('leads', '', 'view')) { ?>
                                        <div class="col-md-3 leads-filter-column">
                                            <?php echo render_select('view_assigned', $staff, ['staffid', ['firstname', 'lastname']], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')], [], 'no-mbot'); ?>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-3 leads-filter-column">
                                            <?php
                                        $selected = [];
                                        if ($this->input->get('status')) {
                                            $selected[] = $this->input->get('status');
                                        } else {
                                            foreach ($statuses as $key => $status) {
                                                if ($status['isdefault'] == 0) {
                                                    $selected[] = $status['id'];
                                                } else {
                                                    $statuses[$key]['option_attributes'] = ['data-subtext' => _l('leads_converted_to_client')];
                                                }
                                            }
                                        }
                                        echo '<div id="leads-filter-status">';
                                        echo render_select('view_status[]', $statuses, ['id', 'name'], '', $selected, ['data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                                        echo '</div>';
                                        ?>
                                        </div>
                                        <div class="col-md-3 leads-filter-column">
                                            <?php
                                                echo render_select('view_source', $sources, ['id', 'name'], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('leads_source')], [], 'no-mbot');
                                                ?>
                                        </div>
                                        <div class="col-md-3 leads-filter-column">
                                            <div class="select-placeholder">
                                                <select name="custom_view"
                                                    title="<?php echo _l('additional_filters'); ?>" id="custom_view"
                                                    class="selectpicker" data-width="100%">
                                                    <option value=""></option>
                                                    <option value="lost">
                                                        <?php echo _l('lead_lost'); ?>
                                                    </option>
                                                    <option value="junk">
                                                        <?php echo _l('lead_junk'); ?>
                                                    </option>
                                                    <option value="public">
                                                        <?php echo _l('lead_public'); ?>
                                                    </option>
                                                    <option value="contacted_today">
                                                        <?php echo _l('lead_add_edit_contacted_today'); ?>
                                                    </option>
                                                    <option value="created_today">
                                                        <?php echo _l('created_today'); ?>
                                                    </option>
                                                    <?php if (has_permission('leads', '', 'edit')) { ?>
                                                    <option value="not_assigned">
                                                        <?php echo _l('leads_not_assigned'); ?>
                                                    </option>
                                                    <?php } ?>
                                                    <?php if (isset($consent_purposes)) { ?>
                                                    <optgroup label="<?php echo _l('gdpr_consent'); ?>">
                                                        <?php foreach ($consent_purposes as $purpose) { ?>
                                                        <option value="consent_<?php echo $purpose['id']; ?>">
                                                            <?php echo $purpose['name']; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3 leads-filter-column">
                                            <?php
                                        $selected2 = [];
                                        if ($this->input->get('departments')) {
                                            $selected2[] = $this->input->get('departments');
                                        } 
                                        echo '<div id="leads-filter-departments">';
                                        echo render_select('view_departments[]', $departments, ['departmentid', 'name'], '', $selected2, ['data-width' => '100%', 'data-none-selected-text' => _l('departments'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                                        echo '</div>';
                                        ?>
                                        </div>
                                    </div>                       

                                    <hr class="hr-panel-separator" />
                                </div>
                                <div class="clearfix"></div>

                                <div class="col-md-12">
                                    <a href="#" data-toggle="modal" data-table=".table-leads"
                                        data-target="#leads_bulk_actions" class="hide bulk-actions-btn table-btn">
                                        <?php echo _l('bulk_actions'); ?>
                                    </a>
                                    <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">
                                                        <?php echo _l('bulk_actions'); ?>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?php if (has_permission('leads', '', 'delete')) { ?>
                                                    <div class="checkbox checkbox-danger">
                                                        <input type="checkbox" name="mass_delete" id="mass_delete">
                                                        <label for="mass_delete">
                                                            <?php echo _l('mass_delete'); ?>
                                                        </label>
                                                    </div>
                                                    <hr class="mass_delete_separator" />
                                                    <?php } ?>
                                                    <div id="bulk_change">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary checkbox-inline">
                                                                <input type="checkbox" name="leads_bulk_mark_lost"
                                                                    id="leads_bulk_mark_lost" value="1">
                                                                <label for="leads_bulk_mark_lost">
                                                                    <?php echo _l('lead_mark_as_lost'); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?php echo render_select('move_to_status_leads_bulk', $statuses, ['id', 'name'], 'ticket_single_change_status'); ?>
                                                        <?php
                                             echo render_select('move_to_source_leads_bulk', $sources, ['id', 'name'], 'lead_source');
                                             echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                             echo render_select('assign_to_leads_bulk', $staff, ['staffid', ['firstname', 'lastname']], 'leads_dt_assigned');
                                             ?>
                                                        <div class="form-group">
                                                            <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                            <input type="text" class="tagsinput" id="tags_bulk"
                                                                name="tags_bulk" value="" data-role="tagsinput">
                                                        </div>
                                                        <hr />
                                                        <div class="form-group no-mbot">
                                                            <div class="radio radio-primary radio-inline">
                                                                <input type="radio" name="leads_bulk_visibility"
                                                                    id="leads_bulk_public" value="public">
                                                                <label for="leads_bulk_public">
                                                                    <?php echo _l('lead_public'); ?>
                                                                </label>
                                                            </div>
                                                            <div class="radio radio-primary radio-inline">
                                                                <input type="radio" name="leads_bulk_visibility"
                                                                    id="leads_bulk_private" value="private">
                                                                <label for="leads_bulk_private">
                                                                    <?php echo _l('private'); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                                        <?php echo _l('close'); ?>
                                                    </button>
                                                    <a href="#" class="btn btn-primary"
                                                        onclick="leads_bulk_action(this); return false;">
                                                        <?php echo _l('confirm'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->
                                    <?php

                              $table_data  = [];
                              $_table_data = [
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                [
                                 'name'     => _l('the_number_sign'),
                                 'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                               ],
                                [
                                 'name'     => _l('leads_dt_name'),
                                 'th_attrs' => ['class' => 'toggleable', 'id' => 'th-name'],
                               ],
                              ];
                              if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                  $_table_data[] = [
                                    'name'     => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                    'th_attrs' => ['id' => 'th-consent', 'class' => 'not-export'],
                                 ];
                              }
                              $_table_data[] = [
                               'name'     => _l('lead_company'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_dt_email'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-email'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_dt_phonenumber'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
                              ];
                              $_table_data[] = [
                                 'name'     => _l('leads_dt_lead_value'),
                                 'th_attrs' => ['class' => 'toggleable', 'id' => 'th-lead-value'],
                                ];
                              $_table_data[] = [
                               'name'     => _l('tags'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-tags'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_dt_assigned'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-assigned'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_dt_status'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-status'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_source'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-source'],
                              ];
                              $_table_data[] = [
                               'name'     => _l('leads_dt_last_contact'),
                               'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-contact'],
                              ];
                              $_table_data[] = [
                                'name'     => _l('leads_dt_datecreated'),
                                'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-date-created'],
                              ];
                              $_table_data[] = [
                                'name'     => _l('department'),
                                'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-deparments'],
                              ];
                              foreach ($_table_data as $_t) {
                                  array_push($table_data, $_t);
                              }
                              $custom_fields = get_custom_fields('leads', ['show_on_table' => 1]);
                              foreach ($custom_fields as $field) {
                                  array_push($table_data, [
                                   'name'     => $field['name'],
                                   'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                                ]);
                              }
                              $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                               ?>
                                    <div class="panel-table-full">
                                        <?php
                                 render_datatable(
                                   $table_data,
                                   'leads',
                                   ['customizable-table number-index-2'],
                                   [
                                    'id'                         => 'table-leads',
                                    'data-last-order-identifier' => 'leads',
                                    'data-default-order'         => get_table_last_order('leads'),
                                 ]
                               );
                                ?>
                                    </div>
                                </div>
                            </div>
                            <?php }  ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
<?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once(APPPATH . 'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
<script>
    var openLeadID = '<?php echo $leadid; ?>';
    $(function () {
        leads_kanban();
        $('#leads_bulk_mark_lost').on('change', function () {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh')
        });
        $('#move_to_status_leads_bulk').on('change', function () {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });
    });
</script>
</body>

</html>