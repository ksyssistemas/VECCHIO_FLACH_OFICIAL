<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="col-md-1">
                           <p class="bold fil_cl"><?php echo _l('filter_by'); ?> :</p>
                        </div>
                        <div class="col-md-4">
                           <div class="leads-filter-column">
                              <?php $selected = null;
                              $select_attrs = [];
                              is_admin() ? $selected = null : $selected = get_staff_user_id();
                              is_admin() ? $select_attrs = ['data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')] : $select_attrs = ['disabled' => 'disabled', 'data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')]; ?>
                              <?php echo render_select('view_assigned', $staff, array('staffid', array('firstname', 'lastname')), '', $selected, $select_attrs, array(), 'no-mbot'); ?>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <select name="period" id="period" class="form-control">
                              <option value="1">Últimas 24 Hrs</option>
                              <option value="7">Última Semana</option>
                              <option value="30">1 Mês</option>
                              <option value="90">3 Mêses</option>
                              <option value="180">6 Mêses</option>
                              <option value="365">12 Mêses</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <hr class="hr-panel-heading" />
                  <div id="dashboard-data">
                     <div class="row">
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo isset($audio_calls['outgoing']) ? $audio_calls['outgoing'] : 0; ?></h3>
                                 <span class="text-primary">Chamadas externas</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo isset($audio_calls['incoming']) ? $audio_calls['incoming'] : 0; ?></h3>
                                 <span class="text-primary">Chamadas de entrada</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $missed_call ? $missed_call : 0; ?></h3>
                                 <span class="text-danger">Chamadas perdidas</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $leads_converted ? $leads_converted : 0; ?></h3>
                                 <span class="text-success">Leads convertidos</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $sms ? $sms : 0; ?></h3>
                                 <span class="text-success">SMS enviados</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $zoom['waiting'] + $zoom['end']; ?></h3>
                                 <span class="text-primary">Chamadas agendadas</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $zoom['waiting'] ? $zoom['waiting'] : 0; ?></h3>
                                 <span class="text-warning">Próximas chamadas</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo $zoom['end'] ? $zoom['end'] : 0; ?></h3>
                                 <span class="text-success">Chamadas Atendidas</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo isset($audio_calls_duration['incoming']) ? $audio_calls_duration['incoming'] : '00:00:00'; ?></h3>
                                 <span class="text-primary">Durações das chamadas recebidas</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total"><?php echo isset($audio_calls_duration['outgoing']) ? $audio_calls_duration['outgoing'] : '00:00:00'; ?></h3>
                                 <span class="text-primary">Durações das chamadas de saída</span>
                              </div>
                           </div>
                        </div>
                        <?php if (is_admin()) { ?>
                           <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                              <div class="panel_s">
                                 <div class="panel-body">
                                    <h3 class="text-muted _total">$<?php echo $twilio['balance'] ? $twilio['balance'] : '0:00'; ?></h3>
                                    <span class="text-warning">Saldo Twilio</span>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-3 col-xs-12 col-md-12 total-column">
                              <div class="panel_s">
                                 <div class="panel-body">
                                    <h3 class="text-muted _total"><?php echo $twilio['numbers'] ? $twilio['numbers'] : '0'; ?></h3>
                                    <span class="text-primary">Números Totais Twilio</span>
                                 </div>
                              </div>
                           </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
   var url = window.location.href;
   $("#view_assigned").change(function() {
      staffId = $(this).val();
      period = $("#period").val();
      $.get(admin_url + 'lead_manager/dashboard', {
         'staff_id': staffId,
         'days': period
      }, function(response) {
         $("#dashboard-data").html(response);
      })
   })
   $("#period").change(function() {
      period = $(this).val();
      staffId = $("#view_assigned").val()
      $.get(admin_url + 'lead_manager/dashboard', {
         'staff_id': staffId,
         'days': period
      }, function(response) {
         $("#dashboard-data").html(response);
      })
   })
</script>
</body>

</html>