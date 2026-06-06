<?php

namespace App\Filament\Resources\Barangs;

use App\Filament\Resources\Barangs\Pages\CreateBarang;
use App\Filament\Resources\Barangs\Pages\EditBarang;
use App\Filament\Resources\Barangs\Pages\ListBarangs;
use App\Filament\Resources\Barangs\Pages\ViewBarang;
use App\Filament\Resources\Barangs\Schemas\BarangForm;
use App\Filament\Resources\Barangs\Schemas\BarangInfolist;
use App\Filament\Resources\Barangs\Tables\BarangsTable;
use App\Models\Barang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Resources\Barangs\RelationManagers\StokBarangRelationManager;
use App\Filament\Resources\Barangs\RelationManagers\LogBarangRelationManager;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = "nama_barang";

    protected static ?string $navigationLabel = "Barang";

    protected static string|\UnitEnum|null $navigationGroup = "Kelola Barang";
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $hasActiveLoan = \App\Models\DetailPeminjaman::whereHas('peminjaman', function ($query) {
            $query->whereIn('status', [
                \App\Enums\StatusPeminjaman::DIPINJAM,
                \App\Enums\StatusPeminjaman::TERLAMBAT,
            ]);
        })->whereIn('id_stok_barang', $record->stokBarang->pluck('id'))->exists();

        return !$hasActiveLoan;
    }

    public static function getRelations(): array
    {
        return [
            StokBarangRelationManager::class,
            LogBarangRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => ListBarangs::route("/"),
            "create" => CreateBarang::route("/create"),
            "view" => ViewBarang::route("/{record}"),
            "edit" => EditBarang::route("/{record}/edit"),
        ];
    }
}
