<?php

namespace Gwannon\PHPClientifyAPI;

class contactClientify {
  public $id;
  private $firstName;
  private $lastName;
  private $position;
  //private $company;
  private $websites;
  private $emails;
  private $phones;
  private $addresses;
  private $tags;
  private $status;
  private $custom_fields;
  private $picture;
  private $linkedin_url;
  private $updateData;
  private $updateCustomFields;
  private $updateEmails;
  private $updatePhones;
  private $updateAddresses;
  private $updateTags;
  private $updateWebsites;

  public function __construct($id = 0, $createIfNotExists = false) {

    if (is_numeric($id) && $id > 0) {
      $response = contactClientify::curlClientfyCall("/contacts/{$id}/");
    } else if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
      $temp = contactClientify::curlClientfyCall("/contacts/?query={$id}&email={$id}");
      $response = (count($temp->results) > 0 ? $temp->results[0] : false);
    }

    //print_r($response);


    if(isset($response) && is_object($response)) {
      $this->id = $response->id;
      $this->firstName = $response->first_name;
      $this->lastName = $response->last_name;
      $this->emails = $response->emails;
      $this->phones = $response->phones;
      $this->position = $response->title;
      //$this->company = $response->company;
      $this->websites = $response->websites;
      $this->linkedin_url = $response->linkedin_url;
      if(isset($response->addresses)) {
        $this->addresses = $response->addresses;
      } else {
        $addresses = contactClientify::curlClientfyCall("/contacts/{$this->id}/addresses/");
        $this->addresses = $addresses->results;
      }
      $this->picture = $response->picture_url;
      if(isset($response->custom_fields) && count($response->custom_fields) > 0) {
        foreach ($response->custom_fields as $custom_field) {
          $this->custom_fields[] = [
            "field" => $custom_field->field,
            "value" => ($custom_field->value != '--' ? $custom_field->value : "")
          ];
        }
      } else {
        $this->custom_fields = [];
      }
      $this->tags = $response->tags;
      $this->status = $response->status;
      $this->updateData = false;
      $this->updateCustomFields = false;
      $this->updateEmails = false;
      $this->updatePhones = false;
      $this->updateAddresses = false;
      $this->updateTags = false;
      $this->updateWebsites = false;
    } else {
      $this->id = 0;
      if($createIfNotExists && filter_var($id, FILTER_VALIDATE_EMAIL)) {
        $this->create("", "", $id);
      }
    }
  }

  public function create($firstName, $lastName, $email, $phone = "", $status = "cold-lead", $tags = []) {
    $payload = [
      "first_name" => $firstName,
      "last_name" => $lastName,
      "phone" => $phone,
      "email" => $email,
      "status" => $status,
      "tags" => $tags,
    ];
    $response = contactClientify::curlClientfyCallPost("/contacts/", json_encode($payload));


    if(isset($response) && is_object($response)) {
      $this->id = $response->id;
      $this->firstName = $response->first_name;
      $this->lastName = $response->last_name;
      $this->emails = $response->emails;
      $this->phones = $response->phones;
      $this->position = "";
      //$this->company = "";
      $this->websites = [];
      $this->linkedin_url = "";
      $this->addresses = [];
      $this->custom_fields = [];
      $this->picture = "";
      $this->tags = $response->tags;
      $this->status = $response->status;
      $this->updateData = false;
      $this->updateCustomFields = false;
      $this->updateEmails = false;
      $this->updatePhones = false;
      $this->updateAddresses = false;
      $this->updateTags = false;
      $this->updateWebsites = false;
    }
  }

  public function delete() {
    if (is_numeric($id) && $id > 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/");
    $this->id = 0;
    $this->firstName = "";
    $this->lastName = "";
    $this->emails = "";
    $this->phones = "";
    $this->position = "";
    //$this->company = "";
    $this->websites = [];
    $this->linkedin_url = "";
    $this->addresses = "";
    $this->custom_fields = "";
    $this->picture = "";
    $this->tags = "";
    $this->status = "";
  }

  public function update() {
    if($this->updateData) $this->updateData();
    if($this->updateCustomFields) $this->updateCustomFields();
    if($this->updateEmails) $this->updateEmails();
    if($this->updateAddresses) $this->updateAddresses();
    if($this->updatePhones) $this->updatePhones();
    if($this->updateWebsites) $this->updateWebsites();
    if($this->updateTags) $this->updateTags();
  } 

  /* Data */
  public function getFirstName() { return $this->firstName; }

  public function getLastName() { return $this->lastName; }

  public function getPosition() { return $this->position; }

  //public function getCompany() { return $this->company; }

  public function getStatus() { return $this->status; }

  public function getPicture() { return $this->picture; }

  public function getCustomField($field) { 
    $key = array_search($field, array_column($this->custom_fields, 'field'));
    if($key >= 0 && $key !== '' && isset($this->custom_fields[$key]['field']) &&$this->custom_fields[$key]['field'] == $field) return $this->custom_fields[$key];
    return false;
  }

  public function getLinkedinUrl() { return $this->linkedin_url; }

  public function setFirstName($firstName) { $this->firstName = $firstName; $this->updateData = true; }

  public function setLastName($lastName) { $this->lastName = $lastName; $this->updateData = true; }

  //public function setCompany($company) { $this->company = $company; $this->updateData = true; }

  public function setPosition($position) { $this->position = $position; $this->updateData = true; }

  public function setStatus($status) { $this->status = $status; $this->updateData = true; }

  public function setPicture($picture) { $this->picture = $picture; $this->updateData = true; }

  public function setCustomField($field, $value) { 
    $control = 0;
    foreach($this->custom_fields as $key => $custom_field) {
      if($custom_field['field'] == $field) {
        $this->custom_fields[$key]['field'] = $field;
        $this->custom_fields[$key]['value'] = $value;
        $control = 1;
        $this->updateCustomFields = true; 
        break;
      }
    }
    if($control == 0) {
      $this->custom_fields[] = [
        'field' => $field,
        'value' => $value
      ];
      $this->updateCustomFields = true; 
    }
  }

  public function setLinkedinUrl($linkedin_url) { $this->linkedin_url = $linkedin_url; $this->updateData = true; }


  public function updateData() {
    $payload = [
      "first_name" => $this->firstName,
      "last_name" => $this->lastName,
      //"company" => $this->company,
      "title" => $this->position,
      "status" => $this->status,
      "linkedin_url" => $this->linkedin_url,
    ];

    if(isset($this->picture) && $this->picture != '') $payload['picture_url'] = $this->picture;
    //echo "PAYLOAD---------------<pre>"; print_r($payload); echo "</pre>";
    $response = contactClientify::curlClientfyCallPut("/contacts/{$this->id}/", json_encode($payload));
    //echo "RESPONSE-------------<pre>"; print_r($response);  echo "</pre>";
  }

  public function updateCustomFields() {
    $payload['custom_fields'] = [];
    foreach($this->custom_fields as $key => $custom_field) {
      $payload['custom_fields'][$key] = [
        'field' => $custom_field['field'],
        'value' => ($custom_field['value'] != '' ? $custom_field['value'] : "--")
      ];
    }
    $response = contactClientify::curlClientfyCallPut("/contacts/{$this->id}/", json_encode($payload));
  }

  /* EMAILS */
  public function getEmails() { return $this->emails; }
  public function getEmailsByType($type) {
    $emails = [];
    foreach($this->emails as $email) {
      if($email->type == $type) $emails [] = $email;
    }
    return $emails; 
  } //1=trabajo
  
  public function hasEmail($email) { 
    $email = strtolower($email);
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && 
      in_array($email, array_column(json_decode(json_encode($this->emails),TRUE), 'email'))) {
      return true;
    }
    return false;
  }

  public function addEmail($email, $type) { 
    $email = strtolower($email);
    if (!$this->hasEmail($email)) {
      $this->emails[] = (object) [
        "id" => 0,
        "type" => $type,
        "email" => $email
      ];
      $this->updateEmails = true;
      return true;
    }
    return false;
  }

  public function deleteEmail($email) { 
    $email = strtolower($email);
    if ($this->hasEmail($email)) {
      $key = array_search($email, array_column(json_decode(json_encode($this->emails),TRUE), 'email'));
      if(isset($this->emails[$key])) {
        unset($this->emails[$key]);
        $this->updateEmails = true;
        return true;
      }
    }
    return false;
  }

  public function updateEmails() {    
    $deleteEmails = [];
    foreach($this->emails as $email) {
      if($email->id == 0) $response = contactClientify::curlClientfyCallPost("/contacts/{$this->id}/emails/", json_encode(["email" => $email->email, "type" => $email->type]));
    }
    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/emails/");
    $currentEmails = $response->results;
    foreach($currentEmails as $currentEmail) {
      $controlDelete = 0;
      foreach($this->emails as $email) {
        if($email->email == $currentEmail->email) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/emails/{$currentEmail->id}/");
    }
    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/emails/");
    $this->emails = $response->results;
  }

  /* WEBSITES */
  public function getWebsites() { return $this->websites; }
  public function getWebsitesByType($type) {
    $websites = [];
    foreach($this->websites as $website) {
      if($website->type == $type) $websites [] = $website;
    }
    return $websites; 
  } //1=trabajo
  
  public function hasWebsite($website) { 
    $website = strtolower($website);
    if (filter_var($website, FILTER_VALIDATE_EMAIL) && 
      in_array($website, array_column(json_decode(json_encode($this->websites),TRUE), 'website'))) {
      return true;
    }
    return false;
  }

  public function addWebsite($website, $type) { 
    $website = strtolower($website);
    if (!$this->hasWebsite($website)) {
      $this->websites[] = (object) [
        "id" => 0,
        "type" => $type,
        "website" => $website
      ];
      $this->updateWebsites = true;
      return true;
    }
    return false;
  }

  public function deleteWebsite($website) { 
    $website = strtolower($website);
    if ($this->hasWebsite($website)) {
      $key = array_search($website, array_column(json_decode(json_encode($this->websites),TRUE), 'website'));
      if(isset($this->websites[$key])) {
        unset($this->websites[$key]);
        $this->updateWebsites = true;
        return true;
      }
    }
    return false;
  }

  public function updateWebsites() {    
    $deleteWebsites = [];
    foreach($this->websites as $website) {
      if($website->id == 0) $response = contactClientify::curlClientfyCallPost("/contacts/{$this->id}/websites/", json_encode(["website" => $website->website, "type" => $website->type]));
    }

    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/websites/");
    $currentWebsites = $response->results;
    foreach($currentWebsites as $currentWebsite) {
      $controlDelete = 0;
      foreach($this->websites as $website) {
        if($website->website == $currentWebsite->website) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/websites/{$currentWebsite->id}/");
    }
  }

  /* ADDRESSES */
  public function getAddresses() { return $this->addresses; }

  public function addAddress($street, $city, $state, $country, $postal_code, $type) { 
    $this->addresses[] = (object) [
      "id" => 0,
      "type" => $type,
      "street" => $street,
      "city" => $city,
      "state" => $state,
      "country" => $country,
      "postal_code" => $postal_code
    ];
    $this->updateAddresses = true;
    return true;
  }

  public function deleteAddress($id_address) { 
    $key = array_search($id_address, array_column(json_decode(json_encode($this->addresses),TRUE), 'id'));
    if(isset($this->addresses[$key])) {
      unset($this->addresses[$key]);
      $this->updateAddresses = true;
      return true;
    }
    return false;
  }

  public function updateAddresses() {    
    $deleteAddresses = [];
    foreach($this->addresses as $key => $address) {
      if($address->id == 0) {
        $payload = [
          "type" => $address->type,
          "street" => $address->street,
          "city" => $address->city,
          "state" => $address->state,
          "country" => $address->country,
          "postal_code" => $address->postal_code
        ];
        $response = contactClientify::curlClientfyCallPost("/contacts/{$this->id}/addresses/", json_encode($payload));
        $this->addresses[$key]->id = $response->id;
      }
    }

    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/addresses/");
    $currentAddresses = $response->results;
    foreach($currentAddresses as $currentAddress) {
      $controlDelete = 0;
      foreach($this->addresses as $address) {
        if($address->id == $currentAddress->id) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/addresses/{$currentAddress->id}/");
    }
  }

  /* PHONES */
  public function getPhones() { return $this->phones; }
  public function getPhonesByType($type) {
    $phones = [];
    foreach($this->phones as $phone) {
      if($phone->type == $type) $phones [] = $phone;
    }
    return $phones; 
  } //3=trabajo
  
  public function hasPhone($phone) { 
    $phone = strtolower($phone);
    if (in_array($phone, array_column(json_decode(json_encode($this->phones),TRUE), 'phone'))) {
      return true;
    }
    return false;
  }

  public function addPhone($phone, $type) { 
    $phone = strtolower($phone);
    if (!$this->hasPhone($phone)) {
      $this->phones[] = (object) [
        "id" => 0,
        "type" => $type,
        "phone" => $phone
      ];
      $this->updatePhones = true;
      return true;
    }
    return false;
  }

  public function deletePhone($phone) { 
    $phone = strtolower($phone);
    if ($this->hasPhone($phone)) {
      $key = array_search($phone, array_column(json_decode(json_encode($this->phones),TRUE), 'phone'));
      if(isset($this->phones[$key])) {
        unset($this->phones[$key]);
        $this->updatePhones = true;
        return true;
      }
    }
    return false;
  }

  public function updatePhones() {    
    $deletePhones = [];
    foreach($this->phones as $key => $phone) {
      if($phone->id == 0) {
        $response = contactClientify::curlClientfyCallPost("/contacts/{$this->id}/phones/", json_encode(["phone" => $phone->phone, "type" => $phone->type]));
        $this->phones[$key]->id = $response->id;
      }
    }

    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/phones/");
    $currentPhones = $response->results;
    foreach($currentPhones as $currentPhone) {
      $controlDelete = 0;
      foreach($this->phones as $phone) {
        if($phone->phone == $currentPhone->phone) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/phones/{$currentPhone->id}/");
    }
  }

  /* TAGS */
  public function getTags() { return $this->tags; }

  public function hasTag($tag) {
    if(in_array($tag, $this->tags)) return true;
    else return false;
  }

  public function addTag($tag) { 
    $tag = strtolower($tag);
    if (!$this->hasTag($tag)) {
      $this->tags[] = $tag; 
      $this->updateTags = true;
      return true;
    }
    return false;
  }

  public function deleteTag($tag) { $this->tags = array_diff($this->tags, [$tag]); }
      
  public function updateTags() {    
    $deleteTags = [];
    $newTags = [];
    $response = contactClientify::curlClientfyCall("/contacts/{$this->id}/tags/");
    $currentTags = $response->results;
    foreach($this->tags as $tag) {
      $controlNew = 0;
      foreach($currentTags as $currentTag) {
        if($tag == $currentTag->name) {
          $controlNew = 1;
        }
      }
      if($controlNew == 0) $response = contactClientify::curlClientfyCallPost("/contacts/{$this->id}/tags/", json_encode(["name" => $tag]));
    }

    foreach($currentTags as $currentTag) {
      $controlDelete = 0;
      foreach($this->tags as $tag) {
        if($tag == $currentTag->name) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = contactClientify::curlClientfyCallDelete("/contacts/{$this->id}/tags/{$currentTag->id}/");
    }
  }

  /* OTHERS */
  public function addUpdateNote($title, $text) {
    $payload = [
      "name" => $title,
      "comment" => $text
    ];
    return contactClientify::curlClientfyCallPost("/contacts/{$this->id}/note/", json_encode($payload));
  }

  public function executeAutomation($autom_id) {
    $payload = [
      "contact_id" => $this->id
    ];
    return contactClientify::curlClientfyCallPost("/automations/{$autom_id}/add_contacts/", json_encode($payload));
  }

  public static function existsContact($id) {
    if (is_numeric($id) && $id > 0) {
      $response = contactClientify::curlClientfyCall("/contacts/{$id}/");
    } else if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
      $temp = contactClientify::curlClientfyCall("/contacts/?query={$id}&email={$id}");
      $response = (count($temp->results) > 0 ? $temp->results[0] : false);
    }
    if(isset($response) && 
      is_object($response) && 
      isset($response->id) && 
      is_numeric($response->id) && 
      $response->id > 0) return true;
    return false;
  }

  //Funciones CURL-----------------------------
  static private function curlClientfyCall($link, $request = 'GET', $payload = false) {
    $now = date("Y-m-d H:i:s");
    $curl = curl_init();
    $headers[] = 'Authorization: Token '.CLIENTIFY_API_KEY;
    curl_setopt($curl, CURLOPT_URL, CLIENTIFY_API_URL.$link);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    if (in_array($request, array("PUT", "POST", "DELETE"))) curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
    if ($payload) {
      curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
      $headers[] = 'Content-Type: application/json';
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    $json = json_decode($response);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    if (in_array($httpcode, array(200, 201, 204))) {
      if(CLIENTIFY_LOG_API_CALLS) contactClientify::curlClientfyLog("logs", $link, $request, $httpcode, $payload );
      return $json;
    } else {
      if(CLIENTIFY_LOG_API_CALLS) contactClientify::curlClientfyLog("errors", $link, $request, $httpcode, $payload, json_encode($json));
      //throw new Exception($httpcode." - ".json_encode($json));
      return false;
    }
  }

  //GET
  static private function curlClientfyCallGet($link) { return contactClientify::curlClientfyCall($link); }

  //PUT
  static private function curlClientfyCallPut($link, $payload) { return contactClientify::curlClientfyCall($link, "PUT", $payload); }

  //POST
  static private function curlClientfyCallPost($link, $payload) { return contactClientify::curlClientfyCall($link, "POST", $payload); }

  //DELETE
  static private function curlClientfyCallDelete($link) { return contactClientify::curlClientfyCall($link, "DELETE"); }

  //Log system
  static private function curlClientfyLog($file, $link, $request, $httpcode, $payload = "", $json = "") {
    $f = fopen(dirname(__FILE__)."/../logs/".$file.".txt", "a+");
    $line = date("Y-m-d H:i:s")."|".$link."|".$request."|".$httpcode;
    if($payload != '') $line .= "|".$payload;
    if($json != '') $line .= "|".$json;
    $line .= "\n";
    fwrite($f, $line);
    fclose($f);
  }
}
