<?php

namespace local_custom_notification\task;

class send_notification extends \core\task\adhoc_task
{
    public function execute()
    {
        $data = $this->get_custom_data();
        $studentid = $data->studentid;
        $activityurl = $data->activityurl;
        $activityname = $data->activityname;
        $activitycreated = $data->activitycreatedat;
        $student = \core_user::get_user($studentid);

        $created_at = $this->getUserDate($activitycreated, $student);
        $fields = $this->getActivityInfo($student, $data->contextinstanceid);
        // Get student details

        // Prepare the message (similar to your original code)
        $subject = get_string_manager()
            ->get_string('activity_created_subject',
                'local_custom_notification', $activityname, $student->lang);
        $fullmessage = get_string_manager()
            ->get_string('activity_created_message',
                'local_custom_notification',
                (object) [
                    'activityname' => $activityname,
                    'activityurl'  => $activityurl
                ],
                $student->lang);
        $fullmessagehtml = get_string_manager()->get_string(
            'activity_created_message_html',
            'local_custom_notification',
            (object) [
                'activityname' => $activityname,
                'activityurl'  => $activityurl,
                'created_at'   => $created_at,
                'fields'       => implode('<br>', $fields)
            ]
            , $student->lang);

        // Create and send the message
        $message = new \core\message\message();
        $message->component = 'local_custom_notification';
        $message->name = 'activity_created';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $student->id;
        $message->subject = $subject;
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = 'New activity created';
        $message->notification = 1;
        $message->contexturl = $activityurl;
        $message->contexturlname = 'View Activity';

        message_send($message);
    }

    private function getActivityInfo($user, $contextinstanceid): array
    {
        global $DB;
        // Assuming $event contains event information, including the course module ID (cmid)
        $cm = get_coursemodule_from_id(null, $contextinstanceid);
        $modname = $cm->modname; // This will be 'assign', 'quiz', 'scorm', etc.

        $activity = null;
        switch ($modname) {
            case 'assign':
                // Fetch assignment details
                $activity = $DB->get_record('assign', ['id' => $cm->instance]);
                break;
            case 'quiz':
                // Fetch quiz details
                $activity = $DB->get_record('quiz', ['id' => $cm->instance]);
                break;

            case 'scorm':
                // Fetch SCORM details
                $activity = $DB->get_record('scorm', ['id' => $cm->instance]);
                break;

            // You can add more case blocks for other activity types (e.g., forum, workshop)
            default:
                // Handle other module types or unknown ones
                break;
        }

        if (!$activity) {
            return [];
        }
        return $this->getLabels($modname, $activity, $user);

    }

    private function getLabels($modname, $activity, $user)
    {
        $fieldlabels = [];
        switch ($modname) {
            case 'assign':
                if ($activity->allowsubmissionsfromdate){
                    $fieldlabels['allowsubmissionsfromdate']
                        = get_string_manager()->get_string('allowsubmissionsfromdate', 'assign', null, $user->lang)
                        .': '.$this->getUserDate($activity->allowsubmissionsfromdate, $user);
                }

                if ($activity->duedate) {
                    $fieldlabels['duedate']
                        = get_string_manager()->get_string('duedate', 'assign', null, $user->lang)
                        .': '.$this->getUserDate($activity->duedate, $user);
                }
                if ($activity->cutoffdate){
                    $fieldlabels['cutoffdate'] = get_string_manager()->get_string('cutoffdate', 'assign', null, $user->lang)
                        .': '.$this->getUserDate($activity->cutoffdate, $user);
                }

                break;

            case 'quiz':
                if ($activity->timeopen) {
                    $fieldlabels['timeopen']
                        = get_string_manager()->get_string('timeopen', 'quiz', null, $user->lang)
                        .': '.$this->getUserDate($activity->timeopen, $user);
                }
                if ($activity->timeclose) {
                    $fieldlabels['timeclose']
                        = get_string_manager()->get_string('timeclose', 'quiz', null, $user->lang)
                        .': '.$this->getUserDate($activity->timeclose, $user);
                }
                if ($activity->timelimit) {
                    $fieldlabels['timelimit']
                        = get_string_manager()->get_string('timelimit', 'quiz', null, $user->lang)
                        .': '.format_time($activity->timelimit);
                }
                break;

            case 'scorm':
                if ($activity->timeopen) {
                    $fieldlabels['timeopen']
                        = get_string_manager()->get_string('timeopen', 'scorm', null, $user->lang)
                        .': '.$this->getUserDate($activity->timeopen, $user);
                }
                if ($activity->timeclose) {
                    $fieldlabels['timeclose']
                        = get_string_manager()->get_string('timeclose', 'scorm', null, $user->lang)
                        .': '.$this->getUserDate($activity->timeclose, $user);
                }
                break;

            // Add other activity types here...
        }

        return $fieldlabels;
    }

    private function getUserDate($date, $user)
    {
        $calendartype = \core_calendar\type_factory::get_calendar_instance($user->calendartype);
        $format = '%d/%m/%Y, %H:%M';
        if ($user->lang === 'fa'){
            $format = '%Y/%m/%d, %H:%M';
        }
        return $calendartype->timestamp_to_date_string($date, $format, $user->timezone, true, true);

    }
}
