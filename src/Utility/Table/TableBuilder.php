<?php

namespace App\Utility\Table;

use Closure;
use ErrorException;
use InvalidArgumentException;
use Ucscode\Paginator\Paginator;

/**
 * A utility class for generating HTML tables dynamically in Symfony applications, designed to be used with Symfony templates.
 * 
 * This class facilitates the creation of HTML table structures by providing a fluent interface for defining table columns and rows. 
 * The resulting table can be rendered in Symfony templates using the provided `utility/table.html.twig` template.
 * 
 * The `utility/table.html.twig` template leverages the information provided by an instance of this class to construct a table suitable for use 
 * in Symfony applications or with Symfony's EasyAdminBundle. It ensures compatibility with various scenarios, including rendering 
 * tables with no data.
 */
class TableBuilder
{
    protected ?string $name = null;

    /**
     * @var ColumnCell[]
     */
    protected array $columns = [];

    /**
     * @var Array<DataCell[]>
     */
    protected array $rows = [];

    protected ?Closure $cellAttributes = null;

    protected ?Closure $cellValueTransformer = null;

    protected bool $batchActions = false;

    protected ?string $associateIndex = null;

    protected Paginator $paginator;
    
    public function __construct(?string $name = '')
    {
        $this->setName($name);
        $this->paginator = new Paginator();
        $this->setDefaultCellAttributes();
    }

    public function setName(?string $name): static
    {
        // Remove leading digits
        $name = preg_replace('/^[0-9]+/', '', $name);
        // Remove invalid characters
        $name = preg_replace('/[^a-zA-Z0-9_\-:.]/', '', $name);
        // Ensure it starts with a letter
        $this->name = preg_replace('/^[^a-zA-Z]+/', '', $name);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param Array<string|ColumnCell> $columns
     */
    public function setColumns(array $columns): static
    {
        $this->columns = $this->convertToCell($columns, ColumnCell::class);

        return $this;
    }

    /**
     * @return ColumnCell[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(ColumnCell $cell): static
    {
        $this->columns[] = $cell;

        return $this;
    }

    public function removeColumn(ColumnCell $cell): static
    {
        if(false !== ($key = array_search($cell, $this->columns))) {
            unset($this->columns[$key]);
        }

        return $this;
    }

    public function getColumnBy(string $property, string $value): ?ColumnCell
    {
        $method = sprintf('get%s', ucfirst($property));
        
        foreach($this->columns as $cell) {
            if(method_exists($cell, $method) && $cell->{$method}() == $value) {
                return $cell;
            }
        }

        return null;
    }

    /**
     * Each row will be iterated and the data will be converted to a cell if it is a string
     *
     * @param Array<(DataCell|string)[]> $rows     An array of arrays containing Cell (or string)
     */
    public function setRows(array $rows): static
    {
        $this->rows = array_map(function(array $row) {
            return $this->convertToCell($row, DataCell::class);
        }, $rows);

        return $this;
    }

    /**
     * @return Array<DataCell[]>
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
     * @param Array<DataCell|string> $row
     */
    public function addRow(array $row): static
    {
        $this->rows[] = $this->convertToCell($row, DataCell::class);

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
        if($callback && !($callback instanceof Closure)) {
            $callback = Closure::fromCallable($callback);
        }

        $this->cellAttributes = $callback;

        return $this;
    }

    /**
     * @internal
     */
    public function getCellAttributes(Cell $cell, int $offset): array
    {
        $attributes = $this->cellAttributes ? call_user_func($this->cellAttributes, $cell, $offset) : [];

        if(!is_array($attributes)) {
            throw new ErrorException(
                sprintf("The return type of cell attributes callback must be an array, %s returned instead.", gettype($attributes))
            );
        }

        return $attributes;
    }

    public function setCellValueTransformer(?callable $callback): static
    {
        if($callback && !($callback instanceof Closure)) {
            $callback = Closure::fromCallable($callback);
        }

        $this->cellValueTransformer = $callback;

        return $this;
    }

    /**
     * Note: Return an empty string to get an empty cell
     * 
     * @internal
     */
    public function getCellValueTransformer(Cell $cell, int $offset): mixed
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
     * Whether to add checkboxes to the table when rendered
     */
    public function setBatchActions(bool $batches, ?string $associateIndex = null): static
    {
        $this->batchActions = $batches;

        if($this->batchActions && $associateIndex === null) {
            throw new InvalidArgumentException(
                sprintf("%s(): requires a associate Index to set values of checkbox inputs", __METHOD__)
            );
        }

        $this->associateIndex = $associateIndex;

        return $this;
    }

    public function hasBatchActions(): bool
    {
        return $this->batchActions;
    }

    public function getAssociateIndex(): ?string
    {
        return $this->associateIndex;
    }

    public function getPaginator(): Paginator
    {
        $this->paginator->setTotalItems(count($this->getRows()));

        return $this->paginator;
    }

    /**
     * Convert all data in the array into cell instances
     *
     * @param Array<string|Cell> $sequence  A single row of data
     * @param string $cellFqcn              The fully qualified class name of the cell to convert into
     * @return Cell[]
     * @throws Exception if datatype cannot be converted into cell or the data is not an instance of the specified $cellFqcn
     */
    private function convertToCell(array $sequence, string $cellFqcn): array
    {
        $container = [];

        foreach($sequence as $key => $cell) {

            if(!($cell instanceof $cellFqcn)) {
                /**
                 * if `$cell` is an instance of a different object, 
                 * then it should have the `__toString()` method.
                 * Otherwise, it will not be able to convert into a cell
                 */
                $cell = $cellFqcn::new($key)->setValue($cell);
            }
            
            if(!($cell instanceof $cellFqcn)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Cell data requires a collection of %s or string, %s given instead.', 
                        $cellFqcn, 
                        gettype($cell) == 'object' ? $cell::class : gettype($cell)
                    )
                );
            }

            $container[] = $cell;
        }

        return $container;
    }

    private function setDefaultCellAttributes(): void
    {
        $this->setCellAttributes(function (Cell $cell, int $offset): array 
        {
            $attributes = [
                'data-cell' => $offset
            ];

            if($cell instanceof ColumnCell) {
                return array_replace($attributes, [
                    'data-column' => $cell->getLabel() ?: $cell->getValue(),
                    'class' => 'cell-column',
                ]);
            }
            
            return array_replace($attributes, [
                'data-label' => $cell->getLabel(),
                'class' => 'cell-data',
            ]);
        });
    }
}
