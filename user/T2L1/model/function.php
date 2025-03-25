<?php
    include 'connexion.php';

    function getArticle($id = null) {
        if (!empty($id)) {
            $sql = "SELECT a.*, c.nom_categorie 
                    FROM article AS a
                    LEFT JOIN categorie AS c ON a.id_categorie = c.id
                    WHERE a.id = ?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
            return $req->fetch();
        } else {
            $sql = "SELECT a.*, c.nom_categorie 
                    FROM article AS a
                    LEFT JOIN categorie AS c ON a.id_categorie = c.id";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
    }

    function getClient($id = null) {
        if (! empty ($id)) {
            $sql = "SELECT * FROM client WHERE id=?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
        return $req->fetch();
        }
        else {
            $sql = "SELECT * FROM client";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
    }

    function getCategorie($id = null) {
        if (! empty ($id)) {
            $sql = "SELECT * FROM categorie WHERE id=?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
        return $req->fetch();
        }
        else {
            $sql = "SELECT * FROM categorie";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
    }

    function getVente($id = null) {
        if (!empty($id)) {
            $sql = "SELECT v.id AS id_vente, v.quantite, v.prix, v.date_vente, 
                    a.id AS id_article, a.nom_article, 
                    c.nom AS nom_client, c.prenom AS prenom_client, c.adresse AS adresse_client, c.telephone AS telephone_client,
                    cat.description_categorie, cat.nom_categorie
                FROM vente AS v
                LEFT JOIN article AS a ON v.id_article = a.id
                LEFT JOIN client AS c ON v.id_client = c.id
                LEFT JOIN categorie AS cat ON a.id_categorie = cat.id
                WHERE v.id = ?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
            return $req->fetch();
        } else {
            $sql = "SELECT v.id AS id_vente, v.quantite, v.prix, v.date_vente, 
                    a.id AS id_article, a.nom_article, 
                    c.nom AS nom_client, c.prenom AS prenom_client, c.adresse AS adresse_client, c.telephone AS telephone_client,
                    cat.description_categorie, cat.nom_categorie
                FROM vente AS v
                LEFT JOIN article AS a ON v.id_article = a.id
                LEFT JOIN client AS c ON v.id_client = c.id
                LEFT JOIN categorie AS cat ON a.id_categorie = cat.id";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
    }
    
    
    
    
    function getFournisseur($id = null) {
        if (! empty ($id)) {
            $sql = "SELECT * FROM fournisseur WHERE id=?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
        return $req->fetch();
        }
        else {
            $sql = "SELECT * FROM fournisseur";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } 
    }

    function getCommande($id = null) {
        if (! empty ($id)) {
            $sql = "SELECT nom_article, nom, prenom, co.quantite, prix, date_commande, co.id, prix_unitaire, adresse, telephone
            FROM fournisseur AS f, commande AS co, article AS a WHERE co.id_article=a.id AND co.id_fournisseur=f.id AND co.id=?";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute(array($id));
        return $req->fetch();
        }
        else {
            $sql = "SELECT nom_article, nom, prenom, co.quantite, prix, date_commande, co.id, a.id AS idArticle
            FROM fournisseur AS f, commande AS co, article AS a WHERE co.id_article=a.id AND co.id_fournisseur=f.id";
            $req = $GLOBALS['connexion']->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
    }

    function getAllCommande() {
        $sql = "SELECT count(*) AS nbre FROM commande";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    function getAllVente() {
        $sql = "SELECT count(*) AS nbre FROM vente";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    function getAllArticle() {
        $sql = "SELECT count(*) AS nbre FROM article";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    function getCA() {
        $sql = "SELECT SUM(prix) AS montant FROM vente";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetch();
    }

    function getLastVente() {
        $sql = "SELECT nom_article, nom, prenom, v.quantite, prix, date_vente, v.id, a.id AS idArticle
        FROM client AS c, vente AS v, article AS a WHERE v.id_article=a.id AND v.id_client=c.id 
        ORDER BY date_vente DESC LIMIT 10";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

    function getMostVente() {
        $sql = "SELECT nom_article, SUM(prix) AS prix
        FROM client AS c, vente AS v, article AS a WHERE v.id_article=a.id AND v.id_client=c.id
        GROUP BY a.id 
        ORDER BY SUM(prix) DESC LIMIT 10";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

    function getAllVenteDisplayed() {
        $sql = "SELECT nom_article, nom, prenom, v.quantite, prix, date_vente, v.id, a.id AS idArticle
        FROM client AS c, vente AS v, article AS a WHERE v.id_article=a.id AND v.id_client=c.id 
        ORDER BY date_vente DESC";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

?>