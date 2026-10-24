<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Traits\ApiResponse;
use App\Models\AdvisorAvailability;
use App\Models\AdvisorMessage;
use App\Models\Appointment;
use App\Models\CareerAdvisor;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvisorController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuditService $audit,
        private NotificationService $notifications
    ) {}

    public function index(): JsonResponse
    {
        $advisors = CareerAdvisor::where('active', true)
            ->with('user')
            ->get()
            ->map(fn($a) => [
                'id'           => $a->id,
                'display_name' => $a->display_name,
                'specialisms'  => $a->specialisms,
                'bio'          => $a->bio,
                'photo_path'   => $a->photo_path,
            ]);

        return $this->success($advisors);
    }

    public function availability(CareerAdvisor $advisor): JsonResponse
    {
        $slots = AdvisorAvailability::where('advisor_id', $advisor->id)
            ->where('booked', false)
            ->where('available_date', '>=', now()->toDateString())
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get(['id', 'available_date', 'start_time', 'end_time']);

        return $this->success($slots);
    }

    public function appointments(Request $request): JsonResponse
    {
        $appointments = Appointment::where('user_id', $request->user()->id)
            ->with(['advisor', 'availability'])
            ->orderByDesc('booked_at')
            ->get();

        return $this->success(AppointmentResource::collection($appointments));
    }

    public function book(StoreAppointmentRequest $request): JsonResponse
    {
        $slot = AdvisorAvailability::findOrFail($request->availability_id);

        if ($slot->booked) {
            return $this->error('This slot is no longer available.', 422);
        }

        $appointment = Appointment::create([
            'user_id'         => $request->user()->id,
            'advisor_id'      => $request->advisor_id,
            'availability_id' => $request->availability_id,
            'topic'           => $request->topic,
            'notes'           => $request->notes,
            'status'          => 'confirmed',
            'booked_at'       => now(),
        ]);

        $slot->update(['booked' => true]);

        $this->notifications->send(
            $request->user(),
            'appointment_reminder',
            ['appointment_id' => $appointment->id, 'topic' => $appointment->topic]
        );

        $this->audit->log('appointment.booked', $request->user()->id, $appointment);

        return $this->created(
            new AppointmentResource($appointment->load('advisor', 'availability'))
        );
    }

    public function showAppointment(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return $this->success(new AppointmentResource(
            $appointment->load('advisor', 'availability')
        ));
    }

    public function messages(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        $messages = $appointment->messages()->with('sender:id,name')->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'message'     => $m->message,
                'sender_name' => $m->sender?->name,
                'sent_at'     => $m->sent_at?->toISOString(),
                'read'        => $m->read,
            ]);

        return $this->success($messages);
    }

    public function sendMessage(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $message = AdvisorMessage::create([
            'appointment_id' => $appointment->id,
            'sender_id'      => $request->user()->id,
            'message'        => $request->message,
            'sent_at'        => now(),
        ]);

        return $this->created([
            'id'      => $message->id,
            'sent_at' => $message->sent_at->toISOString(),
        ]);
    }
}
