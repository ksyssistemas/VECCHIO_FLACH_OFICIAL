<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    private $staff_no_view_permissions;

    public function __construct()
    {
        parent::__construct();

        $this->staff_no_view_permissions  = !staff_can('view', 'appointments') && !staff_can('view_own', 'appointments');

        $this->load->model('appointly_model', 'apm');
    }

    /**
     * Main view
     *
     * @return void
     */
    public function index()
    {
        if ($this->staff_no_view_permissions) {
            access_denied('Appointments');
        }

        $data['td_appointments'] = $this->getTodaysAppointments();

        $this->load->view('index', $data);
    }

    /**
     * Single appointment view
     *
     * @return void
     */
    public function view()
    {
        $appointment_id = $this->input->get('appointment_id');

        $attendees = $this->atm->attendees($appointment_id);
        /**
         * If user is assigned to a appointment but have no permissions at all eg. edit or view
         * User will be able to open the url send to mail (But only to view this specific meeting or meetings that the user is assigned to)
         */

        if (!in_array(get_staff_user_id(), $attendees)) {
            // Global view permissions required
            if (!staff_can('view', 'appointments')) {
                access_denied('Appointments');
            }
        }

        $data['appointment'] = fetch_appointment_data($appointment_id);

        if ($data['appointment']) {
            $data['appointment']['public_url'] = site_url('appointly/appointments_public/client_hash?hash=' . $data['appointment']['hash']);
        } else {
            appointly_redirect_after_event('warning', _l('appointment_not_exists'));
        }

        if (!$data['appointment']) {
            show_404();
        }

        $this->load->view('tables/appointment', $data);
    }

    /**
     * Render table view
     *
     * @return void
     */
    public function table()
    {
        if ($this->staff_no_view_permissions) {
            access_denied();
        }

        $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'tables/index'));
    }

    /**
     * Get contact data
     *
     * @return json
     */
    public function fetch_contact_data()
    {
        if (!$this->input->is_ajax_request() || !is_staff_logged_in()) {
            show_404();
        }

        $id = $this->input->post('contact_id');
        $is_lead = $this->input->post('lead');

        if ($id) {
            header('Content-Type: application/json');
            echo json_encode($this->apm->apply_contact_data($id, $is_lead));
        }
    }

    /**
     * Modal edit and modal update trigger views with data
     *
     * @return void
     */
    public function modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->model('staff_model');

        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

        $data['contacts'] = appointly_get_staff_customers();

        if ($this->input->post('slug') === 'create') {

            $this->load->view('modals/create', $data);
        } else if ($this->input->post('slug') === 'update') {

            $data['appointment_id'] = $this->input->post('appointment_id');

            $data['history'] = fetch_appointment_data($data['appointment_id']);

            if (isset($data['notes'])) {
                $data['notes'] = htmlentities($data['notes']);
            }

            $this->load->view('modals/update', $data);
        }
    }

    /**
     * Update appointment
     *
     * @return void
     */
    public function update()
    {

        $appointment = $this->input->post();
        $appointment['notes'] = $this->input->post('notes', false);

        if (staff_can('edit', 'appointments') || staff_appointments_responsible()) {
            if ($appointment) {
                if ($this->apm->update_appointment($appointment)) {
                    header('Content-Type: application/json');
                    echo json_encode(['result' => true]);
                }
            }
        }
    }

    /**
     * Create appointment
     *
     * @return void
     */
    public function create()
    {
        if (!staff_can('create', 'appointments') && !staff_appointments_responsible()) {
            access_denied();
        }

        $data = array();

        $data = $this->input->post();
        if (!empty($data)) {
            if ($this->apm->insert_appointment($data)) {
                header('Content-Type: application/json');
                echo json_encode(['result' => true]);
            }
        }
    }

    /**
     * Delete appointment
     *
     * @param [type] appointment $id
     * @return mixed
     */
    public function delete($id)
    {
        $appointment = $this->apm->get_appointment_data($id);

        if (staff_can('delete', 'appointments') && $appointment['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
            if (!$this->input->is_ajax_request() && isset($id)) {
                if ($this->apm->delete_appointment($id)) {
                    appointly_redirect_after_event('success', _l('appointment_deleted'));
                }
            }

            if (isset($id)) {
                if ($this->apm->delete_appointment($id)) {
                    echo json_encode(['success' => true, 'message' => _l('appointment_deleted')]);
                    return;
                }
            } else {
                show_404();
            }
        }
    }


    /**
     * Approve new appointment
     *
     * @return void
     */
    public function approve()
    {
        if (!is_admin() && !staff_appointments_responsible()) {
            access_denied();
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['result' => $this->apm->approve_appointment($this->input->post('appointment_id'))]);
            die;
        }

        if ($this->apm->approve_appointment($this->input->get('appointment_id'))) {
            appointly_redirect_after_event('success', _l('appointment_appointment_approved'));
        }
    }

    /**
     * Mark appointment as finished
     *
     * @return json
     */
    public function finished()
    {
        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);

        if (staff_can('edit', 'appointments') && $appointment['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
            return $this->apm->mark_as_finished($id);
        }

        return false;
    }

    /**
     * Mark appointment as ongoing
     *
     * @return json
     */
    public function mark_as_ongoing_appointment()
    {
        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);

        if (staff_can('edit', 'appointments') && $appointment['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
            return $this->apm->mark_as_ongoing($appointment);
        }

        return false;
    }

    /**
     * Mark appointment as cancelled
     *
     * @return json
     */
    public function cancel_appointment()
    {
        $id = $this->input->post('id');

        $appointment = $this->apm->get_appointment_data($id);

        if (staff_can('edit', 'appointments') && $appointment['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
            return $this->apm->cancel_appointment($id);
        }

        return false;
    }

    /**
     * Get todays appointments
     *
     * @return array
     */
    public function getTodaysAppointments()
    {
        return $this->apm->fetch_todays_appointments();
    }

    /**
     * Send appointment early reminders
     *
     * @param [string] appointment_id
     * @return json
     */
    public function send_appointment_early_reminders()
    {
        if ($this->staff_no_view_permissions || !staff_appointments_responsible()) {
            access_denied();
        }

        if ($this->apm->send_appointment_early_reminders($this->input->post('id'))) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }


    /** 
     * Load user settings view
     * @return view
     */
    public function user_settings_view()
    {
        $data = [];

        if ($this->staff_no_view_permissions) {
            access_denied();
        }

        $data = getAppoinlyUserMeta();

        $data['filters'] = get_appointments_table_filters();

        $this->load->view('users/index', $data);
    }



    /** 
     * User settings request for updating options in meta table
     * @return void
     */
    public function user_settings()
    {
        $data  = $this->input->post();

        if ($data) {

            $meta = [
                'appointly_show_summary' => $this->input->post('appointly_show_summary'),
                'appointly_default_table_filter' => $this->input->post('appointly_default_table_filter'),
            ];

            $this->apm->update_appointment_types($data, $meta);

            appointly_redirect_after_event('success', _l('settings_updated'), 'appointments/user_settings_view/settings');
        }
    }

    /** 
     * Add new appointment type
     * @return json
     */
    public function new_appointment_type()
    {
        if (!staff_appointments_responsible() && !staff_can('create', 'appointments')) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($this->apm->new_appointment_type(
                $this->input->post('type'),
                $this->input->post('color')
            )) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            }
        }
        return false;
    }


    /**
     * Delete appointment type
     * @param [string] id
     * @return boolean
     */
    public function delete_appointment_type()
    {
        if (!staff_can('delete', 'appointments') && !staff_appointments_responsible()) {
            access_denied();
        }
        return $this->apm->delete_appointment_type($this->input->post('id'));
    }

    /** 
     * Add event to google calendar
     * @return json
     */
    public function addEventToGoogleCalendar()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!staff_can('edit', 'appointments') && !staff_appointments_responsible()) {
            access_denied();
        }

        $data = array();

        $data = $this->input->post();
        if ($data && !empty($data)) {
            header('Content-Type: application/json');
            $result = $this->apm->add_event_to_google_calendar($data);
            if ($result) {
                echo json_encode($result);
            }
        }
    }

    /**
     * Request new appointment feedback
     *
     * @param string $id
     * @return json
     */
    public function requestAppointmentFeedback($id)
    {
        if ($id && !empty($id)) {
            header('Content-Type: application/json');
            $result = $this->apm->request_appointment_feedback($id);
            if ($result) {
                echo json_encode($result);
            }
        }
    }

    /**
     * Get attendee details
     *
     * @return json
     */
    public function getAttendeeData()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if ($this->input->post('ids')) {
            header('Content-Type: application/json');
            echo json_encode($this->atm->details($this->input->post('ids')));
        }
    }

    /**
     * Add new outlook event to calendar
     *
     * @return json
     */
    public function newOutlookEvent()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = array();

        $data = $this->input->post();

        if ($data && !empty($data)) {
            header('Content-Type: application/json');
            echo json_encode(['result' => $this->apm->inserNewOutlookEvent($data)]);
        }
    }
    /**
     * Add new outlook event to calendar from existing appointnent
     *
     * @return json
     */
    public function updateAndAddExistingOutlookEvent()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = array();
        $data = $this->input->post();

        if ($data && !empty($data)) {
            header('Content-Type: application/json');
            echo json_encode(['result' => $this->apm->updateAndAddExistingOutlookEvent($data)]);
        }
    }

    /**
     * Send custom email to request meet via Google Meet
     *
     * @return json
     */
    public function sendCustomEmail()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = array();

        $data = $this->input->post();

        if ($data && !empty($data)) {
            header('Content-Type: application/json');
            echo json_encode($this->apm->sendGoogleMeetRequestEmail($data));
        }
    }
}
