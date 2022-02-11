<?php
namespace tool_airtable\models; 
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/group/lib.php');
class group 
{
    public function __construct(){

    }

    public function list_group(){
        global $DB, $CFG;
        $url = "https://api.airtable.com/v0/appllUHPDMy0Hvp6F/GRUPO"; 
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

    public function create_group(){ 
        global $DB; 
        $groups = $this->list_group(); 
        foreach($groups as $group){
            foreach($group as $g){
                $courses = $g->fields->course;
                foreach($courses as $c){
                    $course = $DB->get_record("course", array('shortname' => $c));
                    $data = new \stdClass();
                    $data->courseid = $course->id;
                    $data->idnumber = uniqid();
                    $data->name = $g->fields->Name;
                    $data->description = 'Group';
                    $existgroup = $DB->get_record("groups", array('name' => $g->fields->Name, 'courseid' => $course->id)); 
                    if($existgroup){
                        echo "El grupo ya existe "; 
                    }else{
                        echo "No existe ";
                        $newgroupid = groups_create_group($data);
                    }
                }
            }
        } 
    }

    public function assignment_group(){
        global $DB; 
        $list_group = $this->list_group(); 

        foreach($list_group as $list){
            foreach($list as $l){
                $user = $l->fields->username; 
                foreach($user as $u){
                    $courses = $l->fields->course; 
                    foreach($courses as $c){
                        $users = $DB->get_record("user", array("username" => $u)); 
                        $course = $DB->get_record("course", array("shortname" => $c));
                        $existUsers = $DB->get_record_sql("SELECT mu.id as userid, mc.id as courseid
                        from mdl_user mu 
                        inner join mdl_user_enrolments mue on mue.userid = mu.id
                        inner join mdl_enrol me  on me.id = mue.enrolid 
                        inner join mdl_course mc on mc.id = me.courseid WHERE mu.id = $users->id and mc.id = $course->id"); 
                        echo json_encode($existUsers); 
                        if($existUsers){
                            $group = $DB->get_record("groups", array('name' => $l->fields->Name, 'courseid' => $existUsers->courseid)); 
                            groups_add_member($group->id, $existUsers->userid);

                        }
                    }
                }
            }
        }
    }
}