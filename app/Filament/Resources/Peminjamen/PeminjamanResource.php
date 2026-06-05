<?php

namespace App\Filament\Resources\Peminjamen;

use App\Filament\Resources\Peminjamen\Pages\CreatePeminjaman;
use App\Filament\Resources\Peminjamen\Pages\EditPeminjaman;
use App\Filament\Resources\Peminjamen\Pages\ListPeminjamen;
use App\Filament\Resources\Peminjamen\Pages\ViewPeminjaman;
use App\Filament\Resources\Peminjamen\Schemas\PeminjamanForm;
use App\Filament\Resources\Peminjamen\Schemas\PeminjamanInfolist;
use App\Filament\Resources\Peminjamen\Tables\PeminjamenTable;
use App\Models\Peminjaman;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $recordTitleAttribute = "id";

    protected static ?string $navigationLabel = "Peminjaman";

    protected static string|\UnitEnum|null $navigationGroup = "Kelola Peminjaman";
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PeminjamanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PeminjamanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeminjamenTable::configure($table);
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
            "index" => ListPeminjamen::route("/"),
            "create" => CreatePeminjaman::route("/create"),
            "view" => ViewPeminjaman::route("/{record}"),
            "edit" => EditPeminjaman::route("/{record}/edit"),
        ];
    }
}
