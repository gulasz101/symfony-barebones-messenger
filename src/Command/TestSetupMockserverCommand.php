<?php

namespace App\Command;

use App\Support\Illuminate\HttpAware;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestSetupMockserverCommand extends Command
{
    use HttpAware;

    protected static $defaultName = 'test:setup:mockserver';
    protected static $defaultDescription = '';

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this
            ->http
            ->withBody($this->getOpenApiSpec(), 'application/json')
            ->put($_ENV['TRACKING_API_URI'] . '/mockserver/openapi')
            ->throw();

        $io->info($response->body());

        return Command::SUCCESS;
    }

    private function getOpenApiSpec(): string
    {
        return <<<JSON
{
	"specUrlOrPayload": {
		"openapi": "3.0.0",
		"info": {
			"version": "0.0.1",
			"title": "tracking service mock",
			"license": {
				"name": "MIT"
			}
		},
		"servers": [
			{
				"url": "http://tracking-service"
			}
		],
		"paths": {
			"/trackings/{trackingId}": {
				"get": {
					"summary": "",
					"oprationId": "tracking.get",
					"parameters": [
						{
							"name": "trackingId",
							"in": "path",
							"required": true,
							"description": "tracking number",
							"schema": {
								"type": "string",
								"format": "uuid"
							}
						}
					],
					"responses": {
						"200": {
							"description": "Tracking details",
							"content": {
								"application/json": {
									"schema": {
										"\$ref": "#/components/schemas/Tracking"
									}
								}
							}
						}
					}
				}
			}
		},
		"components": {
			"schemas": {
				"Tracking": {
					"type": "object",
					"required": [
						"id",
						"status",
						"delivery_at",
						"registered_at"
					],
					"properties": {
						"id": {
							"type": "string",
							"format": "uuid"
						},
						"status": {
							"type": "string",
							"enum": ["in depot", "cancelled", "delivered"]
						},
						"delivery_at": {
							"type": "string",
							"format": "date"
						},
						"registered_at": {
							"type": "string",
							"format": "date"
						}
					}
				}
			}
		}
}
}
JSON;
    }
}
