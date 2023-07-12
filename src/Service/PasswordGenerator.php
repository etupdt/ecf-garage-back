<?php

namespace App\Service;

class PasswordGenerator
{
  public function generateRandomPassword(int $length): string
  {

    $symbols = "*-+?!";
    $numbers = "0123456789";
    $lowers = "abcdefghijklmnopqrstuvwxyz";
    $uppers = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $password = $this->caracter($lowers);
    $password = $password.$this->caracter($numbers);
    $password = $password.$this->caracter($uppers);
    $password = $password.$this->caracter($symbols);

    for ($i = 0; $i < $length - 4; $i++) {
      $password = $password.$this->caracter($lowers.$uppers.$numbers.$symbols);
    }

    return $password;

  }

  private function caracter(string $caracters) {
    return substr($caracters, (random_int(0, strlen($caracters))), 1);
  }

}