<?php

namespace Modules\Cms\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Place\Models\Booking;
use Modules\Place\Models\Feedback;
use Modules\Place\Models\Guest;

class ApiController extends Controller
{
    public function reservation(Request $request)
    {

        $validated = $request->validate([
            'place_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'time' => 'required|date',
            'person' => 'required|integer|min:1',
            'comment' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);

            if (str_starts_with($cleanPhone, '0')) {
                $formattedPhone = '+38'.$cleanPhone;
            } elseif (str_starts_with($cleanPhone, '380')) {
                $formattedPhone = '+'.$cleanPhone;
            } else {
                $formattedPhone = '+'.$cleanPhone;
            }

            $guest = Guest::updateOrCreate(
                ['phone' => $formattedPhone],
                [
                    'name' => $validated['name'],
                    'place_id' => $validated['place_id'],
                ]
            );

            // 4. Создание резерва с полученным guest_id
            $reservation = Booking::create([
                'user_id' => 1,
                'guest_id' => $guest->id,
                'place_id' => $validated['place_id'],
                'time' => $validated['time'],
                'person' => $validated['person'],
                'comment' => $validated['comment'] ?? null,
                'complete' => 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Резерв успешно создан',
                'data' => $reservation->load('guest'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка создания резерва: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении',
            ], 500);
        }

    }

    public function review(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'contact' => 'required|string',
            'text' => 'required|string',
            'place_id' => 'required|exists:places,id',
        ]);

        Feedback::create($validated);

        return response()->json(['message' => 'Success'], 200);
    }
}
