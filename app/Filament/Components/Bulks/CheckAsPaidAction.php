<?php
    namespace App\Filament\Components\Bulks;

    use Filament\Actions\BulkAction;
    use Exception;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Facades\Log;
    use Filament\Forms\Components\Select;
    use Filament\Notifications\Notification;

class CheckAsPaidAction extends BulkAction {

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Paid'))
            ->icon('heroicon-o-document-check')
            ->requiresConfirmation()

            ->action(function (Collection $records, array $data) {
                try {
                    foreach ($records as $record) {
                        $record->update([
                            'pay' => 1,
                        ]);
                    }

                    Notification::make('Success')
                        ->title('Status updated successfully!')
                        ->success()
                        ->send();

                } catch (Exception $e) {
                    Log::error($e);
                    Notification::make('Error')
                        ->title('An error occurred during update.')
                        ->danger()
                        ->send();
                }
            });
    }
}
