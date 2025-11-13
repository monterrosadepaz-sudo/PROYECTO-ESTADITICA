<?php

namespace App\Servicios;

class EstadisticaService
{
    // Series simples
    public static function mediaSimple(array $valores): float
    {
        return array_sum($valores) / count($valores);
    }

    public static function medianaSimple(array $valores): float
    {
        sort($valores);
        $n = count($valores);
        $medio = intdiv($n, 2);

        return ($n % 2 === 0)
            ? ($valores[$medio - 1] + $valores[$medio]) / 2
            : $valores[$medio];
    }

    public static function modaSimple(array $valores): array
    {
        $frecuencias = array_count_values($valores);
        $max = max($frecuencias);
        return array_keys(array_filter($frecuencias, fn($f) => $f === $max));
    }

    public static function varianzaSimple(array $valores): float
    {
        $media = self::mediaSimple($valores);
        $n = count($valores);
        $suma = array_reduce($valores, fn($carry, $x) => $carry + pow($x - $media, 2), 0);
        return $suma / $n;
    }

    // Series agrupadas
    public static function mediaAgrupada(array $clases): float
    {
        $numerador = array_sum(array_map(fn($c) => $c['pmf'], $clases));
        $denominador = array_sum(array_column($clases, 'frecuencia'));
        return $denominador > 0 ? $numerador / $denominador : 0;
    }

    public static function medianaAgrupada(array $clases): float
{
    $clases = self::frecuenciaAcumulada($clases);
    $n = array_sum(array_column($clases, 'frecuencia'));
    $n2 = $n / 2;

    foreach ($clases as $i => $clase) {
        if ($clase['frecuencia_acumulada'] >= $n2) {
            $L = $clase['lim_inf'];
            $F = $i > 0 ? $clases[$i - 1]['frecuencia_acumulada'] : 0;
            $f = $clase['frecuencia'];
            $h = $clase['lim_sup'] - $clase['lim_inf'];
            return $L + (($n2 - F) / $f) * $h;
        }
    }

    return 0;

}
    public static function modaAgrupada(array $clases):  float
{
    $modalIndex = array_keys(array_column($clases, 'frecuencia'), max(array_column($clases, 'frecuencia')))[0];
    $modal = $clases[$modalIndex];

    $f1 = $modal['frecuencia'];
    $f0 = $modalIndex > 0 ? $clases[$modalIndex - 1]['frecuencia'] : 0;
    $f2 = $modalIndex < count($clases) - 1 ? $clases[$modalIndex + 1]['frecuencia'] : 0;
    $L = $modal['lim_inf'];
    $h = $modal['lim_sup'] - $modal['lim_inf'];

    return $L + (($f1 - $f0) / (($f1 - $f0) + ($f1 - $f2))) * $h;
}
    public static function varianzaAgrupada(array $clases): float
{
    $media = self::mediaAgrupada($clases);
    $suma = array_reduce($clases, function ($carry, $clase) use ($media) {
        return $carry + $clase['frecuencia'] * pow($clase['marca'] - $media, 2);
    }, 0);

    $total = array_sum(array_column($clases, 'frecuencia'));
    return $total > 0 ? $suma / $total : 0;
}

    // Auxiliares
    public static function puntoMedio(float $limInf, float $limSup): float
    {
        return ($limInf + $limSup) / 2;
    }

    public static function frecuenciaRelativa(int $fi, int $total): float
    {
        return $total > 0 ? $fi / $total : 0;
    }

    public static function frecuenciaAcumulada(array $clases): array
    {
        $acumulada = 0;
        return array_map(function ($clase) use (&$acumulada) {
            $acumulada += $clase['frecuencia'];
            $clase['frecuencia_acumulada'] = $acumulada;
            return $clase;
        }, $clases);
    }

    public static function productoMarcaPorFrecuencia(float $marca, int $fi): float
    {
        return $marca * $fi;
    }
}

