<?php

namespace App\Utility\Table;

use Closure;
use ErrorException;
use InvalidArgumentException;
use RuntimeException;
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
    private const CONFIGURATOR_OFFSET = '@default';

    protected ?string $name = null;

    /**
     * @var ColumnCell[]
     */
    protected array $columns = [];

    /**
     * @var Array<DataCell[]>
     */
    protected array $rows = [];

    /**
     * @var Closure[]
     */
    protected array $configurators = [];

    protected bool $batchActions = false;

    protected ?string $associateIndex = null;

    protected Paginator $paginator;
    
    public function __construct(?string $name = '')
    {
        $this->setName($name);
        $this->paginator = new Paginator();
        $this->setDefaultConfigurator();
    }

    public function setName(?string $name): static
    {
        $name = preg_replace('/^[0-9]+/', '', $name); // Remove leading digits
        $name = preg_replace('/[^a-zA-Z0-9_\-:.]/', '', $name); // Remove invalid characters
        $this->name = preg_replace('/^[^a-zA-Z]+/', '', $name); // Ensure it starts with a letter

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
        $this->columns = $this->cellValues($columns, ColumnCell::class);

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

    /**
     * Each row will be iterated and the data will be converted to a cell if it is a string
     *
     * @param Array<(DataCell|string)[]> $rows     An array of arrays containing Cell (or string)
     */
    public function setRows(array $rows): static
    {
        $this->rows = array_map(function(array $row) {
            return $this->cellValues($row, DataCell::class);
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
     * Get a single row from the available rows
     * 
     * @param int $index        The index of the row
     * @return DataCell[]|null  Returns a collection of DataCell objects or null if row is not found.
     */
    public function getRow(int $index): ?array
    {
        return $this->rows[$index] ?? null;
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
        $this->rows = array_filter($this->rows, fn(array $row) => call_user_func($callback, $row));

        return $this;
    }

    /**
     * @param Array<DataCell|string> $row
     */
    public function addRow(array $row): static
    {
        $this->rows[] = $this->cellValues($row, DataCell::class);

        return $this;
    }

    public function setConfigurator(string $name, ?callable $callback): static
    {
        $this->throwConfiguratorException($name, sprintf('Cannot override "%s" configurator', self::CONFIGURATOR_OFFSET));

        if($callback && !($callback instanceof Closure)) {
            $callback = Closure::fromCallable($callback);
        }

        $this->configurators[$name] = $callback;

        return $this;
    }

    public function getConfigurator(string $name): ?Closure
    {
        return $this->configurators[$name] ?? null;
    }

    public function removeConfigurator(string $name): static
    {
        $this->throwConfiguratorException($name, sprintf('Cannot remove "%s" configurator', self::CONFIGURATOR_OFFSET));

        if(array_key_exists($name, $this->configurators)) {
            unset($this->configurators[$name]);
        }

        return $this;
    }

    /**
     * @internal
     */
    public function configureCell(Cell $cell, int $offset): void
    {
        foreach($this->configurators as $key => $configurator) {
            call_user_func(
                $configurator, 
                $cell,  
                $offset, 
                $this->columns[$offset] ?? null
            );
        }
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
     * Convert all data in the array into Column cell instances
     *
     * @param Array<string|Cell> $sequence  A single row of data
     * @return Cell[]
     */
    private function cellValues(array $sequence, string $cellFqcn): array
    {
        return array_values(array_map(function($value) use($cellFqcn) {
            if($value instanceof $cellFqcn) {
                return $value;
            }

            if(is_object($value) && !method_exists($value, '__toString')) {
                throw new InvalidArgumentException(
                    sprintf("%s::__construct() requires ?string argument, %s provided instead", $cellFqcn, $value::class)
                );
            }

            return $cellFqcn::new($value);;
        }, $sequence));
    }

    private function throwConfiguratorException(string $name, string $error): void
    {
        if(array_key_exists(self::CONFIGURATOR_OFFSET, $this->configurators) && $name === self::CONFIGURATOR_OFFSET) {
            throw new RuntimeException($error);
        }
    }

    private function setDefaultConfigurator(): void
    {
        $this->setConfigurator(self::CONFIGURATOR_OFFSET, function (Cell $cell, int $offset, ?ColumnCell $columnCell) 
        {            
            // set meta label to column value;
            $cell->setMeta('label', $columnCell->getMeta('label') ?? $columnCell->getValue());

            $attributes = ['data-cell' => $offset];

            if($cell instanceof ColumnCell) {
                $attributes += [
                    'data-column' => $cell->getValue(),
                    'class' => 'cell-column',
                ];

                return $cell->setAttributes($attributes);
            }
            
            $attributes += [
                'data-label' => $columnCell?->getValue(),
                'class' => 'cell-data',
            ];

            $cell->setAttributes($attributes);

            if(filter_var($cell->getMeta('value'), FILTER_VALIDATE_EMAIL)) {
                $cell->setMeta('anchor', [
                    'href' => sprintf('mailto:%s', $cell->getMeta('value'))
                ]);
            }

            if(filter_var($cell->getMeta('value'), FILTER_VALIDATE_URL)) {
                $cell->setMeta('anchor', [
                    'href' => $cell->getMeta('value'),
                    'target' => '_blank',
                ]);
            }
        });
    }
}
