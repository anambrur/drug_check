<?php

namespace App\Enums;

enum QuestDocType: string
{
    case QPassport = 'QPassport';
    case LabReport = 'LabReport';
    case MROLetter = 'MROLetter';
    case Copy1 = 'Copy1';
    case Copy2 = 'Copy2';
    case ATF = 'ATF';
    case AlcoholReport = 'AlcoholReport';
    case OHS = 'OHS';

    public function label(): string
    {
        return match ($this) {
            self::QPassport => 'QPassport',
            self::LabReport => 'Lab Report',
            self::MROLetter => 'MRO Letter',
            self::Copy1 => 'Copy 1 (CCF)',
            self::Copy2 => 'Copy 2 (CCF)',
            self::ATF => 'ATF',
            self::AlcoholReport => 'Alcohol Report',
            self::OHS => 'OHS Report',
        };
    }

    public function formatHint(): string
    {
        return match ($this) {
            self::Copy1, self::Copy2 => 'TIF',
            default => 'PDF',
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public static function resultDocTypeForScreen(string $screenType): self
    {
        return match (strtolower($screenType)) {
            'alcohol' => self::AlcoholReport,
            'physical' => self::OHS,
            default => self::LabReport,
        };
    }

    public static function fallbackDocTypeForScreen(string $screenType): ?self
    {
        return match (strtolower($screenType)) {
            'drug' => self::MROLetter,
            'alcohol' => self::ATF,
            default => null,
        };
    }
}
