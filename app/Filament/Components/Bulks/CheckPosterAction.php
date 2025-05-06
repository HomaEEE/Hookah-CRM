<?php
    namespace App\Filament\Components\Bulks;

    use Filament\Tables\Actions\BulkAction;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Facades\Log;
    use Filament\Forms\Components\Select;
    use Filament\Notifications\Notification;

class CheckPosterAction extends BulkAction {

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Poster'))
            ->icon('heroicon-o-arrow-down-circle')
            ->color('info')
            ->requiresConfirmation()

            ->action(function (Collection $records, array $data) {
                try {
                    foreach ($records as $record) {
                        $record->update([
                            'poster' => 1,
                        ]);
                    }

                    Notification::make('Success')
                        ->title('Status updated successfully!')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Log::error($e);
                    Notification::make('Error')
                        ->title('An error occurred during update.')
                        ->danger()
                        ->send();
                }
            });
    }
}
