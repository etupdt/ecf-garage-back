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
 