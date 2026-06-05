<?php

namespace App\Filament\Resources\Wargas;

use App\Filament\Resources\Wargas\Pages\CreateWarga;
use App\Filament\Resources\Wargas\Pages\EditWarga;
use App\Filament\Resources\Wargas\Pages\ListWargas;
use App\Filament\Resources\Wargas\Pages\ViewWarga;
use App\Filament\Resources\Wargas\Schemas\WargaForm;
use App\Filament\Resources\Wargas\Schemas\WargaInfolist;
use App\Filament\Resources\Wargas\Tables\WargasTable;
use App\Models\Warga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WargaResource extends Resource
{
    protected static ?string $model = Warga::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = "nama";

    protected static ?string $navigationLabel = "Warga";

    protected static string|\UnitEnum|null $navigationGroup = "Kelola Warga";
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return WargaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WargaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WargasTable::configure($table);
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
            "index" => ListWargas::route("/"),
            "create" => CreateWarga::route("/create"),
            "view" => ViewWarga::route("/{record}"),
            "edit" => EditWarga::route("/{record}/edit"),
        ];
    }
}
