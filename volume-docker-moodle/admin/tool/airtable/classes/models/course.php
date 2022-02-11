<?php
namespace tool_airtable\models; 
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/group/lib.php');
class course 
{
    public function __construct(){

    }

    public function list_course(){
        global $DB, $CFG;
        $url = "https://api.airtable.com/v0/appllUHPDMy0Hvp6F/CURSO"; 
        $authorization = "Authorization: Bearer key78gwAL47RyFyUg";
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            $authorization,
            'Content-Type: application/json',
            'Cookie: brw=brwoda9AKM2y1ajbR'
            ),
        ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($response);
    return $json; 
    }

    public function enrolled_course(){
        global $DB, $CFG;
        $courses = $this->list_course(); 
        foreach($courses as $course){
            foreach($course as $c){
                //Validacion del curso
                $course = $DB->get_record("course", array('shortname' => $c->fields->Name)); 
                $plugin_instance = $DB->get_record("enrol", array('courseid'=>$course->id, 'enrol'=>'manual'));
                $plugin = enrol_get_plugin('manual');
                //Matriculacion en el curso
                $userairtable = $c->fields->username;
                foreach($userairtable as $userair){
                    $user = $DB->get_record("user", array('username' => $userair));
                    $plugin->enrol_user($plugin_instance, $user->id, $plugin_instance->roleid);
                } 
            }
        }
    }
}