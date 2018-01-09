# Installation avec Docker  <a name="docker"></a>

Nous fournissons dans le dépot, les fichiers de configuration nécessaire au déploiement docker d'une instance ORDaR.

### Première étape : installer docker sur votre machine :
	https://docs.docker.com/engine/installation/

### Deuxieme etape : cloner le depot sur votre système avec git

		git clone https://github.com/OTELO-OSU/ORDaR.git
		
### Troisème étape : Adapter les fichiers configuration :

		
Vous devez configurer le fichier Configure.env qui contient toutes les variable de configuration des différents services docker :

Voici une configuration de test, à vous de l'adapter.

		############ SPECIFIC ORDARUI ##############
		
		# Defini le nom du repository ainsi que le nom utilisé pour la generation des DOIs (après le prefix vous étant attribué)		
		REPOSITORY_NAME=DOCKER-ORDAR
		#Indiquer ici l'url sur lequel le projet sera hebergé
		REPOSITORY_URL=https://test-ordar.univ-lorraine.fr
	
		UPLOAD_FOLDER=/data/applis/ORDaR/Uploads/
	défini l'emplacement des Uploads des utilisateurs (A ne pas modifier)
	
		DATAFILE_UNIXUSER="toto"
	Il s'agit du user à qui appartient les fichiers uploader (niveau système de fichier).
	
		NO_REPLY_MAIL="Noreply@ordar.fr"
	Mail de No-reply
	
		SOCIAL_SHARING=true
	Activation/désactivation du partage via les réseaux sociaux
	
		##SPECIFIQUE AU SCRIPT DE MOISSONNAGE (ne pas implémenter)
		SSH_HOST=IPofservice
		SSH_UNIXUSER=user
		SSH_UNIXPASSWD=pass

		SMTP="smtp-int.univ-lorraine.fr"
	L'adresse de votre relais de messagerie
	
		DATASET_FILES_MAX_SIZE=1G
	Valeur maximale pour 1 jeux de données (pouvant être constitué de plusieurs fichiers)
	
		#DOI CONFIG
		DOI_PREFIX=XX.XXXX
	Votre Préfix DOI
		DOI_database=DOI
		user_doi=test4
		password_doi=test4

	
		#DATACITE CREDENTIALS
		AUTH_CONFIG_DATACITE="YOUR SECRETS CREDENTIALS HERE"
	La clef permettant à l'application de se connecter à DataCite pour l'enregistrement / Mise à jour des DOI

		#MONGO CONFIG
		HOST_MONGO=mongo
		PORT_MONGO=27017
		BDDNAME=ORDaR
		SUPER_USER_NAME=test
		SUPER_USER_PASSWORD=test
		USER_READWRITE=test2
		PASSWORD_READWRITE=test2
		USER_BACKUP=test3
		PASSWORD_BACKUP=test3



		#ELASTICSEARCH
		ESHOST=elasticsearch
		ESPORT=9200


		#SPECIFIC OAI PMH
		REPOSITORY_URL_OAI=test
		PROTOCOL_VERSION=3.0
		ADMINMAIL=test@test.fr
		GRANULARITY=YYYY-MM-DD
		TOKENKEY="test"
		SpecialSet="openaire"

	Note: Pour plus de détails sur le fonctionnement de cette partie : https://github.com/OTELO-OSU/ORDaR_OAI-PMH 

		#MYSQL AUTH DB
		DRIVER=mysql
		HOSTMYSQL=mysql_db
		MYSQL_ROOT_PASSWORD=root
		MYSQL_DATABASE=authentication
		MYSQL_USER=test2
		MYSQL_PASSWORD=test
		CHARSETMYSQL=utf8
		COLLATIONMYSQL=utf8_unicode_ci	
	Note 1 : pour plus d'information sur cette partie se reporter à la section "configuration de l'authentification" dans Installation (hors docker)
	Note 2: Les utilisateurs mongo et mysql sont créés automatiquement.

	
		#ORCID
		ORCID_client_id="Your key"
		ORCID_client_secret="Your secret"
	Note : Pour utiliser ORCID, vous devez aussi modifier la valuer clientid (ligne 1546 et 1627) dans le fichier Frontend/src/js/search.js.
	

