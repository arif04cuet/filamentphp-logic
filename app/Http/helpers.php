<?php

if (!function_exists('readCSV')) {
    function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }

        $columns = collect($line_of_text[0])
            ->map(fn ($column) => strtolower(str_replace(['/', ' '], '_', $column)))
            ->toArray();

        fclose($file_handle);

        return collect($line_of_text)
            ->skip(1)
            ->filter(fn ($item) => $item)
            ->map(fn ($item) => collect($item)->mapWithKeys(fn ($v, $key) => [$columns[$key] => $v]));
    }
}
