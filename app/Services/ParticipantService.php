<?php

namespace App\Services;

use App\Repositories\Contracts\IEventParticipantRepository;
use App\Repositories\Contracts\IEventRepository;
use App\Repositories\Contracts\IUserFineRepository;
use App\Repositories\Contracts\IUserRepository;

class ParticipantService
{

    public function __construct(
        protected IEventRepository $eventRepository,
        protected IEventParticipantRepository $participantRepository,
        protected IUserFineRepository $userFineRepository,
        protected IUserRepository $userRepository
    ) {
    }

    public function voteForEvent($eventId, $userId)
    {
        if ($this->userRepository->checkIfUserIsFined($userId)) {
            return [
                'status' => false,
                'message' => 'Sizga jarima qo`yilgan, shu sababli tadbirlarga qatnashishingiz vaqtincha mumkin emas'
            ];
        }

        $event = $this->eventRepository->find($eventId);
        if (!$event->isVotingOpen()) {
            return [
                'status' => false,
                'message' => 'Bu tadbirga ovoz berish muddati tugagan'
            ];
        }

        $participation = $this->participantRepository->getParticipant($eventId, $userId);
        if ($participation) {
            return [
                'status' => false,
                'message' => 'Bu tadbirga ovoz bergansiz'
            ];
        }

        if ($event->participantsCount() >= $event->max_participants) {
            return [
                'status' => false,
                'message' => 'Bu tadbir uchun maksimal qatnashchilar soniga yetib keldik'
            ];
        }

        $this->participantRepository->create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'status' => 'voted'
        ]);

        return [
            'status' => true,
            'message' => 'Siz bu tadbirga ovoz berdingiz'
        ];
    }
    public function cancelVote($eventId, $userId)
    {
        $event = $this->eventRepository->find($eventId);
        if (!$event->isVotingOpen()) {
            return [
                'status' => false,
                'message' => 'Bu tadbirga ovoz berish muddati tugagan'
            ];
        }

        $participation = $this->participantRepository->getParticipant($eventId, $userId);
        if (!$participation) {
            return [
                'status' => false,
                'message' => 'Bu tadbirga ovoz bermagansiz'
            ];
        }

        // Delete participation
        $this->participantRepository->deleteParticipation($eventId, $userId);

        return [
            'status' => true,
            'message' => 'Tadbirga bergan ovozingiz bekor qilindi'
        ];
    }

    public function markAttendance($participantId, $status)
    {
        if ($status === 'attended') {
            return $this->participantRepository->markAsAttended($participantId);
        } elseif ($status === 'no_show') {
            $participant = $this->participantRepository->markAsNoShow($participantId);

            $this->userFineRepository->createFineForNoShow(
                $participant->user_id,
                $participant->event_id
            );

            $fineDuration = $this->userFineRepository->checkActiveFineDuration($participant->user_id);
            $this->userRepository->update(['fine_until' => $fineDuration], $participant->user_id);

            return $participant;
        }

        return false;
    }

    public function getEventParticipants($eventId)
    {
        return $this->participantRepository->getEventParticipants($eventId);
    }

    public function getUserParticipations($userId)
    {
        return $this->participantRepository->getUserParticipations($userId);
    }
}
