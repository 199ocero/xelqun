<?php

namespace App\Demo;

use Xelqun\Panels\Support\Colors\Color;
use Xelqun\Panels\Support\Preset;

/**
 * What a visitor picked for their own session. Grays and colors are kept apart,
 * because a colored gray makes every surface of the panel look wrong.
 */
final class DemoAppearance
{
    private const string DEFAULT_PRIMARY = '#0071e3';

    private const array GRAYS = [Color::Slate, Color::Gray, Color::Zinc, Color::Neutral, Color::Stone, Color::Taupe, Color::Mauve, Color::Mist, Color::Olive];

    /**
     * @return array{primary: Color|string, gray: Color}
     */
    public static function colors(): array
    {
        return [
            'primary' => Color::fromName(session('demo.primary')) ?? self::DEFAULT_PRIMARY,
            'gray' => Color::fromName(session('demo.gray')) ?? Color::Zinc,
        ];
    }

    public static function preset(): Preset
    {
        return Preset::tryFrom((string) session('demo.preset')) ?? Preset::Default;
    }

    /**
     * The logo, painted in the primary color the visitor picked.
     */
    public static function logoUrl(string $theme): string
    {
        $primary = self::colors()['primary'];
        $hex = $primary instanceof Color ? $primary->value : $primary;

        return route('demo.logo', ['theme' => $theme, 'color' => strtolower(ltrim($hex, '#'))], false);
    }

    /**
     * @return list<string>
     */
    public static function grayNames(): array
    {
        return array_map(fn (Color $color): string => $color->name, self::GRAYS);
    }

    /**
     * @return list<string>
     */
    public static function primaryNames(): array
    {
        return array_values(array_diff(array_keys(Color::options()), self::grayNames()));
    }

    /**
     * @return array{preset: string, presets: list<string>, primary: string|null, gray: string, primaries: array<string, string>, grays: array<string, string>, defaultPrimary: string, updateUrl: string}
     */
    public static function props(): array
    {
        $swatches = fn (array $names): array => array_combine($names, array_map(fn (string $name): string => (string) Color::fromName($name)?->value, $names));

        return [
            'preset' => self::preset()->value,
            'presets' => array_column(Preset::cases(), 'value'),
            'primary' => session('demo.primary'),
            'gray' => session('demo.gray', Color::Zinc->name),
            'primaries' => $swatches(self::primaryNames()),
            'grays' => $swatches(self::grayNames()),
            'defaultPrimary' => self::DEFAULT_PRIMARY,
            'updateUrl' => route('demo.appearance'),
        ];
    }
}
