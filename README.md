Projet de réservation d' Événements : 

Ce projet est une application web permettant d'afficher et de s'inscrire à des événements. Il a été développé avec Symfony et Twig pour la partie frontend, et utilise MySQL comme base de données.

🚀 Fonctionnalités

Affichage des événements : Consultez la liste des événements disponibles.
Inscription aux événements : Enregistrez-vous pour participer aux événements qui vous intéressent.
Gestion des utilisateurs : Possibilité de créer un compte et de gérer ses réservations.  (si implémenté).

🛠️ Technologies utilisées

Symfony : Framework PHP utilisé pour la structure du projet.
Twig : Moteur de templates pour générer les pages HTML.
MySQL : Base de données pour stocker les informations sur les utilisateurs et les événements.

📦 Installation

Prérequis : 
- PHP 8.0+
- Composer
- MySQL
- Symfony 6
  
Étapes d'installation

1) Cloner le dépôt :
   
git clone https://github.com/chirelhalioua/projet-symfony.git

cd nom-du-projet
Installer les dépendances

2) Installer Composer :
   
composer install

3) Configurer la base de données : 
Créez une base de données MySQL et configurez le fichier .env :

DATABASE_URL="mysql://username:password@127.0.0.1:3306/nom_de_la_base"

4) Migrations :
   
Ensuite, exécutez les migrations pour créer les tables :
php bin/console doctrine:migrations:migrate

5) Lancer le serveur de développement :
   
symfony server:start
Le projet sera disponible à l'adresse : http://localhost:8000.

🎨 Aperçu : 

![projet-symfony](https://github.com/user-attachments/assets/b8f281b9-af7b-4fc2-82aa-21a4c8258cc7)

🤝 Contribuer : 

Les contributions sont les bienvenues ! N'hésitez pas à faire une pull request pour proposer de nouvelles fonctionnalités ou corriger des bugs.

Forker le projet : 

- Créez une branche pour votre fonctionnalité (git checkout -b feature/NouvelleFonctionnalite).
- Commitez vos modifications (git commit -am 'Ajoute une nouvelle fonctionnalité').
- Pushez votre branche (git push origin feature/NouvelleFonctionnalite).
- Ouvrez une Pull Request.

📜 Licence : 

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.