### Quatrième étape (informations): passage en revue du fichier docker-compose.yml (servant à générer les images)



Le service  harvester-geo-stations (spécifique à OTELo) permet de mettre en place l'upload automatic des jeux de données d'un projet,
pour cela configurer le fichier Docker/harvester-geo-stations/config.ini avec les valeurs prédemment rentré.
Scripts privés disponible sur demande.


ATTENTION: Un projet = un service d'upload automatique!

ATTENTION: Dans les services OrdarUI et  harvester-geo-stations, il faut configurer les volumes afin de monter les fichiers Uploader et les jeux de donné présent sur OTELO-CLOUD.
Pour cela rendez-vous dans le fichier docker-compose.yml :

Exemple pour le service harvester-geo-stations

	 volumes:
	     - /data/applis/ORDaR/Uploads/:/data/applis/ORDaR/Uploads/  (Chemin machine hôte : Chemin du docker interne NE PAS MOFIFIER LE CHEMIN INTERNE)
     - /data/applis/ORDaR/excel/:/data/applis/ORDaR/excel/ (Chemin machine hôte : Chemin du docker interne NE PAS MOFIFIER LE CHEMIN INTERNE)

Exemple pour le service OrdarUI
	 
	 volumes:
	     - /data/applis/ORDaR/Uploads/:/data/applis/ORDaR/Uploads/  (Chemin machine hôte : Chemin du docker interne NE PAS MOFIFIER LE CHEMIN INTERNE)

Configuration du relais mail :
Modifier le fichier Docker/Apache_PHP/ssmtp.conf:

	mailhub= ADRESSE DE VOTRE SMTP

Paramétrage de la taille de fichier maximale au niveau php :
Modifier le fichier Dockerfile a la racine du projet:
Remplacer TAILLESOUHAITE par une taille

	RUN echo 'upload_max_filesize = TAILLESOUHAITE' >> /usr/local/etc/php/php.ini
	RUN echo 'post_max_size = TAILLESOUHAITE' >> /usr/local/etc/php/php.ini


Le script de moissonage harvester-geo-stations étant stocké sur un repository privé de bitbucket :
Modifier le fichier Docker/harvester-geo-stations/Dockerfile:

Ajouter votre access token bitbucket afin de pouvoir cloner le projet ordar_script

	pour créer votre access token (valable 1 heure), se rendre sur le compte bitbucket :settings : OAuth
	copier votre "key" et votre "secret"
	
	-> générer votre token : 
	curl https://bitbucket.org/site/oauth2/access_token -d grant_type=client_credentials -u key:secret

Un fois cela effectué, lancer docker-compose:

	docker-compose up

Patientez  pendant les installations et les initialisations.

L'installation est terminée!

Pour stopper les containers:

	docker-compose stop


Pour les lancer :

	docker-compose start


Il y a 3 volumes présent sur ce projet afin de garantir la persistance des données:

	- mongodb, pour la base de données
	- elasticsearch, pour les données indexées par ES
	- mysql , pour les comptes utilisateurs

Une fois déployé, vous pouvez vous loguer avec un compte ADMIN générique afin de créer le votre, et par la suite supprimer celui ci.

	Adresse mail : admin@admin.fr
	Mot de passe: admin@ORDAR1

Les fichiers de données sont stockés sur le systeme hôte et ensuite monté dans les différents container qui les utilisent.

Le container OAI-PMH est activé par défaut, ce qui rends l'entrepôt de données moissonable, libre à vous de ne pas le demarrer si vous ne souhaitez pas mettre à disposition le protocole OAI-PMH.

ATTENTION: Mongo-connector indexe 2 minutes après le lancement des conteneurs, ceci est du au demmarrage des différents services (mongo, elasticsearch)