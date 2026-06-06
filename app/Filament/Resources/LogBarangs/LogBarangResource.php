<?php

namespace App\Filament\Resources\LogBarangs;

use App\Filament\Resources\LogBarangs\Pages\ListLogBarangs;
use App\Filament\Resources\LogBarangs\Pages\ViewLogBarang;
use App\Filament\Resources\LogBarangs\Schemas\LogBarangForm;
use App\Filament\Resources\LogBarangs\Schemas\LogBarangInfolist;
use App\Filament\Resources\LogBarangs\Tables\LogBarangsTable;
use App\Models\LogBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogBarangResource extends Resource
{
    protected static ?string $model = LogBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocument;

    protected static ?string $recordTitleAttribute = "id";

    protected static ?string $navigationLabel = "Log Barang";

    protected static string|\UnitEnum|null $navigationGroup = "Kelola Barang";
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LogBarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogBarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogBarangsTable::configure($table);
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
            "index" => ListLogBarangs::route("/"),
            "view" => ViewLogBarang::route("/{record}"),
        ];
    }
}
