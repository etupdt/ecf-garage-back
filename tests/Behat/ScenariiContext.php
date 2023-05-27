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
  private $response;
  private array $roles;
  private $token;
  private $garage;

  public function __construct(private HttpClientInterface $httpClient)
  {
    $this->email = "";
    $this->password = "";
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
    
    $this->response = $response;

  }
  
  /**
   * @Then le code retour est :status
   */
  public function leCodeRetourEst(int $status)
  {
    $this->assertSame($this->response->getStatusCode(), $status);
  }
    
  /**
   * @Then les roles contiennent :role
   */
  public function lesRolesContiennent(string $role)
  {
    $this->assertSame(in_array($role, $this->roles), true);
  }
    
  /**
   * @Given le garage est:
   */
  public function jeSuisConnecteEnAdmin(PyStringNode $garage)
  {
    $this->garage = (string)$garage;   //json_decode((string)$garage , true, 512, JSON_THROW_ON_ERROR);
  }

   /**
   * @When je cree le garage
   */
  public function jeCreeLeGarage()
  {

    var_dump($this->garage);

    $response = $this->httpClient->request(
      'POST',
      $this->apiUrl.'/api/garage',
      [
        'verify_peer' => false,
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => '*/*',
          'authorization' => 'Bearer '.$this->token
        ],
        'body' => $this->garage
      ]
    );

    $this->response = $response;

  }
  
  /**
   * @Then le message de retour est :message
   */
  public function leMessageDeRetourEst(string $message)
  {
    $content = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    var_dump($content);
    $this->assertSame($content['message'], $message);
  }
    
}