<?php

namespace App\Services;

use App\Repositories\Contracts\IEventImageRepository;
use App\Repositories\Contracts\IEventRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    protected $maxEventImages = 10;

    public function __construct(
        protected IEventImageRepository $eventImageRepository,
        protected IEventRepository $eventRepository
    ) {
    }

    public function uploadEventImage($eventId, $userId, UploadedFile $image)
    {
        $event = $this->eventRepository->find($eventId);
        if (!in_array($event->status, ['active', 'completed'])) {
            return [
                'status' => false,
                'message' => 'Jarayonda bo\'lgan yoki tugagan tadbirlarga rasm yuklay olmaysiz'
            ];
        }

        $isCreator = $event->user_id === $userId;
        $isParticipant = $event->participants()->where('user_id', $userId)->exists();

        if (!$isCreator && !$isParticipant) {
            return [
                'status' => false,
                'message' => 'Rasm yuklash uchun tadbir qatnashuvchisi yoki egasi bo\'lishingiz kerak'
            ];
        }

        $currentImageCount = $this->eventImageRepository->countEventImages($eventId);
        if ($currentImageCount >= $this->maxEventImages) {
            return [
                'status' => false,
                'message' => "Bu tadbir uchun maksimal yuklanadigan rasmlar soni {$this->maxEventImages} ta"
            ];
        }

        $path = $image->store('event_images', 'public');

        $eventImage = $this->eventImageRepository->create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'image_path' => $path
        ]);

        return [
            'status' => true,
            'message' => 'Rasm yuklandi',
            'data' => $eventImage
        ];
    }

    public function deleteEventImage($imageId, $userId)
    {
        $image = $this->eventImageRepository->find($imageId);

        $event = $this->eventRepository->find($image->event_id);
        $isUploader = $image->user_id === $userId;
        $isEventCreator = $event->user_id === $userId;

        if (!$isUploader && !$isEventCreator) {
            return [
                'status' => false,
                'message' => 'Bu rasmni o\'chirish uchun sizda ruxsat yo\'q'
            ];
        }

        Storage::disk('public')->delete($image->image_path);

        $this->eventImageRepository->delete($imageId);

        return [
            'status' => true,
            'message' => 'Rasm o\'chirildi'
        ];
    }

    public function getEventImages($eventId)
    {
        return $this->eventImageRepository->getEventImages($eventId);
    }

    public function getUserUploadedImages($userId)
    {
        return $this->eventImageRepository->getUserUploadedImages($userId);
    }
}
