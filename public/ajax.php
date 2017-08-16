<?php

$result = [
	'reserved' => [
		'A-3-3',
		'A-4-3'
	],
	'reservation' => [
		'A-3-4',
		'A-3-5'
	],
	'free' => [
		'A-1-1',
		'A-1-2',
		'A-1-3',
		'A-1-4',
		'A-1-5',
		'A-1-6',
		'A-1-7',
		'A-1-8',
		'A-1-9',
		'A-1-10',
	]
];

die(json_encode($result));