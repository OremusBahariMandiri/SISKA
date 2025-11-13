<?php
// file: app/Http/Middleware/DDoSProtection.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Services\ActivityHubClient;

class DDoSProtection
{
    protected $activityHub;
    // White-listed IPs that should never be banned
    protected $trustedIps = [
        // Add your company or trusted IPs here
        // '203.0.113.1',
    ];

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    /**
     * Handle an incoming request with more balanced thresholds.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Allow trusted IPs to bypass protection entirely
        if ($this->isTrustedIp($ip)) {
            return $next($request);
        }

        // Skip check for static resources to improve performance
        if ($this->isStaticResource($request)) {
            return $next($request);
        }

        $userAgent = $request->userAgent() ?? 'Unknown';

        // Check if IP is banned
        if ($this->isIpBanned($ip)) {
            // Only log if not already logged recently
            $logKey = "banned_ip_logged:{$ip}";
            if (!Cache::has($logKey)) {
                Log::warning('Blocked banned IP attempting access', [
                    'ip' => $ip,
                    'url' => $request->fullUrl(),
                    'user_agent' => $userAgent
                ]);

                // Log to Activity Hub
                try {
                    $this->activityHub->logSecurityEvent('blocked_ip', 'medium', [
                        'ip_address' => $ip,
                        'url' => $request->fullUrl(),
                        'user_id' => auth()->id(),
                        'user_email' => auth()->user()->email ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
                }

                // Prevent log flooding by caching that we've logged this IP recently
                Cache::put($logKey, true, 300); // 5 minutes
            }

            // Handle the response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Your access is temporarily limited due to suspicious activity.',
                    'retry_after' => $this->getBanTimeRemaining($ip)
                ], 403);
            }

            $errorCode = Str::random(8);
            return response()->view('errors.blocked', [
                'retryAfter' => $this->getBanTimeRemaining($ip),
                'errorCode' => $errorCode
            ], 403);
        }

        // For authenticated users, we're more lenient with thresholds
        $isAuthenticated = auth()->check();

        // Track request patterns - use separate tracking for auth vs non-auth
        $this->trackRequest($ip, $request, $isAuthenticated);

        // Detect suspicious activity with more balanced thresholds
        if ($this->detectSuspiciousActivity($ip, $isAuthenticated)) {
            // Ban duration is shorter for authenticated users
            $banDuration = $isAuthenticated ? 5 : 10;
            $this->banIp($ip, $banDuration);

            Log::alert('Potential DDoS attack detected - IP temporarily limited', [
                'ip' => $ip,
                'requests' => $this->getRequestCount($ip, $isAuthenticated),
                'user_agent' => $userAgent,
                'url' => $request->fullUrl(),
                'is_authenticated' => $isAuthenticated
            ]);

            // Log to Activity Hub
            try {
                $this->activityHub->logSecurityEvent('ddos_attempt', 'high', [
                    'ip_address' => $ip,
                    'url' => $request->fullUrl(),
                    'request_count' => $this->getRequestCount($ip, $isAuthenticated),
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Rate limit exceeded. Please slow down your requests.',
                    'retry_after' => $banDuration * 60
                ], 429);
            }

            $errorCode = Str::random(8);
            return response()->view('errors.blocked', [
                'retryAfter' => $banDuration * 60,
                'errorCode' => $errorCode
            ], 429);
        }

        // We'll only log, not block, for suspicious user agents
        if ($this->isSuspiciousUserAgent($userAgent)) {
            $logKey = "suspicious_ua_logged:{$ip}:{$userAgent}";
            if (!Cache::has($logKey)) {
                Log::warning('Suspicious user agent detected', [
                    'ip' => $ip,
                    'user_agent' => $userAgent
                ]);

                // Only log to Activity Hub once every 24 hours for the same IP/UA combo
                try {
                    $this->activityHub->logSecurityEvent('suspicious_activity', 'low', [
                        'ip_address' => $ip,
                        'url' => $request->fullUrl(),
                        'user_agent' => $userAgent,
                        'user_id' => auth()->id(),
                        'user_email' => auth()->user()->email ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
                }

                Cache::put($logKey, true, 1440); // 24 hours
            }
        }

        return $next($request);
    }

    /**
     * Check if IP is in trusted list
     */
    protected function isTrustedIp(string $ip): bool
    {
        // Always trust localhost and internal networks
        $localIps = ['127.0.0.1', '::1', 'localhost'];
        if (in_array($ip, $localIps) || strpos($ip, '192.168.') === 0) {
            return true;
        }

        // Check custom trusted IPs
        return in_array($ip, $this->trustedIps);
    }

    /**
     * Track request from IP - with auth status consideration
     */
    protected function trackRequest(string $ip, Request $request, bool $isAuthenticated): void
    {
        // Skip if request is to static asset
        if ($this->isStaticResource($request)) {
            return;
        }

        $suffix = $isAuthenticated ? 'auth' : 'anon';
        $key = "request_tracking:{$ip}:{$suffix}";
        $requests = Cache::get($key, []);

        $requests[] = [
            'timestamp' => now()->timestamp,
            'url' => $request->path(),
            'method' => $request->method(),
        ];

        // Store last 100 requests in 5 minutes
        $requests = array_slice($requests, -100);
        Cache::put($key, $requests, 300);
    }

