<?php
namespace tool_airtable\models; 
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/moodlelib.php');

class user
{
    public function __construct(){

    }

    public function list_user(){
        global $DB, $CFG;
        $url = "https://api.airtable.com/v0/appllUHPDMy0Hvp6F/CONTROL%20GENERAL"; 
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

    public function create_user(){
        global $DB, $CFG;
        $data_user = $this->list_user();
        foreach($data_user as $data){
            foreach($data as $d){
                // echo "Estado ".$d->fields->suspended."<br>"; 
                $correo = "Correo Electronico";
                $departamento  = "Departamento text"; 
                $puesto = "Puesto text"; 
                $empresa = "Empresa text"; 
                $fecha_nacimiento = "Fecha de Nacimiento";
                if($d->fields->suspended == '0'){
                    // airtable variables
                    // echo "Entro"; 
                    $user = $DB->get_record('user', array('username' => $d->fields->$correo)); 
                    if(!$user){
                        $user = create_user_record($d->fields->$correo, 'Inicio#14', $auth = 'manual');
                        if(!$user){
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al crear el usuario'
                            ));
                        }
                    }
                    $arrayNombre= explode(" ", $d->fields->Nombre, 4);
                    $nombre1 =  json_encode($arrayNombre[0]); 
                    $nombre2 =  json_encode($arrayNombre[1]);
                    $apellido1 = json_encode($arrayNombre[2]);
                    $apellido2 = json_encode($arrayNombre[3]);

                    if($nombre1 === "null"){
                        $nam1 = " "; 
                    }else{
                        $name1 = $nombre1; 
                    }

                    if($nombre2 === "null"){
                        $name2 = " "; 
                    }else{
                        $name2 = $nombre2; 
                    }

                    if($apellido1 === "null"){
                        $lastname1 = " "; 
                    }else{
                        $lastname1 = $apellido1; 
                    }

                    if($apellido2 === "null"){
                        $lastname2 = " ";
                    }else{
                        $lastname2 = $apellido2;
                    }
                    
                    $nombres = $name1." ".$name2;
                    $apellidos = $lastname1." ".$lastname2; 

                    $user->firstname = str_replace('"', '', $nombres); 
                    $user->lastname  = str_replace('"', '', $apellidos);
                    $user->email = $d->fields->$correo;
                    $user->phone1 = $d->fields->Telefono; 
                    $user->department = $d->fields->$departamento;
                    $user->suspended = $d->fields->suspended;
                    $user->timemodified = time();
                    update_user_record($user->username);
                    
                    //dpi record creation and updating
                    if($d->fields->DPI){
                        $campo=$DB->get_record('user_info_data', array('fieldid' =>"5",'userid' =>$user->id));
                        if($campo){
                            $campo->data = $d->fields->DPI; 
                            $actualizar=$DB->update_record('user_info_data', $campo);
                            if( !$actualizar)
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al actualizar la posición'
                            ));
                        }else{ 
                            $campo = new \stdClass();
                            $campo->userid=$user->id;
                            $campo->fieldid='5';
                            $campo->data=$d->fields->DPI;
                            $campo->dataformat='0';
                            $id = $DB->insert_record('user_info_data', $campo);
                            if( !$id )
                            return json_encode(array(
                                'error' => 0,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al insertar la nueva posición'
                            ));
                        }
                    }

                    // puesto record creation and updating
                    if($d->fields->$puesto){
                        $campo=$DB->get_record('user_info_data', array('fieldid' =>"4",'userid' =>$user->id));
                        if($campo){
                            $campo->data = $d->fields->$puesto; 
                            $actualizar=$DB->update_record('user_info_data', $campo);
                            if( !$actualizar)
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al actualizar la posición'
                            ));
                        }else{ 
                            $campo = new \stdClass();
                            $campo->userid=$user->id;
                            $campo->fieldid='4';
                            $campo->data=$d->fields->$puesto;
                            $campo->dataformat='0';
                            $id = $DB->insert_record('user_info_data', $campo);
                            if( !$id )
                            return json_encode(array(
                                'error' => 0,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al insertar la nueva posición'
                            ));
                        }
                    }

                    // empresa record creation and updating
                    if($d->fields->$empresa){
                        $campo=$DB->get_record('user_info_data', array('fieldid' =>"3",'userid' =>$user->id));
                        if($campo){
                            $campo->data = $d->fields->$empresa; 
                            $actualizar=$DB->update_record('user_info_data', $campo);
                            if( !$actualizar)
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al actualizar la posición'
                            ));
                        }else{ 
                            $campo = new \stdClass();
                            $campo->userid=$user->id;
                            $campo->fieldid='3';
                            $campo->data=$d->fields->$empresa;
                            $campo->dataformat='0';
                            $id = $DB->insert_record('user_info_data', $campo);
                            if( !$id )
                            return json_encode(array(
                                'error' => 0,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al insertar la nueva posición'
                            ));
                        }
                    }
                
                    // edad record creation and updating
                    if($d->fields->Edad){
                        $campo=$DB->get_record('user_info_data', array('fieldid' =>"2",'userid' =>$user->id));
                        if($campo){
                            $campo->data = $d->fields->Edad; 
                            $actualizar=$DB->update_record('user_info_data', $campo);
                            if( !$actualizar)
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al actualizar la posición'
                            ));
                        }else{ 
                            $campo = new \stdClass();
                            $campo->userid=$user->id;
                            $campo->fieldid='2';
                            $campo->data=$d->fields->Edad;
                            $campo->dataformat='0';
                            $id = $DB->insert_record('user_info_data', $campo);
                            if( !$id )
                            return json_encode(array(
                                'error' => 0,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al insertar la nueva posición'
                            ));
                        }
                    }

                    //fecha record creation and updating
                    if($d->fields->$fecha_nacimiento){
                        $campo=$DB->get_record('user_info_data', array('fieldid' =>"1",'userid' =>$user->id));
                          if($campo){
                            $campo->data=strtotime($d->fields->$fecha_nacimiento);
                            $actualizar=$DB->update_record('user_info_data', $campo);
                            if( !$actualizar)
                            return json_encode(array(
                                'error' => 1,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al actualizar la fecha'
                            ));
                          }else{
                            $campo = new \stdClass();
                            $campo->userid=$user->id;
                            $campo->fieldid='1';
                            $campo->data=strtotime($d->fields->$fecha_nacimiento);
                            $campo->dataformat='0';
                            $id = $DB->insert_record('user_info_data', $campo);
                            if( !$id )
                            return json_encode(array(
                                'error' => 0,
                                'username' => $d->fields->$correo,
                                'mensaje' => 'Error al insertar la fecha'
                            ));
                        }
                    }

                    $actualizar = $DB->update_record('user', $user);
                    if(!$actualizar){
                        return json_encode(array(
                            'error' => 1, 
                            'username' =>$d->fields->$correo,
                            'mensaje' => 'Error al crear los datos'
                        )); 
                    }
                }else{
                    // echo "Salio";
                    $user = $DB->get_record('user', array('username' => $d->fields->$correo)); 
                    if(!$user){
                       echo "El usuario no existe"; 
                    }else{
                        $user->suspended = $d->fields->suspended;
                        $user->timemodified = time();
                        update_user_record($user->username);
                        $actualizar = $DB->update_record('user', $user);
                    }
                }
            }
        }
    }
}

