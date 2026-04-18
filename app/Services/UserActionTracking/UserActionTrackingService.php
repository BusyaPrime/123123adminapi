<?php

namespace App\Services\UserActionTracking;

use App\Constants\TrackingSessionStatus;
use App\Constants\UserType;
use App\Constants\TrackingOrderOutcome;
use App\Constants\PlatformType;
use App\Domain\TrackingSession\Models\TrackingSession;
use App\Domain\TrackingOrderFunnel\Models\TrackingOrderFunnel;
use App\Domain\Users\Models\User;
use App\Domain\TrackingEvent\Models\TrackingEvent;
use App\Services\Tracking\TrackingOrderFunnelDefinition;
use DomainException;
use App\Services\InfoByIpService;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;

class UserActionTrackingService {
    public function startSession(
        $userId,
        $ipAddress,
        $language,
        $screen,
        $platform,
        $appVersion,
        $userAgent,
        $os,
        $deviceId,
        $deviceModel,
        $referrerUrl,
        $utmSource,
        $utmMedium,
        $utmCampaign
    )
    {
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        $platform = $this->normalizePlatform($platform, $os, $userAgent);

        if($agent->isRobot()) {
            throw new DomainException('Robot detected');
        }

        $browser = $agent->browser() ?: null;
        $browserVersion = $agent->version($browser) ?: null;

        //Computed from ip-api.com
        $ipInfo = InfoByIpService::getInfoByIp($ipAddress);
        $country = $ipInfo['country'] ?? null;
        $countryCode = $ipInfo['countryCode'] ?? null;
        $city = $ipInfo['regionName'] ?? null;
        $lat = $ipInfo['lat'] ?? null;
        $lng = $ipInfo['lon'] ?? null;
        $timezone = $ipInfo['timezone'] ?? null;
        
        $session = TrackingSession::create([
            'user_id' => $userId,
            'user_type' => $userId !== null ? UserType::REGISTERED : UserType::ANONYMOUS,
            'platform' => $platform,
            'app_version' => $appVersion,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'os' => $os,
            'device_id' => $deviceId,
            'device_model' => $deviceModel,
            'device_type' => $this->detectDeviceType($agent),
            'referrer_url' => $referrerUrl,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'country' => $country,
            'country_code' => $countryCode,
            'city' => $city,
            'lat' => $lat,
            'lng' => $lng,
            'language' => $language,
            'timezone' => $timezone,
            'status' => TrackingSessionStatus::ACTIVE,
            'last_seen_at' => now(),
        ]);

        TrackingEvent::create([
            'session_id' => $session->id,
            'event_type' => $platform === PlatformType::WEB ? TrackingEvent::TYPE_PAGE_VIEW : TrackingEvent::TYPE_APP_OPEN,
            'user_id' => $userId,
            'screen_name' => $screen,
        ]);

        return $session;
    }

    public function identifySession(
        TrackingSession $session,
        User $user
    ) {
        if($session->user_id !== null) {
            return $session;
        }

        $userId = $user->id;
        $session->user_id = $userId;
        $session->user_type = UserType::REGISTERED;
        $session->last_seen_at = now();

        TrackingEvent::create([
            'session_id' => $session->id,
            'user_id' => $userId,
            'screen_name' => 'home',
            'event_type' => TrackingEvent::TYPE_LOGIN_SUCCESS,
            'properties' => json_encode([ 'was_anonymous_before' => true ]),
            'time_since_session_start' => now()->diffInSeconds($session->created_at)
        ]);

        $session->save();
    }

    public function endSession(TrackingSession $session, $screen)
    {
        TrackingEvent::create(
            [
                'user_id' => $session->user_id,
                'session_id' => $session->id,
                'event_type' => TrackingEvent::TYPE_APP_CLOSE,
                'screen_name' => $screen,
                'time_since_session_start' => now()->diffInSeconds($session->created_at)
            ]
        );

        $session->update([
            'status' => TrackingSessionStatus::COMPLETED,
            'ended_at' => now(),
            'last_seen_at' => now(),
            'duration_seconds' => now()->diffInSeconds($session->created_at)
        ]);
    }