    /**
     * Detect suspicious activity - more balanced thresholds
     */
    protected function detectSuspiciousActivity(string $ip, bool $isAuthenticated): bool
    {
        $suffix = $isAuthenticated ? 'auth' : 'anon';
        $key = "request_tracking:{$ip}:{$suffix}";
        $requests = Cache::get($key, []);

        if (empty($requests)) {
            return false;
        }

        $now = now()->timestamp;

        // Count requests in last minute
        $recentRequests = array_filter($requests, function ($req) use ($now) {
            return ($now - $req['timestamp']) <= 60;
        });

        $requestCount = count($recentRequests);

        // Different thresholds based on authentication
        // Authenticated users get higher limits
        $rateThreshold = $isAuthenticated ? 180 : 120;
        $patternThreshold = $isAuthenticated ? 0.03 : 0.05;
        $patternMinRequests = $isAuthenticated ? 150 : 100;

        // Rate-based detection
        if ($requestCount > $rateThreshold) {
            return true;
        }

        // Pattern-based detection (same URL repeatedly)
        if ($requestCount > $patternMinRequests) {
            $urls = array_column($recentRequests, 'url');
            $uniqueUrls = count(array_unique($urls));

            // If more than 95% (anon) or 97% (auth) of requests are to the same URL
            if ($uniqueUrls <= ($requestCount * $patternThreshold)) {

                // Check if it's just a polling endpoint which is legitimate
                $mostCommonUrl = $this->getMostCommonUrl($urls);
                if ($this->isLegitimatePollingEndpoint($mostCommonUrl)) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Get the most common URL from an array
     */
    protected function getMostCommonUrl(array $urls): string
    {
        $counts = array_count_values($urls);
        arsort($counts);
        return key($counts);
    }

    /**
     * Check if URL is a legitimate polling endpoint
     */
    protected function isLegitimatePollingEndpoint(string $url): bool
    {
        // Add your polling/live update endpoints here
        $pollingEndpoints = [
            // Example: 'api/notifications',
            // Example: 'api/status-updates'
        ];

        foreach ($pollingEndpoints as $endpoint) {
            if (strpos($url, $endpoint) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is to static resource
     */
    protected function isStaticResource(Request $request): bool
    {
        $path = $request->path();
        return preg_match('/(\.css|\.js|\.jpg|\.jpeg|\.png|\.gif|\.ico|\.svg|\.woff2?|\.ttf|\.eot|\.map)$/i', $path);
    }

    /**
     * Ban IP address - shorter duration
     */
    protected function banIp(string $ip, int $minutes = 5): void
    {
        $key = "banned_ip:{$ip}";
        Cache::put($key, [
            'banned_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'reason' => 'Temporary rate limit'
        ], $minutes * 60);

        $this->logBan($ip, $minutes);

        try {
            $this->activityHub->logSecurityEvent('blocked_ip', 'medium', [
                'ip_address' => $ip,
                'reason' => 'Temporary rate limit',
                'duration_minutes' => $minutes
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
        }
    }

    /**
     * Check if IP is banned
     */
    protected function isIpBanned(string $ip): bool
    {
        return Cache::has("banned_ip:{$ip}");
    }

    /**
     * Get remaining ban time
     */
    protected function getBanTimeRemaining(string $ip): int
    {
        $key = "banned_ip:{$ip}";
        $banData = Cache::get($key);

        if (!$banData) {
            return 0;
        }

        $expiresAt = $banData['expires_at'] ?? now();
        return max(0, now()->diffInSeconds($expiresAt));
    }

    /**
     * Get request count for IP
     */
    protected function getRequestCount(string $ip, bool $isAuthenticated): int
    {
        $suffix = $isAuthenticated ? 'auth' : 'anon';
        $key = "request_tracking:{$ip}:{$suffix}";
        $requests = Cache::get($key, []);

        $now = now()->timestamp;
        $recentRequests = array_filter($requests, function ($req) use ($now) {
            return ($now - $req['timestamp']) <= 60;
        });

        return count($recentRequests);
    }

    /**
     * Log banned IP
     */
    protected function logBan(string $ip, int $minutes): void
    {
        $banLog = Cache::get('ban_log', []);
        $banLog[] = [
            'ip' => $ip,
            'banned_at' => now()->toDateTimeString(),
            'duration' => $minutes,
            'reason' => 'Temporary rate limit'
        ];

        // Keep last 1000 logs
        $banLog = array_slice($banLog, -1000);
        Cache::put('ban_log', $banLog, 86400); // 24 hours
    }

    /**
     * Detect suspicious user agent - reduced patterns
     */
    protected function isSuspiciousUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        // Only detect clearly malicious tools
        $suspiciousPatterns = [
            'nikto', 'sqlmap', 'nmap', 'masscan', 'acunetix'
        ];

        $userAgentLower = strtolower($userAgent);

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}