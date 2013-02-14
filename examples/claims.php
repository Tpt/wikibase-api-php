<?php
/**
 * Basic example for the use of the libary with some small edits
 */
include '../include.php';

//Instance the Api object with the base domain of the wiki and the user agent.
$api = new WikibaseApi( 'wikidata-test-repo.wikimedia.de', 'Test of a new lib by User:Tpt' );

//login with user:demo and test as password
$api->login( 'demo', 'test' );

//Get some entities
$entities = $api->getEntitiesFromIds( array( 'q82' ) );

//Get the entity q82 (we supposed here that all works fine)
$entity = $entities['q82'];

//Create a new statement
$statement = $entity->createStatementForSnak( new Snak( 'value', 'p3', '{"entity-type": "item", "numeric-id": 16 }' ) );

//Save it
$statement->save();

//Create a new statement
$statement->setMainSnak( new Snak( 'novalue', 'p3' ) );

//Save the change
$statement->save();

//See the list of claims
print_r($entity->getClaims());

//Log out
$api->logout();
