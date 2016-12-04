DROP TABLE  IF EXISTS paniers,commandes, aquariums, users, typeAquarium, etats;

-- --------------------------------------------------------
-- Structure de la table typeaquarium
--
CREATE TABLE IF NOT EXISTS typeAquarium (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typeaquarium
INSERT INTO typeAquarium (id, libelle) VALUES
(1, 'Arrondi'),
(2, 'Rectangulaire'),
(3, 'Triangulaire');

-- --------------------------------------------------------
-- Structure de la table etats

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A preparer'),
(2, 'Expedie');

-- --------------------------------------------------------
-- Structure de la table produits

CREATE TABLE IF NOT EXISTS aquariums (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeAquarium_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_produits_typeAquarium FOREIGN KEY (typeAquarium_id) REFERENCES typeAquarium (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO aquariums (id,typeAquarium_id,nom,prix,photo,dispo,stock) VALUES
(1,1, 'Bocale','100','imageProduit.jpeg',1,5),
(2,1, 'SuperAquarium','5.5','imageProduit.jpeg',1,4),
(3,2, 'Le grand bleu','8.5','imageProduit.jpeg',1,10),
(4,2, 'Aquarium tropical','8','imageProduit.jpeg',1,5),
(5,2, 'Iceberg','55','imageProduit.jpeg',1,4),
(6,3, 'Aquarium simple','5','imageProduit.jpeg',1,10);


-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  login varchar(255) NOT NULL,
  nom varchar(255) NOT NULL,
  code_postal varchar(255) NOT NULL,
  ville varchar(255) NOT NULL,
  adresse varchar(255) NOT NULL,
  valide tinyint NOT NULL,
  droit varchar(255) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

-- Contenu de la table users
INSERT INTO users (id,login,password,email,valide,droit) VALUES
(1, 'admin', 'admin', 'admin@gmail.com',1,'DROITadmin'),
(2, 'vendeur', 'vendeur', 'vendeur@gmail.com',1,'DROITadmin'),
(3, 'client', 'client', 'client@gmail.com',1,'DROITclient'),
(4, 'client2', 'client2', 'client2@gmail.com',1,'DROITclient'),
(5, 'client3', 'client3', 'client3@gmail.com',1,'DROITclient');



-- --------------------------------------------------------
-- Structure de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  date_achat  timestamp default CURRENT_TIMESTAMP,
  etat_id int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
) DEFAULT CHARSET=utf8 ;



-- --------------------------------------------------------
-- Structure de la table paniers
CREATE TABLE IF NOT EXISTS paniers (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantite int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  dateAjoutPanier timestamp default CURRENT_TIMESTAMP,
  user_id int(11) NOT NULL,
  aquarium_id int(11) NOT NULL,
  commande_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_paniers_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_paniers_aquariums FOREIGN KEY (aquarium_id) REFERENCES aquariums (id),
  CONSTRAINT fk_paniers_commandes FOREIGN KEY (commande_id) REFERENCES commandes (id)
) DEFAULT CHARSET=utf8 ;

