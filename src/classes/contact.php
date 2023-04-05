<?php

class contactClientify {
  public $id;
  public $firstName;
  public $lastName;
  public $emails;
  public $phones;
  public $tags;
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
      print_r($temp);
      $response = (count($temp->results) > 0 ? $temp->results[0] : false);
      print_r ($response);
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
      //"title" => "Mrs.",
      //"company" => "Henderson Gomez LLC",
      //"contact_type" => "",
      //"contact_source" => "",
      //"addresses" => [json_decode('{"street":"camino de la coquina, 23", "city":"Lugo", "state":"Galicia", "country":"Spain", "postal_code":"34", "type":1}')],
      //"custom_fields" => [],
      //"description" => "Sunt vitae consequun",
      //"remarks" => "Consequatur aliquid",
      //"summary" => "Voluptas dolorem com",
      //"message" => "Nobis aliquip quia c",
      //"re_property_name" => "Hakeem Hicks",
      //"last_contact" => null,
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

  public static function existsContact($id) {
    return true;
  }

}
