<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    /**
     * Process chatbot message
     */
    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array'
        ]);

        $userMessage = strtolower(trim($request->message));
        $context = $request->input('context', []);
        $user = auth()->user();

        // Check for FAQ matches first
        $faqResponse = $this->checkFaqMatch($userMessage);
        if ($faqResponse) {
            return response()->json([
                'success' => true,
                'response' => $faqResponse,
                'type' => 'faq',
                'suggestions' => $this->getSuggestions($userMessage)
            ]);
        }

        // Check for intent-based responses
        $intentResponse = $this->processIntent($userMessage, $user, $context);
        if ($intentResponse) {
            return response()->json([
                'success' => true,
                'response' => $intentResponse['message'],
                'type' => $intentResponse['type'],
                'data' => $intentResponse['data'] ?? null,
                'suggestions' => $intentResponse['suggestions'] ?? $this->getSuggestions($userMessage)
            ]);
        }

        // Try AI response if configured
        if (config('services.openai.key')) {
            $aiResponse = $this->getAIResponse($userMessage, $user, $context);
            if ($aiResponse) {
                return response()->json([
                    'success' => true,
                    'response' => $aiResponse,
                    'type' => 'ai',
                    'suggestions' => $this->getSuggestions($userMessage)
                ]);
            }
        }

        // Default response
        return response()->json([
            'success' => true,
            'response' => "I'm not sure how to help with that. Here are some things I can help you with:",
            'type' => 'default',
            'suggestions' => [
                'How do I find a carer?',
                'How do I post a job?',
                'What are the fees?',
                'How does payment work?',
                'Contact support'
            ]
        ]);
    }

    /**
     * Check if message matches any FAQ
     */
    private function checkFaqMatch($message)
    {
        $keywords = $this->extractKeywords($message);

        $faqs = Cache::remember('chatbot_faqs', 3600, function () {
            return Faq::all();
        });

        $bestMatch = null;
        $highestScore = 0;

        foreach ($faqs as $faq) {
            $questionKeywords = $this->extractKeywords($faq->question);
            $score = count(array_intersect($keywords, $questionKeywords));

            if ($score > $highestScore && $score >= 2) {
                $highestScore = $score;
                $bestMatch = $faq;
            }
        }

        return $bestMatch ? $bestMatch->answer : null;
    }

    /**
     * Process user intent
     */
    private function processIntent($message, $user, $context)
    {
        // Find a carer
        if ($this->containsAny($message, ['find carer', 'find babysitter', 'find cleaner', 'need carer', 'looking for'])) {
            $roles = Role::where('seller', true)->where('active', true)->get();

            return [
                'type' => 'action',
                'message' => "I can help you find a carer! What type of service are you looking for?",
                'data' => [
                    'action' => 'select_service',
                    'options' => $roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->title,
                        'url' => route('sellers') . '?type=' . $r->id
                    ])
                ],
                'suggestions' => $roles->pluck('title')->toArray()
            ];
        }

        // Post a job
        if ($this->containsAny($message, ['post job', 'create job', 'post a job', 'hire'])) {
            return [
                'type' => 'action',
                'message' => "Great! You can post a job to find the perfect carer. Click below to get started:",
                'data' => [
                    'action' => 'redirect',
                    'url' => route('contract.create'),
                    'button_text' => 'Post a Job'
                ]
            ];
        }

        // Pricing / fees
        if ($this->containsAny($message, ['price', 'cost', 'fee', 'how much', 'payment', 'pay'])) {
            return [
                'type' => 'info',
                'message' => "💰 **How Pricing Works**\n\n" .
                    "• Carers set their own hourly rates (typically £10-25/hour)\n" .
                    "• You agree on the rate directly with the carer\n" .
                    "• Payment is processed securely through our platform\n" .
                    "• We charge a small service fee to cover platform costs\n\n" .
                    "Would you like to see available carers and their rates?",
                'suggestions' => ['View carers', 'How do I pay?', 'Is payment secure?']
            ];
        }

        // Account / profile
        if ($this->containsAny($message, ['account', 'profile', 'settings', 'my profile'])) {
            if ($user) {
                return [
                    'type' => 'action',
                    'message' => "Here are your account options:",
                    'data' => [
                        'action' => 'menu',
                        'options' => [
                            ['text' => 'Edit Profile', 'url' => route('profile')],
                            ['text' => 'My Messages', 'url' => route('inbox')],
                            ['text' => 'My Orders', 'url' => route('order.index')],
                            ['text' => 'My Documents', 'url' => route('documents')]
                        ]
                    ]
                ];
            } else {
                return [
                    'type' => 'action',
                    'message' => "You need to be logged in to access your account. Would you like to:",
                    'data' => [
                        'action' => 'menu',
                        'options' => [
                            ['text' => 'Log In', 'url' => route('login')],
                            ['text' => 'Register', 'url' => route('register.type', ['type' => 'client'])]
                        ]
                    ]
                ];
            }
        }

        // Booking status
        if ($this->containsAny($message, ['booking status', 'my booking', 'order status', 'track'])) {
            if ($user) {
                return [
                    'type' => 'action',
                    'message' => "You can view all your bookings and their status here:",
                    'data' => [
                        'action' => 'redirect',
                        'url' => route('order.index'),
                        'button_text' => 'View My Bookings'
                    ]
                ];
            }
        }

        // Contact support / human
        if ($this->containsAny($message, ['contact', 'support', 'human', 'speak to someone', 'help', 'problem', 'issue'])) {
            return [
                'type' => 'support',
                'message' => "I'll connect you with our support team. You can:\n\n" .
                    "📧 Email: support@jimacare.com\n" .
                    "📞 Phone: Available Mon-Fri 9am-5pm\n\n" .
                    "Or describe your issue and I'll try to help!",
                'data' => [
                    'action' => 'show_support',
                    'email' => 'support@jimacare.com'
                ],
                'suggestions' => ['Email support', 'Common issues', 'Back to menu']
            ];
        }

        // Safety / verification
        if ($this->containsAny($message, ['safe', 'trust', 'verify', 'dbs', 'background check', 'secure'])) {
            return [
                'type' => 'info',
                'message' => "🔒 **Your Safety is Our Priority**\n\n" .
                    "• All carers can upload DBS certificates\n" .
                    "• Email and phone verification required\n" .
                    "• Reference checks available\n" .
                    "• Secure payment processing\n" .
                    "• Review and rating system\n" .
                    "• In-app messaging (no personal details shared)\n\n" .
                    "Look for the ✓ verified badge on carer profiles!"
            ];
        }

        // Greeting
        if ($this->containsAny($message, ['hi', 'hello', 'hey', 'good morning', 'good afternoon'])) {
            $greeting = $user ? "Hi {$user->firstname}!" : "Hello!";
            return [
                'type' => 'greeting',
                'message' => "$greeting 👋 I'm here to help you find care services. What can I help you with today?",
                'suggestions' => ['Find a carer', 'Post a job', 'How it works', 'Pricing info']
            ];
        }

        return null;
    }

    /**
     * Get AI response using OpenAI/Claude
     */
    private function getAIResponse($message, $user, $context)
    {
        try {
            $systemPrompt = "You are a helpful customer support assistant for Jimacare, " .
                "a platform connecting families with carers, babysitters, and cleaners. " .
                "Be friendly, concise, and helpful. If you don't know something specific, " .
                "suggest contacting support. Keep responses under 150 words.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.key'),
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'max_tokens' => 200,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            \Log::error('Chatbot AI error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get suggested follow-up questions
     */
    private function getSuggestions($message)
    {
        $defaultSuggestions = [
            'Find a carer',
            'How does it work?',
            'Pricing & fees',
            'Contact support'
        ];

        return $defaultSuggestions;
    }

    /**
     * Extract keywords from message
     */
    private function extractKeywords($text)
    {
        $stopWords = ['i', 'a', 'the', 'is', 'are', 'was', 'were', 'be', 'been', 
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 
            'could', 'should', 'may', 'might', 'must', 'can', 'to', 'of', 'in', 
            'for', 'on', 'with', 'at', 'by', 'from', 'as', 'into', 'through', 
            'during', 'before', 'after', 'above', 'below', 'between', 'under', 
            'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 
            'why', 'how', 'all', 'each', 'few', 'more', 'most', 'other', 'some', 
            'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 
            'too', 'very', 'just', 'and', 'but', 'if', 'or', 'because', 'until', 
            'while', 'this', 'that', 'these', 'those', 'am', 'an', 'my', 'your', 
            'me', 'you', 'we', 'they', 'what', 'which', 'who', 'whom'];

        $words = preg_split('/\s+/', strtolower($text));
        $words = array_map(function ($word) {
            return preg_replace('/[^a-z]/', '', $word);
        }, $words);

        return array_values(array_diff($words, $stopWords));
    }

    /**
     * Check if message contains any of the given phrases
     */
    private function containsAny($message, $phrases)
    {
        foreach ($phrases as $phrase) {
            if (str_contains($message, $phrase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get initial chatbot greeting and options
     */
    public function init()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'greeting' => $user 
                ? "Hi {$user->firstname}! 👋 How can I help you today?"
                : "Hello! 👋 Welcome to Jimacare. How can I help you today?",
            'quick_actions' => [
                ['text' => '🔍 Find a carer', 'action' => 'find_carer'],
                ['text' => '📝 Post a job', 'action' => 'post_job'],
                ['text' => '❓ How it works', 'action' => 'how_it_works'],
                ['text' => '💬 Contact support', 'action' => 'contact_support']
            ]
        ]);
    }
}

