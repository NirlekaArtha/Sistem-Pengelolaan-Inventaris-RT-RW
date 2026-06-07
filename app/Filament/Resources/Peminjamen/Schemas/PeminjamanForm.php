<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use App\Models\Barang;
use App\Models\StokBarang;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // === Informasi Peminjaman ===
            Section::make("Informasi Peminjaman")
                ->description("Isi data warga dan periode peminjaman barang")
                ->columnSpanFull()
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make("id_warga")
                            ->label("Warga Peminjam")
                            ->relationship("warga", "nama")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder("Cari nama warga...")
                            ->prefixIcon(Heroicon::OutlinedUser),

                        Hidden::make("id_admin")->default(fn() => auth()->id()),
                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make("tanggal_pinjam")
                            ->label("Tanggal Pinjam")
                            ->default(now())
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedCalendar)
                            ->displayFormat("d/m/Y"),

                        DatePicker::make("tenggat_pengembalian")
                            ->label("Tenggat Pengembalian")
                            ->required()
                            ->prefixIcon(Heroicon::OutlinedCalendarDays)
                            ->displayFormat("d/m/Y")
                            ->helperText("Batas akhir pengembalian barang"),
                    ]),
                ]),

            // === Daftar Barang Pinjaman ===
            Section::make("Daftar Barang Pinjaman")
                ->description(
                    "Tambahkan barang yang akan dipinjam beserta jumlah dan kondisinya",
                )
                ->columnSpanFull()
                ->icon(Heroicon::OutlinedArchiveBox)
                ->schema([
                    Repeater::make("detailPeminjaman")
                        ->label("")
                        ->schema(
                            fn(Schema $innerSchema) => $innerSchema->components(
                                [
                                    Grid::make(5)->schema([
                                        Select::make("id_barang")
                                            ->label("Nama Barang")
                                            ->options(
                                                Barang::query()->pluck(
                                                    "nama_barang",
                                                    "id",
                                                ),
                                            )
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->placeholder("Pilih barang...")
                                            ->afterStateUpdated(
                                                fn(
                                                    $state,
                                                    callable $set,
                                                ) => $set(
                                                    "id_stok_barang",
                                                    null,
                                                ),
                                            )
                                            ->columnSpan(2),

                                        Select::make("kondisi")
                                            ->label("Kondisi Barang")
                                            ->options([
                                                "baik" => "🟢 Baik",
                                                "rusak_ringan" =>
                                                    "🟠 Rusak Ringan",
                                                "rusak_berat" =>
                                                    "🔴 Rusak Berat",
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (
                                                $state,
                                                callable $get,
                                                callable $set,
                                            ) {
                                                $barangId = $get("id_barang");
                                                if ($barangId && $state) {
                                                    $stok = StokBarang::where(
                                                        "id_barang",
                                                        $barangId,
                                                    )
                                                        ->where(
                                                            "kondisi",
                                                            $state,
                                                        )
                                                        ->first();
                                                    if ($stok) {
                                                        $set(
                                                            "id_stok_barang",
                                                            $stok->id,
                                                        );
                                                        $set(
                                                            "stok_tersedia",
                                                            $stok->jumlah,
                                                        );
                                                    } else {
                                                        $set(
                                                            "id_stok_barang",
                                                            null,
                                                        );
                                                        $set(
                                                            "stok_tersedia",
                                                            0,
                                                        );
                                                    }
                                                } else {
                                                    $set(
                                                        "id_stok_barang",
                                                        null,
                                                    );
                                                    $set("stok_tersedia", 0);
                                                }
                                            }),

                                        Hidden::make(
                                            "id_stok_barang",
                                        )->required(),

                                        TextInput::make("stok_tersedia")
                                            ->label("Stok Tersedia")
                                            ->numeric()
                                            ->readOnly()
                                            ->placeholder("—")
                                            ->dehydrated(false)
                                            ->live()
                                            ->suffix("unit")
                                            ->helperText(
                                                "Stok yang tersedia saat ini",
                                            ),

                                        TextInput::make("jumlah")
                                            ->label("Jumlah Pinjam")
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(
                                                fn(callable $get) => (int) $get(
                                                    "stok_tersedia",
                                                ) ?:
                                                1,
                                            )
                                            ->suffix("unit")
                                            ->helperText(
                                                "Maks: sesuai stok tersedia",
                                            ),
                                    ]),
                                ],
                            ),
                        )
                        ->addActionLabel("+ Tambah Barang")
                        ->required()
                        ->minItems(1)
                        ->reorderable(false)
                        ->collapsible(false),
                ]),
        ]);
    }
}
