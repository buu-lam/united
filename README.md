united
======

R&amp;D sur un générateur de tests unitaires. Deux étapes :

1) Création d'un système d'annotations inspiré 
des @asserts de PHPUnit Skeleton et des possibilités du langage Praspel.

2) Création d'un parseur de code qui permet de générer des tests unitaires
se basant sur les annotations et/ou sur le typage des paramètres, puis
sur l'exécution du minimum de combinaisons afin de faire une couverture de code.

Pour l'instant la gestion des annotations se fait par Phalcon.
