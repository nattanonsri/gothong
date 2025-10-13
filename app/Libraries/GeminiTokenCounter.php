<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class GeminiTokenCounter
{
    private $apiKey;
    private $countTokensUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:countTokens';
    private $generateUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';
    
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey ?? getenv('GEMINI_API_KEY');
    }
    
    /**
     * Count tokens for text content
     * 
     * @param string $text
     * @return array
     */
    public function countTokens($text)
    {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ]
        ];
        
        return $this->makeRequest($data, $this->countTokensUrl);
    }
    
    /**
     * Calculate cost estimation for input/output tokens
     * 
     * @param int $inputTokens
     * @param int $outputTokens
     * @param string $model
     * @return array
     */
    public function calculateCost($inputTokens, $outputTokens, $model = 'gemini-2.0-flash')
    {
        // Pricing per 1M tokens (USD)
        $pricing = [
            'gemini-2.5-flash' => [
                'input' => 0.30,    // $0.30 per 1M input tokens (text/image/video)
                'output' => 2.50    // $2.50 per 1M output tokens
            ],
            'gemini-2.5-pro' => [
                'input' => 1.25,    // $1.25 per 1M input tokens (<=200k)
                'input_long' => 2.50, // $2.50 per 1M input tokens (>200k)
                'output' => 10.00,   // $10.00 per 1M output tokens (<=200k)
                'output_long' => 15.00 // $15.00 per 1M output tokens (>200k)
            ],
            'gemini-2.0-flash' => [
                'input' => 0.075,
                'output' => 0.30
            ],
            'gemini-2.0-flash-exp' => [
                'input' => 0.075,
                'output' => 0.30
            ],
            'gemini-1.5-flash' => [
                'input' => 0.075,
                'output' => 0.30
            ],
            'gemini-1.5-pro' => [
                'input' => 1.25,
                'output' => 5.00
            ],
            'gemini-2.0-pro' => [
                'input' => 2.50,
                'output' => 10.00
            ]
        ];
        
        if (!isset($pricing[$model])) {
            return [
                'success' => false,
                'error' => 'Unknown model: ' . $model
            ];
        }
        
        $rates = $pricing[$model];
        $inputCost = ($inputTokens / 1000000) * $rates['input'];
        $outputCost = ($outputTokens / 1000000) * $rates['output'];
        $totalCost = $inputCost + $outputCost;
        
        return [
            'success' => true,
            'model' => $model,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $inputTokens + $outputTokens,
            'input_cost_usd' => round($inputCost, 6),
            'output_cost_usd' => round($outputCost, 6),
            'total_cost_usd' => round($totalCost, 6),
            'cost_breakdown' => [
                'input_rate_per_1m' => $rates['input'],
                'output_rate_per_1m' => $rates['output']
            ]
        ];
    }
    
    /**
     * Generate content with full cost tracking
     * 
     * @param string $prompt
     * @param string $model
     * @return array
     */
    public function generateWithCostTracking($prompt, $model = 'gemini-2.0-flash-exp')
    {
        // Step 1: Count input tokens
        $inputTokenResult = $this->countTokens($prompt);
        
        if (!$inputTokenResult['success']) {
            return $inputTokenResult;
        }
        
        $inputTokens = $inputTokenResult['total_tokens'];
        
        // Step 2: Generate content
        $generateResult = $this->generateContent($prompt, $model);
        
        if (!$generateResult['success']) {
            return $generateResult;
        }
        
        // Step 3: Count output tokens
        $outputText = $generateResult['response_text'];
        $outputTokenResult = $this->countTokens($outputText);
        
        if (!$outputTokenResult['success']) {
            return $outputTokenResult;
        }
        
        $outputTokens = $outputTokenResult['total_tokens'];
        
        // Step 4: Calculate costs
        $costResult = $this->calculateCost($inputTokens, $outputTokens, $model);
        
        return [
            'success' => true,
            'response_text' => $outputText,
            'token_usage' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens
            ],
            'cost_analysis' => $costResult,
            'model_used' => $model
        ];
    }
    
    /**
     * Generate content using Gemini API
     * 
     * @param string $prompt
     * @param string $model
     * @return array
     */
    public function generateContent($prompt, $model = 'gemini-2.0-flash-exp')
    {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post($this->generateUrl . '?key=' . $this->apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
                'timeout' => 60
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() === 200) {
                $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                return [
                    'success' => true,
                    'response_text' => $responseText,
                    'full_response' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error']['message'] ?? 'Unknown error',
                    'status_code' => $response->getStatusCode()
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Count tokens for multiple messages (chat format)
     * 
     * @param array $messages Format: [['role' => 'user', 'content' => 'text'], ...]
     * @return array
     */
    public function countTokensForChat($messages)
    {
        $contents = [];
        
        foreach ($messages as $message) {
            $role = $message['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']]
                ]
            ];
        }
        
        $data = ['contents' => $contents];
        
        return $this->makeRequest($data, $this->countTokensUrl);
    }
    
    /**
     * Count tokens with system instruction
     * 
     * @param string $systemInstruction
     * @param string $userText
     * @return array
     */
    public function countTokensWithSystem($systemInstruction, $userText)
    {
        $data = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => [
                [
                    'parts' => [
                        ['text' => $userText]
                    ]
                ]
            ]
        ];
        
        return $this->makeRequest($data, $this->countTokensUrl);
    }
    
    /**
     * Count tokens for content with image (multimodal)
     * 
     * @param string $text
     * @param string $imageBase64
     * @param string $mimeType
     * @return array
     */
    public function countTokensWithImage($text, $imageBase64, $mimeType = 'image/jpeg')
    {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inlineData' => [
                                'data' => $imageBase64,
                                'mimeType' => $mimeType
                            ]
                        ],
                        ['text' => $text]
                    ]
                ]
            ]
        ];
        
        return $this->makeRequest($data, $this->countTokensUrl);
    }
    
    /**
     * Make HTTP request to Gemini API
     * 
     * @param array $data
     * @param string $url
     * @return array
     */
    private function makeRequest($data, $url)
    {
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post($url . '?key=' . $this->apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
                'timeout' => 30
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() === 200) {
                return [
                    'success' => true,
                    'total_tokens' => $result['totalTokens'] ?? 0,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error']['message'] ?? 'Unknown error',
                    'status_code' => $response->getStatusCode()
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
} 