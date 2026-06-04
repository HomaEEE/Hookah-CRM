<?php

namespace Modules\Cms\app\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Hero extends Brick
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public static function getLabel(): string
    {
        return 'Hero Block';
    }

    public static function getPreviewLabel(array $config): string
    {
        return $config['title'] ?? static::getLabel();
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return view('mason.hero.preview', [
            'config' => $config,
        ])->render();
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.hero.index', [
            'config' => $config,
            'data' => $data,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок'),
                TextInput::make('names')
                    ->label('Имена')
                    ->required(),
                FileUpload::make('video_url')
                    ->label('Видео (фоновое)')
                    ->disk('public')
                    ->directory('media/hero'),
                FileUpload::make('bg_url')
                    ->label('Фото (фоновое)')
                    ->disk('public')
                    ->directory('media/hero'),
                DatePicker::make('date')->native(false)
                    ->label('Дата'),
            ]);
    }
}
