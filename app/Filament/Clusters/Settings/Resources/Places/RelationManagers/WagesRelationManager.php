<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use App\Models\Workshop;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WagesRelationManager extends RelationManager
{
    protected static string $relationship = 'wages';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return __('Wages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Wages');
    }

    public function form(Schema $schema): Schema
    {

        return $schema
            ->components([
                Hidden::make('place_id')->label(__('Place'))
                    ->default(Filament::getTenant()->id),

                TextInput::make('name')->translateLabel()
                    ->required()
                    ->columnSpan(1)
                    ->maxLength(255),

                TextInput::make('wage')->translateLabel()
                    ->required()
                    ->columnSpan(1)
                    ->numeric()
                    ->maxLength(5),

                Repeater::make('procent')
                    ->translateLabel()
                    ->schema([
                        Select::make('workshop_id')->label(__('Workshop'))
                            ->native(false)
                            ->options(Workshop::where('place_id', $this->ownerRecord->id)->get()->pluck('name', 'workshop_id')),
                        TextInput::make('num')->translateLabel()->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                    ])->columns(2)->columnSpanFull(),

                TextInput::make('procent')
                    ->visible(fn (Get $get) => $get('type') == '2')
                    ->translateLabel(),
            ])->columns(2);

    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('wage')->translateLabel()->badge(),
                TextColumn::make('procent')->translateLabel()->badge()
                    ->badge()
                    ->html() // Разрешаем HTML, чтобы можно было сделать перенос строк <br>
                    ->getStateUsing(function (Model $record) {
                        $raw = $record->procent;

                        // Превращаем данные в массив
                        $data = is_string($raw) ? json_decode($raw, true) : json_decode(json_encode($raw), true);

                        // Если данных нет, выводим заглушку
                        if (! is_array($data) || empty($data)) {
                            return 'Нет данных';
                        }

                        // Если это вдруг один объект, а не массив объектов — оборачиваем его в массив для универсальности
                        if (isset($data['workshop_id'])) {
                            $data = [$data];
                        }

                        $results = [];

                        // Проходимся по всем элементам массива
                        foreach ($data as $item) {
                            $workshopId = $item['workshop_id'] ?? null;
                            $num = $item['num'] ?? 0;

                            // Если у вас num = 0.6, раскомментируйте умножение на 100.
                            // Если num = 60, оставляем как есть.
                            $percent = $num; // $num * 100;

                            $workshopName = 'Не указано';
                            if ($workshopId) {
                                // Ищем название цеха
                                $workshopName = Workshop::query()
                                    ->where('id', $workshopId)
                                    ->value('name') ?? 'Неизвестно';
                            }

                            // Добавляем результат в список
                            $results[] = "{$workshopName}: {$percent}%";
                        }

                        // Объединяем все цехи через запятую или перенос строки <br>
                        return implode('<br>', $results);
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
