#!/usr/bin/php
<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

set_include_path(
	implode(PATH_SEPARATOR, array(
		realpath(__DIR__ . '/../lib'),
		get_include_path(),
	))
);
require 'autoloader.php';

$host = 'localhost';
$zkPort  = 2181; //zookeeper
$kPort   = 9092; //kafka server
$topic   = 'test';
$maxSize = 1000000;
$socketTimeout = 5;

$offset    = 0;
$partition = 0;

$consumer = new Kafka_SimpleConsumer($host, $kPort, $socketTimeout, $maxSize);
while (true) {
	//create a fetch request for topic "test", partition 0, current offset and fetch size of 1MB
	$fetchRequest = new Kafka_FetchRequest($topic, $partition, $offset, $maxSize);
	//get the message set from the consumer and print them out
	$partialOffset = 0;
	$messages = $consumer->fetch($fetchRequest);
	foreach ($messages as $msg) {
		echo "\nconsumed[$offset][$partialOffset]: " . $msg->payload();
		$partialOffset = $messages->validBytes();
	}
	//advance the offset after consuming each message
	$offset += $messages->validBytes();
	//echo "\n---[Advancing offset to $offset]------(".date('H:i:s').")";
	unset($fetchRequest);
	sleep(2);
}
