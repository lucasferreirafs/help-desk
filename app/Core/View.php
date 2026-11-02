<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

final class View
{
    private const BASE_PATH = __DIR__ . '/../../resources/views/';

    public static function render(
        string $page,
        array $data = [],
        string $layout = 'main'
    ): void {
        $content = self::renderFile(
            'pages/' . $page,
            $data
        );

        $layoutData = $data;

        $layoutData['content'] = $content;

        $html = self::renderFile(
            'layouts/' . $layout,
            $layoutData
        );

        echo $html;
    }

    public static function component(
        string $component,
        array $data = []
    ): string {
        return self::renderFile(
            'components/' . $component,
            $data
        );
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars(
            $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    private static function renderFile(
        string $name,
        array $data
    ): string {
        $file = self::resolvePath($name);

        $bufferLevel = ob_get_level();

        try {
            if (!ob_start()) {
                throw new RuntimeException(
                    'Não foi possível iniciar a renderização.'
                );
            }

            (static function (string $__viewFile, array $__viewData): void {
                extract($__viewData, EXTR_SKIP);
                require $__viewFile;
            })($file, $data);

            $html = ob_get_clean();

            if($html === false) {
                throw new RuntimeException(
                    'Não foi possível recuperar o HTML da view.'
                );
            }

            return $html;
        } catch (Throwable $exception) {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            throw new RuntimeException(
                "Não foi possível renderizar a view: {$name}",
                0,
                $exception
            );
        }
    }

    private static function resolvePath(string $name): string
    {
        if (
            preg_match(
                '~\A[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*\z~',
                $name
            ) !== 1
        ) {
            throw new RuntimeException(
                'The name provided for the view is invalid.'
            );
        }

        $file = self::BASE_PATH . $name . '.php';

        if (!is_file($file) || !is_readable($file)) {
            throw new RuntimeException(
                "The view does not exist or cannot be read: {$name}.</p>"
            );
        }

        return $file;
    }
}