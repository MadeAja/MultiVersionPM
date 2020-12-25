<?php

namespace Bavfalcon9\MultiVersion\utils;

class ProtocolVersion {
	public const 1_16_200 = 421; // current PM version
	public const 1_16_100 = 418;
	public const 1_16_20 = 408;
	public const 1_16_0 = 407;
	public const 1_14_60 = 390;
	public const 1_13_0 = 388;
	public const 1_12_0 = 361;

	public const SUPPORTED = [
		self::1_16_100,
		self::1_16_0,
		self::1_13_0,
		self::1_12_0
	];
}