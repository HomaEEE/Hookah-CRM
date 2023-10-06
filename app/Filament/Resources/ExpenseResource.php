<?php

namespace App\Filament\Resources;

use App\Enums\Bill as BillEnum;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\{Expense, ExpenseCategory, Supply};
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';

    protected static ?string $navigationGroup = 'Finance';

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category', 'user', 'supply');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('user_id')->default(Auth::user()->id),
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                DateTimePicker::make('added_at')
                                    ->label(__('Date Added'))
                                    ->native(false)
                                    ->maxDate(now())->default(now()),
                                Select::make('category_id')->label(__('Category'))
                                    ->relationship('categories', 'name')
                                    ->hidden(fn (Get $get) => $get('place_id') == null)
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        if (null == ($get('place_id'))) {
                                            return null;
                                        } else {
                                            return ExpenseCategory::where('place_id', $get('place_id'))->pluck('name', 'id');
                                        }
                                    })
                                    ->createOptionForm([
                                        Section::make()
                                            ->schema([
                                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                                TextInput::make('name')->translateLabel(),
                                                TextInput::make('category_id')->translateLabel()->default(0),
                                            ]),
                                    ]),
                                TextInput::make('sum')->label(__('Sum'))
                                    ->numeric()
                                    ->prefix('₴')
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                                Select::make('type_pay')->label(__('Type pay'))
                                    ->default(0)
                                    ->options([
                                        0 => __('Cash'),
                                        1 => __('Card')
                                    ])
                                    ->required(),
                                Textarea::make('comment')->translateLabel()->rows(2),
                            ]),
                    ]),

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('invoice')
                                    ->collection('supplies')
                                    ->multiple()
                                    ->imageResizeTargetWidth('1600')
                                    ->imageResizeMode('cover')
                                    ->reorderable()
                                    ->openable()
                                    ->imagePreviewHeight('250')
                                    ->loadingIndicatorPosition('left')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->uploadButtonPosition('left')
                                    ->uploadProgressIndicatorPosition('left')
                            ]),
                    ]),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('md')
                    ->badge()
                    ->description(fn (Expense $record): string => $record->user->name ?? ''),

                TextColumn::make('category.name')->label(__('Category'))
                    ->badge(),

                TextColumn::make('supply_id')->badge()->label(__('Supply'))
                    ->wrap()
                    ->description(fn (Expense $record): string => $record->supply->supplier->company ?? ''),

                TextColumn::make('sum')->label(__('Sum'))->money('uah', true)
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),
                TextColumn::make('type_pay')->translateLabel()->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            '0' => __('Cash'),
                            '1' => __('Card'),
                        }
                    ),

                IconColumn::make('pay')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name,
                        ]);
                    }),
                IconColumn::make('poster')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name,
                        ]);
                    }),

            ])->defaultSort('added_at', 'desc')


            ->filters([
                SelectFilter::make('Place')
                    ->label(__('Place'))
                    ->form([
                        Select::make('category_id')->label(__('Category'))
                            ->searchable()
                            ->preload()
                            ->options(
                                ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')
                            ),
                    ])
                    ->query(
                        function (Builder $query, array $data) {
                            $place_id = (int) Filament::getTenant()->id;
                            $category_id = (int) $data['category_id'];

                            if (!empty($category_id)) {
                                $query->where('category_id', '=', $category_id);
                            }
                        }
                    ),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->subMonth()->format('d/m/Y')),
                        DatePicker::make('created_until')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->format('d/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Расходы с ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Расходы до ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
                SelectFilter::make('type_pay')->translateLabel()
                    ->options(
                        [
                            0 => __('Cash'),
                            1 => __('Card')
                        ]
                    )
                    ->attribute('type_pay'),

                TernaryFilter::make('pay')
                    ->translateLabel()
                    ->nullable()
                    ->placeholder(__('All'))
                    ->trueLabel(__('Paid'))
                    ->falseLabel(__('Not Pay')),

                TernaryFilter::make('poster')
                    ->translateLabel()
                    ->nullable()
                    ->placeholder(__('All'))
                    ->falseLabel(__('In Poster'))
                    ->trueLabel(__('Not in Poster')),


                // TernaryFilter::make('pay')->label(__('Pay'))
                //     ->placeholder((__('All')))
                //     ->trueLabel(__('Paid'))
                //     ->falseLabel(__('Not Pay'))
                //     ->queries(
                //         true: fn (Builder $query) => $query->Paid(),
                //         false: fn (Builder $query) => $query->NotPaid(),
                //         blank: fn (Builder $query) => $query->AllPaid(),
                //     ),
                // TernaryFilter::make('poster')->translateLabel()
                //     ->placeholder((__('All')))
                //     ->trueLabel(__('In Poster'))
                //     ->falseLabel(__('Not in Poster'))
                //     ->queries(
                //         true: fn (Builder $query) => $query->InPoster(),
                //         false: fn (Builder $query) => $query->NotPoster(),
                //         blank: fn (Builder $query) => $query->AllPoster(),
                //     ),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('added_at')->translateLabel(),
                TextEntry::make('place.city')->translateLabel(),
                TextEntry::make('type_pay')->translateLabel(),
                TextEntry::make('media')->translateLabel()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExpenses::route('/'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            App\Filament\Widgets\ExpenseChart::class
        ];
    }

    public function updateCategories(array $data): void
    {
        if (blank($data['place_id'])) {
            $this->notify('danger', __('Please, select place'));
        } else {
            (new PosterApi())->getTransatcionCategories($data['place_id']);
            $this->notify('success', __('Menu updated'));
        }
    }
}
