<?php
namespace tool_airtable\task;

use tool_airtable\models\course;
use tool_airtable\models\user;
use tool_airtable\models\group;

class airtable extends \core\task\scheduled_task
{
    /**
     * return name of task for admin panel.
     *
     * @return string name
     */
    public function get_name()
    {
        return get_string('cronenroll', 'tool_airtable');
    }

    /**
     * method to execute by cron task.
     */
    public function execute()
    {
      // mtrace("Hola mundo");
      global $CFG;
      $user = new user(); 
      $course = new course();
      $group = new group(); 
      $user->create_user();     
      $course->enrolled_course();
      $group->create_group();    
      $group->assignment_group();
    }
}
