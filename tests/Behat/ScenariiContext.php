<?php declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Behat\Gherkin\Node\PyStringNode;

class ScenariiContext extends TestCase implements Context
{

  private $apiUrl = "https://localhost:8000";
  private $email;
  private $password;
  private $status;
  private array $roles;
  private $token;

  public function __construct(private HttpClientInterface $httpClient)
  {
    $this->email = "";
    $this->password = "";
    $this->status = 0;
    $this->roles = [];
    $this->token = "";
  }    

  /**
   * @Given mes coordonnees sont:
   */
  public function mesCoordonneesSont(PyStringNode $coordonnees)
  {

    $row = json_decode((string)$coordonnees , true, 512, JSON_THROW_ON_ERROR);

    $this->email = $row['email'];
    $this->password = $row['password'];

  }

  /**
   * @When je me connecte
   */
  public function jeMeConnecte()
  {

    $response = $this->httpClient->request(
      'POST',
      $this->apiUrl.'/api/login',
      [
        'verify_peer' => false,
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => '*/*',
          'Host' => 'localhost' 
        ],
        'body' => json_encode([
          'username' => $this->email,
          'password'=> $this->password
        ], JSON_THROW_ON_ERROR)
      ]
    );

    $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

    $this->token = $content['token'];
    $this->roles = $content['data']['roles'];
    
    $this->status = $response->getStatusCode();

  }
  
  /**
   * @Then le code retour est :status
   */
  public function leCodeRetourEst(int $status)
  {
    $this->assertSame($this->status, $status);
  }
    
  /**
   * @Then les roles contiennent :role
   */
  public function lesRolesContiennent(string $role)
  {
    $this->assertSame(in_array($role, $this->roles), true);
  }
    
}