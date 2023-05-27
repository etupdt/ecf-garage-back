#language: fr
Fonctionnalité: Test de connexion admin

  Scénario: Je me connecte
    Etant donné que mes coordonnees sont:
		"""
		{
			"email" : "vincent.parrot@garage.com",
			"password" : "achanger"
		}
		"""
    Lorsque je me connecte
    Alors le code retour est 200
    Et les roles contiennent "ROLE_ADMIN"
 
    Etant donné que le garage est:
      """
      {
        "raison" : "Garage Vincent Parrot",
        "phone" : "1234567890",
        "address1" : "12 rue de la Paix",
        "address2" : "",
        "zip" : "34000",
        "locality" : "Montpellier",
        "day1hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day2hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day3hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day4hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day5hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day6hours" : "09:00 - 12:00, 14:00 - 18;:00",
        "day7hours" : "Fermé"
      }
      """
  	Lorsque je cree le garage
    Alors le code retour est 200
    Et le message de retour est "garage created!"
	
