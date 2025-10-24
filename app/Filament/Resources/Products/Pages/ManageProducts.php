<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Widgets\ProductWidget;
use App\Http\Controllers\PosterApiController;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('update')
                ->label(__('Update menu'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    (new PosterApiController)->getCategories(Filament::getTenant()->id);
                    (new PosterApiController)->getProducts(Filament::getTenant()->id);
                    Notification::make()
                        ->title(__('Updated successfully'))
                        ->icon('heroicon-o-qr-code')
                        ->iconColor('success')
                        ->send();
                }),
            ExportAction::make()
                ->exporter(ProductExporter::class),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductWidget::class,
        ];
    }

    // public function getTabs(): array
    // {
    //     $tabs = [null => Tab::make(__('All'))];

    //     $categories = Category::where('parent_category', 0)->get();

    //     foreach ($categories as $category) {
    //         $tabs[__($category->category_name)] = Tab::make()->query(function ($query) use ($category) {
    //             $query->where('menu_category_id', $category->category_id)
    //             ;
    //         })->badge(Product::query()->where('menu_category_id', $category->category_id)->count());
    //     }

    //     return $tabs;
    // }
}
