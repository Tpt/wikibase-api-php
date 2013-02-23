<?php
/**
 * Basic example for the use of the libary with some small edits
 */
include '../include.php';

//Instance the Api object with the base domain of the wiki and the user agent.
$api = new WikibaseApi( 'wikidata-test-repo.wikimedia.de', 'WikibasePhpLibExample/0.1' );

//Instance the entity provider that allows to get entites from the wiki
$entityProvider = new EntityProvider( $api );

//login with user:demo and test as password
$api->login( 'demo', 'test' );

//Get an entity
$entity = $entityProvider->getEntityFromId( EntityId::newFromPrefixedId( 'q82' ) );

//Create a new statement
$statement = $entity->createStatementForSnak( new Snak( 'value', 'p3', EntityId::newFromPrefixedId( 'Q22' ) ) );

//Save it
$statement->save( 'Test of wikibase-php-lib' );

//Change the main value of the statement
$statement->setMainSnak( new Snak( 'novalue', 'p3' ) );

//Save the change
$statement->save();

//See the list of claims
print_r($entity->getClaims());

//Remove the created statement
$statement->deleteAndSave( 'End of test' );

//Log out
$api->logout();
