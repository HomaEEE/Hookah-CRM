<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\User;
use Auth;
use Filament\Actions\ActionGroup;
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPluralModelLabel(): string
    {
        return __('Tasks');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tasks');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }


    public static function getNavigationBadge(): ?string
    {
        return Task::query()->whereIn('place_id', Auth::user()->place->pluck('id'))
        ->where(function ($query) {
            $query->where('for_user_id', Auth::user()->id)->where('approve', 0)
                ->orWhere('for_user_id', null);
        })->count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Select::make('place_id')->label(__('Place'))
                    ->options(
                        Auth::user()->place->pluck('nameCity', 'id')
                    )
                    ->afterStateUpdated(fn (callable $set) => $set('for_user_id', null))
                    ->required()
                    ->reactive(),
                Select::make('for_user_id')->label(__('For User'))
                    ->hidden(fn (Get $get) => $get('place_id') == null)
                    ->options(function (callable $get) {
                        return User::whereHas('places', function ($query) use ($get) {
                            $query->where('place_id', $get('place_id'));
                        })->get()
                            ->pluck('name', 'id');
                    })
                    ->allowHtml()
                    ->searchable(),
                TextInput::make('name')->label(__('Task')),
                RichEditor::make('text')->label(__('Task text')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('place.nameCity')->label(__('Place')),
                TextColumn::make('name')->label(__('Task')),
                TextColumn::make('forUser.name')->label(__('For User')),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('opened')->label('Открытые задачи')
                    ->query(fn (Builder $query): Builder => $query->where('approve', false)),
            ])
            ->actions([
                Action::make('approve')->button()->label(__(''))
                ->icon('heroicon-s-check-circle')
                ->color('success')
                ->action(function (Task $record): void {
                    $record->update(
                        [
                            'approve' => 1,
                        ]
                    );
                    $record->save();
                })->visible(fn (Task $record) => $record->approve == 0),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->visible(fn (Task $record) =>$record->approve == 0),
                    Tables\Actions\DeleteAction::make()->visible(fn (Task $record) => $record->approve == 0)
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('place.nameCity')->label(__('Place')),
            TextEntry::make('forUser.name')->label(__('For User')),
            TextEntry::make('text')->translateLabel()
            ->html(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTasks::route('/'),
        ];
    }

}
