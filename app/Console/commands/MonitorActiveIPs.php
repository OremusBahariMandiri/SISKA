<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorActiveIPs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ip:monitor
                            {action=list : Action to perform (list|stats|top|watch)}
                            {--lines=100 : Number of lines to analyze}
                            {--interval=1 : Watch interval in seconds}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor active IPs accessing the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listActiveIPs();
                break;

            case 'stats':
                $this->showStats();
                break;

            case 'top':
                $this->showTopIPs();
                break;

            case 'watch':
                $this->watchLive();
                break;

            default:
                $this->error('Invalid action. Use: list, stats, top, or watch');
        }

        return 0;
    }

    /**
     * List all active IPs from tracking cache
     */
    protected function listActiveIPs()
    {
        $this->info('Active IPs (from request tracking):');
        $this->newLine();

        // Get all tracked IPs from cache
        $trackedIps = $this->getTrackedIPs();

        if (empty($trackedIps)) {
            $this->warn('No active IPs tracked.');
            return;
        }

        $data = [];
        foreach ($trackedIps as $ip) {
            $requests = Cache::get("request_tracking:{$ip}", []);
            $recentRequests = $this->getRecentRequests($requests, 300); // Last 5 minutes

            if (!empty($recentRequests)) {
                $data[] = [
                    'ip' => $ip,
                    'requests_5min' => count($recentRequests),
                    'last_seen' => date('H:i:s', end($recentRequests)['timestamp']),
                    'status' => Cache::has("banned_ip:{$ip}") ? '🚫 BANNED' : '✅ Active',
                ];
            }
        }

        if (empty($data)) {
            $this->warn('No recent activity.');
            return;
        }

        // Sort by request count
        usort($data, function($a, $b) {
            return $b['requests_5min'] <=> $a['requests_5min'];
        });

        $this->table(
            ['IP Address', 'Requests (5min)', 'Last Seen', 'Status'],
            $data
        );
    }

    /**
     * Show statistics
     */
    protected function showStats()
    {
        $this->info('IP Activity Statistics:');
        $this->newLine();

        $trackedIps = $this->getTrackedIPs();
        $activeCount = 0;
        $bannedCount = 0;
        $totalRequests = 0;

        foreach ($trackedIps as $ip) {
            $requests = Cache::get("request_tracking:{$ip}", []);
            $recentRequests = $this->getRecentRequests($requests, 300);

            if (!empty($recentRequests)) {
                $activeCount++;
                $totalRequests += count($recentRequests);
            }

            if (Cache::has("banned_ip:{$ip}")) {
                $bannedCount++;
            }
        }

        $this->line("Total Tracked IPs: " . count($trackedIps));
        $this->line("Active IPs (last 5 min): {$activeCount}");
        $this->line("Banned IPs: {$bannedCount}");
        $this->line("Total Requests (last 5 min): {$totalRequests}");

        if ($activeCount > 0) {
            $avgRequests = round($totalRequests / $activeCount, 2);
            $this->line("Average Requests per IP: {$avgRequests}");
        }

        // Nginx log analysis (if accessible)
        $this->newLine();
        $this->analyzeNginxLog();
    }

    /**
     * Show top IPs by request count
     */
    protected function showTopIPs()
    {
        $lines = $this->option('lines');
        $this->info("Top 10 Most Active IPs (analyzing last {$lines} requests):");
        $this->newLine();

        $trackedIps = $this->getTrackedIPs();
        $data = [];

        foreach ($trackedIps as $ip) {
            $requests = Cache::get("request_tracking:{$ip}", []);
            $recentRequests = $this->getRecentRequests($requests, 3600); // Last hour

            if (!empty($recentRequests)) {
                // Calculate request rate
                $duration = max(1, (now()->timestamp - $recentRequests[0]['timestamp']) / 60);
                $requestsPerMin = round(count($recentRequests) / $duration, 2);

                // Get most accessed URLs
                $urls = array_column($recentRequests, 'url');
                $urlCounts = array_count_values($urls);
                arsort($urlCounts);
                $topUrl = array_key_first($urlCounts);

                $data[] = [
                    'ip' => $ip,
                    'requests_1h' => count($recentRequests),
                    'req_per_min' => $requestsPerMin,
                    'top_url' => substr($topUrl, 0, 30),
                    'status' => Cache::has("banned_ip:{$ip}") ? '🚫' : '✅',
                ];
            }
        }

        // Sort by request count
        usort($data, function($a, $b) {
            return $b['requests_1h'] <=> $a['requests_1h'];
        });

        $this->table(
            ['IP Address', 'Requests (1h)', 'Req/Min', 'Top URL', 'Status'],
            array_slice($data, 0, 10)
        );
    }

    /**
     * Watch IPs in real-time
     */
    protected function watchLive()
    {
        $interval = $this->option('interval');
        $this->info("Watching active IPs (refresh every {$interval}s, press Ctrl+C to stop):");
        $this->newLine();

        while (true) {
            // Clear screen
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                system('cls');
            } else {
                system('clear');
            }

            $this->line('=== ACTIVE IP MONITOR ===');
            $this->line('Time: ' . now()->format('Y-m-d H:i:s'));
            $this->newLine();

            $this->listActiveIPs();

            sleep($interval);
        }
    }

    /**
     * Get all tracked IPs
     */
    protected function getTrackedIPs(): array
    {
        // Try to get from Redis
        try {
            $keys = \Illuminate\Support\Facades\Redis::keys('request_tracking:*');
            return array_map(function($key) {
                return str_replace('request_tracking:', '', $key);
            }, $keys);
        } catch (\Exception $e) {
            // Fallback: scan cache
            return [];
        }
    }

    /**
     * Filter recent requests
     */
    protected function getRecentRequests(array $requests, int $seconds): array
    {
        $now = now()->timestamp;
        return array_filter($requests, function($req) use ($now, $seconds) {
            return ($now - $req['timestamp']) <= $seconds;
        });
    }

    /**
     * Analyze Nginx log
     */
    protected function analyzeNginxLog()
    {
        $logFile = '/var/log/nginx/access.log';

        if (!file_exists($logFile)) {
            $this->warn('Nginx log not accessible.');
            return;
        }

        $this->info('Nginx Access Log Summary (last 1000 lines):');

        // Count unique IPs from nginx log
        $command = "tail -n 1000 {$logFile} | awk '{print \$1}' | sort | uniq | wc -l";
        $uniqueIps = trim(shell_exec($command));

        $this->line("Unique IPs (Nginx): {$uniqueIps}");

        // Top 5 IPs from nginx
        $command = "tail -n 1000 {$logFile} | awk '{print \$1}' | sort | uniq -c | sort -rn | head -5";
        $topIps = shell_exec($command);

        if ($topIps) {
            $this->newLine();
            $this->line('Top 5 IPs from Nginx log:');
            $this->line($topIps);
        }
    }
}