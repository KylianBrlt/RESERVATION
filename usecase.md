### Acteurs :
1. **Utilisateur** : Personne qui interagit avec le système pour créer un compte, se connecter, gérer ses informations personnelles, prendre des rendez-vous, etc.
2. **Système** : Plateforme logicielle qui gère les interactions avec l'utilisateur et la base de données.
3. **Base de Données** : Stockage des informations utilisateur et des rendez-vous.

### Cas d'Utilisation :

#### 1. Création de Compte
- **Acteurs** : Utilisateur, Système, Base de Données
- **Scénario Principal** :
  1. L'utilisateur accède au formulaire d'inscription.
  2. L'utilisateur remplit le formulaire avec ses informations personnelles (nom, prénom, date de naissance, adresse postale, numéro de téléphone, email, mot de passe).
  3. Le système vérifie l'unicité de l'email dans la base de données.
  4. Si l'email est unique, le système envoie un email de vérification à l'utilisateur.
  5. L'utilisateur clique sur le lien de vérification dans l'email.
  6. Le système active le compte et enregistre les informations dans la base de données.

#### 2. Connexion
- **Acteurs** : Utilisateur, Système
- **Scénario Principal** :
  1. L'utilisateur accède au formulaire de connexion.
  2. L'utilisateur entre son email et son mot de passe.
  3. Le système vérifie les informations.
  4. Si les informations sont correctes, le système redirige l'utilisateur vers son profil.

#### 3. Modification des Informations Personnelles
- **Acteurs** : Utilisateur, Système, Base de Données
- **Scénario Principal** :
  1. L'utilisateur accède à son profil.
  2. L'utilisateur modifie ses informations personnelles (nom, prénom, date de naissance, adresse postale, numéro de téléphone, email).
  3. Le système vérifie l'unicité du nouvel email dans la base de données.
  4. Si l'email est unique, le système met à jour les informations dans la base de données.

#### 4. Prise de Rendez-vous
- **Acteurs** : Utilisateur, Système, Base de Données
- **Scénario Principal** :
  1. L'utilisateur accède au calendrier interactif.
  2. L'utilisateur sélectionne une date et une heure pour le rendez-vous.
  3. Le système vérifie la disponibilité du créneau horaire dans la base de données.
  4. Si le créneau est disponible, le système enregistre le rendez-vous dans la base de données.

#### 5. Annulation de Rendez-vous
- **Acteurs** : Utilisateur, Système, Base de Données
- **Scénario Principal** :
  1. L'utilisateur accède à la liste de ses rendez-vous.
  2. L'utilisateur sélectionne un rendez-vous à annuler.
  3. Le système annule le rendez-vous dans la base de données.
  4. Le système confirme l'annulation à l'utilisateur.

#### 6. Suppression de Compte
- **Acteurs** : Utilisateur, Système, Base de Données
- **Scénario Principal** :
  1. L'utilisateur accède à son profil.
  2. L'utilisateur confirme la suppression de son compte.
  3. Le système supprime toutes les données associées à l'utilisateur dans la base de données.
  4. Le système confirme la suppression à l'utilisateur.