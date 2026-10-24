<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreQualificationRequest;
use App\Http\Requests\Profile\UpdatePreferencesRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\CandidateProfileResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\CandidatePreference;
use App\Models\CandidateProfile;
use App\Models\CandidateQualification;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private AuditService $audit) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(
            'candidateProfile.profession',
            'candidateProfile.specialty',
            'candidatePreferences',
            'qualifications'
        );

        return $this->success([
            'user'            => new UserResource($user),
            'profile'         => $user->candidateProfile
                ? new CandidateProfileResource($user->candidateProfile)
                : null,
            'preferences'     => $user->candidatePreferences,
            'qualifications'  => $user->qualifications,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        // Update user base fields
        $user->update($request->only('name', 'phone', 'country'));

        // Update or create candidate profile
        $profileData = $request->only([
            'profession_id', 'specialty_id', 'current_country', 'uk_location_preference',
            'experience_years', 'registration_body', 'registration_status',
            'uk_registration_status', 'sponsorship_required', 'nhs_band_preference',
            'professional_summary',
        ]);

        $profile = CandidateProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        $this->audit->log('profile.updated', $user->id, $user);

        return $this->success([
            'user'    => new UserResource($user),
            'profile' => new CandidateProfileResource($profile->load('profession', 'specialty')),
        ], 'Profile updated');
    }

    public function updatePreferences(UpdatePreferencesRequest $request): JsonResponse
    {
        $prefs = CandidatePreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        return $this->success($prefs, 'Preferences updated');
    }

    public function storeQualification(StoreQualificationRequest $request): JsonResponse
    {
        $qual = CandidateQualification::create(
            array_merge($request->validated(), ['user_id' => $request->user()->id])
        );

        return $this->created($qual, 'Qualification added');
    }

    public function destroyQualification(Request $request, CandidateQualification $qualification): JsonResponse
    {
        if ($qualification->user_id !== $request->user()->id) {
            return $this->error('Unauthorised', 403);
        }

        $qualification->delete();
        return $this->noContent();
    }
}
