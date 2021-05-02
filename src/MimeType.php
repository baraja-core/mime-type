<?php

declare(strict_types=1);

namespace Baraja\MimeType;


final class MimeType
{
	/**
	 * Returns the mime type of the file according to the file extension,
	 * or returns NULL if it does not know the extension.
	 */
	public static function byExtension(string $extension): ?string
	{
		return self::get()[strtolower(trim($extension, '. '))] ?? null;
	}


	public static function getDefinitionsPath(): string
	{
		return __DIR__ . '/../definitions.json';
	}


	/**
	 * @return array<string, string>
	 */
	private static function get(): array
	{
		static $cache;
		if ($cache === null) {
			$json = (string) file_get_contents(self::getDefinitionsPath());
			try {
				$cache = (array) json_decode($json, true, 512, JSON_THROW_ON_ERROR);
			} catch (\Throwable $e) {
				throw new \RuntimeException('Mime type definitions can not be resolved: ' . $e->getMessage(), 500, $e);
			}
		}

		return $cache;
	}
}
