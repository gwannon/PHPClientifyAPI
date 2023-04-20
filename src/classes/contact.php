<?php

class contactClientify {
  public $id;
  public $firstName;
  public $lastName;
  private $emails;
  public $phones;
  private $tags;
  public $status;

  /*
    Identifier	Type
    ----------------
    1	Work
    2	Personal
    3	Other
    4	Main
  */

  public function __construct($id = 0, $createIfNotExists = false) {

    if (is_numeric($id) && $id > 0) {
      $response = curlClientfyCall("/contacts/{$id}/");
    } else if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
      $temp = curlClientfyCall("/contacts/?query={$id}&email={$id}");
      $response = (count($temp->results) > 0 ? $temp->results[0] : false);
    }

    if(isset($response) && is_object($response)) {
      $this->id = $response->id;
      $this->firstName = $response->first_name;
      $this->lastName = $response->last_name;
      $this->emails = $response->emails;
      $this->phones = $response->phones;
      $this->tags = $response->tags;
      $this->status = $response->status;
    } else {
      $this->id = 0;
    }
  }

  public function create($firstName, $lastName, $email, $phone = "", $status = "", $tags = []) {
    $payload = [
      "first_name" => $firstName,
      "last_name" => $lastName,
      "phone" => $phone,
      "email" => $email,
      "status" => "lead-frio",
      "tags" => $tags,
    ];
    $response = curlClientfyCallPost("/contacts/", json_encode($payload));
    if(isset($response) && is_object($response)) {
      $this->id = $response->id;
      $this->firstName = $response->first_name;
      $this->lastName = $response->last_name;
      $this->emails = $response->emails;
      $this->phones = $response->phones;
      $this->tags = $response->tags;
      $this->status = $response->status;
    }
  }

  public function delete() {
    if (is_numeric($id) && $id > 0) $response = curlClientfyCallDelete("/contacts/{$this->id}/");
    $this->id = 0;
    $this->firstName = "";
    $this->lastName = "";
    $this->emails = "";
    $this->phones = "";
    $this->tags = "";
    $this->status = "";
  }

  public function updateData() {
    $payload = [
      "first_name" => $this->firstName,
      "last_name" => $this->lastName,
      "status" => $this->status,
      //"phone" => $this->phone,
      //"email" => $this->email,
    ];
    $response = curlClientfyCallPut("/contacts/{$this->id}/", json_encode($payload));
  }

  /* EMAILS */
  public function addEmail($email, $type) { 
    $email = strtolower($email);
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && 
      !array_search($email, array_column(json_decode(json_encode($this->emails),TRUE), 'email'))) {
      $this->emails[] = (object) [
        "id" => 0,
        "type" => $type,
        "email" => $email
      ];
      return true;
    }
    return false;
  }

  public function deleteEmail($email) { 
    $key = array_search(strtolower($email), array_column(json_decode(json_encode($this->emails),TRUE), 'email'));
    if(isset($this->emails[$key])) {
      unset($this->emails[$key]);
      return true;
    }
    return false;
  }

  public function getEmails() { return $this->emails; }

  public function updateEmails() {    
    $deleteEmails = [];
    foreach($this->emails as $email) {
      if($email->id == 0) $response = curlClientfyCallPost("/contacts/{$this->id}/emails/", json_encode(["email" => $email->email, "type" => $email->type]));
    }

    $response = curlClientfyCall("/contacts/{$this->id}/emails/");
    $currentEmails = $response->results;
    foreach($currentEmails as $currentEmail) {
      $controlDelete = 0;
      foreach($this->emails as $email) {
        if($email->email == $currentEmail->email) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = curlClientfyCallDelete("/contacts/{$this->id}/emails/{$currentEmail->id}/");
    }
  }

  /* ADDRESSES */

  /* PHONES */

  /* TAGS */
  public function getTags() { return $this->tags; }

  public function addTag($tag) { $this->tags[] = $tag; return true; }

  public function deleteTag($tag) { $this->tags = array_diff($this->tags, [$tag]); }
      
  public function updateTags() {    
    $deleteTags = [];
    $newTags = [];
    $response = curlClientfyCall("/contacts/{$this->id}/tags/");
    $currentTags = $response->results;
    foreach($this->tags as $tag) {
      $controlNew = 0;
      foreach($currentTags as $currentTag) {
        if($tag == $currentTag->name) {
          $controlNew = 1;
        }
      }
      if($controlNew == 0) $response = curlClientfyCallPost("/contacts/{$this->id}/tags/", json_encode(["name" => $tag]));
    }

    foreach($currentTags as $currentTag) {
      $controlDelete = 0;
      foreach($this->tags as $tag) {
        if($tag == $currentTag->name) {
          $controlDelete = 1;
        }
      }
      if($controlDelete == 0) $response = curlClientfyCallDelete("/contacts/{$this->id}/tags/{$currentTag->id}/");
    }
  }

  /* OTHERS */
  public static function existsContact($id) {
    if (is_numeric($id) && $id > 0) {
      $response = curlClientfyCall("/contacts/{$id}/");
    } else if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
      $temp = curlClientfyCall("/contacts/?query={$id}&email={$id}");
      $response = (count($temp->results) > 0 ? $temp->results[0] : false);
    }
    if(isset($response) && 
      is_object($response) && 
      isset($response->id) && 
      is_numeric($response->id) && 
      $response->id > 0) return true;
    return false;
  }
}
