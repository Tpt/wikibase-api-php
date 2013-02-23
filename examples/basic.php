<?php
/**
 * Basic example for the use of the libary with some small edits
 */
include '../include.php';

//Instance the Api object with the base domain of the wiki and the user agent.
$api = new WikibaseApi( 'wikidata-test-repo.wikimedia.de', 'WikibasePhpLibExample/1.0' );

//Instance the entity provider that allows to get entites from the wiki
$entityProvider = new EntityProvider( $api );

//login with user:demo and test as password
$api->login( 'demo', 'test' );

//Get an entity
$entity = $entityProvider->getEntityFromId( EntityId::newFromPrefixedId( 'q82' ) );

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
