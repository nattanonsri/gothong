<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\CURLRequest;
use App\Models\GeminiUsageMongoModel;
use App\Models\GeminiUsageSQLModel;

class GeminiAPI
{
    protected string $apiKey;
    protected string $model = 'gemini-2.5-flash';
    protected array $config = [];
    protected CURLRequest $client;
    protected GeminiTokenCounter $tokenCounter;
    protected GeminiUsageMongoModel $usageLogModel;
    public function __construct()
    {
        $this->apiKey = getenv('GEMINI_API_KEY'); // Ensure GEMINI_API_KEY is set in your .env
        $this->client = Services::curlrequest();
        $this->tokenCounter = new GeminiTokenCounter($this->apiKey);
        $this->usageLogModel = new GeminiUsageMongoModel();

        $this->config = [
            'thinkingConfig' => [
                'thinkingBudget' => -1,
            ],
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'object',
                'properties' => [
                    'store_name' => ['type' => 'string'],
                    'date' => ['type' => 'string'],
                    'invoice_number' => ['type' => 'string'],
                    'total_amount' => ['type' => 'number'], // Changed to number for total_amount
                    'items' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'], // Changed to string for item name
                                'quantity' => ['type' => 'integer'], // Changed to integer for quantity
                                'unit_price' => ['type' => 'number'], // Changed to number for unit_price
                                'brand_id' => ['type' => 'integer', 'nullable' => true], // Added brand_id
                                'category_id' => ['type' => 'integer', 'nullable' => true], // Added category_id
                                'brand_other' => ['type' => 'string', 'nullable' => true], // Added brand_other
                                'category_other' => ['type' => 'string', 'nullable' => true], // Added category_other
                                'confidence' => ['type' => 'number', 'nullable' => true], // Added confidence
                                'reason' => ['type' => 'string', 'nullable' => true] // Added reason
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function scanInvoice(string $imageDataBase64, array $brandData = [], array $categoryData = [], $receipt_data = []): ?array
    {
        $startTime = microtime(true);
        $memoryStart = memory_get_usage(true);
        $inputTokenResult = ['success' => false, 'total_tokens' => 0];

        $contents = [
            [
                'role' => 'user',
                'parts' => [
                    [
                        'inlineData' => [
                            'data' => $imageDataBase64,
                            'mimeType' => 'image/jpeg',
                        ],
                    ],
                    [
                        'text' => 'ไฟล์ที่แนบมาให้เป็นรูปภาพใบเสร็จ ให้อ่านข้อมูลใบเสร็จแล้วแปลงข้อมูลเป็น JSON โดยมีสิ่งที่ต้องการจากภาพใบเสร็จดังนี้ 1.ชื่อร้านค้า 2.วัน/เดือน/ปี ที่ซื้อสินค้า 3.เลขที่ใบเสร็จ 4.ราคารวมในใบเสร็จ 5.รายการสินค้าแต่ละชิ้น โดยจะต้องจับคู่รายการสินค้าจากใบเสร็จเข้ากับ 5.1  brand_id, category_id จาก Master Brand Data และ Master Category Data 5.2  เพิ่มค่า Confidence และ Reason ว่าทำไมถึงจับคู่ brand_id และ category_id เข้ากับรายการสินค้านั้น',
                    ],
                ],
            ],
            // Add SQL data as context if provided
            [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => 'ข้อมูล Brand (brand_id, brand_name): ' . json_encode($brandData)
                    ],
                    [
                        'text' => 'ข้อมูล Category (category_id, category_name): ' . json_encode($categoryData)
                    ]
                ]
            ]
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $this->apiKey,
        ];

        $body = [
            'contents' => $contents,
            'generationConfig' => $this->config,
        ];

        try {
            // Count input tokens before making the request
            $inputTokenResult = $this->tokenCounter->countTokensWithImage(
                json_encode($contents),
                $imageDataBase64
            );
            if (!$inputTokenResult['success']) {
                log_message('error', 'Failed to count input tokens: ' . ($inputTokenResult['error'] ?? 'Unknown error'));
            }

            $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
                'headers' => $headers,
                'json' => $body,
                'timeout' => 60, // Set a timeout for the request
            ]);

            $jsonResponse = json_decode($response->getBody(), true);
            $responseText = null;
            $parsedResult = null;

            if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
                $responseText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
                $parsedResult = json_decode($responseText, true);
            }

            // Count output tokens
            $outputTokenResult = $responseText ? $this->tokenCounter->countTokens($responseText) : ['success' => true, 'total_tokens' => 0];

            // Calculate costs
            $costResult = $this->tokenCounter->calculateCost(
                $inputTokenResult['success'] ? $inputTokenResult['total_tokens'] : 0,
                $outputTokenResult['success'] ? $outputTokenResult['total_tokens'] : 0,
                $this->model
            );

