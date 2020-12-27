<?php

declare(strict_types=1);

namespace Bavfalcon9\MultiVersion\protocol;

class ProtocolVersion {
	public const BETA_1_16_210_53 = 424;
	public const BETA_1_16_210_50 = 423;
	public const BETA_1_16_200_52 = 421;
	public const v1_16_200 = 422; // current PM version
	public const v1_16_100 = 418;
	public const v1_16_20 = 408;
	public const v1_16_0 = 407;
	public const v1_14_60 = 390;
	public const v1_13_0 = 388;
	public const v1_12_0 = 361;

	public const SUPPORTED_CLIENTS = [
		self::v1_16_100,
		self::v1_16_0,
		self::v1_13_0,
		self::v1_12_0
	];

	public const SUPPORTED_SERVERS = [
		self::v1_16_200
	];

	/**
	 * This will be changed by a config
	 */
	public static array $ALLOWED = [];
}