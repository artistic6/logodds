<?php

return [
	'1' => [
		/**
		Race 1
		*/
		'7, 8, 9, 5, 12, 2, 3, 1, 6, 10, 11, 4',
		'Poor man\'s bet' ,
			'Win' => '5, 12, 3, 1',
		'Qin($10), Tce($1)' ,
			'5, 7, 8, 9, 12',
		'Qin($20)' ,
			'7, 8, 9, 5',
		'Qpl($30)' ,
			'7 X 8, 2, 6',
		'Qpl($20)' ,
			'9 X 8, 2, 6',
		'Qpl($10)' ,
			'7, 8, 9 X 10, 11, 4',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'7, 8, 9 X 12, 3, 1',
	],
	'2' => [
		/**
		Race 2
		*/
		'8, 9, 11, 4, 6, 3, 1, 5, 12, 2, 7, 10',
		'Poor man\'s bet' ,
			'Win' => '4, 6, 2, 10',
		'Qin($10), Tce($1)' ,
			'4, 6, 8, 9, 11',
		'Qin($20)' ,
			'8, 9, 11, 4',
		'Qpl($30)' ,
			'8 X 9, 3, 1',
		'Qpl($20)' ,
			'11 X 9, 3, 1',
		'Qpl($10)' ,
			'8, 9, 11 X 5, 12, 7',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'8, 9, 11 X 6, 2, 10',
	],
	'3' => [
		/**
		Race 3
		*/
		'1, 5, 4, 2, 3, 10, 9, 12, 6, 8, 11, 7',
		'Poor man\'s bet' ,
			'Win' => '3, 9, 12, 7',
		'Qin($10), Tce($1)' ,
			'1, 2, 3, 4, 5, 9',
		'Qin($20)' ,
			'1, 5, 4, 2',
		'Qpl($30)' ,
			'1 X 4, 2, 10',
		'Qpl($20)' ,
			'5 X 4, 2, 10',
		'Qpl($10)' ,
			'1, 5, 4 X 6, 8, 11',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'1, 5, 4 X 9, 12, 7',
	],
	'4' => [
		/**
		Race 4
		*/
		'8, 7, 2, 10, 9, 4, 3, 11, 5, 12, 6, 1',
		'Poor man\'s bet' ,
			'Win' => '10, 4, 11, 6',
		'Qin($10), Tce($1)' ,
			'2, 4, 7, 8, 10',
		'Qin($20)' ,
			'8, 7, 2, 10',
		'Qpl($30)' ,
			'8 X 7, 9, 3',
		'Qpl($20)' ,
			'2 X 7, 9, 3',
		'Qpl($10)' ,
			'8, 7, 2 X 5, 12, 1',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'8, 7, 2 X 4, 11, 6',
	],
	'5' => [
		/**
		Race 5
		*/
		'4, 5, 10, 1, 11, 9, 6, 2, 7, 12, 3, 8',
		'Poor man\'s bet' ,
			'Win' => '11, 6, 2, 8',
		'Qin($10), Tce($1)' ,
			'1, 4, 5, 6, 10, 11',
		'Qin($20)' ,
			'4, 5, 10, 1',
		'Qpl($30)' ,
			'4 X 5, 1, 9',
		'Qpl($20)' ,
			'10 X 5, 1, 9',
		'Qpl($10)' ,
			'4, 5, 10 X 7, 12, 3',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'4, 5, 10 X 6, 2, 8',
	],
	'6' => [
		/**
		Race 6
		*/
		'1, 8, 2, 7, 9, 3, 11, 4, 12, 6, 5, 10',
		'Poor man\'s bet' ,
			'Win' => '9, 3, 12, 5',
		'Qin($10), Tce($1)' ,
			'1, 2, 3, 7, 8, 9',
		'Qin($20)' ,
			'1, 8, 2, 7',
		'Qpl($30)' ,
			'1 X 8, 2, 11',
		'Qpl($20)' ,
			'7 X 8, 2, 11',
		'Qpl($10)' ,
			'1, 8, 2, 7 X 4, 6, 10',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'1, 8, 2, 7 X 3, 12, 5',
	],
	'7' => [
		/**
		Race 7
		*/
		'2, 9, 11, 1, 4, 10, 5, 7, 6, 8, 3',
		'Poor man\'s bet' ,
			'Win' => '4, 10, 6, 8',
		'Qin($10), Tce($1)' ,
			'1, 2, 4, 9, 10, 11',
		'Qin($20)' ,
			'2, 9, 11, 1',
		'Qpl($30)' ,
			'2 X 9, 1, 5',
		'Qpl($20)' ,
			'11 X 9, 1, 5',
		'Qpl($10)' ,
			'2, 9, 11 X 5, 7, 3',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'2, 9, 11 X 10, 6, 8',
	],
	'8' => [
		/**
		Race 8
		*/
		'5, 2, 9, 1, 4, 3, 7, 6, 11, 12, 8, 10',
		'Poor man\'s bet' ,
			'Win' => '1, 3, 7, 12',
		'Qin($10), Tce($1)' ,
			'1, 2, 3, 5, 9',
		'Qin($20)' ,
			'5, 2, 9, 1',
		'Qpl($30)' ,
			'5 X 2, 4, 6',
		'Qpl($20)' ,
			'9 X 2, 4, 6',
		'Qpl($10)' ,
			'5, 2, 9 X 11, 8, 10',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'5, 2, 9 X 3, 7, 12',
	],
	'9' => [
		/**
		Race 9
		*/
		'2, 5, 7, 1, 6, 3, 12, 10, 8, 9, 4, 11',
		'Poor man\'s bet' ,
			'Win' => '10, 8, 4, 11',
		'Qin($10), Tce($1)' ,
			'1, 2, 5, 6, 7, 8, 10',
		'Qin($20)' ,
			'2, 5, 7, 1',
		'Qpl($30)' ,
			'2 X 5, 7, 1',
		'Qpl($20)' ,
			'6 X 5, 7, 1',
		'Qpl($10)' ,
			'2, 5, 7, 6 X 3, 12, 9',
		'-----------ideally-----------------',
		'Qpl, Qin($10)' ,
			'2, 5, 7, 6 X 8, 4, 11',
	],
];
