<?php

namespace App\Filament\Resources\Dendas;

use App\Filament\Resources\Dendas\Pages\ListDendas;
use App\Filament\Resources\Dendas\Schemas\DendaForm;
use App\Filament\Resources\Dendas\Tables\DendasTable;
use App\Models\Denda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DendaResource extends Resource
{
    protected static ?string $model = Denda::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = "Denda";

    protected static string|\UnitEnum|null $navigationGroup = "Kelola Peminjaman";
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DendaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DendasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDendas::route('/'),
        ];
    }
}
