# README du site web Twitturtle

Ce README explique comment mettre en place le site web et présente les fonctionnalités principales.

## Table des matières
1. [Prérequis pour la mise en place du site web](#prérequis-pour-la-mise-en-place-du-site-web)
2. [Fonctionnalités du site web](#fonctionnalités-du-site-web)
   1. [Inscription](#inscription)
   2. [Envoi de messages](#envoi-de-messages)
   3. [Interaction avec les messages](#interaction-avec-les-messages)
   4. [Interactions avec les profils](#interactions-avec-les-profils)
   5. [Réinitialisation du mot de passe](#réinitialisation-du-mot-de-passe)
   6. [Système de notification](#système-de-notification)
3. [Contacts](#contacts)

## Prérequis pour la mise en place du site web
### Activer l'extension PHP GD dans XAMPP
1. Ouvrir le fichier php.ini du configurateur Apache.
2. Rechercher la ligne ;extension=gd puis supprimer le ;.
3. Relancer Apache & MySQL.
### Versions d'utilisation
1. Le site a été testé en versions PHP 8.2.4 et 8.1.18
2. Les versions MySQL utilisées sont 5.2.1 et 5.7.11
### Initialisation de l'environnement de travail
- Pour lier le site avec la base de données, modifier les informations de root et mot de passe dans la classe Database.

## Fonctionnalités du site web
### Inscription
- Les utilisateurs peuvent indiquer s'ils sont une organisation ou non. Les organisations peuvent ajouter des animaux à la recherche d'un propriétaire. Les utilisateurs qui consultent ce profil peuvent envoyer des demandes d'adoption à l'organisation. Un profil organisation possède un contour doré à la photo de profil et une pastille spéciale sur son nom de compte pour indiquer qu'il s'agit d'une organisation.
- Les utilisateurs doivent ajouter une adresse e-mail valide pour pouvoir réinitialiser leur mot de passe en cas de perte.

### Envoi de messages
- Les utilisateurs peuvent ajouter une localisation, liée par API à Google Maps.
- Les utilisateurs peuvent ajouter une image.
- Les utilisateurs peuvent ajouter un ou plusieurs animal/animaux, qui apparaîtront dans les mentions du message, et le message en question sera affiché sur le profil de chaque animal mentionné.
- Les utilisateurs peuvent sélectionner différents types de message : conseil, événement, sauvetage ou classique. Par défaut, le mode d'envoi est classique.
- Les utilisateurs peuvent ajouter un ou plusieurs hashtag sur un message en précédant ce dernier par le symbole #.
- Les utilisateurs peuvent envoyer un nouveau message soit directement depuis la navigation bar, ou depuis la section « explorer ».

### Interaction avec les messages
- Les utilisateurs peuvent aimer des messages, répondre à un message, ou répondre à une réponse de message, etc.
- Les utilisateurs peuvent directement répondre à un message sur leur page actuelle, ou se rendre sur le message en question, puis explorer les réponses et éventuellement envoyer une réponse à leur tour.
- Les messages « réponse » affichent également le message père qui leur correspond.

### Interactions avec les profils
- Il y a deux types de profil : Profil animal et profil utilisateur.
- Les profils utilisateurs possèdent un nombre de messages, un nombre d'abonnés et d'abonnements, la possibilité d'ajouter un nouvel animal sur leur profil et la possibilité de modifier leur profil.
- Les profils utilisateurs disposent de 3 volets (Messages, Réponses, J'aime).
- Les profils animaux possèdent une image de leur propriétaire, seulement un nombre d'abonnés et une modification de profil si vous en êtes le propriétaire.
- Les profils animaux possèdent seulement les messages auxquels ils sont mentionnés, permettant de retracer tous les messages leur correspondant.

### Réinitialisation du mot de passe
- L'utilisateur a la possibilité de réinitialiser son mot de passe : un code de récupération est envoyé à l'utilisateur par e-mail. Ce dernier doit renseigner le code reçu par e-mail sur le site. Si le code entré par l'utilisateur est correct, il dispose de 5 minutes pour réinitialiser son mot de passe.

### Système de notification
- Lorsqu'un utilisateur aime un message, suit un utilisateur, ou répond à un message d'un utilisateur, ce dernier sera averti avec une notification dans sa section « Notifications ».
- Il y a également une notification spéciale : L'adoption. Cette dernière est exclusive aux organisations. Lorsqu'un utilisateur souhaite adopter un animal, qui est à adopter, d'une organisation, il a la possibilité d'envoyer une demande d'adoption qui informera l'organisation par notification. L'organisation peut ainsi à son tour accepter ou rejeter la demande d'adoption.

## Contacts
Si vous avez d'éventuelles questions, n'hésitez surtout pas à nous contacter par e-mail :
- raphael.perrin@utbm.fr
- eileen.jovenin@utbm.fr

Raphaël PERRIN, Eileen JOVENIN - Projet WE4A, Automne 2022
UTBM