            // Prepare tracking data
            $trackingData = [
                'input_tokens' => $inputTokenResult['success'] ? $inputTokenResult['total_tokens'] : 0,
                'output_tokens' => $outputTokenResult['success'] ? $outputTokenResult['total_tokens'] : 0,
                'input_cost_usd' => $costResult['success'] ? $costResult['input_cost_usd'] : 0,
                'output_cost_usd' => $costResult['success'] ? $costResult['output_cost_usd'] : 0,
                'total_cost_usd' => $costResult['success'] ? $costResult['total_cost_usd'] : 0,
                'success' => $parsedResult !== null,
                'error_message' => $parsedResult === null ? 'Failed to parse response' : null,
                'items_extracted' => $parsedResult && isset($parsedResult['items']) ? count($parsedResult['items']) : 0,
                'confidence_score' => null,
                'prompt_length' => strlen(json_encode($contents))
            ];
            $this->logUsage(
                $receipt_data, // Log context
                $trackingData,
                $brandData,
                $categoryData,
                $imageDataBase64,
                $parsedResult,
                $startTime,
                $memoryStart
            );

            return $parsedResult;

        } catch (\Exception $e) {
            log_message('error', 'Gemini API Error: ' . $e->getMessage());

            // Log error
            $this->logUsage(
                $receipt_data,
                [
                    'input_tokens' => $inputTokenResult['success'] ? $inputTokenResult['total_tokens'] : 0,
                    'output_tokens' => 0,
                    'input_cost_usd' => 0,
                    'output_cost_usd' => 0,
                    'total_cost_usd' => 0,
                    'success' => false,
                    'error_message' => $e->getMessage(),
                    'items_extracted' => 0,
                    'confidence_score' => null,
                    'prompt_length' => strlen(json_encode($contents))
                ],
                $brandData,
                $categoryData,
                $imageDataBase64,
                null,
                $startTime,
                $memoryStart
            );

            return null;
        }
    }
    public function scanInvoiceWithCostTracking(string $imageDataBase64, array $brandData = [], array $categoryData = []): ?array
    {
        $startTime = microtime(true);
        $memoryStart = memory_get_usage(true);

        $promptText = 'ไฟล์ที่แนบมาให้เป็นรูปภาพใบเสร็จ ให้อ่านข้อมูลใบเสร็จแล้วแปลงข้อมูลเป็น JSON โดยมีสิ่งที่ต้องการจากภาพใบเสร็จดังนี้ 1.ชื่อร้านค้า 2.วัน/เดือน/ปี ที่ซื้อสินค้า 3.เลขที่ใบเสร็จ 4.ราคารวมในใบเสร็จ 5.รายการสินค้าแต่ละชิ้น โดยจะต้องจับคู่รายการสินค้าจากใบเสร็จเข้ากับ 5.1  brand_id, category_id จาก Master Brand Data และ Master Category Data 5.2  เพิ่มค่า Confidence และ Reason ว่าทำไมถึงจับคู่ brand_id และ category_id เข้ากับรายการสินค้านั้น';

        $brandText = 'ข้อมูล Brand (brand_id, brand_name): ' . json_encode($brandData);
        $categoryText = 'ข้อมูล Category (category_id, category_name): ' . json_encode($categoryData);

        $combinedText = $promptText . "\n" . $brandText . "\n" . $categoryText;

        try {
            // Step 1: Count input tokens (with image)
            $inputTokenResult = $this->tokenCounter->countTokensWithImage($combinedText, $imageDataBase64);

            if (!$inputTokenResult['success']) {
                log_message('error', 'Token counting failed: ' . $inputTokenResult['error']);
                return null;
            }

            $inputTokens = $inputTokenResult['total_tokens'];

            // Step 2: Scan invoice (original method)
            $scanResult = $this->scanInvoice($imageDataBase64, $brandData, $categoryData);

            // Log text generation with proper context
            $this->logTextGeneration(
                ['request_id' => uniqid()], // Log context
                [
                    'input_tokens' => $inputTokens,
                    'output_tokens' => 0, // Will be updated after response
                    'success' => true,
                    'error_message' => null
                ],
                $combinedText,
                null,
                $startTime,
                $memoryStart
            );

            if ($scanResult === null) {
                return null;
            }

            // Step 3: Count output tokens
            $outputText = json_encode($scanResult);
            $outputTokenResult = $this->tokenCounter->countTokens($outputText);

            if (!$outputTokenResult['success']) {
                log_message('error', 'Output token counting failed: ' . $outputTokenResult['error']);
                // Return result without cost tracking
                return $scanResult;
            }

            $outputTokens = $outputTokenResult['total_tokens'];

            // Step 4: Calculate costs
            $costResult = $this->tokenCounter->calculateCost($inputTokens, $outputTokens, $this->model);

            // Add cost tracking to the result
            $scanResult['_cost_tracking'] = [
                'token_usage' => [
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'total_tokens' => $inputTokens + $outputTokens
                ],
                'cost_analysis' => $costResult,
                'model_used' => $this->model
            ];

            return $scanResult;
        } catch (\Exception $e) {
            log_message('error', 'Gemini API Error with cost tracking: ' . $e->getMessage());
            return null;
        }
    }

    public function getTokenCounter(): GeminiTokenCounter
    {
        return $this->tokenCounter;
    }

    public function calculateCost(string $prompt, string $response, string $model = null): array
    {
        $modelToUse = $model ?? $this->model;

        try {
            // Count input tokens
            $inputTokenResult = $this->tokenCounter->countTokens($prompt);
            if (!$inputTokenResult['success']) {
                return $inputTokenResult;
            }

            // Count output tokens
            $outputTokenResult = $this->tokenCounter->countTokens($response);
            if (!$outputTokenResult['success']) {
                return $outputTokenResult;
            }

            // Calculate cost
            return $this->tokenCounter->calculateCost(
                $inputTokenResult['total_tokens'],
                $outputTokenResult['total_tokens'],
                $modelToUse
            );
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function logUsage(
        array $logContext,
        array $trackingData,
        array $brandData,
        array $categoryData,
        string $imageDataBase64,
        ?array $scanResult,
        float $startTime,
        int $memoryStart
    ): void {
        try {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $memoryUsed = round((memory_get_usage(true) - $memoryStart) / 1024 / 1024, 2);

            $imageSizeKb = strlen($imageDataBase64) * 0.75 / 1024;

            $request = Services::request();

            $logData = array_merge($logContext, [
                'receipt_id' => $logContext['receipt_id'],
                'user_id' => $logContext['user_id'],
                'event_id' => $logContext['event_id'],
                'model_used' => $this->model,
                'operation_type' => 'invoice_scan',
                'token_usage' => [
                    'input_tokens' => $trackingData['input_tokens'] ?? 0,
                    'output_tokens' => $trackingData['output_tokens'] ?? 0,
                    'total_tokens' => ($trackingData['input_tokens'] ?? 0) + ($trackingData['output_tokens'] ?? 0)
                ],
                'cost_analysis' => [
                    'input_cost_usd' => $trackingData['input_cost_usd'] ?? 0,
                    'output_cost_usd' => $trackingData['output_cost_usd'] ?? 0,
                    'total_cost_usd' => $trackingData['total_cost_usd'] ?? 0
                ],
                'request_metadata' => [
                    'has_image' => true,
                    'image_size_kb' => round($imageSizeKb, 2),
                    'brand_data_count' => count($brandData),
                    'category_data_count' => count($categoryData),
                    'response_time_ms' => $responseTime,
                    'memory_used_mb' => $memoryUsed,
                    'prompt_length' => $trackingData['prompt_length'] ?? 0
                ],
                'response_metadata' => [
                    'success' => $trackingData['success'] ?? true,
                    'error_message' => $trackingData['error_message'] ?? null,
                    'items_extracted' => $trackingData['items_extracted'] ?? 0,
                    'confidence_score' => $trackingData['confidence_score'] ?? null
                ],
                'system_info' => [
                    'ip_address' => $request->getIPAddress(),
                    'user_agent' => $request->getUserAgent()->getAgentString(),
                    'server_name' => $_SERVER['SERVER_NAME'] ?? null,
                    'php_version' => PHP_VERSION
                ]
            ]);

            $logResult = $this->usageLogModel->insertUsageLog($logData);

            if (!$logResult['success']) {
                log_message('error', 'Failed to log invoice scan usage: ' . ($logResult['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error logging invoice scan usage: ' . $e->getMessage());
        }
    }

    private function logTextGeneration(
        array $logContext,
        array $trackingData,
        string $prompt,
        ?string $responseText,
        float $startTime,
        int $memoryStart
    ): void {
        try {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $memoryUsed = round((memory_get_usage(true) - $memoryStart) / 1024 / 1024, 2);

            // Get request info
            $request = Services::request();

            $logData = array_merge($logContext, [
                'model_used' => $this->model,
                'operation_type' => 'text_generation',
                'token_usage' => [
                    'input_tokens' => $trackingData['input_tokens'] ?? 0,
                    'output_tokens' => $trackingData['output_tokens'] ?? 0,
                    'total_tokens' => ($trackingData['input_tokens'] ?? 0) + ($trackingData['output_tokens'] ?? 0)
                ],
                'cost_analysis' => [
                    'input_cost_usd' => $trackingData['input_cost_usd'] ?? 0,
                    'output_cost_usd' => $trackingData['output_cost_usd'] ?? 0,
                    'total_cost_usd' => $trackingData['total_cost_usd'] ?? 0
                ],
                'request_metadata' => [
                    'has_image' => false,
                    'prompt_length' => strlen($prompt),
                    'response_length' => $responseText ? strlen($responseText) : 0,
                    'response_time_ms' => $responseTime,
                    'memory_used_mb' => $memoryUsed
                ],
                'response_metadata' => [
                    'success' => $trackingData['success'] ?? true,
                    'error_message' => $trackingData['error_message'] ?? null
                ],
                'system_info' => [
                    'ip_address' => $request->getIPAddress(),
                    'user_agent' => $request->getUserAgent()->getAgentString(),
                    'server_name' => $_SERVER['SERVER_NAME'] ?? null,
                    'php_version' => PHP_VERSION
                ]
            ]);

            // Insert log
            $logResult = $this->usageLogModel->insertUsageLog($logData);

            if (!$logResult['success']) {
                log_message('error', 'Failed to log text generation usage: ' . ($logResult['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error logging text generation usage: ' . $e->getMessage());
        }
    }
}
