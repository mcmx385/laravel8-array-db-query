# Laravel-8-Array-Database-Query
## Introduction
You can query database with an array of instructions
## Installation
add to App/Library/Larocket/
change the namespace as you will
## Usage Example
follow the examples below, you dont have to input all the parameters
## Read Example
	$result = ArrayQueryBuilder::read([
		'table' => 'users',
		'select' => [
			'users' => [
				'id',
				'created_at' => [
					'date' => 'Y-m-d h:i',
					'as' => 'created',
				],
				[
					'count' => '',
					'as' => 'count',
				],
				[
					'concat' => ['users.id', 'users.username'],
					'as' => 'concat',
				],
			],
			'user_profile' => ['gender']
		],
		'filter' => ['field'],
		'where' => [],
		'anywhere' => '',
		'order' => [
			'field' => 'asc',
			'field' => 'desc',
		],
		'limit' => 10,
		'offset' => 0,
		'group' => 'field',
		'having' => [
			'field' => ''
		],
		'join' => [
			'left' => [
				'user_profile' => ['users.id' => 'user_profile.user_id']
			]
		],
		'if' => [
			'table' => 'table',
			'where' => [],
		],
	]);
## Create Example
	$result = ArrayQueryBuilder::create([
		'table' => '',
		'set' => [],
		'dup' => false,
		'if' => [],
	]);

## Update Example
	$result = ArrayQueryBuilder::update([
        'table' => '',
        'set' => [],
        'where' => [],
        'inc' => [],
        'dec' => [],
        'if' => [],
	]);
## Exist Example
	$result = ArrayQueryBuilder::exist([
        'table' => '',
        'set' => [],
        'where' => [],
        'refer' => [],
        'alter' => [],
	]);
## Delete Example
	$result = ArrayQueryBuilder::delete([
        'table' => '',
        'where' => [],
        'trunc' => true,
        'if' => [],
	]);
## Progress
18-3
support create, read, update, delete
## Future plans
add more attributes, but only the necessary ones
