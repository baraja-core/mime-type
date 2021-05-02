<?php

declare(strict_types=1);

namespace Baraja\MimeType;


final class ApacheGenerator
{
	/**
	 * @return array<string, string>
	 */
	public function generate(): array
	{
		$apacheTypes = (string) file_get_contents('httpd-2.2.17/docs/conf/mime.types');

		$extensionToMimeType = [];
		if (preg_match_all('/^([^#]\S+)\s+([a-z0-9 ]+)$/im', $apacheTypes, $matches)) {
			foreach ($matches[2] ?? [] as $index => $extension) {
				if (!str_contains($extension, ' ')) {
					$extensionToMimeType[$extension] = $matches[1][$index];
				} else {
					foreach (explode(' ', $extension) as $extensionItem) {
						$extensionToMimeType[$extensionItem] = $matches[1][$index];
					}
				}
			}
		}

		ksort($extensionToMimeType);

		$return = [];
		foreach ($extensionToMimeType as $extension => $mimeType) {
			$return[(string) $extension] = (string) $mimeType;
		}

		return $return;
	}


	public function saveToFile(): void
	{
		try {
			$json = json_encode($this->generate(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
		} catch (\Throwable $e) {
			throw new \RuntimeException('Json can not be generated: ' . $e->getMessage(), 500, $e);
		}

		file_put_contents(MimeType::getDefinitionsPath(), $json);
	}
}
