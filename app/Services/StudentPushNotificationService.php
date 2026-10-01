<?php

namespace App\Services;

use App\Models\AttendanceItem;
use App\Models\MobileNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentPushNotificationService
{
    public function __construct(private readonly StudentAudienceService $audience) {}

    public function sendAbsentAttendance(): int
    {
        $items = AttendanceItem::query()->with(['attendance', 'student.latestAcademicInformation'])
            ->where('status', 'absent')->whereHas('attendance', fn ($q) => $q->whereDate('date', today()))
            ->whereIn('student_id', $this->audience->activeStudents()->select('students.id'))
            ->get();
        $sent = 0;
        foreach ($items as $item) {
            foreach ($item->student->devices()->where('is_active', true)->get() as $device) {
                $key = 'absent:'.$item->id.':'.$device->id;
                $log = MobileNotificationLog::firstOrCreate(['unique_event_key' => $key], ['student_id' => $item->student_id, 'device_id' => $device->id, 'notification_type' => 'attendance_absent', 'title' => 'Attendance alert', 'body' => 'You were marked absent today.', 'reference_type' => AttendanceItem::class, 'reference_id' => $item->id, 'delivery_status' => 'queued']);
                // Queued, failed, and unconfigured records are retried on the next
                // scheduler run; sent records are permanently deduplicated.
                if ($log->delivery_status === 'sent') continue;
                $result = $this->send($device->fcm_token, 'Attendance alert', 'You were marked absent today.', ['type' => 'attendance_absent', 'attendance_item_id' => (string) $item->id]);
                if ($result['status'] === 'invalid_token') {
                    $device->update(['is_active' => false]);
                }
                $log->update(['delivery_status' => $result['status'], 'sent_at' => $result['status'] === 'sent' ? now() : null, 'error_message' => $result['error']]);
                if ($result['status'] === 'sent') $sent++;
            }
        }
        return $sent;
    }

    private function send(string $token, string $title, string $body, array $data): array
    {
        $json = config('services.firebase.service_account_json');
        if (!$json) return ['status' => 'skipped_unconfigured', 'error' => 'Firebase service account is not configured.'];
        $credentials = is_string($json) ? json_decode($json, true) : $json;
        if (!is_array($credentials) || empty($credentials['project_id']) || empty($credentials['private_key']) || empty($credentials['client_email'])) return ['status' => 'failed', 'error' => 'Invalid Firebase service account configuration.'];
        try {
            $now = time();
            $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64Url(json_encode(['iss' => $credentials['client_email'], 'scope' => 'https://www.googleapis.com/auth/firebase.messaging', 'aud' => 'https://oauth2.googleapis.com/token', 'iat' => $now, 'exp' => $now + 3600]));
            openssl_sign($header.'.'.$claims, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $oauth = Http::asForm()->post('https://oauth2.googleapis.com/token', ['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $header.'.'.$claims.'.'.$this->base64Url($signature)])->throw()->json();
            Http::withToken($oauth['access_token'])->post('https://fcm.googleapis.com/v1/projects/'.$credentials['project_id'].'/messages:send', ['message' => ['token' => $token, 'notification' => ['title' => $title, 'body' => $body], 'data' => $data]])->throw();
            return ['status' => 'sent', 'error' => null];
        } catch (\Throwable $e) {
            Log::warning('Student push notification failed', ['error' => $e->getMessage()]);
            $message = substr($e->getMessage(), 0, 1000);
            $invalidToken = str_contains($message, 'UNREGISTERED') || str_contains($message, 'registration-token-not-registered');
            return ['status' => $invalidToken ? 'invalid_token' : 'failed', 'error' => $message];
        }
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
