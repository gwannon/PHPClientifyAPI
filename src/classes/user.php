<?php

/* ----------------- CLASS User -------------------- */
/*class UserAC {
  public $id;
  public $hash;
  public $nombre;
  public $apellidos;
  public $email;
  public $telefono;
  public $fields;
  public $tags;
  public $lists;

  public function __construct($id, $createifnotexists = false) {
    $response = false;
    if (is_numeric($id)) {
      $temp = curlCallGet("/contacts?ids=".$id);
      if(isset($temp->contacts[0])) $response = $temp->contacts[0];
    } else if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
      $temp = curlCallGet("/contacts?email=".$id);
      if(isset($temp->contacts[0])) $response = $temp->contacts[0];
    }

    if($response) {
      $this->id = $response->id;
      $this->hash = $response->hash;
      $this->nombre = $response->firstName;
      $this->apellidos = $response->lastName;
      $this->email = $response->email;
      $this->telefono = $response->phone;
      $this->fields = $this->getApiFields(); //Campos personalizados
      $this->tags = $this->getApiTags(); //Etiquetas
      $this->lists = $this->getApiLists(); //Listas
    } else if ($createifnotexists && filter_var($id, FILTER_VALIDATE_EMAIL)) { //Si no existe y tenemos el email lo creamos
      $data['contact'] = [
        'email' => $id, 
      ];
      $response = curlCallPost("/contacts", json_encode($data))->contact;
      $this->id = $response->id;
      $this->email = $response->email;
    }
  }
	
  //SETs --------------------------------	
  function setNombre($val) { $this->nombre = $val; }
  function setApellidos($val) { $this->apellidos = $val; }
  function setEmail($val) { $this->email = $val; }
  function setTelefono($val) { $this->telefono = $val; }
  function setField($field_id, $value) { 
    $this->fields[$field_id] = $value;
  } 

  function setTag($tag_id) { 
    $data['contactTag'] = [
      "contact" => $this->id,
      "tag"     => $tag_id
    ];
    $response = curlCallPost("/contactTags", json_encode($data)); 
    $this->tags[$tag_id] = $response->contactTag->id;
  } 

  function setList($list_id, $status = 1) {
    $data['contactList'] = [
      "contact" => $this->id,
      "list"     => $list_id,
      "status" => $status  // Status: Set to "1" to subscribe the contact to the list. Set to "2" to unsubscribe the contact from the list.  
    ];
    $response = curlCallPost("/contactLists", json_encode($data)); 
    $this->lists[$list_id] = $response->contactList->status;
  } 

  //EXECUTE
  function executeAutomation ($automation_id) {
    $data['contactAutomation'] = [
      "contact" => $this->id,
      "automation" => $automation_id
    ];
    $response = curlCallPost("/contactAutomations", json_encode($data)); 
    return ($response->contactAutomation->status == 1 ? true : false );
  }

  //HAS --------------------------------
  function hasTag($tag_id) {
    if(isset($this->tags[$tag_id]) && $this->tags[$tag_id] > 0) return true;
    else return false;
  }

  function hasList($list_id) {
    if(isset($this->lists[$list_id]) && $this->lists[$list_id] == 1) return true;
    else return false;
  }

  //DELETE --------------------------------
  function deleteTag($tag_id) { 
    $response = curlCallDelete("/contactTags/".$this->tags[$tag_id]);
    $this->tags[$tag_id] = "";
  } 

	//UPDATEs --------------------------------
	function updateProfileAC() {
    $userFields = getFields('fields');
    if(WPATG_LAST_UPDATE_FIELD_ID > 0) $userFields[] =array ("id" => WPATG_LAST_UPDATE_FIELD_ID);
    foreach ($userFields as $field) {
      $myfields[] = [
        "field" => $field['id'],
        "value" => $this->fields[$field['id']]
      ];
    }
    $data['contact'] = [
      'email'       => $this->email, 
			'firstName'   => $this->nombre,
    	'lastName'    => $this->apellidos,
      'phone'       => $this->telefono,
      'fieldValues' => $myfields
		];
    $response = curlCallPut("/contacts/".$this->id, json_encode($data));
		return $response;
	}

  //APIs calls --------------------------------
  function getApiTags() {
    $tags = array_merge(
      getFields('langs'),
      getFields("interests"),
      array(array("id" => 322, "tag" => "interes-newsletter-todos", "text" =>  __('Todos', 'wp-a-tu-gusto'))),
      getFields("companies"),
      getFields("newsletters"),
      getFields("notifications"));
    $usertags = curlCallGet("/contacts/".$this->id."/contactTags")->contactTags;
    foreach ($tags as $tag) {
      $currenttags[$tag['id']] = false;
      foreach ($usertags as $usertag) {
        if ($tag['id'] == $usertag->tag) {
          $currenttags[$tag['id']] = $usertag->id;
          break;
        }
      }
    }
    return $currenttags;
  }

  function getApiFields() {
    $userFields = getFields('fields');
    $userFields[] = ["id" => WPATG_LAST_UPDATE_FIELD_ID];
    $userfields = curlCallGet("/contacts/".$this->id."/fieldValues")->fieldValues;
    $currentfields = [];
    foreach ($userFields as $field) {
      $currentfields[$field['id']] = false;
      foreach ($userfields as $userfield) {
        if ($field['id'] == $userfield->field) {
          $currentfields[$field['id']] = $userfield->value;
          break;
        }
      }
    }
    return $currentfields;
  }

  function getApiLists() {
    $lists = [WPATG_MAIN_NEWLETTER_ID];
    $userlists = curlCallGet("/contacts/".$this->id."/contactLists")->contactLists;
    $currentlists = [];
    if(count($userlists) > 0){
      foreach ($lists as $list_id) {
        $currentlists[$list_id] = false;
        foreach ($userlists as $userlist) {
        if ($list_id == $userlist->list) {
            $currentlists[$list_id] = $userlist->status;
            break;
          }
        }
      }
    }
    return $currentlists;
  }
}

function createUserAC ($email) {
  $data['contact'] = [
    'email' => $email, 
  ];
  $response = curlCallPost("/contacts", json_encode($data))->contact;
  if($response->id > 0) {
    $user = new UserAC($response->id);
    return $user;
  } 
  return false;
}

function existsUserAC ($email) {
  $temp = curlCallGet("/contacts?email=".$email);
  if(isset($temp->contacts[0])) return true;
	else return false;
}*/