    public function startOrderFunnel(TrackingSession $session)
    {
        $funnel = TrackingOrderFunnel::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'max_step_reached' => 1,
            'total_steps' => count(TrackingOrderFunnelDefinition::steps()),
            'outcome' => TrackingOrderOutcome::IN_PROGRESS,
        ]);

        return $funnel->id;
    }

    public function saveOrderFunnelStep(
        TrackingOrderFunnel $funnel,
        $screenName,
        $step,
        $stepName,
        $completed,
        $stepData
    ) {
        $stepsData = $funnel->steps_data ?: [];

        $timeSeconds  = null;
        $previousStep = isset($stepsData[$step - 1]) ? $stepsData[$step - 1] : null;
        $session = $funnel->session;

        if ($previousStep && isset($previousStep['completed_at'])) {
            $prevTime    = Carbon::parse($previousStep['completed_at']);
            $timeSeconds = now()->diffInSeconds($prevTime);
        }

        $stepsData[$step] = [
            'name'         => $stepName,
            'started_at'   => now()->toIso8601String(),
            'completed_at' => $completed ? now()->toIso8601String() : null,
            'time_seconds' => $timeSeconds,
            'data'         => $stepData,
        ];

        $updateData = [
            'steps_data'       => $stepsData,
            'max_step_reached' => max($funnel->max_step_reached, $step),
        ];

        if (isset($stepData['from_address']))           $updateData['from_address']         = $stepData['from_address'] ?? null;
        if (isset($stepData['to_address']))             $updateData['to_address']           = $stepData['to_address'] ?? null;
        if (isset($stepData['calculated_price']))       $updateData['calculated_price']     = $stepData['calculated_price'] ?? null;
        if (isset($stepData['cargo_type_id']))          $updateData['cargo_type_id']        = $stepData['cargo_type_id'] ?? null;
        if (isset($stepData['car_type_id']))            $updateData['car_type_id']          = $stepData['car_type_id'] ?? null;
        if (isset($stepData['weight']))                 $updateData['cargo_weight']         = $stepData['weight'] ?? null;
        if (isset($stepData['has_changed_price']))      $updateData['has_changed_price']    = $stepData['has_changed_price'] ?? false;
        if (isset($stepData['changed_price']))          $updateData['changed_price']        = $stepData['changed_price'] ?? null;

        TrackingEvent::create([
            'session_id' => $session->id,
            'event_type' => TrackingEvent::TYPE_ORDER_STEP_COMPLETE,
            'user_id' => $session->user_id,
            'screen_name' => $screenName,
            'properties' => $stepData,
            'funnel_step' => $step,
            'funnel_name' => $stepName,
            'value' => $stepData['changed_price'] ?? $stepData['calculated_price'] ?? null,
            'time_since_session_start' => now()->diffInSeconds($session->created_at)
        ]);

        $funnel->update($updateData);
    }

    public function completeFunnel(
        TrackingOrderFunnel $funnel,
        $orderId,
        $value
    ) {
        if($funnel->outcome === TrackingOrderOutcome::COMPLETED) {
            return;
        }

        $session = $funnel->session;
        $stepsData = $funnel->steps_data ?: [];
        $completedAt = now();
        $previousStep = isset($stepsData[3]) ? $stepsData[3] : null;
        $timeSeconds = null;

        if ($previousStep && isset($previousStep['completed_at'])) {
            $timeSeconds = $completedAt->diffInSeconds(Carbon::parse($previousStep['completed_at']));
        }

        $stepsData[4] = [
            'name' => 'completed',
            'started_at' => $completedAt->toIso8601String(),
            'completed_at' => $completedAt->toIso8601String(),
            'time_seconds' => $timeSeconds,
            'data' => [
                'order_id' => $orderId,
                'value' => $value,
            ],
        ];

        $funnel->update([
            'outcome' => TrackingOrderOutcome::COMPLETED,
            'order_id' => $orderId,
            'steps_data' => $stepsData,
            'max_step_reached' => 4,
            'ended_at' => $completedAt,
            'total_time_seconds' => $completedAt->diffInSeconds($funnel->created_at)
        ]);

        $session->update([
            'order_id' => $orderId,
            'last_seen_at' => now(),
            'resulted_in_order' => true,
            'status' => TrackingSessionStatus::COMPLETED,
            'ended_at' => $completedAt,
            'duration_seconds' => $completedAt->diffInSeconds($session->created_at)
        ]);

        TrackingEvent::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'order_id' => $orderId,
            'event_type' => TrackingEvent::TYPE_ORDER_CREATED,
            'screen_name' => 'calculator',
            'properties' => $funnel->steps_data,
            'funnel_step' => 4,
            'funnel_name' => 'completed',
            'value' => $value,
            'time_since_session_start' => now()->diffInSeconds($session->created_at)
        ]);
    }

    public function abandonFunnel(TrackingOrderFunnel $funnel) {
        if ($funnel->outcome !== TrackingOrderOutcome::IN_PROGRESS) {
            return;
        }

        $session = $funnel->session;
        $pendingStep = $this->resolvePendingStep($funnel);
        $abandonedAt = now();

        $funnel->update([
            'outcome' => TrackingOrderOutcome::ABANDONED,
            'order_id' => null,
            'abandoned_at_step_name' => $pendingStep['name'],
            'ended_at' => $abandonedAt,
            'abandoned_at' => $abandonedAt,
            'total_time_seconds' => $abandonedAt->diffInSeconds($funnel->created_at)
        ]);

        $session->update([
            'order_id' => null,
            'last_seen_at' => now(),
            'resulted_in_order' => false,
            'status' => TrackingSessionStatus::ABANDONED,
            'ended_at' => $abandonedAt,
            'duration_seconds' => $abandonedAt->diffInSeconds($session->created_at)
        ]);

        TrackingEvent::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'order_id' => null,
            'event_type' => TrackingEvent::TYPE_ORDER_ABANDONED,
            'screen_name' => 'calculator',
            'properties' => $funnel->steps_data,
            'funnel_step' => $pendingStep['order'],
            'funnel_name' => $pendingStep['name'],
            'value' => null,
            'time_since_session_start' => now()->diffInSeconds($session->created_at)
        ]);
    }

    public function trackEvent(
        $session,
        $eventType,
        $screenName,
        $properties,
        $funnelStep,
        $funnelName,
        $orderId,
        $value
    ) {
        return TrackingEvent::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'event_type' => $eventType,
            'screen_name' => $screenName,
            'properties' => $properties,
            'funnel_step' => $funnelStep,
            'funnel_name' => $funnelName,
            'order_id' => $orderId,
            'value' => $value,
            'time_since_session_start' => now()->diffInSeconds($session->created_at)
        ]);
    }

    private function normalizePlatform($platform, $os, $userAgent)
    {
        if (in_array($platform, PlatformType::cases(), true)) {
            return $platform;
        }

        $os = mb_strtolower((string) $os);
        $userAgent = mb_strtolower((string) $userAgent);

        if (str_contains($os, 'android') || str_contains($userAgent, 'android')) {
            return PlatformType::ANDROID;
        }

        if (str_contains($os, 'ios') || str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad')) {
            return PlatformType::IOS;
        }

        return PlatformType::WEB;
    }

    private function resolvePendingStep(TrackingOrderFunnel $funnel)
    {
        $steps = TrackingOrderFunnelDefinition::steps();
        $stepsData = $funnel->steps_data ?: [];

        foreach ($steps as $step) {
            if ($step['order'] === 4) {
                return $step;
            }

            if (empty($stepsData[$step['order']]['completed_at'])) {
                return $step;
            }
        }

        return end($steps);
    }

    private function detectDeviceType(Agent $agent)
    {
        if($agent->isDesktop()) {
            return 'desktop';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        }

        return 'mobile';
    }
}
