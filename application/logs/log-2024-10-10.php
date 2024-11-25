<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2024-10-10 14:16:41 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:16:41 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:16:41 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:16:41 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:16:41 --> Severity: Warning --> Attempt to read property "vat" on null /var/www/html/flach/application/models/Clients_model.php 47
ERROR - 2024-10-10 14:16:41 --> Severity: error --> Exception: Attempt to assign property "vat" on null /var/www/html/flach/application/models/Clients_model.php 47
ERROR - 2024-10-10 14:24:38 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:38 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:38 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:38 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:38 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. /var/www/html/flach/application/helpers/deprecated_helper.php 48
ERROR - 2024-10-10 14:24:38 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Query error: Unknown column 'tblleads.lm_follow_up' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblleads.id as id, tblleads.name as name, 1, tblleads.company as company, tblleads.phonenumber as phonenumber, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads.lastcontact as lastcontact, tblleads.lm_follow_up as lm_follow_up, tblleads.dateadded as dateadded, 1, tbllead_manager_meeting_remark.remark as last_remark ,junk,lost,color,assigned,lastname as assigned_lastname,tblleads.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tblleads.id) as is_converted,zip,tbllead_manager_meeting_remark.lm_follow_up_date
    FROM tblleads
    LEFT JOIN tblstaff ON tblstaff.staffid = tblleads.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tblleads.status LEFT JOIN (
              SELECT    MAX(id) max_id, rel_id
              FROM      tbllead_manager_meeting_remark 
              GROUP BY  rel_id
          ) rm_max ON (rm_max.rel_id = tblleads.id) LEFT JOIN tbllead_manager_meeting_remark ON (tbllead_manager_meeting_remark.id = rm_max.max_id)
    
    WHERE  lost = 0 AND junk = 0
    
    ORDER BY tblleads.dateadded DESC
    LIMIT 0, 25
    
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:24:39 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:24:39 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:05 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:05 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:05 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:05 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:05 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. /var/www/html/flach/application/helpers/deprecated_helper.php 48
ERROR - 2024-10-10 14:25:05 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:06 --> Query error: Unknown column 'tblleads.lm_follow_up' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblleads.id as id, tblleads.name as name, 1, tblleads.company as company, tblleads.phonenumber as phonenumber, firstname as assigned_firstname, tblleads_status.name as status_name, tblleads.lastcontact as lastcontact, tblleads.lm_follow_up as lm_follow_up, tblleads.dateadded as dateadded, 1, tbllead_manager_meeting_remark.remark as last_remark ,junk,lost,color,assigned,lastname as assigned_lastname,tblleads.addedfrom as addedfrom,(SELECT count(leadid) FROM tblclients WHERE tblclients.leadid=tblleads.id) as is_converted,zip,tbllead_manager_meeting_remark.lm_follow_up_date
    FROM tblleads
    LEFT JOIN tblstaff ON tblstaff.staffid = tblleads.assigned LEFT JOIN tblleads_status ON tblleads_status.id = tblleads.status LEFT JOIN (
              SELECT    MAX(id) max_id, rel_id
              FROM      tbllead_manager_meeting_remark 
              GROUP BY  rel_id
          ) rm_max ON (rm_max.rel_id = tblleads.id) LEFT JOIN tbllead_manager_meeting_remark ON (tbllead_manager_meeting_remark.id = rm_max.max_id)
    
    WHERE  lost = 0 AND junk = 0
    
    ORDER BY tblleads.dateadded DESC
    LIMIT 0, 25
    
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:06 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:06 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:06 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:34 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:34 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:34 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:34 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:35 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. /var/www/html/flach/application/helpers/deprecated_helper.php 48
ERROR - 2024-10-10 14:25:35 --> Could not find the language line "lm_mailbox_is_read"
ERROR - 2024-10-10 14:25:35 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:36 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:36 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:42 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:42 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:42 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:42 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:45 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:45 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:45 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:45 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:45 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. /var/www/html/flach/application/helpers/deprecated_helper.php 48
ERROR - 2024-10-10 14:25:45 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:46 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_whatsapp_templates_setup_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manager_whatsapp_menu"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lead_manger_whatsapp"
ERROR - 2024-10-10 14:25:46 --> Could not find the language line "lm_permission_show_contact"
ERROR - 2024-10-10 14:25:46 --> Severity: 8192 --> Required parameter $pusher follows optional parameter $to_client /var/www/html/flach/modules/prchat/models/Prchat_model.php 516
