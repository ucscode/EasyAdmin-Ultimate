<?php

namespace App\Model\Table;

use Closure;
use ErrorException;

class Table
{
    /**
     * @var Cell[]
     */
    private array $columns = [];

    /**
     * @var Array<Cell[]>
     */
    private array $rows = [];

    private ?Closure $cellAttributes = null;

    private ?Closure $cellValueTransformer = null;

    public function __construct()
    {
        $this->setCellAttributes(function (Cell $cell): array {
            return [
                'data-column' => $cell->getLabel() ?: $cell->getValue(),
                'class' => $cell->getType() == Cell::TYPE_COLUMN ? 'cell-column' : 'cell-data',
                'data-label' => $cell->getLabel(),
            ];
        });
    }

    /**
     * @param Array<string|Cell> $columns
     */
    public function setColumns(array $columns): static
    {
        $this->columns = $this->convertToCell($columns, Cell::TYPE_COLUMN);

        return $this;
    }

    /**
     * @return Cell[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(Cell $cell): static
    {
        $this->columns[] = $cell;

        return $this;
    }

    public function removeColumn(Cell $cell): static
    {
        if(false !== ($key = array_search($cell, $this->columns))) {
            unset($this->columns[$key]);
        }

        return $this;
    }

    public function getColumn(string $label): ?Cell
    {
        foreach($this->columns as $cell) {
            if($cell->getLabel() == $label) {
                return $cell;
            }
        }

        return null;
    }

    /**
     * Each row will be iterated and the data will be converted to a cell if it is a string
     *
     * @param Array<(Cell|string)[]> $rows     An array of arrays containing Cell (or string)
     */
    public function setRows(array $rows): static
    {
        $this->rows = array_map(fn (array $row) => $this->convertToCell($row, Cell::TYPE_DATA), $rows);

        return $this;
    }

    /**
     * @return Array<Cell[]>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Remove a row from the row set.
     *
     * This method receives a callback as parameter and the callback receives an array containing Cells as argument.
     * If the callback returns true, the associated array/row will be removed.
     *
     * @param callable $callback    A callback that will receive and determine the rows to remove
     */
    public function removeRows(callable $callback): static
    {
        $this->rows = array_filter($this->rows, fn (array $row) => call_user_func($callback, $row));

        return $this;
    }

    /**
     * @param Array<Cell|string> $row
     */
    public function addRow(array $row): static
    {
        $this->rows[] = $this->convertToCell($row, Cell::TYPE_DATA);

        return $this;
    }

    /**
     * Set HTML attributes for each cell elements.
     *
     * The callback receives the current cell being iterated and must return an array representing HTML attributes and values
     *
     * @param callable $callback    The callable to dynamically define the cell attributes
     */
    public function setCellAttributes(?callable $callback): static
    {
        $this->cellAttributes = $callback ? Closure::fromCallable($callback) : null;

        return $this;
    }

    public function getCellAttributes(Cell $cell): array
    {
        $attributes = $this->cellAttributes ? call_user_func($this->cellAttributes, $cell) : [];

        if(!is_array($attributes)) {
            throw new ErrorException(
                sprintf("The return type of cell attributes callback must return an array, %s returned instead.", gettype($attributes))
            );
        }

        return $attributes;
    }

    public function setCellValueTransformer(?callable $callback): static
    {
        $this->cellValueTransformer = $callback ? Closure::fromCallable($callback) : null;

        return $this;
    }

    /**
     * Note: Return an empty string to get an empty cell
     */
    public function getCellValueTransformer(Cell $cell): mixed
    {
        $value = $this->cellValueTransformer ? call_user_func($this->cellValueTransformer, $cell) : $cell->getValue();

        if(!is_scalar($value) && !is_null($value)) {
            throw new ErrorException(
                sprintf("The return type of formatted cell value must be scalar, %s returned instead.", gettype($value))
            );
        }

        return $value ?? $cell->getValue();
    }

    /**
     * Convert all data in the array into cell instances
     *
     * @param Array<string|Cell> $sequence  A single row of data
     * @return Cell[]
     * @throws Exception if datatype cannot be converted into cell
     */
    private function convertToCell(array $sequence, string $cellType): array
    {
        $container = [];

        foreach($sequence as $key => $cell) {
            if(!($cell instanceof Cell)) {
                $cell = Cell::new($key)->setValue($cell);
            }

            $cell->setType($cellType);

            $container[] = $cell;
        }

        return $container;
    }
}
