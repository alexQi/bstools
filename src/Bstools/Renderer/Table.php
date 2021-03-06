<?php

namespace Bstools\Renderer;

class Table implements RendererInterface
{
    public function render(array $statsData)
    {
        $columns = array();
        $columnWidths = array();
        $output = "";

        foreach ($statsData as $data) {
            foreach ($data as $key => $val) {
                $columns[] = $key;
            }
        }
        $columns = array_unique($columns);
        sort($columns);

        $rows = array_keys($statsData);

        $columnWidths["rows"] = $this->getMaxLength($rows);
        foreach ($columns as $column) {
            $tempValues = array();
            $tempValues[] = $column;
            foreach ($statsData as $tube => $data) {
                $tempValues[] = $data[$column];
            }
            $columnWidths[$column] = $this->getMaxLength($tempValues);
        }

        $dividerLine = str_repeat('-', array_sum($columnWidths) + 4 +  (3 * count($columns))) . "\n";
        $output .= $dividerLine;

        //output header row
        $output .= "| ";
        $cols[] = str_repeat(' ', $columnWidths["rows"]);
        foreach ($columns as $col) {
            $cols[] = '<info>' . str_pad($col, $columnWidths[$col], ' ') . '</info>';
        }
        $output .= implode(' | ', $cols);
        $output .= " |\n";
        $output .= $dividerLine;

        //output rows
        foreach ($statsData as $tube => $data) {
            $output .= "| ";
            $output .= str_pad($tube, $columnWidths["rows"], ' ', STR_PAD_LEFT);
            $output .= " | ";
            $cols = array();
            foreach ($columns as $col) {
                $str = str_pad($data[$col], $columnWidths[$col], ' ');
                if ($col == 'current-jobs-buried' && $data[$col] != 0) {
                    $str = "<error>$str</error>";
                } else if ($data[$col] != 0) {
                    $str = "<comment>$str</comment>";
                }
                $cols[] = $str;
            }
            $output .= implode(" | ", $cols);
            $output .= " |\n";
        }
        $output .= $dividerLine;
        return $output;

    }

    protected function getMaxLength($items)
    {
        $max = 0;
        foreach ($items as $item) {
            $length = strlen($item);
            if ($length > $max) {
                $max = $length;
            }
        }
        return $max;
    }
}
