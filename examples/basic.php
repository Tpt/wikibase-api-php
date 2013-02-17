<?php
/**
 * Basic example for the use of the libary with some small edits
 */
include '../include.php';

//Instance the Api object with the base domain of the wiki and the user agent.
$api = new WikibaseApi( 'wikidata-test-repo.wikimedia.de', 'WikibasePhpLibExample/1.0' );

//login with user:demo and test as password
$api->login( 'demo', 'test' );

//Get some entities
$entities = $api->getEntitiesFromIds( array( 'q82' ) );

//Get the entity q82 (we supposed here that all works fine)
$entity = $entities['q82'];

//Output the description in French
echo $entity->getDescription( 'fr' );

//Change the label in French
$entity->setLabel( 'fr', 'Or' );

//Add an alias in English
$entity->addAlias( 'en', 'Test' );

//Remove an alias
$entity->removeAlias( 'en', 'Test2' );

//Delete the sitelink in French
$entity->setSitelink( 'frwiki', '' );

//Save changes
$entity->save( 'Test of wikibase-php-lib' );

//See the updated aliases in English
print_r($entity->getAlias( 'en' ));

//Log out
$api->logout();
