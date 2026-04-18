<?php

namespace App\Http\Controllers\Api;

use App\Domain\TrackingOrderFunnel\Models\TrackingOrderFunnel;
use App\Domain\TrackingOrderFunnel\Requests\TrackingOrderFunnelStepRequest;
use App\Domain\TrackingEvent\Requests\CreateTrackingEventRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\TrackingSession\Requests\TrackingSessionRequest;
use App\Domain\TrackingSession\Requests\EndTrackingSessionRequest;
use App\Domain\TrackingSession\Models\TrackingSession;
use App\Services\UserActionTracking\UserActionTrackingService;
use App\Domain\TrackingSession\Resources\TrackingSessionResource;
use Exception;

class TrackingController extends Controller
{
    public function startSession(TrackingSessionRequest $request, UserActionTrackingService $service)
    {
        try {
            $userId = $request->input('authUser') ? $request->input('authUser')->id : null;
            $platform = $request->header('X-Platform');
            $appVersion = $request->input('device_info.app_version');
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $language = $request->header('Accept-Language');
            $screen = $request->input('screen');
            $os = $request->input('device_info.os');

            $deviceId = $request->input('device_info.device_id');
            $deviceModel = $request->input('device_info.device_model');
            $referrerUrl = $request->header('Referrer');

            //Source
            $utmSource = $request->input('utm_source');
            $utmMedium = $request->input('utm_medium');
            $utmCampaign = $request->input('utm_campaign');

            return TrackingSessionResource::make(
                $service->startSession(
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
            );
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function endSession(
        EndTrackingSessionRequest $request,
        UserActionTrackingService $service
    ) {
        try {
            $session = TrackingSession::where('session_token', $request->header('X-Session-Token', ''))->first();

            if (!$session instanceof TrackingSession) {
                return response()->json(['message' => 'Session not found']);
            }

            $service->endSession($session, $request->input('screen'));

            return response()->json(['status' => 'ok']);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function identifyUser(
        Request $request,
        UserActionTrackingService $service
    ) {
        try {
            $session = TrackingSession::where('session_token', $request->header('X-Session-Token', ''))->first();

            if(!$session instanceof TrackingSession) {
                return response()->json(['message' => 'Session not found']);
            }

            $service->identifySession($session, $request->input('authUser'));

            return response()->json(['status' => 'ok']);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function trackEvent(
        CreateTrackingEventRequest $request,
        UserActionTrackingService $service
    ) {
        try {
            $session = TrackingSession::where('session_token', $request->header('X-Session-Token', ''))->first();

            if(!$session instanceof TrackingSession) {
                return response()->json(['message' => 'Session not found']);
            }

            $eventType = $request->input('event_type');
            $screenName = $request->input('screen_name');
            $properties = $request->input('properties');
            $funnelStep = $request->input('funnel_step');
            $funnelName = $request->input('funnel_name');
            $orderId = $request->input('order_id');
            $value = $request->input('value');

            $event = $service->trackEvent(
                $session,
                $eventType,
                $screenName,
                $properties,
                $funnelStep,
                $funnelName,
                $orderId,
                $value
            );

            return response()->json(['status' => 'ok', 'event_id' => $event->id]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    
    public function startOrderFunnel(
        Request $request, 
        UserActionTrackingService $service
    ) {
        try {
            $session = TrackingSession::where('session_token', $request->header('X-Session-Token', ''))->first();

            if(!$session instanceof TrackingSession) {
                return response()->json(['status' => 'error', 'message' => 'Session not found']);
            }

            return response()->json([
                'funnel_id' => $service->startOrderFunnel($session)
            ]);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    
    public function orderFunnelStep(
        int $funnelId,
        TrackingOrderFunnelStepRequest $request,
        UserActionTrackingService $service
    ) {
        try {
            $funnel = TrackingOrderFunnel::where('id', $funnelId)
                        ->whereHas('session', function($qb) use($request) {
                            $qb->where('session_token', $request->header('X-Session-Token', ''));
                        })
                        ->first();

            if(!$funnel instanceof TrackingOrderFunnel) {
                return response()->json(['status' => 'error', 'message' => 'Order funnel not found']);
            }

            $service->saveOrderFunnelStep(
                $funnel,
                $request->input('screen_name'),
                $request->input('step'),
                $request->input('step_name'),
                $request->input('completed'),
                $request->except(['step', 'step_name', 'completed'])
            );

            return response()->json(['status' => 'success']);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    
    public function completeOrderFunnel(
        int $funnelId,
        TrackingOrderFunnelStepRequest $request,
        UserActionTrackingService $service
    )
    {
        try {
            $funnel = TrackingOrderFunnel::where('id', $funnelId)
                        ->whereHas('session', function($qb) use($request) {
                            $qb->where('session_token', $request->header('X-Session-Token', ''));
                        })
                        ->first();

            if(!$funnel instanceof TrackingOrderFunnel) {
                return response()->json(['status' => 'error', 'message' => 'Order funnel not found']);
            }

            $service->completeFunnel(
                $funnel,
                $request->input('order_id'),
                $request->input('value')
            );

            return response()->json(['status' => 'success']);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function abandonOrderFunnel(
        int $funnelId,
        TrackingOrderFunnelStepRequest $request,
        UserActionTrackingService $service
    )
    {
        try {
            $funnel = TrackingOrderFunnel::where('id', $funnelId)
                        ->whereHas('session', function($qb) use($request) {
                            $qb->where('session_token', $request->header('X-Session-Token', ''));
                        })
                        ->first();

            if(!$funnel instanceof TrackingOrderFunnel) {
                return response()->json(['status' => 'error', 'message' => 'Order funnel not found']);
            }

            $service->abandonFunnel(
                $funnel,
                // $request->input('step'),
                // $request->input('step_name'),
                // $request->input('completed'),
                // $request->input('step_data')
            );

            return response()->json(['status' => 'success']);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
